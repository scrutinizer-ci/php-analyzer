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
 * @ORM\Table(name = "interface_methods", uniqueConstraints = {
 *     @ORM\UniqueConstraint(columns = {"interface_id", "name"}),
 *     @ORM\UniqueConstraint(columns = {"interface_id", "method_id"})
 * })
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class InterfaceMethod implements ContainerMethodInterface
{
    /** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue(strategy = "AUTO") */
    private $id;

    /** @ORM\ManyToOne(targetEntity = "InterfaceC", inversedBy = "methods") */
    private $interface;

    /** @ORM\ManyToOne(targetEntity = "Method", cascade = {"persist"}) */
    private $method;

    /** @ORM\Column(type = "string") */
    private $name;

    /** @ORM\Column(type = "string", nullable = true) */
    private $declaringInterface;

    public function __construct(InterfaceC $interface, Method $method, $declaringInterface = null)
    {
        $this->interface = $interface;
        $this->method = $method;
        $this->name = strtolower($method->getName());

        if ($interface->getName() === $declaringInterface) {
            $declaringInterface = null;
        }
        $this->declaringInterface = $declaringInterface;
    }

    public function getInterface()
    {
        return $this->interface;
    }

    public function getContainer()
    {
        return $this->interface;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getQualifiedName()
    {
        return $this->getDeclaringInterface().'::'.$this->method->getName();
    }

    public function getDeclaringClass()
    {
        return $this->getDeclaringInterface();
    }

    public function getDeclaringInterface()
    {
        return $this->declaringInterface ?: $this->interface->getName();
    }

    public function isInherited()
    {
        return null !== $this->declaringInterface;
    }

    public function __call($name, $args)
    {
        return call_user_func_array(array($this->method, $name), $args);
    }
}