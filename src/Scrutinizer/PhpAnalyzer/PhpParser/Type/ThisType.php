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

use Scrutinizer\PhpAnalyzer\Model\MethodContainer;

/**
 * Represents a late binding type that is resolved to the objects thisType in
 * the context that it is being used in.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class ThisType extends ProxyObjectType
{
    /**
     * Constructor.
     *
     * In contrast to the parent constructor, we force the type to be an object here.
     *
     * @param TypeRegistry $registry
     * @param ObjectType $creationContext
     */
    public function __construct(TypeRegistry $registry, ObjectType $creationContext)
    {
        parent::__construct($registry, $creationContext);
    }

    public function getReferenceName()
    {
        $type = $this->getReferencedType();

        if ($type instanceof ProxyObjectType) {
            return $type->getReferenceName();
        }

        if ($type instanceof MethodContainer) {
            return $type->getName();
        }

        throw new \LogicException(sprintf('Unsupported this type context "%s".', get_class($type)));
    }

    public function getDisplayName()
    {
        return 'object<'.$this->getReferenceName().'>';
    }

    public function __toString()
    {
        return 'this<'.$this->getReferenceName().'>';
    }
}