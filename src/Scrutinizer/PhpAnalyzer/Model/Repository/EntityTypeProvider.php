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

use Doctrine\ORM\EntityManager;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\PackageAwareTypeProviderInterface;
use Scrutinizer\PhpAnalyzer\Model\GlobalConstant;
use Scrutinizer\PhpAnalyzer\Model\GlobalFunction;
use Scrutinizer\PhpAnalyzer\Model\MethodContainer;
use Scrutinizer\PhpAnalyzer\Model\PackageVersion;
use Doctrine\ORM\QueryBuilder;

/**
 * Loads types from the database.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class EntityTypeProvider implements PackageAwareTypeProviderInterface
{
    private $em;
    private $packageVersions;

    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function setPackageVersions(array $packageVersions)
    {
        if ( ! $packageVersions) {
            $this->packageVersions = null;

            return;
        }

        $this->packageVersions = new \SplObjectStorage();
        foreach ($packageVersions as $version) {
            assert($version instanceof PackageVersion);
            $this->packageVersions->attach($version);
        }
    }

    public function getImplementingClasses($name)
    {
        $classes = array();

        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('c')
            ->from('Scrutinizer\PhpAnalyzer\Model\Clazz', 'c')
            ->where($qb->expr()->like('c.implementedInterfaces', ':pattern'))
        ;
        $qb->setParameter('pattern', '%'.$name.'%');
        $this->addPackageCondition($qb, 'c');

        foreach ($qb->getQuery()->getResult() as $class) {
            // We need to do an additional check as the name might have only returned a partial
            // match. Alternatively, we could use a regex to query the database, but Doctrine does
            // not seem to support that natively.
            if ( ! $class->isImplementing($name)) {
                continue;
            }

            // The class belongs to a package version which is not being analyzed.
            if (null !== $this->packageVersions && ! isset($this->packageVersions[$class->getPackageVersion()])) {
                continue;
            }

            $classes[] = $class;
        }

        return $classes;
    }

    public function loadClasses(array $names)
    {
        $loweredNames = array();
        foreach ($names as $name) {
            $loweredNames[strtolower($name)] = $name;
        }

        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('c')
            ->from('Scrutinizer\PhpAnalyzer\Model\MethodContainer', 'c')
            ->where($qb->expr()->in('c.name', ':names'))
        ;
        $qb->setParameter('names', $names);
        $this->addPackageCondition($qb, 'c');

        $rs = array();
        foreach ($qb->getQuery()->getResult() as $class) {
            $rs[$loweredNames[strtolower($class->getName())]][] = $class;
        }

        foreach ($rs as $i => $classes) {
            if (null === $this->packageVersions) {
                $rs[$i] = reset($classes);

                continue;
            }

            foreach ($classes as $class) {
                if (isset($this->packageVersions[$class->getPackageVersion()])) {
                    $rs[$i] = $class;

                    continue 2;
                }
            }

            unset($rs[$i]);
        }

        return $rs;
    }

    public function loadClass($name)
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('c')
            ->from('Scrutinizer\PhpAnalyzer\Model\MethodContainer', 'c')
            ->where($qb->expr()->eq('c.name', ':name'))
        ;
        $qb->setParameter('name', $name);
        $this->addPackageCondition($qb, 'c');

        $possibleClasses = $qb->getQuery()->getResult();

        if (null === $this->packageVersions) {
            return $possibleClasses ? reset($possibleClasses) : null;
        }

        foreach ($possibleClasses as $class) {
            assert($class instanceof MethodContainer);

            if (isset($this->packageVersions[$class->getPackageVersion()])) {
                return $class;
            }
        }

        return null;
    }

    public function loadFunction($name)
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('f')
            ->from('Scrutinizer\PhpAnalyzer\Model\GlobalFunction', 'f')
            ->where($qb->expr()->eq('f.name', ':name'))
        ;
        $qb->setParameter('name', $name);
        $this->addPackageCondition($qb, 'f');

        $possibleFunctions = $qb->getQuery()->getResult();

        if (null === $this->packageVersions) {
            return $possibleFunctions ? reset($possibleFunctions) : null;
        }

        foreach ($possibleFunctions as $function) {
            assert($function instanceof GlobalFunction);

            if (isset($this->packageVersions[$function->getPackageVersion()])) {
                return $function;
            }
        }

        return null;
    }

    public function loadConstant($name)
    {
        $qb = $this->em->createQueryBuilder();
        $qb
            ->select('c')
            ->from('Scrutinizer\PhpAnalyzer\Model\GlobalConstant', 'c')
            ->where($qb->expr()->eq('c.name', ':name'))
        ;
        $qb->setParameter('name', $name);
        $this->addPackageCondition($qb, 'c');

        $possibleConstants = $qb->getQuery()->getResult();

        if (null === $this->packageVersions) {
            return $possibleConstants ? reset($possibleConstants) : null;
        }

        foreach ($possibleConstants as $constant) {
            assert($constant instanceof GlobalConstant);

            if (isset($this->packageVersions[$constant->getPackageVersion()])) {
                return $constant;
            }
        }

        return null;
    }


    private function addPackageCondition(QueryBuilder $qb, $alias)
    {
        if (null === $this->packageVersions) {
            return;
        }

        $packages = array();
        foreach ($this->packageVersions as $packageVersion) {
            $packages[] = $qb->expr()->eq($alias.'.packageVersion', ':package'.($i = count($packages)));
            $qb->setParameter('package'.$i, $packageVersion);
        }

        $qb->andWhere(call_user_func_array(array($qb->expr(), 'orX'), $packages));
    }
}