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

namespace JMS\Tests\PhpAnalyzer\Model\Persister;

use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;
use Scrutinizer\PhpAnalyzer\Model\CallGraph\CallSite;
use Scrutinizer\PhpAnalyzer\Model\Clazz;
use Scrutinizer\PhpAnalyzer\Model\Constant;
use Scrutinizer\PhpAnalyzer\Model\GlobalFunction;
use Scrutinizer\PhpAnalyzer\Model\Method;
use Scrutinizer\PhpAnalyzer\Model\Package;
use Scrutinizer\PhpAnalyzer\Model\PackageVersion;
use Scrutinizer\PhpAnalyzer\Model\Persister\ArgumentPersister;
use Scrutinizer\PhpAnalyzer\Model\Persister\CallSitePersister;
use Scrutinizer\PhpAnalyzer\Model\Persister\ConstantPersister;
use Scrutinizer\PhpAnalyzer\Model\Persister\FunctionPersister;
use Scrutinizer\PhpAnalyzer\Model\Persister\GlobalConstantPersister;
use Scrutinizer\PhpAnalyzer\Model\Persister\MethodContainerPersister;
use Scrutinizer\PhpAnalyzer\Model\Persister\MethodPersister;
use Scrutinizer\PhpAnalyzer\Model\Persister\PackageVersionPersister;
use Scrutinizer\PhpAnalyzer\Model\Persister\ParameterPersister;
use Scrutinizer\PhpAnalyzer\Model\Persister\PropertyPersister;
use Scrutinizer\PhpAnalyzer\Util\TestUtils;

class IntegrationTest extends \PHPUnit_Framework_TestCase
{
    private $em;
    private $persister;
    private $packageVersion;
    private $typeRegistry;

    public function testPersist()
    {
        $this->packageVersion->addContainer($foo = new Clazz('Foo'));
        $foo->addImplementedInterface('Iterator');
        $foo->addMethod($fooMethod = new Method('foo'));

        $this->packageVersion->addFunction($count_foo = new GlobalFunction('count_foo'));
        CallSite::create($fooMethod, $count_foo);

        $this->persister->persist($this->packageVersion);

        $reloadedFoo = $this->em->createQuery('SELECT c FROM Scrutinizer\PhpAnalyzer\Model\Clazz c WHERE c.name = :name')
                            ->setParameter('name', 'Foo')
                            ->getSingleResult();
        assert($reloadedFoo instanceof Clazz);

        $this->assertEquals(array('Iterator'), $reloadedFoo->getImplementedInterfaces());
        $this->assertTrue($reloadedFoo->hasMethod('foo'));

        $reloadedCountFoo = $this->em->createQuery('SELECT f FROM Scrutinizer\PhpAnalyzer\Model\GlobalFunction f WHERE f.name = :name')
                                ->setParameter('name', 'count_foo')
                                ->getSingleResult();
        $this->assertCount(1, $inSites = $reloadedCountFoo->getInCallSites());
        $this->assertSame($reloadedFoo->getMethod('foo')->getMethod(), $inSites[0]->getSource());
    }

    public function testPersistClazzWithConstant()
    {
        $this->packageVersion->addContainer($foo = new Clazz('Foo'));
        $foo->addConstant($constant = new Constant('BAR'));
        $constant->setPhpType($this->typeRegistry->getNativeType('string'));

        $this->persister->persist($this->packageVersion);

        $reloadedFoo = $this->em->createQuery('SELECT c FROM Scrutinizer\PhpAnalyzer\Model\Clazz c WHERE c.name = :name')
                            ->setParameter('name', 'Foo')
                            ->getSingleResult();
        assert($reloadedFoo instanceof Clazz);

        $this->assertTrue($reloadedFoo->hasConstant('BAR'));
        $this->assertEquals('string', (string) $reloadedFoo->getConstant('BAR')->getPhpType());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testPersistThrowsErrorOnTransientPackageVersion()
    {
        $this->persister->persist(new PackageVersion(new Package('foo'), '1.0', null, null));
    }

    public function testDocTypesOfMethods()
    {
        $this->packageVersion->addContainer($foo = new Clazz('Foo'));
        $foo->addMethod($method = new Method('foo'));

        $method->setReturnDocType('self');
        $method->setParamDocType(0, 'string');

        $this->persister->persist($this->packageVersion);

        $reloadedFoo = $this->em->createQuery('SELECT c FROM Scrutinizer\PhpAnalyzer\Model\Clazz c WHERE c.name = :name')
                ->setParameter('name', 'Foo')
                ->getSingleResult();

        $reloadedMethod = $reloadedFoo->getMethod('foo');
        $this->assertEquals(array('param_0' => 'string', 'return' => 'self'), $reloadedMethod->getDocTypes());
    }

    protected function setUp()
    {
        $this->typeRegistry = new TypeRegistry();

        $this->em = TestUtils::createTestEntityManager(true);

        $con = $this->em->getConnection();
        $paramPersister = new ParameterPersister($con);
        $callSitePersister = new CallSitePersister($con, new ArgumentPersister($con));
        $this->persister = new PackageVersionPersister($con,
            new MethodContainerPersister($con, new MethodPersister($con, $paramPersister, $callSitePersister),
                                         new PropertyPersister($con), new ConstantPersister($con)),
            new FunctionPersister($con, $paramPersister, $callSitePersister),
            new GlobalConstantPersister($con));

        $package = new Package('foo');
        $this->packageVersion = $package->createVersion('1.0');
        $this->em->persist($package);
        $this->em->flush();
    }
}