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
use Scrutinizer\PhpAnalyzer\Model\AbstractFunction;

/**
 * @ORM\Entity(readOnly = true)
 * @ORM\Table(name = "global_functions", uniqueConstraints = {
 *     @ORM\UniqueConstraint(columns = {"packageVersion_id", "name"})
 * })
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 * @author Johannes
 */
class GlobalFunction extends AbstractFunction
{
    /** @ORM\Id @ORM\GeneratedValue(strategy = "AUTO") @ORM\Column(type = "integer") */
    private $id;

    /** @ORM\ManyToOne(targetEntity = "PackageVersion", inversedBy = "functions") */
    private $packageVersion;

    /** @ORM\OneToMany(targetEntity = "FunctionParameter", indexBy = "name", cascade = {"persist", "remove"}, mappedBy = "function", fetch = "EAGER") */
    private $parameters;

    /** @ORM\OneToMany(targetEntity = "Scrutinizer\PhpAnalyzer\Model\CallGraph\MethodToFunctionCallSite", mappedBy = "targetFunction", cascade = {"persist", "remove"}) */
    private $inMethodCallSites;

    /** @ORM\OneToMany(targetEntity = "Scrutinizer\PhpAnalyzer\Model\CallGraph\FunctionToFunctionCallSite", mappedBy = "targetFunction", cascade = {"persist", "remove"}) */
    private $inFunctionCallSites;

    /** @ORM\OneToMany(targetEntity = "Scrutinizer\PhpAnalyzer\Model\CallGraph\FunctionToMethodCallSite", mappedBy = "sourceFunction", cascade = {"persist", "remove"}, orphanRemoval = true) */
    private $outMethodCallSites;

    /** @ORM\OneToMany(targetEntity = "Scrutinizer\PhpAnalyzer\Model\CallGraph\FunctionToFunctionCallSite", mappedBy = "sourceFunction", cascade = {"persist", "remove"}, orphanRemoval = true) */
    private $outFunctionCallSites;

    // NON-PERSISTENT FIELDS
    private $importedNamespaces = array();

    public function __construct($name)
    {
        parent::__construct($name);

        $this->parameters = new ArrayCollection();
        $this->inMethodCallSites = new ArrayCollection();
        $this->inFunctionCallSites = new ArrayCollection();
        $this->outMethodCallSites = new ArrayCollection();
        $this->outFunctionCallSites = new ArrayCollection();
    }

    public function getShortName()
    {
        if (false === $pos = strrpos($this->getName(), '\\')) {
            return $this->getName();
        }

        return substr($this->getName(), $pos + 1);
    }

    public function isFunction()
    {
        return true;
    }

    public function isMethod()
    {
        return false;
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

    public function addParameter(Parameter $parameter)
    {
        $parameter->setFunction($this);
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

    public function getPackageVersion()
    {
        return $this->packageVersion;
    }

    public function setImportedNamespaces(array $namespaces)
    {
        $this->importedNamespaces = $namespaces;
    }

    public function getImportedNamespaces()
    {
        return $this->importedNamespaces;
    }

    public function getNamespace()
    {
        $parts = explode("\\", $this->getName());
        if (1 === count($parts)) {
            return '';
        }

        return implode("\\", array_slice($parts, 0, -1));
    }

    public function setPackageVersion(PackageVersion $packageVersion)
    {
        $this->packageVersion = $packageVersion;
    }
}
