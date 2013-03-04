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

namespace Scrutinizer\PhpAnalyzer\PhpParser\Scope;

use Scrutinizer\PhpAnalyzer\PhpParser\Type\PhpType;

class Scope implements StaticScopeInterface
{
    private $vars = array();
    private $parent;
    private $depth;
    private $rootNode;

    /** The type for $this in the current scope */
    private $thisType;

    /** Whether this is the bottom scope (for purposes of type inference) */
    private $bottom;

    public static function createBottomScope(\PHPParser_Node $rootNode, PhpType $thisType = null)
    {
        $scope = new self($rootNode, null, $thisType);
        $scope->bottom = true;

        return $scope;
    }

    public function __construct(\PHPParser_Node $rootNode, Scope $parent = null, PhpType $thisType = null)
    {
        $this->rootNode = $rootNode;
        $this->parent = $parent;
        $this->thisType = $thisType;
        $this->depth = null === $parent ? 0 : $parent->getDepth() + 1;
    }

    public function getRootNode()
    {
        return $this->rootNode;
    }

    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * @param string $name
     * @return Variable
     */
    public function getVar($name)
    {
        return isset($this->vars[$name]) ? $this->vars[$name] : null;
    }

    public function getSlot($name)
    {
        return $this->getVar($name);
    }

    public function hasSlot($name)
    {
        return isset($this->vars[$name]);
    }

    public function isDeclared($name)
    {
        return isset($this->vars[$name]);
    }

    /**
     * @return PhpType|null
     */
    public function getTypeOfThis()
    {
        return $this->thisType;
    }

    /**
     * @return boolean
     */
    public function isBottom()
    {
        return $this->bottom;
    }

    /**
     * @return boolean
     */
    public function isGlobal()
    {
        return null === $this->parent;
    }

    /**
     * @return boolean
     */
    public function isLocal()
    {
        return !$this->isGlobal();
    }

    /**
     * @return Scope
     */
    public function getParentScope()
    {
        return $this->parent;
    }

    public function getVars()
    {
        return $this->vars;
    }

    public function getVarNames()
    {
        return array_keys($this->vars);
    }

    public function getVarCount()
    {
        return count($this->vars);
    }

    /**
     * Declares a variable.
     *
     * @param string $name
     * @param PhpType $type
     * @param boolean $typeInferred
     * @return Variable
     */
    public function declareVar($name, PhpType $type = null, $typeInferred = true)
    {
        if (empty($name)) {
            throw new \InvalidArgumentException('$name cannot be empty.');
        }

        if (isset($this->vars[$name])) {
            throw new \LogicException(sprintf('Variable "%s" was already declared, and cannot be declared twice.', $name));
        }

        return $this->vars[$name] = new Variable($name, $type, $this, $typeInferred, count($this->vars));
    }
}