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

use Scrutinizer\PhpAnalyzer\PhpParser\Type\ArrayType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\CallableType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\ObjectType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\PhpType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\ResourceType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;

class RestrictByGettypeResultVisitor extends AbstractTypeRestrictionVisitor
{
    private $value;
    private $resultEqualsValue;

    public function __construct(TypeRegistry $registry, $value, $resultEqualsValue)
    {
        parent::__construct($registry);

        $this->value = $value;
        $this->resultEqualsValue = $resultEqualsValue;
    }

    public function visitNoObjectType()
    {
        return $this->matches('object') ? $this->registry->getNativeType('object') : null;
    }

    public function visitBooleanType()
    {
        return $this->matches('boolean') ? $this->registry->getNativeType('boolean') : null;
    }

    public function visitNullType()
    {
        return $this->matches('NULL') ? $this->registry->getNativeType('null') : null;
    }

    public function visitIntegerType()
    {
        return $this->matches('integer') ? $this->registry->getNativeType('integer') : null;
    }

    public function visitDoubleType()
    {
        return $this->matches('double') ? $this->registry->getNativeType('double') : null;
    }

    public function visitObjectType(ObjectType $type)
    {
        return $this->matches('object') ? $type : null;
    }

    public function visitStringType()
    {
        return $this->matches('string') ? $this->registry->getNativeType('string') : null;
    }

    public function visitArrayType(ArrayType $type)
    {
        return $this->matches('array') ? $type : null;
    }

    public function visitCallableType(CallableType $type)
    {
        if ($this->matches('array')) {
            return $this->registry->getNativeType('array');
        }
        if ($this->matches('object')) {
            return $this->registry->getNativeType('object');
        }
        if ($this->matches('string')) {
            return $this->registry->getNativeType('string');
        }
        
        return null;
    }
    
    public function visitResourceType(ResourceType $type)
    {
        return $this->matches('resource') ? $type : null;
    }

    protected function visitTopType(PhpType $topType)
    {
        $result = $topType;

        if ($this->resultEqualsValue) {
            $typeByName = ChainableReverseAbstractInterpreter::getNativeTypeForGettypeResultHelper($this->registry, $this->value);
            if (null !== $typeByName) {
                $result = $typeByName;
            }
        }

        return $result;
    }

    private function matches($result)
    {
        return ($result === $this->value) === $this->resultEqualsValue;
    }
}