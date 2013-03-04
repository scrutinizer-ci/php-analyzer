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
use Scrutinizer\PhpAnalyzer\PhpParser\Type\ResourceType;

abstract class RestrictByTrueTypeResultVisitor extends AbstractTypeRestrictionVisitor
{
    public function visitNoObjectType()
    {
        return null;
    }

    public function visitBooleanType()
    {
        return null;
    }

    public function visitNullType()
    {
        return null;
    }

    public function visitIntegerType()
    {
        return null;
    }

    public function visitDoubleType()
    {
        return null;
    }

    public function visitStringType()
    {
        return null;
    }

    public function visitArrayType(ArrayType $type)
    {
        return null;
    }

    public function visitCallableType(CallableType $type)
    {
        return null;
    }
    
    public function visitResourceType(ResourceType $type) 
    {
        return null;
    }
}