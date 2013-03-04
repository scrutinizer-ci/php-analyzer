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

namespace Scrutinizer\PhpAnalyzer\Model\Repository;

use Doctrine\ORM\EntityRepository;
use Scrutinizer\PhpAnalyzer\Model\Package;
use Scrutinizer\PhpAnalyzer\Model\PackageVersion;

class PackageRepository extends EntityRepository
{
    /**
     * Returns the requested package.
     *
     * If the package is not found, it will be created.
     *
     * @param string $name
     * @return Package
     */
    public function getOrCreatePackage($name)
    {
        if ($package = $this->findOneBy(array('name' => $name))) {
            return $package;
        }

        return new Package($name);
    }

    public function getPackage($name)
    {
        if (!$package = $this->findOneBy(array('name' => $name))) {
            throw new \InvalidArgumentException('The package "%s" does not exist.', $name);
        }

        return $package;
    }

    public function getLatestBuiltPackageVersion($name, $version)
    {
        return $this->_em->createQuery("SELECT pv FROM Scrutinizer\PhpAnalyzer\Model\PackageVersion pv INNER JOIN pv.package p WHERE p.name = :name AND pv.version = :version AND pv.lastBuiltAt IS NOT NULL ORDER BY pv.lastBuiltAt DESC")
                    ->setParameter('name', $name)
                    ->setParameter('version', $version)
                    ->setMaxResults(1)
                    ->getOneOrNullResult();
    }

    public function getPackageVersion($name, $version, $sourceReference = null, $dependencyHash = null)
    {
        $qb = $this->_em->createQueryBuilder();

        $qb->select('pv')->from('Scrutinizer\PhpAnalyzer\Model\PackageVersion', 'pv')
            ->innerJoin('pv.package', 'p')
            ->where($qb->expr()->andX(
                        $qb->expr()->eq('p.name', ':name'),
                        $qb->expr()->eq('pv.version', ':version')))
            ->orderBy('pv.id', 'DESC');
        $qb->setParameter('name', $name);
        $qb->setParameter('version', $version);

        if (null !== $sourceReference) {
            $qb->andWhere($qb->expr()->eq('pv.sourceReference', ':sourceReference'));
            $qb->setParameter('sourceReference', $sourceReference);
        }

        if (null !== $dependencyHash) {
            $qb->andWhere($qb->expr()->orX(
                $qb->expr()->isNull('pv.dependencyHash'),
                $qb->expr()->eq('pv.dependencyHash', ':dependencyHash')));
            $qb->setParameter('dependencyHash', $dependencyHash);
        }

        return $qb->getQuery()->setMaxResults(1)->getOneOrNullResult();
    }

    public function createPackagistVersionIfNotExists($packagistName, $version)
    {
        $name = 'packagist__'.$packagistName;
        $package = $this->getOrCreatePackage($name);
        $package->setPackagistName($packagistName);

        if ($package->hasVersion($version)) {
            return;
        }

        $package->createVersion($version);
        $this->_em->persist($package);
        $this->_em->flush();
    }

    /**
     * Deletes the given package version from the repository.
     *
     * This also recursively deletes all package versions which have a dependency on the given
     * package. We need to do this to ensure integrity of the class database.
     *
     * @param \Scrutinizer\PhpAnalyzer\Model\PackageVersion $packageVersion
     */
    public function deletePackageVersion(PackageVersion $packageVersion)
    {
        $this->_em->getConnection()->beginTransaction();
        try {
            $this->deletePackageVersionInternal($packageVersion);
            $this->_em->getConnection()->commit();
        } catch (\Exception $ex) {
            $this->_em->getConnection()->rollback();

            throw $ex;
        }
    }

    private function deletePackageVersionInternal(PackageVersion $packageVersion, array &$deletedVersions = array())
    {
        if ( ! $this->_em->getConnection()->isTransactionActive()) {
            throw new \LogicException('deletePackageVersionInternal() requires an active transaction.');
        }

        if ( ! $this->_em->contains($packageVersion)) {
            return;
        }

        // This makes sure that we do break circular references, and do not end up in a deadlock.
        if (in_array($packageVersion, $deletedVersions, true)) {
            return;
        }
        $deletedVersions[] = $packageVersion;

        // We need to also delete all package versions which depend on this package version since
        // there might be classes which have been inherited; their properties, or methods might have
        // changed etc. So, a re-build for them is required.
        $incomingDeps = $this->_em->createQuery("SELECT p FROM Scrutinizer\PhpAnalyzer\Model\PackageVersion p
                                                 WHERE :version MEMBER OF p.dependencies")
                            ->setParameter('version', $packageVersion)
                            ->getResult();
        foreach ($incomingDeps as $incomingDep) {
            $this->deletePackageVersionInternal($incomingDep, $deletedVersions);
        }

        $packageVersion->getPackage()->removeVersion($packageVersion);
        $this->_em->remove($packageVersion);

        $this->clearPackageVersion($packageVersion);
    }

    public function clearPackageVersion(PackageVersion $packageVersion)
    {
        // classes
        $classes = $this->_em->createQuery("SELECT c, m, p, co FROM Scrutinizer\PhpAnalyzer\Model\Clazz c LEFT JOIN c.methods m LEFT JOIN c.properties p LEFT JOIN c.constants co WHERE c.packageVersion = :packageVersion")
            ->setParameter('packageVersion', $packageVersion)
            ->execute();
        foreach ($classes as $class) {
            $this->_em->remove($class);
        }

        // interfaces
        $interfaces = $this->_em->createQuery("SELECT i, m FROM Scrutinizer\PhpAnalyzer\Model\InterfaceC i LEFT JOIN i.methods m WHERE i.packageVersion = :packageVersion")
            ->setParameter('packageVersion', $packageVersion)
            ->execute();
        foreach ($interfaces as $interface) {
            $this->_em->remove($interface);
        }

        // traits
        $traits = $this->_em->createQuery("SELECT t FROM Scrutinizer\PhpAnalyzer\Model\TraitC t WHERE t.packageVersion = :packageVersion")
                    ->setParameter('packageVersion', $packageVersion)
                    ->execute();
        foreach ($traits as $trait) {
            $this->_em->remove($trait);
        }

        // constants
        foreach ($this->_em->createQuery("SELECT c FROM Scrutinizer\PhpAnalyzer\Model\GlobalConstant c WHERE c.packageVersion = :packageVersion")
                    ->setParameter('packageVersion', $packageVersion)->execute() as $constant) {
            $this->_em->remove($constant);
        }

        // functions
        foreach ($this->_em->createQuery("SELECT f FROM Scrutinizer\PhpAnalyzer\Model\GlobalFunction f WHERE f.packageVersion = :packageVersion")
                    ->setParameter('packageVersion', $packageVersion)->execute() as $function) {
            $this->_em->remove($function);
        }

        // methods
        $methods = $this->_em->createQuery("SELECT m FROM Scrutinizer\PhpAnalyzer\Model\Method m WHERE m.packageVersion = :packageVersion")
                        ->setParameter('packageVersion', $packageVersion)->execute();
        foreach ($methods as $method) {
            $this->_em->remove($method);
        }

        // properties
        foreach ($this->_em->createQuery("SELECT p FROM Scrutinizer\PhpAnalyzer\Model\Property p WHERE p.packageVersion = :packageVersion")
                    ->setParameter('packageVersion', $packageVersion)->execute() as $property) {
            $this->_em->remove($property);
        }

        // constants
        foreach ($this->_em->createQuery("SELECT c FROM Scrutinizer\PhpAnalyzer\Model\Constant c WHERE c.packageVersion = :packageVersion")
                    ->setParameter('packageVersion', $packageVersion)->execute() as $constant) {
            $this->_em->remove($constant);
        }

        $this->_em->flush();
    }
}