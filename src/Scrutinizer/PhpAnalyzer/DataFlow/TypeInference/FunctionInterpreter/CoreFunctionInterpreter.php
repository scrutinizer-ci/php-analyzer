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

use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;
use Scrutinizer\PhpAnalyzer\Model\GlobalFunction;

/**
 * Makes return types for several PHP core functions more precise.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class CoreFunctionInterpreter implements FunctionInterpreterInterface
{
    private $registry;

    public function __construct(TypeRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function getPreciserFunctionReturnTypeKnowingArguments(GlobalFunction $function, array $argValues, array $argTypes)
    {
        switch ($function->getName()) {
            case 'version_compare':
                switch (count($argTypes)) {
                    case 2:
                        return $this->registry->getNativeType('integer');

                    case 3:
                        return $this->registry->getNativeType('boolean');

                    default:
                        return $this->registry->resolveType('integer|boolean');
                }

            case 'unserialize':
                return $this->registry->getNativeType('unknown_checked');

            case 'var_export':
                if (count($argValues) !== 2) {
                    return null;
                }

                if (\Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil::isBoolean($argValues[1])
                        && \Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil::getBooleanValue($argValues[1]) === true) {
                    return $this->registry->getNativeType('string');
                }

                return null;

            case 'min':
            case 'max':
                switch (count($argTypes)) {
                    case 0:
                        return null;

                    case 1:
                        if ($argTypes[0]->isArrayType()) {
                            return $argTypes[0]->getElementType();
                        }

                        return null;

                    default:
                        // TODO: We could make this a bit smarter as some types are always considered
                        //       greater/smaller than other types.
                        //       See http://de1.php.net/manual/en/language.operators.comparison.php
                        return $this->registry->createUnionType($argTypes);
                }

            case 'str_replace':
            case 'preg_filter':
            case 'preg_replace':
            case 'preg_replace_callback':
                if (isset($argTypes[2])) {
                    if ($argTypes[2]->isUnknownType()) {
                        return $this->registry->getNativeType('unknown');
                    }

                    if ($argTypes[2]->isArrayType()) {
                        return $this->registry->resolveType('array<string>');
                    }

                    $nullableScalar = $this->registry->createNullableType($this->registry->getNativeType('scalar'));
                    if ($argTypes[2]->isSubtypeOf($nullableScalar)) {
                        return $this->registry->getNativeType('string');
                    }
                }

                return null; // Use the non-restricted type if we don't know.

            default:
                return null;
        }
    }
}