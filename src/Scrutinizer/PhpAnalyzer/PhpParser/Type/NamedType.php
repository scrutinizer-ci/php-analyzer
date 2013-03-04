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
 * A type where we know the string representation, but not the actual structure of the type.
 *
 * This type acts mostly as UNKNOWN type until actually resolved.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class NamedType extends ProxyObjectType
{
    private $reference;
    private $resolved = false;

    /**
     * Convenience constructor for tests.
     *
     * @param TypeRegistry $registry
     * @param PhpType $type
     *
     * @return NamedType
     */
    public static function createResolved(TypeRegistry $registry, PhpType $type)
    {
        assert('$type->isObjectType()');

        $named = new self($registry, $type->toMaybeObjectType()->getName());
        $named->setReferencedType($type);

        return $named;
    }

    /**
     * Constructor.
     *
     * @param TypeRegistry $registry
     * @param string $reference String Reference to the non-resolved type
     */
    public function __construct(TypeRegistry $registry, $reference)
    {
        parent::__construct($registry, $registry->getNativeType('object'));

        $this->reference = $reference;
    }

    public function isNoResolvedType()
    {
        return ! $this->resolved;
    }

    public function isResolved()
    {
        return $this->resolved;
    }

    public function setReferencedType(PhpType $type)
    {
        if ( ! $type->isObjectType()) {
            throw new \LogicException('The referenced type must be an object type.');
        }

        if ($type->isNoObjectType()) {
            throw new \LogicException('The referenced type must not be the generic object type.');
        }

        parent::setReferencedType($type);
        $this->resolved = true;
    }

    public function getReferenceName()
    {
        return $this->reference;
    }

    public function getDisplayName()
    {
        if (!$this->getReferencedType()->isNoObjectType()) {
            return $this->getReferencedType()->getDisplayName();
        }

        return 'object<'.$this->reference.'>';
    }
}