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
 * @ORM\Table(name = "interface_constants", uniqueConstraints = {
 *     @ORM\UniqueConstraint(columns = {"interface_id", "name"}),
 *     @ORM\UniqueConstraint(columns = {"interface_id", "constant_id"})
 * })
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 * @author Johannes
 */
class InterfaceConstant implements ContainerConstantInterface
{
    /** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue(strategy = "AUTO") */
    private $id;

    /** @ORM\ManyToOne(targetEntity = "InterfaceC", inversedBy = "constants") */
    private $interface;

    /** @ORM\ManyToOne(targetEntity = "Constant", cascade = {"persist"}) */
    private $constant;

    /** @ORM\Column(type = "string") */
    private $name;

    /** @ORM\Column(type = "string", nullable = true) */
    private $declaringInterface;

    public function __construct(InterfaceC $interface, Constant $constant, $declaringInterface = null)
    {
        $this->interface = $interface;
        $this->constant = $constant;
        $this->name = $constant->getName();

        if ($interface->getName() === $declaringInterface) {
            $declaringInterface = null;
        }

        $this->declaringInterface = $declaringInterface;
    }

    public function getInterface()
    {
        return $this->interface;
    }

    public function getConstant()
    {
        return $this->constant;
    }

    public function getName()
    {
        return $this->name;
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

    public function __call($method, $args)
    {
        return call_user_func_array(array($this->constant, $method), $args);
    }
}