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

/**
 * @ORM\Entity(readOnly = true)
 * @ORM\Table(name = "class_methods", uniqueConstraints = {
 *     @ORM\UniqueConstraint(columns = {"class_id", "name"}),
 *     @ORM\UniqueConstraint(columns = {"class_id", "method_id"})
 * })
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 * @author Johannes
 */
class ClassMethod implements ContainerMethodInterface
{
    /** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue(strategy = "AUTO") */
    private $id;

    /** @ORM\ManyToOne(targetEntity = "Clazz", inversedBy = "methods") */
    private $class;

    /** @ORM\ManyToOne(targetEntity = "Method", cascade = {"persist"}) */
    private $method;

    /** @ORM\Column(type = "string") */
    private $name;

    /** @ORM\Column(type = "string", nullable = true) */
    private $declaringClass;

    public function __construct(Clazz $class, Method $method, $declaringClass = null)
    {
        $this->class = $class;
        $this->method = $method;
        $this->name = strtolower($method->getName());

        if ($class->getName() === $declaringClass) {
            $declaringClass = null;
        }

        $this->declaringClass = $declaringClass;
    }

    public function getClass()
    {
        return $this->class;
    }

    public function getContainer()
    {
        return $this->class;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getDeclaringClass()
    {
        return $this->declaringClass ?: $this->class->getName();
    }

    public function getQualifiedName()
    {
        return $this->getDeclaringClass().'::'.$this->method->getName();
    }

    /**
     * Whether a method is constrained by a language-level contract.
     *
     * This is either an interface contract, or a contract given by an abstract
     * method of a super class.
     *
     * @return boolean
     */
    public function isConstrainedByContract()
    {
        // Abstract methods are not constrained by a contract, but are the
        // contract themself.
        if ($this->method->isAbstract()) {
            return false;
        }

        $class = $this->getDeclaringClassType();
        while (null !== $class = $class->getSuperClassType()) {
            if ( ! $class->hasMethod($this->name)) {
                break;
            }

            if ($class->getMethod($this->name)->isAbstract()) {
                return true;
            }
        }

        foreach ($this->getDeclaringClassType()->getImplementedInterfaceTypes() as $interface) {
            if ($interface->hasMethod($this->name)) {
                return true;
            }
        }

        return false;
    }

    public function getDeclaringClassType()
    {
        if ($this->declaringClass) {
            return $this->class->getTypeRegistry()->getClass($this->declaringClass);
        }

        return $this->class;
    }

    public function isInherited()
    {
        return null !== $this->declaringClass;
    }

    public function __call($method, $args)
    {
        return call_user_func_array(array($this->method, $method), $args);
    }
}
