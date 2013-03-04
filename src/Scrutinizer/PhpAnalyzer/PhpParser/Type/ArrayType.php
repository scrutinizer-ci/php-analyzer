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

namespace Scrutinizer\PhpAnalyzer\PhpParser\Type;

/**
 * Represents all sorts of array types.
 *
 * Currently, this type deals with list-style arrays, associative, map-style arrays, and unknown-style arrays. We might
 * consider splitting the different behaviors into dedicated sub-classes at some point.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class ArrayType extends PhpType
{
    /** @var PhpType */
    private $keyType;

    /** @var PhpType */
    private $elementType;

    /** @var PhpType[] */
    private $itemTypes;

    public function __construct(TypeRegistry $registry, PhpType $elementType = null, PhpType $keyType = null, array $itemTypes = array())
    {
        parent::__construct($registry);

        // In PHP, the key is always either an integer, or a string. If something else is
        // passed, then we automatically narrow it down.
        if ($keyType && ! $keyType->isSubTypeOf($registry->getNativeType('generic_array_key'))) {
            $keyType = $registry->getNativeType('generic_array_key');
        }

        // PHP always forces either a string, or an integer as key type.
        $this->keyType = $keyType ?: $registry->getNativeType('generic_array_key');
        $this->elementType = $elementType ?: $registry->getNativeType('generic_array_value');
        $this->itemTypes = $itemTypes;
    }

    public function inferItemType($key, PhpType $type)
    {
        $newType = clone $this;
        $newType->itemTypes[$key] = $type;

        $newKeyType = null;
        if (is_string($key)) {
            $newKeyType = $this->registry->getNativeType('string');
        } else if (is_int($key)) {
            $newKeyType = $this->registry->getNativeType('integer');
        }

        if (null !== $newKeyType) {
            if ($newType->keyType === $this->registry->getNativeType('generic_array_key')) {
                $newType->keyType = $newKeyType;
            } else {
                $newType->keyType = $this->registry->createUnionType([$newType->keyType, $newKeyType]);
            }
        }

        if ($newType->elementType === $this->registry->getNativeType('generic_array_value')) {
            $newType->elementType = $type;
        } else {
            $newType->elementType = $this->registry->createUnionType([$this->elementType, $type]);
        }

        return $newType;
    }

    public function getItemType($key)
    {
        if (isset($this->itemTypes[$key])) {
            return new \PhpOption\Some($this->itemTypes[$key]);
        }

        return \PhpOption\None::create();
    }

    public function getItemTypes()
    {
        return $this->itemTypes;
    }

    public function getDocType(array $importedNamespaces = array())
    {
        if ($this->elementType->isUnknownType() || $this->elementType->isAllType()) {
            return 'array';
        }

        if ($this->keyType->isIntegerType()) {
            return 'array<'.$this->elementType->getDocType($importedNamespaces).'>';

            // TODO: Should we optionally allow PHPDocumentor style below?
//            $elementDocType = $this->elementType->getDocType($importedNamespaces);
//
//            // For unions, PHP documentor supports "(int|string)[]".
//            if ($this->elementType->isUnionType()) {
//                $elementDocType = '('.$elementDocType.')';
//            }
//
//            return $elementDocType.'[]';
        }

        if ($this->keyType->isStringType()) {
            return 'array<string,'.$this->elementType->getDocType($importedNamespaces).'>';
        }

        return 'array<*,'.$this->elementType->getDocType($importedNamespaces).'>';
    }

    public function isSubTypeOf(PhpType $that)
    {
        if (parent::isSubTypeOf($that)) {
            return true;
        }

        if ($that->isArrayType()) {
            // Every Array type is a subtype of the generic Array type.
            if ($that === $this->registry->getNativeType('array')) {
                return true;
            }

            // Key and Element Types must be subtypes.
            if ( ! $this->keyType->isSubTypeOf($that->keyType)) {
                return false;
            }
            if ( ! $this->elementType->isSubTypeOf($that->elementType)
                       // This second condition is a bit special and it might actually be the wrong place to add this
                       // (we will need to watch the impact). Basically, on one side we do not want to loose type information
                       // as soon as an unknown type enters the field, on the other hand if we only can choose between
                       // unknown and all types, then the unknown type is actually preferable as it will lead to less
                       // false-positives. In the end, this behavior here would not work in a real compiler as we basically
                       // cannot guarantee that the element type is always of the more specific type, but in practice in
                       // most cases people only place a single type in arrays unless it is used as map which we handle
                       // with types anyway. If this proves not useful, we might consider moving it to the getLeastSuperType method.
                    || ($this->registry->getNativeType('generic_array_value') === $this->elementType
                            && ! $that->elementType->equals($this->registry->getNativeType('all')))) {
                return false;
            }

            // If the other array has no item types, this is a sub-type of the other array.
            if (empty($that->itemTypes)) {
                return true;
            }

            // If that is the case, we also need to ensure that this type has at least all the items
            // of the other types, and that their respective types are also sub-types.
            foreach ($that->itemTypes as $name => $itemType) {
                if ( ! isset($this->itemTypes[$name])) {
                    return false;
                }

                if ( ! $this->itemTypes[$name]->isSubtypeOf($itemType)) {
                    return false;
                }
            }

            return true;
        }

        if ($that->isCallableType()) {
            return true;
        }


        return false;
    }

    public function visit(VisitorInterface $visitor)
    {
        return $visitor->visitArrayType($this);
    }

    public function testForEquality(PhpType $that)
    {
        $rs = parent::testForEquality($that);
        if (null !== $rs) {
            return $rs;
        }

        // Most of (all?) of the array cases are already handled by the
        // default implementation.
        return TernaryValue::get('false');
    }

    public function isTraversable()
    {
        return true;
    }

    public function equals(PhpType $that)
    {
        if ( ! $that->isArrayType()) {
            return false;
        }

        $that = $that->toMaybeArrayType();
        if ( ! $that->keyType->equals($this->keyType)) {
            return false;
        }

        if ( ! $that->elementType->equals($this->elementType)) {
            return false;
        }

        if (empty($this->itemTypes) && empty($that->itemTypes)) {
            return true;
        }

        if (count($this->itemTypes) !== count($that->itemTypes)) {
            return false;
        }

        foreach ($this->itemTypes as $name => $itemType) {
            if ( ! isset($that->itemTypes[$name])) {
                return false;
            }

            if ( ! $itemType->equals($that->itemTypes[$name])) {
                return false;
            }
        }

        return true;
    }

    public function getElementType()
    {
        return $this->elementType;
    }

    public function getKeyType()
    {
        return $this->keyType;
    }

    public function getDisplayName()
    {
        if ($this->keyType->equals($this->registry->getNativeType('generic_array_key'))
                && $this->elementType->equals($this->registry->getNativeType('generic_array_value'))) {
            return 'array';
        }

        if (empty($this->itemTypes)) {
            return 'array<'.$this->keyType.','.$this->elementType.'>';
        }

        return sprintf(
            'array<%s,%s,%s>',
            $this->keyType,
            $this->elementType,
            json_encode(array_map('strval', $this->itemTypes), JSON_FORCE_OBJECT));
    }

    public function getPossibleOutcomesComparedToBoolean()
    {
        return array(true, false);
    }

    public function isArrayType()
    {
        return true;
    }

    public function toMaybeArrayType()
    {
        return $this;
    }

    public function getLeastSuperType(PhpType $that)
    {
        if ($that->isArrayType()) {
            if ($this->isSubtypeOf($that)) {
                // We have a special case, that is if we compare array<unknown> to array<all>. Although, both types are
                // subtypes of each other, we always must have a predictable outcome of this method regardless of the
                // order of types, i.e. $a->getLeastSuperType($b) === $b->getLeastSuperType($a).
                if ($this->elementType->isUnknownType() && $that->elementType->isAllType()) {
                    return $this;
                }
                if ($that->elementType->isUnknownType() && $this->elementType->isAllType()) {
                    return $that;
                }

                return parent::getLeastSuperType($that);
            }

            $genericArrayType = $this->registry->getNativeType('array');
            if ($this === $genericArrayType) {
                return $this;
            }
            if ($that === $genericArrayType) {
                return $that;
            }

            // If both arrays have different item types defined, we keep them as
            // separate arrays in a union type. It can be used as an indication
            // that a key is not always defined.
            if (count($this->itemTypes) !== count($that->itemTypes)
                    || (boolean) array_diff_key($this->itemTypes, $that->itemTypes)) {
                return parent::getLeastSuperType($that);
            }

            $keyType = $this->registry->createUnionType(array($this->keyType, $that->keyType));
            $elementType = $this->registry->createUnionType(array($this->elementType, $that->elementType));
            $itemTypes = array();
            foreach ($this->itemTypes as $name => $itemType) {
                $itemTypes[$name] = $this->registry->createUnionType(array($itemType, $that->itemTypes[$name]));
            }

            return $this->registry->getArrayType($elementType, $keyType, $itemTypes);
        }

        return parent::getLeastSuperType($that);
    }
}