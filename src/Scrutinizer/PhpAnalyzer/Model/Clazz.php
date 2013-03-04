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
class Clazz extends MethodContainer implements ConstantContainerInterface
{
    const MODIFIER_FINAL = 1;
    const MODIFIER_ABSTRACT = 2;

    /** @ORM\Column(type = "string_nocase") */
    private $superClass;

    /** @ORM\Column(type = "simple_array") */
    private $superClasses = array();

    /** @ORM\Column(type = "simple_array") */
    private $implementedInterfaces = array();

    /** @ORM\OneToMany(targetEntity = "ClassMethod", mappedBy="class", indexBy = "name", orphanRemoval = true, cascade = {"persist", "remove"}, fetch = "EAGER") */
    private $methods;

    /** @ORM\OneToMany(targetEntity = "ClassProperty", mappedBy="class", indexBy = "name", orphanRemoval = true, cascade = {"persist", "remove"}, fetch = "EAGER") */
    private $properties;

    /** @ORM\OneToMany(targetEntity = "ClassConstant", mappedBy = "class", indexBy = "name", orphanRemoval = true, cascade = {"persist", "remove"}, fetch = "EAGER") */
    private $constants;

    /** @ORM\Column(type = "smallint") */
    private $modifier = 0;

    public function __construct($name)
    {
        parent::__construct($name);

        $this->methods = new ArrayCollection();
        $this->properties = new ArrayCollection();
        $this->constants = new ArrayCollection();
    }

    public function setSuperClass($class)
    {
        $this->superClass = $class;
    }

    public function setModifier($modifier)
    {
        $this->modifier = (integer) $modifier;
    }

    public function getSuperClass()
    {
        return $this->superClass;
    }

    public function getSuperClasses()
    {
        return $this->superClasses;
    }

    public function setSuperClasses(array $names)
    {
        $this->superClasses = $names;
    }

    public function getSuperClassType()
    {
        if (null === $this->superClass) {
            return null;
        }

        return $this->registry->getClass($this->superClass);
    }

    /**
     * @return array<InterfaceC>
     */
    public function getImplementedInterfaceTypes()
    {
        $types = array();
        foreach ($this->implementedInterfaces as $name) {
            if (null === $class = $this->registry->getClass($name)) {
                continue;
            }

            $types[] = $class;
        }

        return $types;
    }

    public function getImplementedInterfaces()
    {
        return $this->implementedInterfaces;
    }

    public function addImplementedInterface($name)
    {
        $this->implementedInterfaces[] = $name;
    }

    public function setImplementedInterfaces(array $names)
    {
        $this->implementedInterfaces = $names;
    }

    public function isImplementing($name)
    {
        $lowerName = strtolower($name);
        foreach ($this->implementedInterfaces as $iFace) {
            if ($lowerName === strtolower($iFace)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return ContainerMethodInterface|null
     */
    public function getContractDefiningMethod($name)
    {
        $class = $this;
        do {
            if ( ! $class->hasMethod($name)) {
                return null;
            }

            if ($class->getMethod($name)->isAbstract()) {
                return $class->getMethod($name);
            }
        } while (null !== $class = $class->getSuperClassType());

        foreach ($this->getImplementedInterfaceTypes() as $interface) {
            if ($interface->hasMethod($name)) {
                return $interface->getMethod($name);
            }
        }

        return null;
    }

    public function hasImplementedInterface($name)
    {
        return in_array($name, $this->implementedInterfaces, true);
    }

    public function isTraversable()
    {
        if (!$this->isNormalized()) {
            return true;
        }

        return $this->hasImplementedInterface('Traversable');
    }

    public function isUtilityClass()
    {
        if (null !== $this->superClass
                && ($superClassType = $this->getSuperClassType())
                && ($superClassType = $superClassType->toMaybeObjectType())
                && !$superClassType->isUtilityClass()) {
            return false;
        }

        $hasStaticMembers = false;
        foreach ($this->getMethods() as $method) {
            if ($method->isConstructor() && !$method->isPublic()) {
                continue;
            }

            if (!$method->isStatic()) {
                return false;
            }

            $hasStaticMembers = true;
        }

        return $hasStaticMembers;
    }

    /**
     * Returns the type of the parameter type of the Traversable.
     */
    public function getTraversableElementType()
    {
        if (null === $this->registry) {
            throw new \LogicException('Registry is not set for type: '.$this.' ('.get_class($this).')');
        }

        if ($this->hasImplementedInterface('IteratorAggregate')) {
            // Maybe that method is implemented by a sub-class, and this class
            // has only implemented the interface.
            if (!$this->hasMethod('getIterator')) {
                return $this->registry->getNativeType('unknown');
            }

            // TODO: We need to add support for parameterized types.
            //       So that, we can assign a parameter type to iterators,
            //       and check this here.
            return $this->registry->getNativeType('unknown');
        } else if ($this->hasImplementedInterface('Iterator')) {
            if (!$this->hasMethod('current')) {
                return $this->registry->getNativeType('unknown');
            }

            return $this->getMethod('current')->getReturnType();
        }

        return $this->isNormalized() ?
                    $this->registry->getNativeType('none')
                    : $this->registry->getNativeType('unknown');
    }

    public function isSubTypeOf(PhpType $that)
    {
        if (PhpType::isSubTypeOf($that)) {
            return true;
        }

        if ($that->isNoObjectType()) {
            return true;
        }

        if ($that->isCallableType()) {
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
            foreach ($this->implementedInterfaces as $iName) {
                if ($name === strtolower($iName)) {
                    return true;
                }
            }

            return false;
        }

        // means it is a normal class
        if ($that->isClass()) {
            $name = strtolower($that->toMaybeObjectType()->getName());
            foreach ($this->superClasses as $sName) {
                if ($name === strtolower($sName)) {
                    return true;
                }
            }

            return false;
        }

        if ($that instanceof ProxyObjectType) {
            $name = strtolower($that->getReferenceName());
            foreach ($this->implementedInterfaces as $iName) {
                if ($name === strtolower($iName)) {
                    return true;
                }
            }

            foreach ($this->superClasses as $sName) {
                if ($name === strtolower($sName)) {
                    return true;
                }
            }

            return false;
        }

        return false;
    }

    public function canBeCalled()
    {
        if ( ! $this->isNormalized()) {
            return true;
        }

        return $this->hasMethod('__invoke');
    }

    public function addMethod(Method $method, $declaringClass = null)
    {
        $classMethod = new ClassMethod($this, $method, $declaringClass);
        $this->methods->set(strtolower($method->getName()), $classMethod);
    }

    public function hasMethod($name)
    {
        return $this->methods->containsKey(strtolower($name));
    }

    public function equals(PhpType $type)
    {
        if ($type->isClass() && strtolower($type->toMaybeObjectType()->getName()) === strtolower($this->getName())) {
            return true;
        }

        if ($type instanceof ProxyObjectType && strtolower($type->getReferenceName()) === strtolower($this->getName())) {
            return true;
        }

        return false;
    }

    public function getMethod($name)
    {
        return $this->methods->get(strtolower($name));
    }

    public function getMethodNames()
    {
        $names = array();
        foreach ($this->methods as $classMethod) {
            $names[] = $classMethod->getMethod()->getName();
        }

        return $names;
    }

    public function isFinal()
    {
        return self::MODIFIER_FINAL === ($this->modifier & self::MODIFIER_FINAL);
    }

    public function isAbstract()
    {
        return self::MODIFIER_ABSTRACT === ($this->modifier & self::MODIFIER_ABSTRACT);
    }

    public function getModifier()
    {
        return $this->modifier;
    }

    public function getMethods()
    {
        return $this->methods;
    }

    public function getProperties()
    {
        return $this->properties;
    }

    public function hasProperty($name)
    {
        return $this->properties->containsKey($name);
    }

    public function getProperty($name)
    {
        return $this->properties->get($name);
    }

    public function getPropertyNames()
    {
        return $this->properties->getKeys();
    }

    public function addProperty(Property $property, $declaringClass = null)
    {
        $classProperty = new ClassProperty($this, $property, $declaringClass);
        $this->properties->set($property->getName(), $classProperty);
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
        $classConstant = new ClassConstant($this, $constant, $declaringClass);
        $this->constants->set($constant->getName(), $classConstant);
    }

    public function setPackageVersion(PackageVersion $packageVersion)
    {
        parent::setPackageVersion($packageVersion);

        foreach ($this->properties as $property) {
            if ($property->isInherited()) {
                continue;
            }

            $property->setPackageVersion($packageVersion);
        }
        foreach ($this->constants as $constant) {
            if ($constant->isInherited()) {
                continue;
            }

            $constant->setPackageVersion($packageVersion);
        }
    }
}