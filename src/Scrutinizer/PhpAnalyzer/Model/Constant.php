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
 * @ORM\Entity(readOnly = true)
 * @ORM\Table(name = "constants")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class Constant
{
    /** @ORM\Id @ORM\Column(type = "integer") @ORM\GeneratedValue(strategy = "AUTO") */
    private $id;

    /** @ORM\Column(type = "string_case") */
    private $name;

    /** @ORM\Column(type = "PhpType") */
    private $phpType;

    /** @ORM\ManyToOne(targetEntity = "PackageVersion") */
    private $packageVersion;

    private $astNode;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getPhpType()
    {
        return $this->phpType;
    }

    public function getAstNode()
    {
        return $this->astNode;
    }

    public function setAstNode(\PHPParser_Node $node)
    {
        $this->astNode = $node;
    }

    public function setPhpType(PhpType $type)
    {
        $this->phpType = $type;
    }

    public function setPackageVersion(PackageVersion $packageVersion)
    {
        $this->packageVersion = $packageVersion;
    }
}