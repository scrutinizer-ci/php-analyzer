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

/**
 * @ORM\Entity(readOnly = true)
 *
 * @author Johannes
 */
class TraitC extends MethodContainer
{
    private $methods;
    private $properties;

    public function __construct($name)
    {
        parent::__construct($name);

        $this->methods = new ArrayCollection();
        $this->properties = new ArrayCollection();
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
        // TODO
//         $classProperty = new TraitProperty($this, $property, $declaringClass);
//         $this->properties->set($property->getName(), $classProperty);
    }

    public function hasMethod($name)
    {
        return $this->methods->containsKey($name);
    }

    public function getMethod($name)
    {
        return $this->methods->get($name);
    }

    public function addMethod(Method $method, $declaringClass = null)
    {
        // TODO
    }

    public function getMethods()
    {
        return $this->methods;
    }

    public function getMethodNames()
    {
        $names = array();
        foreach ($this->methods as $method) {
            $names[] = $method->getMethod()->getName();
        }

        return $names;
    }

    // TODO

    public function isTrait()
    {
        return true;
    }

    public function toMaybeObjectType()
    {
        return $this;
    }
}