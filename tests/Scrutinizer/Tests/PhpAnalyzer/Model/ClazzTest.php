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

namespace Scrutinizer\Tests\PhpAnalyzer\Model;

use Scrutinizer\PhpAnalyzer\PhpParser\Type\BooleanType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\DoubleType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\IntegerType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\NamedType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\NoObjectType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\StringType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\UnknownType;
use Scrutinizer\PhpAnalyzer\Model\Clazz;
use Scrutinizer\PhpAnalyzer\Model\InterfaceC;

class ClazzTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getIsSubtypeTests
     */
    public function testisSubTypeOf($thisType, $thatType, $expectedResult)
    {
        $this->assertSame($expectedResult, $thisType->isSubTypeOf($thatType));
    }

    public function getIsSubtypeTests()
    {
        $tests = array();

        try {
            $registry = new TypeRegistry();

            $foo = new Clazz('Foo');
            $foo->setSuperClass('Bar');
            $foo->setSuperClasses(array('Bar'));
            $foo->setImplementedInterfaces(array('Baz', 'FooBar'));
            $foo->setNormalized(true);

            $tests[] = array($foo, new InterfaceC('FooBar'), true);
            $tests[] = array($foo, new InterfaceC('Foo'), false);
            $tests[] = array($foo, $foo, true);
            $tests[] = array($foo, new Clazz('Bar'), true);
            $tests[] = array($foo, new Clazz('FooBar'), false);
            $tests[] = array($foo, NamedType::createResolved($registry, new InterfaceC('Baz')), true);
            $tests[] = array($foo, NamedType::createResolved($registry, new InterfaceC('Moo')), false);
            $tests[] = array($foo, NamedType::createResolved($registry, new Clazz('Foo')), true);
            $tests[] = array($foo, NamedType::createResolved($registry, new Clazz('FoooFooo')), false);
            $tests[] = array($foo, new UnknownType($registry, false), true);
            $tests[] = array($foo, new NoObjectType($registry), true);
            $tests[] = array($foo, new BooleanType($registry), false);
            $tests[] = array($foo, new IntegerType($registry), false);
            $tests[] = array($foo, new DoubleType($registry), false);
            $tests[] = array($foo, new StringType($registry), false);
            $tests[] = array($foo, $registry->createUnionType(array('string', new InterfaceC('Baz'))), true);
            $tests[] = array($foo, $registry->createUnionType(array('string', 'boolean')), false);
        } catch (\Exception $ex) {
            echo sprintf("Could not get tests for isSubtypeTests(): %s\n", $ex->getMessage().' on line '.$ex->getLine().' in file '.$ex->getFile());
        }

        return $tests;
    }
}