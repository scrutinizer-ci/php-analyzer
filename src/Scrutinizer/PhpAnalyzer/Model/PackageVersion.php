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
 * Package Version.
 *
 * @ORM\Entity
 * @ORM\Table(name = "package_versions", uniqueConstraints = {
 *     @ORM\UniqueConstraint(columns = {"package_id", "version", "sourceReference", "dependencyHash"}),
 * })
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class PackageVersion
{
    const TEMP_SUFFIX = '__being_built';
    const TEMP_SUFFIX_LENGTH = 13;

    /** @ORM\Id @ORM\GeneratedValue(strategy = "AUTO") @ORM\Column(type = "integer") */
    private $id;

    /** @ORM\Column(type = "string", length = 36, unique = true) */
    private $uuid;

    /** @ORM\ManyToOne(targetEntity = "Package", inversedBy = "versions") */
    private $package;

    /** @ORM\Column(type = "string") */
    private $version;

    /**
     * A reference for the specific source version that was used to built
     * this package version.
     *
     * @ORM\Column(type = "string", nullable = true)
     */
    private $sourceReference;

    /**
     * This hash uniquely identifies which sub-set of this package version's
     * dependencies were used for building the package. If all dependencies
     * were available this hash is set to null.
     *
     * This hash is necessary to determine whether a package can be used as
     * a depencency in another package. For example, considering that a
     * package A exists which has a dependency on package B. Package B has
     * also dependencies on package C for certain features. However, A does
     * not use these features, and therefore itself has no dependency on
     * package C. If we build package B in this scenario, some classes
     * might not be normalized because the classes of C are missing. If we
     * now have a package D which also depends on B, _and_ which uses B's
     * special features for C, then we need to re-build B with a version of
     * C included. So, we end up with two different versions of B which
     * might have the same source reference, but a different dependency hash.
     *
     * @ORM\Column(type = "string", length = 40, nullable = true)
     */
    private $dependencyHash;

    /** @ORM\Column(type = "datetime") */
    private $createdAt;

    /** @ORM\Column(type = "datetime", nullable = true) */
    private $lastBuiltAt;

    /**
     * A list of packages including their versions, which this package depends on.
     *
     * When analyzing this package version, classes from these packages are also
     * considered.
     *
     * @ORM\ManyToMany(targetEntity = "PackageVersion", cascade = {"refresh"})
     * @ORM\JoinTable(name="package_version_dependencies",
     *     joinColumns = { @ORM\JoinColumn(name = "source_package_id", referencedColumnName = "id") },
     *     inverseJoinColumns = { @ORM\JoinColumn(name = "dest_package_id", referencedColumnName = "id")}
     * )
     */
    private $dependencies;

    /** @ORM\OneToMany(targetEntity = "MethodContainer", mappedBy = "packageVersion", indexBy = "name", orphanRemoval = true, cascade = {"persist"}) */
    private $containers;

    /** @ORM\OneToMany(targetEntity = "GlobalFunction", mappedBy = "packageVersion", indexBy = "name", orphanRemoval = true, cascade = {"persist"}) */
    private $functions;

    /** @ORM\OneToMany(targetEntity = "GlobalConstant", mappedBy = "packageVersion", indexBy = "name", orphanRemoval = true, cascade = {"persist"}) */
    private $constants;

    /** @ORM\Column(type = "json_array") */
    private $attributes = array();

    public function __construct(Package $package, $version, $sourceReference, $dependencyHash)
    {
        $this->package = $package;
        $this->version = $version;
        $this->sourceReference = $sourceReference;
        $this->dependencyHash = $dependencyHash;
        $this->createdAt = new \DateTime();
        $this->containers = new ArrayCollection();
        $this->functions = new ArrayCollection();
        $this->constants = new ArrayCollection();
        $this->dependencies = new ArrayCollection();
    }

    public function setUuid($uuid)
    {
        if (null !== $this->uuid) {
            throw new \LogicException('A uuid can only be set once.');
        }

        $this->uuid = $uuid;
    }

    public function getUuid()
    {
        return $this->uuid;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function setAttributes(array $attrs)
    {
        $this->attributes = $attrs;
    }

    public function getAttribute($key)
    {
        if ( ! isset($this->attributes[$key])) {
            throw new \InvalidArgumentException(sprintf('The attribute "%s" does not exist.', $key));
        }

        return $this->attributes[$key];
    }

    public function hasAttribute($key)
    {
        return isset($this->attributes[$key]);
    }

    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function removeAttribute($key)
    {
        unset($this->attributes[$key]);
    }

    /**
     * Returns whether this is a virtual package.
     *
     * A virtual package is just used to describe dependencies, but does not
     * contain any classes, method, etc. itself.
     *
     * @return boolean
     */
    public function isVirtual()
    {
        if ($this->hasAttribute('dir')) {
            return false;
        }

        if ($this->hasAttribute('url')) {
            return false;
        }

        return true;
    }

    public function __toString()
    {
        return 'PackageVersion("'.$this->package->getName().'", '.$this->version.', ref = '.($this->sourceReference ?: 'null').')';
    }

    public function getName()
    {
        return $this->package->getName();
    }

    public function getDisplayName()
    {
        $name = $this->package->getName();

        return preg_replace('/^[a-z_]+__/', '', $name);
    }

    /**
     * Returns all direct dependencies.
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getDependencies()
    {
        return $this->dependencies;
    }

    /**
     * Returns whether this package version's dependencies are built.
     *
     * @return bool
     */
    public function hasBuiltDependencies()
    {
        foreach ($this->dependencies as $dep) {
            /** @var $dep PackageVersion */

            if ( ! $dep->isBuilt()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns all direct dependencies, and the dependencies of their dependencies etc.
     *
     * @return PackageVersion[]
     */
    public function getAllDependencies(array $deps = array())
    {
        $deps[] = $this;

        foreach ($this->dependencies as $dep) {
            /** @var $dep PackageVersion */

            if (in_array($dep, $deps, true)) {
                continue;
            }
            $deps = $dep->getAllDependencies($deps);
        }

        return $deps;
    }

    public function dependsOn(PackageVersion $otherPackage)
    {
        foreach ($this->dependencies as $dep) {
            if ($dep->getVersion() === $otherPackage->getVersion()
                    && $dep->getPackage()->getName() === $otherPackage->getPackage()->getName()) {
                return true;
            }
        }

        return false;
    }

    public function isFresh($sourceReference)
    {
        if ( ! $this->isBuilt()) {
            return false;
        }

        if (null === $sourceReference) {
            return true;
        }

        return $this->sourceReference === $sourceReference;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getContainers()
    {
        return $this->containers;
    }

    public function getFunctions()
    {
        return $this->functions;
    }

    public function getConstants()
    {
        return $this->constants;
    }

    public function addDependency(PackageVersion $version)
    {
        if ($this->dependencies->contains($version)) {
            return;
        }

        if ($version->isVirtual()) {
            throw new \LogicException('Package '.$this.' cannot depend on '.$version.'. As it is a virtual package. Virtual package must always be root packages.');
        }

        $this->dependencies->add($version);
    }

    public function addContainer(MethodContainer $container)
    {
        $container->setPackageVersion($this);
        $this->containers->set($container->getName(), $container);
    }

    public function addFunction(GlobalFunction $function)
    {
        $function->setPackageVersion($this);
        $this->functions->set($function->getName(), $function);
    }

    public function addConstant(GlobalConstant $constant)
    {
        $constant->setPackageVersion($this);
        $this->constants->set($constant->getName(), $constant);
    }

    /**
     * This method must be called after persisting relations through the
     * customized persisters, but before persisting the package version
     * through Doctrine.
     *
     * @internal
     */
    public function resetCollectionsForPersist()
    {
        $this->containers = new ArrayCollection();
        $this->functions = new ArrayCollection();
        $this->constants = new ArrayCollection();
    }

    public function setLastBuiltAt(\DateTime $date)
    {
        $this->lastBuiltAt = $date;
    }

    public function getDependencyHash()
    {
        return $this->dependencyHash;
    }

    /**
     * @return Package
     */
    public function getPackage()
    {
        return $this->package;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getSourceReference()
    {
        return $this->sourceReference;
    }

    /**
     * @return \DateTime
     */
    public function getLastBuiltAt()
    {
        return $this->lastBuiltAt;
    }

    /**
     * Returns whether this package version is built.
     *
     * This also takes into account dependencies of this package (albeit
     * implicitly) because builds are only started if all of their deps
     * have been built already.
     *
     * @return boolean
     */
    public function isBuilt()
    {
        return null !== $this->lastBuiltAt;
    }
}