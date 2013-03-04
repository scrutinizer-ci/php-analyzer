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

abstract class ProxyObjectType extends ObjectType
{
    private $referencedType;

    /**
     * Constructor.
     *
     * @param TypeRegistry $registry
     * @param string $reference String Reference to the non-resolved type
     */
    public function __construct(TypeRegistry $registry, PhpType $referencedType = null)
    {
        parent::__construct($registry);

        $this->referencedType = $referencedType ?: $registry->getNativeType('object');
    }

    abstract public function getReferenceName();

    public function getDocType(array $importedNamespaces = array())
    {
        return self::getShortestClassNameReference($this->getReferenceName(), $importedNamespaces);
    }

    /**
     * Returns the referenced type.
     *
     * When it has not yet been resolved this will return the UNKNOWN type.
     *
     * @return PhpType
     */
    public function getReferencedType()
    {
        return $this->referencedType;
    }

    public function setReferencedType(PhpType $type)
    {
        if ( ! $type->isObjectType()) {
            throw new \LogicException(sprintf('The referenced type must be an Object type, but got "%s".', $type));
        }
        if ( ! $this->isNoResolvedType()) {
            throw new \LogicException(sprintf('The referenced type "%s" is already resolved, cannot re-resolve to "%s".', $this->referencedType, $type));
        }

        $this->referencedType = $type->addAttributes($this->getAttributes());
    }

    public function addAttribute($key, $value)
    {
        if ($this->isNoResolvedType()) {
            return parent::addAttribute($key, $value);
        }

        $this->referencedType = $this->referencedType->addAttribute($key, $value);

        return $this;
    }

    public function addAttributes(array $kvMap)
    {
        if ($this->isNoResolvedType()) {
            return parent::addAttributes($kvMap);
        }

        $this->referencedType = $this->referencedType->addAttributes($kvMap);

        return $this;
    }

    public function removeAttribute($key)
    {
        if ($this->isNoResolvedType()) {
            return parent::removeAttribute($key);
        }

        $this->referencedType = $this->referencedType->removeAttribute($key);

        return $this;
    }

    public function removeAttributes($kList)
    {
        if ($this->isNoResolvedType()) {
            return parent::removeAttributes($kList);
        }

        $this->referencedType = $this->referencedType->removeAttributes($kList);

        return $this;
    }

    public function isObjectType()
    {
        return true;
    }

    public function isInterface()
    {
        if ($this->isNoResolvedType()) {
            return false;
        }

        return $this->referencedType->isInterface();
    }

    public function isTrait()
    {
        if ($this->isNoResolvedType()) {
            return false;
        }

        return $this->referencedType->isTrait();
    }

    public function isClass()
    {
        if ($this->isNoResolvedType()) {
            return false;
        }

        return $this->referencedType->isClass();
    }

    /**
     * Two proxy object types are equivalent if they reference the same object.
     *
     * Unfortunately, proxy types might not yet be resolved in which case we
     * fallback to do a comparison on the basis of names only.
     *
     * In sane code, all classes should be defined, and thus be resolved after
     * the initial type scanning pass.
     *
     * @return boolean
     */
    public function equals(PhpType $that)
    {
        if ($this === $that) {
            return true;
        }

        if ( ! $this->isNoResolvedType()) {
            return $this->referencedType->equals($that);
        }

        if (null !== $objType = $that->toMaybeObjectType()) {
            return strtolower($objType->getName()) === strtolower($this->getReferenceName());
        }

        if ($that instanceof ProxyObjectType) {
            return strtolower($this->getReferenceName()) === strtolower($that->getReferenceName());
        }

        return false;
    }

    public function matchesObjectContext()
    {
        return $this->referencedType->matchesObjectContext();
    }

    public function canBeCalled()
    {
        return $this->referencedType->canBeCalled();
    }

    public function isNullable()
    {
        return $this->referencedType->isNullable();
    }

    public function isTraversable()
    {
        return $this->referencedType->isTraversable();
    }

    public function toMaybeUnionType()
    {
        return $this->referencedType->toMaybeUnionType();
    }

    public function toMaybeObjectType()
    {
        return $this->referencedType->toMaybeObjectType();
    }

    public function toMaybeArrayType()
    {
        return $this->referencedType->toMaybeArrayType();
    }

    public function testForEquality(PhpType $that)
    {
        if ($this->isNoResolvedType()
                && ($that->isObjectType() || $that->isNoObjectType())) {
            return TernaryValue::get('unknown');
        }

        return $this->referencedType->testForEquality($that);
    }

    public function isSubtypeOf(PhpType $that)
    {
        if (!$this->referencedType instanceof NoObjectType) {
            return $this->referencedType->isSubTypeOf($that);
        }

        if ($that instanceof NamedType) {
            return strtolower($that->getReferenceName()) === strtolower($this->getReferenceName());
        }

        if ($objType = $that->toMaybeObjectType()) {
            return strtolower($objType->getName()) === strtolower($this->getReferenceName());
        }

        if ($that->isNoObjectType() || $that->isUnknownType() || $that->isAllType()
                || $that->isCallableType()) {
            return true;
        }

        if ($that->isUnionType()) {
            foreach ($that->getAlternates() as $alternate) {
                if ($this->isSubtypeOf($alternate)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function canAssignTo(PhpType $that)
    {
        return $this->referencedType->canAssignTo($that);
    }

    public function collapseUnion()
    {
        if ($this->referencedType instanceof UnionType) {
            return $this->referencedType->collapseUnion();
        }

        return $this;
    }
}