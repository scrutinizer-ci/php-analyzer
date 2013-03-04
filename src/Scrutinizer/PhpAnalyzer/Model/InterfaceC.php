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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\PhpType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\ProxyObjectType;

/**
 * @ORM\Entity(readOnly = true)
 *
 * @author Johannes
 */
class InterfaceC extends MethodContainer implements ConstantContainerInterface
{
    /** @ORM\OneToMany(targetEntity = "InterfaceMethod", mappedBy="interface", indexBy = "name", orphanRemoval = true, cascade = {"persist"}, fetch = "EAGER") */
    private $methods;

    /** @ORM\OneToMany(targetEntity = "InterfaceConstant", mappedBy = "interface", indexBy = "name", orphanRemoval = true, cascade = {"persist"}, fetch = "EAGER") */
    private $constants;

    /** @ORM\Column(type = "simple_array") */
    private $extendedInterfaces = array();

    public function __construct($name)
    {
        parent::__construct($name);
        $this->methods = new ArrayCollection();
        $this->constants = new ArrayCollection();
    }

    public function addMethod(Method $method, $declaringInterface = null)
    {
        $interfaceMethod = new InterfaceMethod($this, $method, $declaringInterface);
        $this->methods->set(strtolower($method->getName()), $interfaceMethod);
    }

    public function hasMethod($name)
    {
        return $this->methods->containsKey(strtolower($name));
    }

    public function getMethod($name)
    {
        return $this->methods->get(strtolower($name));
    }

    public function getMethodNames()
    {
        $names = array();
        foreach ($this->methods as $interfaceMethod) {
            $names[] = $interfaceMethod->getMethod()->getName();
        }

        return $names;
    }

    public function getImplementingClasses()
    {
        if (null === $this->registry) {
            throw new \LogicException('The type registry is not set; getImplementingClasses() is not available.');
        }

        return $this->registry->getImplementingClasses($this->getName());
    }

    public function isTraversable()
    {
        if (!$this->isNormalized()) {
            return true;
        }

        return $this->isSubtypeOf($this->registry->getClass('Traversable'));
    }

    public function getTraversableElementType()
    {
        if (!$this->hasExtendedInterface('Traversable') && $this->isNormalized()) {
            return $this->registry->getNativeType('none');
        }

        // The actual type of the traversable is up to the actual implementation.
        // There is no way for us to determine it for interfaces.
        return $this->registry->getNativeType('unknown');
    }

    public function getMethods()
    {
        return $this->methods;
    }

    public function isInterface()
    {
        return true;
    }

    public function equals(PhpType $type)
    {
        if ($type->isInterface() && strtolower($type->toMaybeObjectType()->getName()) === strtolower($this->getName())) {
            return true;
        }

        if ($type instanceof ProxyObjectType && strtolower($type->getReferenceName()) === strtolower($this->getName())) {
            return true;
        }

        return false;
    }

    public function isSubtypeOf(PhpType $that)
    {
        if (PhpType::isSubTypeOf($that)) {
            return true;
        }

        if ($that->isNoObjectType()) {
            return true;
        }

        if ($that->isCallableType()
                && (!$this->isNormalized() || $this->hasMethod('__invoke'))) {
            return true;
        }

        if (false === $that->isObjectType()) {
            return false;
        }

        if (false === $this->isNormalized()) {
            return true;
        }

        if ($that->isInterface()) {
            $name = strtolower($that->toMaybeObjectType()->getName());
            foreach ($this->extendedInterfaces as $iName) {
                if ($name === strtolower($iName)) {
                    return true;
                }
            }

            return false;
        }

        if ($that instanceof ProxyObjectType) {
            $name = strtolower($that->getReferenceName());
            foreach ($this->extendedInterfaces as $iName) {
                if ($name === strtolower($iName)) {
                    return true;
                }
            }

            return false;
        }

        return false;
    }

    public function getExtendedInterfaceTypes()
    {
        $types = array();
        foreach ($this->extendedInterfaces as $name) {
            $types[] = $this->registry->getClass($name);
        }

        return $types;
    }

    public function addExtendedInterface($name)
    {
        $this->extendedInterfaces[] = $name;
    }

    public function setExtendedInterfaces(array $names)
    {
        $this->extendedInterfaces = $names;
    }

    public function hasExtendedInterface($name)
    {
        return in_array($name, $this->extendedInterfaces, true);
    }

    public function getExtendedInterfaces()
    {
        return $this->extendedInterfaces;
    }

    public function getConstants()
    {
        return $this->constants;
    }

    public function hasConstant($name)
    {
        return $this->constants->containsKey($name);
    }

    public function getConstant($name)
    {
        return $this->constants->get($name);
    }

    public function addConstant(Constant $constant, $declaringClass = null)
    {
        $classConstant = new InterfaceConstant($this, $constant, $declaringClass);
        $this->constants->set($constant->getName(), $classConstant);
    }

    public function setPackageVersion(PackageVersion $packageVersion)
    {
        parent::setPackageVersion($packageVersion);

        foreach ($this->constants as $constant) {
            if ($constant->isInherited()) {
                continue;
            }

            $constant->setPackageVersion($packageVersion);
        }
    }
}