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

namespace Scrutinizer\Tests\PhpAnalyzer\Pass;

use Scrutinizer\PhpAnalyzer\Pass\TypeScanningPass;
use Scrutinizer\Tests\PhpAnalyzer\Pass\BaseAnalyzingPassTest;

class TypeScanningPassTest extends BaseAnalyzingPassTest
{
    public function testConstantDefinitions()
    {
        $this->analyzeAst('TypeScanning/global_constants.php');

        $constants = $this->registry->getConstants();
        $this->assertSame(2, count($constants));
    }

    public function testOptionalParameter()
    {
        $this->analyzeAst('TypeScanning/optional_parameter.php');

        $test = $this->registry->getFunction('test');
        $this->assertSame(2, count($params = $test->getParameters()));
        $this->assertFalse($params['foo']->isOptional());
        $this->assertTrue($params['bar']->isOptional());

        $foo = $this->registry->getClass('Foo')->getMethod('foo');
        $this->assertSame(2, count($params = $foo->getParameters()));
        $this->assertFalse($params['foo']->isOptional());
        $this->assertTrue($params['bar']->isOptional());
    }

    public function testVariableParameters()
    {
        $this->analyzeAst('TypeScanning/variable_parameters.php');

        $foo = $this->registry->getFunction('foo');
        $this->assertTrue($foo->hasVariableParameters());

        $bar = $this->registry->getFunction('bar');
        $this->assertFalse($bar->hasVariableParameters());
    }

    public function testMagicProperties()
    {
        $this->analyzeAst('TypeScanning/magic_properties.php');

        $foo = $this->registry->getClass('Foo');

        $this->assertCount(2, $props = $foo->getProperties());
        $this->assertTrue($props->containsKey('foo'));
        $this->assertTrue($props['foo']->isPublic());

        $this->assertTrue($props->containsKey('bar'));
        $this->assertTrue($props['bar']->isPrivate());
    }

    public function testClassWithInterfaces()
    {
        $this->analyzeAst('TypeScanning/class_with_interfaces.php');

        $b = $this->registry->getClass('Foo\B');
        $this->assertInstanceOf('Scrutinizer\PhpAnalyzer\Model\InterfaceC', $b);
        $this->assertEquals(array('Foo\A'), $b->getExtendedInterfaces());

        $foo = $this->registry->getClass('Foo\Foo');
        $this->assertInstanceOf('Scrutinizer\PhpAnalyzer\Model\Clazz', $foo);
        $this->assertEquals('Foo\Bar', $foo->getSuperClass());
        $this->assertEquals(array('Foo\Bar'), $foo->getSuperClasses());
        $this->assertEquals(array('Foo\B', 'Foo\C', 'Foo\D', 'Foo\A'), $foo->getImplementedInterfaces());
        $this->assertEquals(array('foo', 'bar'), $foo->getProperties()->getKeys());
        $this->assertEquals(array('FOO', 'BAR', 'D', 'B', 'A', 'C'), $foo->getConstants()->getKeys());
        $this->assertEquals(array('foo', 'bar', 'd', 'b', 'a', 'c'), $foo->getMethods()->getKeys());
        $this->assertTrue($foo->isNormalized());
    }

    public function testDocComments()
    {
        $this->analyzeAst('TypeScanning/doc_comments.php');

        $fooClass = $this->registry->getClass('FooBar\Foo');
        $this->assertEquals(array(
            'param_0' => 'string',
            'param_1' => '\FooBar\Bar',
            'return'  => 'self',
        ), $fooClass->getMethod('foo')->getDocTypes());
        $this->assertEquals(array(
            'param_0' => 'boolean',
        ), $fooClass->getMethod('bar')->getDocTypes());
    }

    /**
     * @group overridden-method
     */
    public function testOverriddenMethod()
    {
        $this->analyzeAst('TypeScanning/overridden_method.php');

        $a = $this->registry->getClass('A');
        $this->assertTrue($a->hasMethod('a'));
        $aMethod = $a->getMethod('a');
        $this->assertEquals('A', $aMethod->getDeclaringClass());

        $b = $this->registry->getClass('B');
        $this->assertTrue($b->hasMethod('a'));
        $bMethod = $b->getMethod('a');
        $this->assertEquals('B', $bMethod->getDeclaringClass());

        $this->assertNotSame($aMethod->getMethod(), $bMethod->getMethod());
    }

    public function testMethodWoVisibility()
    {
        $this->analyzeAst('TypeScanning/method_wo_visibility.php');

        $a = $this->registry->getClass('A');
        $this->assertTrue($a->hasMethod('a'));
        $this->assertTrue($a->getMethod('a')->isPublic());
        $this->assertTrue($a->hasMethod('b'));
        $this->assertTrue($a->getMethod('b')->isPublic());
    }

    public function testPropertyOnInterfaceIsIgnored()
    {
        $this->analyzeAst('TypeScanning/property_on_interface.php');
        // should not produce a fatal error
    }

    protected function getPasses()
    {
        return array(new TypeScanningPass());
    }

    protected function setUp()
    {
        parent::setUp();
    }
}