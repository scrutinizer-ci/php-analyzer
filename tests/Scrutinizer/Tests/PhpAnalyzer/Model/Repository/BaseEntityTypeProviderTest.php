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

use Doctrine\ORM\EntityManager;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;
use Scrutinizer\PhpAnalyzer\Model\Clazz;
use Scrutinizer\PhpAnalyzer\Model\GlobalConstant;
use Scrutinizer\PhpAnalyzer\Model\GlobalFunction;
use Scrutinizer\PhpAnalyzer\Model\Package;
use Scrutinizer\PhpAnalyzer\Model\Repository\EntityTypeProvider;
use Scrutinizer\PhpAnalyzer\Util\TestUtils;

abstract class BaseEntityTypeProviderTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Doctrine\ORM\EntityManager */
    private $em;

    /** @var EntityTypeProvider */
    private $provider;

    /** @var Package */
    private $package;

    /** @var \Scrutinizer\PhpAnalyzer\Model\PackageVersion */
    private $versionA;

    /** @var \Scrutinizer\PhpAnalyzer\Model\PackageVersion */
    private $versionB;

    /** @var \Scrutinizer\PhpAnalyzer\Model\PackageVersion */
    private $versionC;

    /** @var TypeRegistry */
    private $typeRegistry;

    public function testLoadClass()
    {
        $this->versionA->addContainer($fooA = new Clazz('Foo'));
        $this->versionB->addContainer($fooB = new Clazz('Foo'));
        $this->em->persist($this->package);
        $this->em->flush();

        $loadedClass = $this->provider->loadClass('Foo');
        $this->assertSame($fooA, $loadedClass, 'loadClass() takes the first class if no package versions are set.');

        $loadedClass = $this->provider->loadClass('foo');
        $this->assertSame($fooA, $loadedClass, 'loadClass() treats the name as case-insensitive.');

        $this->provider->setPackageVersions(array($this->versionB));
        $loadedClass = $this->provider->loadClass('Foo');
        $this->assertSame($fooB, $loadedClass, 'loadClass() takes the class from one of the set packages.');

        $this->provider->setPackageVersions(array($this->versionC));
        $this->assertNull($this->provider->loadClass('Foo'), 'loadClass() returns null if class is not found in set packages.');

        $this->assertNull($this->provider->loadClass('Bar'), 'loadClass() returns null if class does not exist.');
    }

    public function testLoadClassWhenNotAvailable()
    {
        $this->versionA->addContainer($foo = new Clazz('Foo'));
        $this->em->persist($foo);
        $this->em->flush();

        $this->provider->setPackageVersions(array($this->versionB));
        $this->assertNull($this->provider->loadClass('Foo'));

        $this->provider->setPackageVersions(array($this->versionA));
        $this->assertNotNull($this->provider->loadClass('Foo'));
    }

    public function testLoadedClassesWhenClassIsNotAvailableInCache()
    {
        $this->versionA->addContainer($foo = new Clazz('Foo'));
        $this->em->persist($foo);
        $this->em->flush();

        $this->provider->setPackageVersions(array($this->versionB));
        $this->assertCount(0, $this->provider->loadClasses(array('Foo')));

        $this->provider->setPackageVersions(array($this->versionA));
        $this->assertCount(1, $this->provider->loadClasses(array('Foo')));
    }

    public function testLoadClasses()
    {
        $this->versionA->addContainer($fooA = new Clazz('Foo'));
        $this->versionB->addContainer($fooB = new Clazz('Foo'));
        $this->versionB->addContainer($barB = new Clazz('Bar'));
        $this->versionC->addContainer($barC = new Clazz('Bar'));
        $this->em->persist($this->package);
        $this->em->flush();

        $loadedClasses = $this->provider->loadClasses(array('Foo', 'Bar', 'baz'));
        $this->assertCount(2, $loadedClasses);
        $this->assertArrayHasKey('Foo', $loadedClasses);
        $this->assertArrayHasKey('Bar', $loadedClasses);
        $this->assertSame($fooA, $loadedClasses['Foo']);
        $this->assertSame($barB, $loadedClasses['Bar']);

        $loadedClasses = $this->provider->loadClasses(array('foo', 'bAr', 'baz'));
        $this->assertCount(2, $loadedClasses);
        $this->assertArrayHasKey('foo', $loadedClasses);
        $this->assertArrayHasKey('bAr', $loadedClasses);
        $this->assertSame($fooA, $loadedClasses['foo']);
        $this->assertSame($barB, $loadedClasses['bAr']);

        $this->provider->setPackageVersions(array($this->versionB));
        $loadedClasses = $this->provider->loadClasses(array('foo', 'bar', 'baz'));
        $this->assertCount(2, $loadedClasses);
        $this->assertArrayHasKey('foo', $loadedClasses);
        $this->assertArrayHasKey('bar', $loadedClasses);
        $this->assertSame($fooB, $loadedClasses['foo']);
        $this->assertSame($barB, $loadedClasses['bar']);
    }

    public function testLoadFunction()
    {
        $this->versionA->addFunction($fooA = new GlobalFunction('foo'));
        $this->versionB->addFunction($fooB = new GlobalFunction('foo'));
        $this->em->persist($this->package);
        $this->em->flush();

        $loadedFunc = $this->provider->loadFunction('foo');
        $this->assertSame($fooA, $loadedFunc, 'loadFunction() takes the first function if no package versions are set.');

        $loadedFunc = $this->provider->loadFunction('FoO');
        $this->assertSame($fooA, $loadedFunc, 'loadFunction() treats the name as case-insensitive.');

        $this->provider->setPackageVersions(array($this->versionB));
        $loadedFunc = $this->provider->loadFunction('Foo');
        $this->assertSame($fooB, $loadedFunc, 'loadFunction() takes the function from one of the set packages.');

        $this->provider->setPackageVersions(array($this->versionC));
        $this->assertNull($this->provider->loadFunction('Foo'), 'loadFunction() returns null if function is not found in set packages.');

        $this->assertNull($this->provider->loadFunction('Bar'), 'loadFunction() returns null if function does not exist.');
    }

    public function testLoadConstant()
    {
        $this->versionA->addConstant($fooA = new GlobalConstant('FOO'));
        $fooA->setPhpType($this->typeRegistry->getNativeType('string'));
        $this->versionB->addConstant($fooB = new GlobalConstant('FOO'));
        $fooB->setPhpType($this->typeRegistry->getNativeType('string'));
        $this->em->persist($this->package);
        $this->em->flush();

        $loadedConst = $this->provider->loadConstant('FOO');
        $this->assertSame($fooA, $loadedConst, 'loadConstant() takes the first constant if no package versions are set.');

        $loadedConst = $this->provider->loadConstant('foo');
        $this->assertNull($loadedConst, 'loadConstant() treats the name **not** as case-insensitive.');

        $this->provider->setPackageVersions(array($this->versionB));
        $loadedConst = $this->provider->loadConstant('FOO');
        $this->assertSame($fooB, $loadedConst, 'loadConstant() takes the constant from one of the set packages.');

        $this->provider->setPackageVersions(array($this->versionC));
        $this->assertNull($this->provider->loadConstant('FOO'), 'loadConstant() returns null if constant is not found in set packages.');

        $this->assertNull($this->provider->loadConstant('BAR'), 'loadConstant() returns null if constant does not exist.');
    }

    protected function setUp()
    {
        $this->em = $this->getEntityManager();
        $this->provider = new EntityTypeProvider($this->em);
        $this->typeRegistry = new TypeRegistry($this->provider);

        $this->package = new Package('foo');
        $this->versionA = $this->package->createVersion('1.0');
        $this->versionB = $this->package->createVersion('1.1');
        $this->versionC = $this->package->createVersion('1.2');

        $this->em->persist($this->package);
        $this->em->flush();
    }

    /**
     * @return EntityManager
     */
    abstract protected function getEntityManager();
}