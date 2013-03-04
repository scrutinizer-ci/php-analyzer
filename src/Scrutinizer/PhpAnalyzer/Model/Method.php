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
 * @ORM\Table(name = "methods")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class Method extends AbstractFunction
{
    const MODIFIER_STATIC = 1;
    const MODIFIER_FINAL = 2;
    const MODIFIER_ABSTRACT = 4;

    const VISIBILITY_PUBLIC = 1;
    const VISIBILITY_PROTECTED = 2;
    const VISIBILITY_PRIVATE = 3;

    /** @ORM\Column(type = "integer") @ORM\Id @ORM\GeneratedValue(strategy = "AUTO") */
    private $id;

    /** @ORM\ManyToOne(targetEntity = "PackageVersion") */
    private $packageVersion;

    /** @ORM\Column(type = "smallint") */
    private $modifier = 0;

    /** @ORM\Column(type = "smallint") */
    private $visibility = self::VISIBILITY_PUBLIC;

    /** @ORM\OneToMany(targetEntity = "MethodParameter", indexBy = "name", cascade = {"persist", "remove"}, mappedBy = "method", fetch = "EAGER") */
    private $parameters;

    /** @ORM\OneToMany(targetEntity = "Scrutinizer\PhpAnalyzer\Model\CallGraph\MethodToMethodCallSite", mappedBy = "targetMethod", cascade = {"persist", "remove"}) */
    private $inMethodCallSites;

    /** @ORM\OneToMany(targetEntity = "Scrutinizer\PhpAnalyzer\Model\CallGraph\FunctionToMethodCallSite", mappedBy = "targetMethod", cascade = {"persist", "remove"}) */
    private $inFunctionCallSites;

    /** @ORM\OneToMany(targetEntity = "Scrutinizer\PhpAnalyzer\Model\CallGraph\MethodToMethodCallSite", mappedBy = "sourceMethod", cascade = {"persist", "remove"}, orphanRemoval = true) */
    private $outMethodCallSites;

    /** @ORM\OneToMany(targetEntity = "Scrutinizer\PhpAnalyzer\Model\CallGraph\MethodToFunctionCallSite", mappedBy = "sourceMethod", cascade = {"persist", "remove"}, orphanRemoval = true) */
    private $outFunctionCallSites;

    /** @ORM\Column(type = "json_array", nullable = true) */
    private $docTypes = array();

    public function __construct($name)
    {
        parent::__construct($name);

        $this->parameters = new ArrayCollection();
        $this->inMethodCallSites = new ArrayCollection();
        $this->inFunctionCallSites = new ArrayCollection();
        $this->outMethodCallSites = new ArrayCollection();
        $this->outFunctionCallSites = new ArrayCollection();
    }

    public function isFunction()
    {
        return false;
    }

    public function isMethod()
    {
        return true;
    }

    public function getInMethodCallSites()
    {
        return $this->inMethodCallSites;
    }

    public function getInFunctionCallSites()
    {
        return $this->inFunctionCallSites;
    }

    public function getOutMethodCallSites()
    {
        return $this->outMethodCallSites;
    }

    public function getOutFunctionCallSites()
    {
        return $this->outFunctionCallSites;
    }

    public function getParameters()
    {
        return $this->parameters;
    }

    public function getModifier()
    {
        return $this->modifier;
    }

    public function getVisibility()
    {
        return $this->visibility;
    }

    public function getDocTypes()
    {
        return $this->docTypes;
    }

    public function getParamDocType($paramIndex)
    {
        if (!isset($this->docTypes['param_'.$paramIndex])) {
            return null;
        }

        return $this->docTypes['param_'.$paramIndex];
    }

    public function getReturnDocType()
    {
        return isset($this->docTypes['return']) ? $this->docTypes['return'] : null;
    }

    /**
     * Sets a param type.
     *
     * @param integer $index
     * @param string $type The resolved type dumped out as doc comment again.
     */
    public function setParamDocType($index, $type)
    {
        $this->docTypes['param_'.$index] = $type;
    }

    /**
     * Sets the return doc type.
     *
     * @param string $type
     */
    public function setReturnDocType($type)
    {
        $this->docTypes['return'] = $type;
    }

    public function addParameter(Parameter $parameter)
    {
        $parameter->setMethod($this);
        $this->parameters->set($parameter->getName(), $parameter);
    }

    public function hasParameter($nameOrIndex)
    {
        if (is_int($nameOrIndex)) {
            return $nameOrIndex < count($this->parameters);
        }

        return $this->parameters->containsKey($nameOrIndex);
    }

    public function getParameter($nameOrIndex)
    {
        if (is_int($nameOrIndex)) {
            foreach ($this->parameters as $param) {
                if ($nameOrIndex === $param->getIndex()) {
                    return $param;
                }
            }

            return null;
        }

        return $this->parameters->get($nameOrIndex);
    }

    public function getId()
    {
        return $this->id;
    }

    public function isStatic()
    {
        return self::MODIFIER_STATIC === ($this->modifier & self::MODIFIER_STATIC);
    }

    public function isFinal()
    {
        return self::MODIFIER_FINAL === ($this->modifier & self::MODIFIER_FINAL);
    }

    public function isAbstract()
    {
        return self::MODIFIER_ABSTRACT === ($this->modifier & self::MODIFIER_ABSTRACT);
    }

    public function isPublic()
    {
        return self::VISIBILITY_PUBLIC === $this->visibility;
    }

    public function isConstructor()
    {
        return '__construct' === strtolower($this->getName());
    }

    public function isProtected()
    {
        return self::VISIBILITY_PROTECTED === $this->visibility;
    }

    public function isPrivate()
    {
        return self::VISIBILITY_PRIVATE === $this->visibility;
    }

    public function setVisibility($visibility)
    {
        $this->visibility = (integer) $visibility;
    }

    public function setModifier($modifier)
    {
        $this->modifier = (integer) $modifier;
    }

    public function __toString()
    {
        return 'Method('.$this->getName().')';
    }

    public function setPackageVersion(PackageVersion $packageVersion)
    {
        if (null !== $this->packageVersion && $this->packageVersion !== $packageVersion) {
            throw new \InvalidArgumentException('The packageVersion cannot be changed for '.$this.' from '.$this->packageVersion.' to '.$packageVersion.'.');
        }
        $this->packageVersion = $packageVersion;
    }
}