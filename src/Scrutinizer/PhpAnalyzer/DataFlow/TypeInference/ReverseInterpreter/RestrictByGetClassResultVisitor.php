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
use Scrutinizer\PhpAnalyzer\PhpParser\Type\NamedType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\ObjectType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\PhpType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\UnionType;
use Scrutinizer\PhpAnalyzer\Model\Clazz;

class RestrictByGetClassResultVisitor extends RestrictByTrueTypeResultVisitor
{
    private $value;
    private $resultEqualsValue;

    public function __construct(TypeRegistry $registry, $value, $resultEqualsValue)
    {
        parent::__construct($registry);

        $this->value = $value;
        $this->resultEqualsValue = $resultEqualsValue;
    }
    
    public function visitBooleanType()
    {
        if ($this->resultEqualsValue) {
            return $this->registry->getNativeType('none');
        }
        
        return $this->registry->getNativeType('boolean');
    }

    public function visitNullType()
    {
        if ($this->resultEqualsValue) {
            return $this->registry->getNativeType('none');
        }
        
        return $this->registry->getNativeType('null');
    }
    
    public function visitResourceType(\Scrutinizer\PhpAnalyzer\PhpParser\Type\ResourceType $type)
    {
        if ($this->resultEqualsValue) {
            return $this->registry->getNativeType('none');
        }
        
        return $type;
    }

    public function visitIntegerType()
    {
        if ($this->resultEqualsValue) {
            return $this->registry->getNativeType('none');
        }
        
        return $this->registry->getNativeType('integer');
    }

    public function visitDoubleType()
    {
        if ($this->resultEqualsValue) {
            return $this->registry->getNativeType('none');
        }
        
        return $this->registry->getNativeType('double');
    }

    public function visitStringType()
    {
        if ($this->resultEqualsValue) {
            return $this->registry->getNativeType('none');
        }
        
        return $this->registry->getNativeType('string');
    }

    public function visitArrayType(ArrayType $type)
    {
        if ($this->resultEqualsValue) {
            return $this->registry->getNativeType('none');
        }
        
        return $type;
    }

    public function visitCallableType(CallableType $type)
    {
        if ($this->resultEqualsValue) {
            return $this->registry->getNativeType('none');
        }
        
        return $type;
    }
    
    public function visitObjectType(ObjectType $type)
    {
        $className = $this->getClassName($type);
        if (null !== $className && $className === $this->value) {
            return $this->resultEqualsValue ? $type : $this->registry->getNativeType('none');
        }
        
        return $type;
    }
    
    public function visitNoObjectType() 
    {
        if ($this->resultEqualsValue) {
            return $this->registry->getClassOrCreate($this->value);
        }
        
        return $this->registry->getNativeType('object');
    }
    
    public function visitUnionType(UnionType $type) 
    {
        if ( ! $this->resultEqualsValue) {
            $types = array();
            foreach ($type->getAlternates() as $alt) {
                if ($this->getClassName($alt) === $this->value) {
                    continue;
                }
                
                $types[] = $alt;
            }
            
            return $this->registry->createUnionType($types);
        }
        
        foreach ($type->getAlternates() as $alt) {
            if ($alt->isNoObjectType()) {
                return $this->registry->getClassOrCreate($this->value);
            }
            
            $className = $this->getClassName($alt);
            if (null !== $className && $className === $this->value) {
                return $alt;
            }
        }
        
        return $this->registry->getNativeType('none');
    }
    
    public function visitTopType(PhpType $topType) 
    {
        if ($this->resultEqualsValue) {
            return $this->registry->getClassOrCreate($this->value);
        }
        
        return $topType;
    }
    
    private function getClassName(PhpType $type)
    {
        if ($type instanceof NamedType) {
            return $type->getReferenceName();
        }
        
        $methodContainer = $type->toMaybeObjectType();
        if ($methodContainer instanceof Clazz) {
            return $methodContainer->getName();
        }

        return null;
    }
}