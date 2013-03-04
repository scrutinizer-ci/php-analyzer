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
 * @ORM\Entity(repositoryClass = "Scrutinizer\PhpAnalyzer\Model\Repository\PackageRepository")
 * @ORM\Table(name = "packages")
 * @ORM\ChangeTrackingPolicy("DEFERRED_EXPLICIT")
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class Package
{
    /** @ORM\Id @ORM\Column(type = "integer") @ORM\GeneratedValue(strategy = "AUTO") */
    private $id;

    /** @ORM\Column(type = "string", unique = true) */
    private $name;

    /** @ORM\OneToMany(targetEntity = "PackageVersion", mappedBy = "package", cascade = {"persist", "remove"}) */
    private $versions;

    public function __construct($name)
    {
        $this->name = $name;
        $this->versions = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    /**
     * Creates a new package version.
     *
     * @param string $version
     * @param string|null $sourceReference
     * @param string|null $dependencyHash
     *
     * @return PackageVersion
     */
    public function createVersion($version, $sourceReference = null, $dependencyHash = null)
    {
        if (empty($version)) {
            throw new \InvalidArgumentException(sprintf('The version cannot be empty (%s).', $this));
        }

        if ($this->hasVersion($version, $sourceReference, $dependencyHash)) {
            throw new \InvalidArgumentException(sprintf('The version "%s" already exists.', $version));
        }

        $this->versions->add($packageVersion = new PackageVersion($this, $version, $sourceReference, $dependencyHash));

        return $packageVersion;
    }

    public function hasVersion($version, $sourceReference = null, $dependencyHash = null)
    {
        return null !== $this->getVersionOrNull($version, $sourceReference, $dependencyHash);
    }

    /**
     * @internal Use PackageRepository::deletePackageVersion($packageVersion) instead
     */
    public function removeVersion(PackageVersion $version)
    {
        $this->versions->removeElement($version);
    }

    /**
     * Returns the PackageVersion, or default if not found.
     *
     * @param string $version
     * @param mixed $default
     *
     * @return null|PackageVersion
     */
    public function getVersionOrNull($version, $sourceReference = null, $dependencyHash = null)
    {
        foreach ($this->versions as $packageVersion) {
            if ($version !== $packageVersion->getVersion()) {
                continue;
            }

            if (null !== $sourceReference && $sourceReference !== $packageVersion->getSourceReference()) {
                continue;
            }

            if (null !== $dependencyHash && null !== $packageVersion->getDependencyHash()
                    && $dependencyHash !== $packageVersion->getDependencyHash()) {
                continue;
            }

            return $packageVersion;
        }

        return null;
    }

    public function getVersion($version, $sourceReference = null)
    {
        if (null === $packageVersion = $this->getVersionOrNull($version, $sourceReference)) {
            throw new \InvalidArgumentException(sprintf('The version "%s" does not exist.', $version));
        }

        return $packageVersion;
    }

    public function __toString()
    {
        return 'Package('.$this->name.')';
    }
}