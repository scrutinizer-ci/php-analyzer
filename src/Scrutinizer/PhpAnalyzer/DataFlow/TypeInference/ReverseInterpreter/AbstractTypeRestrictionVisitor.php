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

namespace Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\ReverseInterpreter;

use Scrutinizer\PhpAnalyzer\PhpParser\Type\PhpType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\UnionType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\VisitorInterface;

abstract class AbstractTypeRestrictionVisitor implements VisitorInterface
{
    /** @var TypeRegistry */
    protected $registry;

    public function __construct(TypeRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Abstracts away the similarities between visiting the UNKNOWN, and the
     * ALL type.
     *
     * @param PhpType $topType
     * @return PhpType the restricted type
     */
    abstract protected function visitTopType(PhpType $topType);

    public function visitAllType()
    {
        return $this->visitTopType($this->registry->getNativeType('all'));
    }

    public function visitUnknownType()
    {
        return $this->visitTopType($this->registry->getNativeType('unknown'));
    }

    public function visitUnionType(UnionType $type)
    {
        $restricted = null;
        foreach ($type->getAlternates() as $alternate) {
            $restrictedAlternate = $alternate->visit($this);
            if (null !== $restrictedAlternate) {
                if (null === $restricted) {
                    $restricted = $restrictedAlternate;
                } else {
                    $restricted = $restrictedAlternate->getLeastSupertype($restricted);
                }
            }
        }

        return $restricted;
    }

    public function visitNoType()
    {
        return $this->registry->getNativeType('none');
    }
}