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

namespace Scrutinizer\PhpAnalyzer\PhpParser\Scope;

use Scrutinizer\PhpAnalyzer\PhpParser\Type\PhpType;

class Variable implements StaticSlotInterface
{
    private $name;
    private $type;
    private $typeInferred;
    private $scope;
    private $nameNode;
    private $index;
    private $reference = false;

    public function __construct($name, PhpType $type = null, Scope $scope, $typeInferred = true, $index = 0)
    {
        $this->name = $name;
        $this->type = $type;
        $this->scope = $scope;
        $this->typeInferred = $typeInferred;
        $this->index = $index;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getScope()
    {
        return $this->scope;
    }

    public function isTypeInferred()
    {
        return $this->typeInferred;
    }

    public function getNameNode()
    {
        return $this->nameNode;
    }

    public function getIndex()
    {
        return $this->index;
    }

    public function isReference()
    {
        return $this->reference;
    }

    public function setType(PhpType $type)
    {
        $this->type = $type;
    }

    public function setTypeInferred($bool)
    {
        $this->typeInferred = (boolean) $bool;
    }

    public function setNameNode(\PHPParser_Node $node)
    {
        $this->nameNode = $node;
    }

    public function setIndex($index)
    {
        $this->index = (integer) $index;
    }

    public function setReference($bool)
    {
        $this->reference = (boolean) $bool;
    }
}