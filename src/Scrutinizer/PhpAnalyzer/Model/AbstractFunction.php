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
use Scrutinizer\PhpAnalyzer\Model\CallGraph\CallSite;

/**
 * Abstract Base Class for Functions, Methods, and Closures.
 *
 * @ORM\MappedSuperclass
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
abstract class AbstractFunction
{
    /** @ORM\Column(type = "PhpType", nullable = true) */
    private $returnType;

    /** @ORM\Column(type = "string_nocase") */
    private $name;

    /** @ORM\Column(type = "boolean") */
    private $returnByRef = false;

    /** @ORM\Column(type = "boolean") */
    private $variableParameters = false;

    private $astNode;

    public function __construct($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getReturnType()
    {
        return $this->returnType;
    }

    public function getAstNode()
    {
        return $this->astNode;
    }

    public function setAstNode(\PHPParser_Node $node)
    {
        $this->astNode = $node;
    }

    public function setReturnType(PhpType $type)
    {
        $this->returnType = $type;
    }

    public function isReturnByRef()
    {
        return $this->returnByRef;
    }

    public function setReturnByRef($bool)
    {
        $this->returnByRef = (boolean) $bool;
    }

    /**
     * Returns whether this function may have variable parameters.
     *
     * For example, this could be the case if ``func_get_args`` et.al.
     * are being used inside the function body.
     *
     * @return boolean
     */
    public function hasVariableParameters()
    {
        return $this->variableParameters;
    }

    public function setVariableParameters($bool)
    {
        $this->variableParameters = (boolean) $bool;
    }

    public function getInCallSites()
    {
        return new ArrayCollection(array_merge($this->getInMethodCallSites()->getValues(), $this->getInFunctionCallSites()->getValues()));
    }

    public function getOutCallSites()
    {
        return new ArrayCollection(array_merge($this->getOutMethodCallSites()->getValues(), $this->getOutFunctionCallSites()->getValues()));
    }

    public function addCallSite(CallSite $site)
    {
        if ($site->getSource() === $this) {
            $target = $site->getTarget();
            if ($target instanceof Method) {
                $this->getOutMethodCallSites()->add($site);
            } else if ($target instanceof GlobalFunction) {
                $this->getOutFunctionCallSites()->add($site);
            } else {
                throw new \LogicException('Previous ifs were exhaustive.');
            }
        } else if ($site->getTarget() === $this) {
            $source = $site->getSource();
            if ($source instanceof Method) {
                $this->getInMethodCallSites()->add($site);
            } else if ($source instanceof GlobalFunction) {
                $this->getInFunctionCallSites()->add($site);
            } else {
                throw new \LogicException('Previous ifs were exhaustive.');
            }
        } else {
            throw new \InvalidArgumentException('$site is neither originating, nor targeting this function.');
        }
    }

    public function removeCallSite(CallSite $site)
    {
        if ($site->getSource() === $this) {
            $target = $site->getTarget();
            if ($target instanceof Method) {
                return $this->getOutMethodCallSites()->removeElement($site);
            } else if ($target instanceof GlobalFunction) {
                return $this->getOutFunctionCallSites()->removeElement($site);
            }

            throw new \LogicException('Previous ifs were exhaustive.');
        } else if ($site->getTarget() === $this) {
            $source = $site->getSource();
            if ($source instanceof Method) {
                return $this->getInMethodCallSites()->removeElement($site);
            } else if ($source instanceof GlobalFunction) {
                return $this->getInFunctionCallSites()->removeElement($site);
            }

            throw new \LogicException('Previous ifs were exhaustive.');
        }

        throw new \InvalidArgumentException('$site is neither originating, nor targeting this function.');
    }

    /**
     * @return ArrayCollection
     */
    abstract public function getInMethodCallSites();

    /**
     * @return ArrayCollection
     */
    abstract public function getInFunctionCallSites();

    /**
     * @return ArrayCollection
     */
    abstract public function getOutMethodCallSites();

    /**
     * @return ArrayCollection
     */
    abstract public function getOutFunctionCallSites();

    /**
     * @return ArrayCollection
     */
    abstract public function getParameters();

    /**
     * @param Parameter $parameter
     * @return void
     */
    abstract public function addParameter(Parameter $parameter);

    /**
     * @param string|Parameter $nameOrParameter
     * @return boolean
     */
    abstract public function hasParameter($nameOrParameter);

    /**
     * @param string|integer $name
     * @return Parameter
     */
    abstract public function getParameter($nameOrIndex);

    /**
     * @return boolean
     */
    abstract public function isFunction();

    /**
     * @return boolean
     */
    abstract public function isMethod();
}