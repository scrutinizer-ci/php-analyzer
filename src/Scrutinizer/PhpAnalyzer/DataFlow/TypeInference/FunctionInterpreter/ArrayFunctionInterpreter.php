<?php

/*
 * Copyright 2013 Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *     http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\FunctionInterpreter;

use Scrutinizer\PhpAnalyzer\PhpParser\Type\PhpType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\UnionType;
use Scrutinizer\PhpAnalyzer\Model\GlobalFunction;

/**
 * Function Interpreter for the different array_??? functions.
 *
 * Since arrays are heavily used in PHP, it is important to get their types right.
 * Otherwise all the other efforts that we are doing in the type inference fall
 * a bit short as soon as a flow goes through one of these functions.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class ArrayFunctionInterpreter implements FunctionInterpreterInterface
{
    private $registry;

    public function __construct(\Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function getPreciserFunctionReturnTypeKnowingArguments(GlobalFunction $function, array $argValues, array $argTypes)
    {
        switch ($function->getName()) {
            case 'array_change_key_case':
                return $this->casePreserveIncomingArray($argTypes, 'false');

            case 'array_chunk':
            case 'array_combine':
                // TODO
                break;

            case 'array_diff_assoc':
            case 'array_diff_key':
            case 'array_diff_uassoc':
            case 'array_diff_ukey':
            case 'array_diff':
                return $this->casePreserveIncomingArray($argTypes);

            case 'array_fill_keys':
            case 'array_fill':
            case 'array_filter':
            case 'array_flip':
                // TODO
                break;

            case 'array_intersect_assoc':
            case 'array_intersect_key':
            case 'array_intersect_uassoc':
            case 'array_intersect_ukey':
            case 'array_intersect':
                return $this->casePreserveIncomingArray($argTypes);

            case 'array_keys':
                return $this->caseArrayKeys($argTypes);

            case 'array_merge_recursive':
            case 'array_replace_recursive':
            case 'array_merge':
            case 'array_replace':
                return $this->caseArrayMergeReplace($argTypes, $function->getName());

            case 'array_pad':
                return $this->caseArrayPad($argTypes);

            case 'array_pop':
                return $this->caseArrayPop($argTypes);

            case 'array_rand':
                // TODO
                break;

            case 'array_reverse':
                return $this->casePreserveIncomingArray($argTypes);

            case 'array_search':
                return $this->caseArraySearch($argTypes);

            case 'array_shift':
                return $this->caseElementTypeOrElse($argTypes, 'null');

            case 'array_slice':
            case 'array_splice':
                return $this->casePreserveIncomingArray($argTypes);

            case 'array_udiff_assoc':
            case 'array_udiff_uassoc':
            case 'array_udiff':
            case 'array_uintersect_assoc':
            case 'array_uintersect_uassoc':
            case 'array_uintersect':
                return $this->casePreserveIncomingArray($argTypes);

            case 'array_unique':
                return $this->casePreserveIncomingArray($argTypes);

            case 'array_values':
                return $this->caseArrayValues($argTypes);

            case 'compact':
                // TODO
                break;

            case 'current':
            case 'end':
            case 'next':
            case 'pos':
            case 'prev':
            case 'reset':
                return $this->caseElementTypeOrElse($argTypes, 'false');

            case 'each':
            case 'extract':
            case 'key':
            case 'range':
                // TODO
                break;
        }

        return null;
    }

    private function caseArraySearch(array $types)
    {
        if ( ! isset($types[1])) {
            return $this->registry->getNativeType('none');
        }

        if ($types[1]->isArrayType()) {
            return $this->registry->createUnionType(array('false', $types[1]->getKeyType()));
        }

        if ($types[1]->isUnionType()) {
            $keyTypes = $this->getKeyTypes($types[1]);
            $keyTypes[] = 'false';

            return $this->registry->createUnionType($keyTypes);
        }
    }

    private function caseArrayPad(array $types)
    {
        if (3 !== count($types)) {
            return $this->registry->getNativeType('none');
        }

        if ($types[0]->isArrayType()) {
            return $this->registry->getArrayType(
                $this->registry->createUnionType(array($types[0]->getElementType(), $types[2])),
                $this->registry->createUnionType(array($types[0]->getKeyType(), 'integer')));
        }

        if ($types[0]->isUnionType()) {
            $elementTypes = $this->getElementTypes($types[0]);
            if ( ! $elementTypes) {
                return;
            }

            $elementTypes[] = $types[2];

            $keyTypes = $this->getKeyTypes($types[0]);
            $keyTypes[] = 'integer';

            return $this->registry->getArrayType(
                $this->registry->createUnionType($elementTypes),
                $this->registry->createUnionType($keyTypes));
        }
    }

    /**
     * Returns the result type for array_merge, array_replace, and their recursive
     * variants.
     *
     * @param array<PhpType> $types
     * @param string $itemMergeMethod
     *
     * @return PhpType
     */
    private function caseArrayMergeReplace(array $types, $itemMergeMethod)
    {
        if ( ! isset($types[0])) {
            return $this->registry->getNativeType('null');
        }

        $keyTypes = array();
        $elementTypes = array();
        $rawItemTypes = array();
        $allTypesArrays = true;
        for ($i=0,$c=count($types); $i<$c; $i++) {
            if (null === $types[$i]) {
                continue;
            }

            if ($types[$i]->isArrayType()) {
                $keyTypes[] = $types[$i]->getKeyType();
                $elementTypes[] = $types[$i]->getElementType();
                $rawItemTypes[] = $types[$i]->getItemTypes();

                continue;
            }

            if ($types[$i]->isUnionType()) {
                foreach ($types[$i]->getAlternates() as $alt) {
                    if ( ! $alt->isArrayType()) {
                        $allTypesArrays = false;

                        continue;
                    }

                    $keyTypes[] = $alt->getKeyType();
                    $elementTypes[] = $alt->getElementType();
                    $rawItemTypes[] = $alt->getItemTypes();
                }

                continue;
            }

            $allTypesArrays = false;
        }

        // No arrays found in arguments.
        if ( ! $keyTypes) {
            return;
        }

        return $this->registry->getArrayType(
            $this->registry->createUnionType($elementTypes),
            $this->registry->createUnionType($keyTypes),
            $allTypesArrays ? $this->mergeItemTypes($itemMergeMethod, $rawItemTypes) : array());
    }

    private function mergeItemTypes($method, array $rawItems)
    {
        if (count($rawItems) === 1) {
            return $rawItems[0];
        }

        switch ($method) {
            case 'array_merge':
            case 'array_replace':
                return call_user_func_array($method, $rawItems);

            case 'array_merge_recursive':
            case 'array_replace_recursive':
                // For some reason, PHP does not like to operate on objects.
                $translationMap = array();
                foreach ($rawItems as &$rawItemArg) {
                    $this->prepareItemTypes($rawItemArg, $translationMap);
                }
                $mergeResult = call_user_func_array($method, $rawItems);

                // This is a bit of overhead as we not necessarily need to create the
                // Array type for the root, but anyway it should be minimal.
                return $this->synthesizeItemTypes($mergeResult, $translationMap)->getItemTypes();

            default:
                throw new \LogicException('Unsupported merge method '.$method);
        }
    }

    private function synthesizeItemTypes(array $mergeResult, array $translationMap)
    {
        $keyTypes = array();
        $elementTypes = array();
        $itemTypes = array();

        foreach ($mergeResult as $k => $v) {
            if (is_integer($k)) {
                $keyTypes[] = 'integer';
            } else {
                $keyTypes[] = 'string';
            }

            if (is_string($v)) {
                $itemTypes[$k] = $translationMap[$v];
                $elementTypes[] = $itemTypes[$k];
                continue;
            } else if (is_array($v)) {
                $itemTypes[$k] = $this->synthesizeItemTypes($v, $translationMap);
                $elementTypes[] = $itemTypes[$k];
                continue;
            }

            throw new \LogicException(sprintf('Unsupported item type '.gettype($v)));
        }

        return $this->registry->getArrayType(
            $this->registry->createUnionType($elementTypes),
            $this->registry->createUnionType($keyTypes),
            $itemTypes
        );
    }

    private function prepareItemTypes(array &$rawItems, array &$translationMap)
    {
        static $i = 0;

        foreach ($rawItems as $k => $v) {
            assert($v instanceof \Scrutinizer\PhpAnalyzer\PhpParser\Type\PhpType);

            if ($v->isArrayType()) {
                $rawItems[$k] = $v->getItemTypes();
                $this->prepareItemTypes($rawItems[$k], $translationMap);

                continue;
            }

            $translationMap['type'.++$i] = $v;
            $rawItems[$k] = 'type'.$i;
        }
    }

    private function extractItemTypes(array $itemTypes)
    {
        $extractedItemTypes = array();
        foreach ($itemTypes as $name => $itemType) {
            if ($itemType->isArrayType()) {
                $extractedItemTypes[$name] = $this->extractItemTypes($itemType->getItemTypes());
                continue;
            }

            $extractedItemTypes[$name] = $itemType;
        }

        return $extractedItemTypes;
    }

    private function caseElementTypeOrElse(array $types, $emptyType = null)
    {
        if ( ! isset($types[0])) {
            return $this->registry->getNativeType('none');
        }

        if ($types[0]->isArrayType()) {
            return $emptyType ? $this->registry->createUnionType(array($types[0]->getElementType(), $emptyType))
                                : $types[0]->getElementType();
        }

        if ($types[0]->isUnionType()) {
            $elementTypes = $this->getElementTypes($types[0]);
            if ( ! $elementTypes) {
                return;
            }

            if ($emptyType) {
                $elementTypes[] = $emptyType;
            }

            return $this->registry->createUnionType($elementTypes);
        }
    }

    private function casePreserveIncomingArray(array $types)
    {
        if ( ! isset($types[0])) {
            return $this->registry->getNativeType('none');
        }

        if ($types[0]->isArrayType()) {
            return $types[0];
        }

        if ($types[0]->isUnionType()) {
            return $this->getByArrayRestrictedUnion($types[0]);
        }
    }

    private function caseArrayValues(array $types)
    {
        if ( ! isset($types[0])) {
            return $this->registry->getNativeType('none');
        }

        if ($types[0]->isArrayType()) {
            return $this->registry->getArrayType($types[0]->getElementType(), $this->registry->getNativeType('integer'));
        }

        if ($types[0]->isUnionType()) {
            $elementTypes = $this->getElementTypes($types[0]);
            if ( ! $elementTypes) {
                return;
            }

            return $this->registry->getArrayType($this->registry->createUnionType($elementTypes), $this->registry->getNativeType('integer'));
        }
    }

    private function caseArrayKeys(array $types)
    {
        if ( ! isset($types[0])) {
            return $this->registry->getNativeType('none');
        }

        if ($types[0]->isArrayType()) {
            return $this->registry->getArrayType($types[0]->getKeyType(), $this->registry->getNativeType('integer'));
        }

        if ($types[0]->isUnionType()) {
            $keyTypes = $this->getKeyTypes($types[0]);
            if (!$keyTypes) {
                return;
            }

            return $this->registry->getArrayType($this->registry->createUnionType($keyTypes), $this->registry->getNativeType('integer'));
        }
    }

    private function caseArrayPop(array $types)
    {
        if ( ! isset($types[0])) {
            return $this->registry->getNativeType('none');
        }

        if ($types[0]->isArrayType()) {
            return $this->registry->createNullableType($types[0]->getElementType());
        }

        if ($types[0]->isUnionType()) {
            return $this->getByArrayRestrictedUnion($types[0], 'null');
        }
    }

    private function getKeyTypes(UnionType $type)
    {
        $keyTypes = array();
        foreach ($type->getAlternates() as $alt) {
            if ( ! $alt->isArrayType()) {
                continue;
            }
            $keyTypes[] = $alt->getKeyType();
        }

        return $keyTypes;
    }

    private function getElementTypes(UnionType $type)
    {
        $elementTypes = array();
        foreach ($type->getAlternates() as $alt) {
            if ( ! $alt->isArrayType()) {
                continue;
            }
            $elementTypes[] = $alt->getElementType();
        }

        return $elementTypes;
    }

    private function getByArrayRestrictedUnion(UnionType $type, $typeOfNonArrayTypes = null)
    {
        $types = array();
        $hasNoArray = false;

        foreach ($type->getAlternates() as $alt) {
            if ( ! $alt->isArrayType()) {
                $hasNoArray = true;
                continue;
            }
            $types[] = $alt;
        }

        if (null !== $typeOfNonArrayTypes && $hasNoArray) {
            $types[] = $typeOfNonArrayTypes;
        }

        return $this->registry->createUnionType($types);
    }
}