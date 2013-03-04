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

namespace Scrutinizer\PhpAnalyzer\Model\CallGraph;

use Doctrine\ORM\Mapping as ORM;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\PhpType;

/**
 * @ORM\Entity(readOnly = true)
 * @ORM\Table(name = "callsite_arguments")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class Argument
{
    /** @ORM\Id @ORM\GeneratedValue(strategy = "AUTO") @ORM\Column(type = "integer") */
    private $id;

    /** @ORM\Column(type = "smallint", name = "index_nb") */
    private $index;

    /** @ORM\Column(type = "PhpType") */
    private $phpType;

    /** @ORM\ManyToOne(targetEntity = "CallSite", inversedBy = "args") */
    private $callSite;

    // NOT PERSISTED
    private $astNode;

    public function __construct($index)
    {
        $this->index = (integer) $index;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getIndex()
    {
        return $this->index;
    }

    public function getPhpType()
    {
        return $this->phpType;
    }

    public function setPhpType(PhpType $type)
    {
        $this->phpType = $type;
    }

    public function getAstNode()
    {
        return $this->astNode;
    }

    public function setAstNode(\PHPParser_Node $node)
    {
        $this->astNode = $node;
    }

    public function setCallSite(CallSite $site)
    {
        if (null !== $this->callSite && $this->callSite !== $site) {
            throw new \InvalidArgumentException('The call site cannot be changed.');
        }
        $this->callSite = $site;
    }

    public function getCallSite()
    {
        return $this->callSite;
    }

    public function __clone()
    {
        $this->callSite = null;
    }
}