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

namespace Scrutinizer\PhpAnalyzer\PhpParser\Type;

use Scrutinizer\PhpAnalyzer\Model\GlobalConstant;
use Scrutinizer\PhpAnalyzer\Model\GlobalFunction;
use Scrutinizer\PhpAnalyzer\Model\MethodContainer;

/**
 * Memory-based type provider implementation.
 *
 * This is solely useful for unit testing.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class MemoryTypeProvider implements TypeProviderInterface
{
    private $classes = array();
    private $functions = array();
    private $constants = array();

    public function addClass(MethodContainer $container)
    {
        $this->classes[strtolower($container->getName())] = $container;
    }

    public function addFunction(GlobalFunction $function)
    {
        $this->functions[$function->getName()] = $function;
    }

    public function addConstant(GlobalConstant $constant)
    {
        $this->constants[$constant->getName()] = $constant;
    }

    public function getImplementingClasses($name)
    {
        $classes = array();
        foreach ($this->classes as $class) {
            if ( ! $class instanceof \Scrutinizer\PhpAnalyzer\Model\Clazz) {
                continue;
            }

            if ( ! $class->isImplementing($name)) {
                continue;
            }

            $classes[] = $class;
        }

        return $classes;
    }

    public function loadClasses(array $names)
    {
        $rs = array();
        foreach ($names as $name) {
            $lowerName = strtolower($name);

            if (!isset($this->classes[$lowerName])) {
                continue;
            }

            $rs[$name] = $this->classes[$lowerName];
        }

        return $rs;
    }

    public function loadClass($name)
    {
        $name = strtolower($name);

        return isset($this->classes[$name]) ? $this->classes[$name] : null;
    }

    public function loadFunction($name)
    {
        return isset($this->functions[$name]) ? $this->functions[$name] : null;
    }

    public function loadConstant($name)
    {
        return isset($this->constants[$name]) ? $this->constants[$name] : null;
    }
}