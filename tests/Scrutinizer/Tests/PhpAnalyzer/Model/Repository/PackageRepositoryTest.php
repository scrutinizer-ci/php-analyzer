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

namespace JMS\Tests\CodeReview\Entity\Repository;

use Scrutinizer\PhpAnalyzer\Analyzer;
use Scrutinizer\PhpAnalyzer\Model\PackageScanner;
use Scrutinizer\PhpAnalyzer\PassConfig;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;
use Scrutinizer\PhpAnalyzer\Model\Clazz;
use Scrutinizer\PhpAnalyzer\Model\Package;
use Scrutinizer\PhpAnalyzer\Model\PackageVersion;
use Scrutinizer\PhpAnalyzer\Model\Property;
use Scrutinizer\PhpAnalyzer\Model\Repository\EntityTypeProvider;
use Scrutinizer\PhpAnalyzer\Model\Repository\PackageRepository;
use Scrutinizer\PhpAnalyzer\Util\TestUtils;

class PackageRepositoryTest extends \PHPUnit_Framework_TestCase
{
    private $em;
    private $typeRegistry;

    /** @var PackageRepository */
    private $repo;

    /** @var Package */
    private $package;

    /** @var PackageVersion */
    private $packageVersion;

    /** @var Package */
    private $otherPackage;

    /** @var PackageVersion */
    private $otherPackageVersion;

    public function testDeletePackageVersion()
    {
        $this->useLibraryFixture('monolog');
        $this->repo->deletePackageVersion($this->packageVersion);
    }

    /**
     * @group packageDependencies
     */
    public function testDeletePackageVersionWhenInheritingBetweenPackages()
    {
        $this->packageVersion->addContainer($foo = new Clazz('Foo'));
        $foo->addProperty($fooProperty = new Property('foo'));
        $fooProperty->setPhpType($this->typeRegistry->getNativeType('string'));
        $this->em->persist($this->packageVersion);

        $this->otherPackageVersion->addDependency($this->packageVersion);
        $this->otherPackageVersion->addContainer($bar = new Clazz('bar'));
        $bar->addProperty($fooProperty, 'Foo');
        $this->em->persist($this->otherPackageVersion);

        $this->em->flush();
        $this->assertVersionsCount(2);
        $this->repo->deletePackageVersion($this->packageVersion);
        $this->em->clear();
        $this->assertVersionsCount(0);
    }

    /**
     * @group packageDependencies
     */
    public function testDeletePackageWhenCircularReference()
    {
        $this->packageVersion->addDependency($this->otherPackageVersion);
        $this->otherPackageVersion->addDependency($this->packageVersion);
        $this->em->persist($this->packageVersion);
        $this->em->persist($this->otherPackageVersion);
        $this->em->flush();

        $this->assertVersionsCount(2);
        $this->repo->deletePackageVersion($this->packageVersion);
        $this->assertVersionsCount(0);
    }

    public function testGetLatestBuiltPackageVersion()
    {
        $a = $this->package->createVersion('dev-master', 'abc');
        $a->setLastBuiltAt(new \DateTime());

        $time = new \DateTime();
        $time->modify('+5 minutes');
        $b = $this->package->createVersion('dev-master', 'def');
        $b->setLastBuiltAt($time);

        $c = $this->package->createVersion('dev-master', '0123');
        $c->setLastBuiltAt(new \DateTime());

        // non-built package version
        $this->package->createVersion('dev-master', '456');

        $this->em->persist($this->package);
        $this->em->flush();

        $latest = $this->repo->getLatestBuiltPackageVersion($this->package->getName(), 'dev-master');
        $this->assertSame($b->getId(), $latest->getId());
    }

    public function testGetLatestBuiltPackageVersionWhenNoBuiltVersionIsAvailable()
    {
        $this->package->createVersion('dev-master', 'abc');
        $this->em->persist($this->package);
        $this->em->flush();

        $this->assertNull($this->repo->getLatestBuiltPackageVersion($this->package->getName(), 'dev-master'));
    }

    private function assertVersionsCount($count)
    {
        $rs = (integer) $this->em->createQuery('SELECT COUNT(p) FROM Scrutinizer\PhpAnalyzer\Model\PackageVersion p')
            ->getSingleScalarResult();

        $this->assertEquals($count, $rs);
    }

    private function useLibraryFixture($name)
    {
        $this->scanFixture($this->packageVersion, $name);

        $this->em->getConnection()->beginTransaction();
        $this->em->persist($this->packageVersion);
        $this->em->flush();
        $this->em->getConnection()->commit();
        $this->em->clear();

        $this->packageVersion = $this->repo->getPackageVersion('foo', '1.0');
        $this->package = $this->packageVersion->getPackage();
    }

    private function scanFixture($packageVersion, $name)
    {
        if ( ! is_file($zipFile = __DIR__.'/Fixture/libraries/'.$name.'.zip')) {
            throw new \InvalidArgumentException(sprintf('Fixture "%s" does not exist.', $name));
        }

        $analyzer = new Analyzer($this->typeRegistry, PassConfig::createForTypeScanning());
        $scanner = new PackageScanner($analyzer);
        $scanner->scanZipFile($packageVersion, $zipFile);
    }

    protected function setUp()
    {
        if ( ! isset($_SERVER['MYSQL_USER'])
                || ! isset($_SERVER['MYSQL_HOST'])
                || ! isset($_SERVER['MYSQL_PASSWORD'])
                || ! isset($_SERVER['MYSQL_DATABASE'])) {
            $this->markTestSkipped('You need to configure a MySQL database, see phpunit.dist.xml');
        }

        $this->em = TestUtils::createMysqlTestEntityManager($_SERVER['MYSQL_USER'], $_SERVER['MYSQL_PASSWORD'], $_SERVER['MYSQL_DATABASE'], $_SERVER['MYSQL_HOST']);
        $this->repo = $this->em->getRepository('Scrutinizer\PhpAnalyzer\Model\Package');

        $this->typeProvider = new EntityTypeProvider($this->em);
        $this->typeRegistry = new TypeRegistry($this->typeProvider);

        $this->package = new Package('foo');
        $this->packageVersion = $this->package->createVersion('1.0');
        $this->packageVersion->setAttribute('dir', __DIR__);
        $this->em->persist($this->package);

        $this->otherPackage = new Package('bar');
        $this->otherPackageVersion = $this->otherPackage->createVersion('0.1');
        $this->otherPackageVersion->setAttribute('dir', __DIR__);
        $this->em->persist($this->otherPackage);

        $this->em->flush();
    }
}