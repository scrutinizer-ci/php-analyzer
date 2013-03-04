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

namespace Scrutinizer\PhpAnalyzer\Model;

use Doctrine\ORM\Mapping as ORM;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\PhpType;

/**
 * @ORM\MappedSuperclass
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
abstract class Parameter
{
    /** @ORM\Column(type = "string") */
    private $name;

    /** @ORM\Column(type = "PhpType") */
    private $phpType;

    /** @ORM\Column(type = "smallint", name ="index_nb") */
    private $index;

    /** @ORM\Column(type = "boolean") */
    private $passedByRef = false;

    /** @ORM\Column(type = "boolean") */
    private $optional = false;

    private $astNode;

    public function __construct($name, $index)
    {
        $this->name = $name;
        $this->index = $index;
    }

    public function getIndex()
    {
        return $this->index;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * @return integer
     */
    abstract public function getId();

    public function getPhpType()
    {
        return $this->phpType;
    }

    public function getAstNode()
    {
        return $this->astNode;
    }

    public function isOptional()
    {
        return $this->optional;
    }

    public function isPassedByRef()
    {
        return $this->passedByRef;
    }

    public function setPassedByRef($bool)
    {
        $this->passedByRef = (boolean) $bool;
    }

    public function setAstNode(\PHPParser_Node $node)
    {
        $this->astNode = $node;
    }

    public function setPhpType(PhpType $type)
    {
        $this->phpType = $type;

        if ($this->astNode) {
            $this->astNode->setAttribute('type', $type);
        }
    }

    public function setOptional($bool)
    {
        $this->optional = (boolean) $bool;
    }
}