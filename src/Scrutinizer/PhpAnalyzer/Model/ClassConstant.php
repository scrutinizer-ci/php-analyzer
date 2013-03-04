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
 * @ORM\Table(name = "class_constants", uniqueConstraints = {
 *     @ORM\UniqueConstraint(columns = {"class_id", "name"}),
 *     @ORM\UniqueConstraint(columns = {"class_id", "constant_id"})
 * })
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 * @author Johannes
 */
class ClassConstant implements ContainerConstantInterface
{
    /** @ORM\Id @ORM\Column(type="integer") @ORM\GeneratedValue(strategy = "AUTO") */
    private $id;

    /** @ORM\ManyToOne(targetEntity = "Clazz", inversedBy = "constants") */
    private $class;

    /** @ORM\ManyToOne(targetEntity = "Constant", cascade = {"persist"}) */
    private $constant;

    /** @ORM\Column(type = "string") */
    private $name;

    /** @ORM\Column(type = "string", nullable = true) */
    private $declaringClass;

    public function __construct(Clazz $class, Constant $constant, $declaringClass = null)
    {
        $this->class = $class;
        $this->constant = $constant;
        $this->name = $constant->getName();

        if ($class->getName() === $declaringClass) {
            $declaringClass = null;
        }

        $this->declaringClass = $declaringClass;
    }

    public function getClass()
    {
        return $this->class;
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
        return $this->declaringClass ?: $this->class->getName();
    }

    public function isInherited()
    {
        return null !== $this->declaringClass;
    }

    public function __call($method, $args)
    {
        return call_user_func_array(array($this->constant, $method), $args);
    }
}