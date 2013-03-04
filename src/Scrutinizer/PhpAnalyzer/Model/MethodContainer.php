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
use Scrutinizer\PhpAnalyzer\PhpParser\Type\ObjectType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\PhpType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\TernaryValue;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;

/**
 * @ORM\Entity(readOnly = true)
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name = "type", type = "string")
 * @ORM\DiscriminatorMap({"trait" = "Scrutinizer\PhpAnalyzer\Model\TraitC", "class" = "Scrutinizer\PhpAnalyzer\Model\Clazz", "interface" = "Scrutinizer\PhpAnalyzer\Model\InterfaceC"})
 * @ORM\Table(name = "method_containers", uniqueConstraints = {
 *     @ORM\UniqueConstraint(columns = {"packageVersion_id", "name"}),
 * })
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
abstract class MethodContainer extends ObjectType
{
    /** @ORM\Column(type = "integer") @ORM\Id @ORM\GeneratedValue(strategy = "AUTO") */
    private $id;

    /** @ORM\ManyToOne(targetEntity = "PackageVersion", inversedBy="containers") */
    private $packageVersion;

    /** @ORM\Column(type = "string") */
    private $name;

    /** @ORM\Column(type = "boolean") */
    private $normalized = false;

    // NON-PERSISTENT FIELDS
    private $importedNamespaces = array();
    private $astNode;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getDocType(array $importedNamespaces = array())
    {
        return self::getShortestClassNameReference($this->name, $importedNamespaces);
    }

    public function getPackageVersion()
    {
        return $this->packageVersion;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDisplayName()
    {
        return sprintf('object<%s>', $this->name);
    }

    public function isNormalized()
    {
        return $this->normalized;
    }

    public function getAstNode()
    {
        return $this->astNode;
    }

    public function setAstNode(\PHPParser_Node $node)
    {
        $this->astNode = $node;
    }

    public function getNamespace()
    {
        $parts = explode("\\", $this->name);
        if (1 === count($parts)) {
            return '';
        }

        return implode("\\", array_slice($parts, 0, -1));
    }

    public function getShortName()
    {
        $parts = explode("\\", $this->name);

        return end($parts);
    }

    public function testForEquality(PhpType $that)
    {
        if (null !== $rs = parent::testForEquality($that)) {
            return $rs;
        }

        if ($that->isNoObjectType()) {
            return TernaryValue::get('unknown');
        }

        if (null === $objType = $that->toMaybeObjectType()) {
            return TernaryValue::get('false');
        }

        if (strtolower($objType->name) !== strtolower($this->name)) {
            return TernaryValue::get('false');
        }

        return TernaryValue::get('unknown');
    }

    public function setImportedNamespaces(array $namespaces)
    {
        $this->importedNamespaces = $namespaces;
    }

    public function getImportedNamespaces()
    {
        return $this->importedNamespaces;
    }

    public function canBeCalled()
    {
        return $this->hasMethod('__invoke');
    }

    public function toMaybeObjectType()
    {
        return $this;
    }

    public function setPackageVersion(PackageVersion $packageVersion)
    {
        if (null !== $this->packageVersion && $this->packageVersion !== $packageVersion) {
            throw new \InvalidArgumentException('The packageVersion of a class cannot be changed.');
        }

        foreach ($this->getMethods() as $method) {
            if ($method->isInherited()) {
                continue;
            }

            $method->setPackageVersion($packageVersion);
        }

        $this->packageVersion = $packageVersion;
    }

    public function setTypeRegistry(TypeRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function getTypeRegistry()
    {
        return $this->registry;
    }

    /**
     * @param boolean $bool
     */
    public function setNormalized($bool)
    {
        $this->normalized = (boolean) $bool;
    }

    /**
     * @return array<ContainerMethodInterface>
     */
    abstract public function getMethods();

    /**
     * @return array<string>
     */
    abstract public function getMethodNames();

    /**
     * @param string $name
     *
     * @return boolean
     */
    abstract public function hasMethod($name);

    /**
     * @param string $name
     *
     * @return ContainerMethodInterface
     */
    abstract public function getMethod($name);

    /**
     * @param Method $method
     * @param string|null $declaringClass
     *
     * @return void
     */
    abstract public function addMethod(Method $method, $declaringClass = null);
}
