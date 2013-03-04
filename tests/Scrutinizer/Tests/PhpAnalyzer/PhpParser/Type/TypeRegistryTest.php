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

namespace Scrutinizer\Tests\PhpAnalyzer\PhpParser\Type;

use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;

class TypeRegistryTest extends BaseTypeTest
{
    /**
     * @group resolve
     */
    public function testResolveType()
    {
        $this->assertResolvedType(
            $this->registry->getArrayType(
                $this->getNative('string'),
                $this->createUnionType($this->getNative('integer'), $this->getNative('string'))
            ),
            'array<integer|string,string>'
        );
    }

    /**
     * @group resolve
     */
    public function testResolveType1()
    {
        $this->assertResolvedType($this->getNative('boolean'), 'boolean');
    }

    /**
     * @group resolve
     */
    public function testResolveType2()
    {
        $type = $this->getNative('boolean');
        $this->assertSame($type, $this->registry->resolveType($type));
    }

    /**
     * @group resolve
     */
    public function testResolveType3()
    {
        $this->assertResolvedType($this->createUnionType($this->getNative('string'), $this->getNative('integer')),
             'string|integer');
    }

    /**
     * @group resolve
     */
    public function testResolveType4()
    {
        $union = $this->createUnionType($this->getNative('string'), $this->getNative('array'), $this->getNative('integer'));
        $this->assertResolvedType($union, 'string|array<string|integer,unknown>|integer');
    }

    /**
     * @group resolve
     */
    public function testResolveType5()
    {
        $resolvedType = $this->registry->resolveType('unknown_checked');
        $this->assertSame($this->registry->getNativeType('unknown_checked'), $resolvedType);
        $this->assertInstanceOf('Scrutinizer\PhpAnalyzer\PhpParser\Type\UnknownType', $resolvedType);
        $this->assertTrue($resolvedType->isChecked());
    }

    /**
     * @group resolve
     */
    public function testResolveType6()
    {
        $resolvedType = $this->registry->resolveType('array<integer,string,{"foo":type(false)}>{"not_empty":false}');

        $this->assertInstanceOf('Scrutinizer\PhpAnalyzer\PhpParser\Type\ArrayType', $resolvedType);
        $this->assertTrue($resolvedType->getItemType('foo')->isDefined());
        $this->assertTypeEquals('false', $resolvedType->getItemType('foo')->get());
        $this->assertFalse($resolvedType->getAttribute('not_empty'));
    }

    /**
     * @group resolve
     */
    public function testResolveWithAttributes()
    {
        $type = $this->getNative('string')->addAttribute('not_empty', true);
        $this->assertResolvedType($type, 'string{"not_empty":true}');
    }

    /**
     * @group resolve
     */
    public function testResolveWithAttributes2()
    {
        $type = $this->getNative('string')->addAttribute('foo', array('bar' => 'baz'));
        $this->assertResolvedType($type, 'string{"foo":{"bar":"baz"}}');
    }

    /**
     * @group resolve
     */
    public function testResolveWithAttributes3()
    {
        $type = $this->createUnionType(
            $this->getNative('string')->addAttribute('not_empty', true),
            $this->getNative('integer')->addAttribute('not_zero', true),
            $this->getNative('boolean'));
        $this->assertResolvedType($type, 'string{"not_empty":true}|integer{"not_zero":true}|boolean');
    }

    /**
     * @group resolve
     * @dataProvider getInvalidTypes
     */
    public function testResolveTypeWithInvalidType($type, $expectedException, $expectedMessage)
    {
        $this->setExpectedException($expectedException, $expectedMessage);
        $this->resolveType($type);
    }

    public function getInvalidTypes()
    {
        return array(
            array('foo', 'RuntimeException', 'There is no native type named "foo".'),
            array('boolean<integer>', 'RuntimeException', 'The type "boolean" cannot have any parameters.'),
            array('array<integer,>', 'JMS\Parser\SyntaxErrorException', 'Expected any of T_TYPENAME or T_FALSE or T_NULL or T_OPEN_BRACE, but got ">" of type T_CLOSE_ANGLE_BRACKET at position 14 (0-based).'),
            array('object<>', 'JMS\Parser\SyntaxErrorException', 'Expected any of T_TYPENAME or T_FALSE or T_NULL or T_OPEN_BRACE, but got ">" of type T_CLOSE_ANGLE_BRACKET at position 7 (0-based).'),
            array('integer,boolean', 'JMS\Parser\SyntaxErrorException', 'Expected end of input, but got "," of type T_COMMA at position 7 (0-based).'),
        );
    }

    public function testResolveNamedTypes()
    {
        $this->registry->getClassOrCreate('Foo');
        $this->registry->getClassOrCreate('Bar');
        $this->registry->getClassOrCreate('Baz');
        $this->registry->getClassOrCreate('FooBar');

        $foo = new \Scrutinizer\PhpAnalyzer\Model\Clazz('foo');
        $this->registry->registerClass($foo);
        $registry = $this->registry;

        $this->provider->expects($this->at(0))
            ->method('loadClasses')
            ->with(array('bar', 'baz', 'foobar'))
            ->will($this->returnCallback(function($classes) use ($registry) {
                $registry->getClassOrCreate('Moo', TypeRegistry::LOOKUP_NO_CACHE);

                $bar = new \Scrutinizer\PhpAnalyzer\Model\Clazz('bar');
                $baz = new \Scrutinizer\PhpAnalyzer\Model\Clazz('Baz');

                return array('bar' => $bar, 'baz' => $baz);
            }));

        $this->provider->expects($this->at(1))
            ->method('loadClasses')
            ->with(array('moo'))
            ->will($this->returnCallback(function($classes) use ($registry) {
                $registry->getClassOrCreate('foobOOH', TypeRegistry::LOOKUP_NO_CACHE);

                $moo = new \Scrutinizer\PhpAnalyzer\Model\Clazz('Moo');

                return array('moo' => $moo);
            }));

        $this->provider->expects($this->at(2))
            ->method('loadClasses')
            ->with(array('foobooh'))
            ->will($this->returnCallback(function($classes) {
                return array();
            }));

        $this->registry->resolveNamedTypes();
    }

    public function testGetClass()
    {
        $this->registry->registerClass($foo = new \Scrutinizer\PhpAnalyzer\Model\Clazz('Foo'));
        $this->assertSame($foo, $this->registry->getClass('foo'));
        $this->assertSame($foo, $this->registry->getClass('FoO'));

        $this->registry->getClassOrCreate('Foo\Bar');
        $this->assertNull($this->registry->getClass('FoO\BAR'));
        $this->assertNull($this->registry->getClass('Foo\Bar'));
    }

    public function testGetClassOrCreate()
    {
        $this->registry->registerClass($foo = new \Scrutinizer\PhpAnalyzer\Model\Clazz('Foo\Foo'));
        $this->assertSame($foo, $this->registry->getClassOrCreate('FoO\FOO'));
        $this->assertSame($foo, $this->registry->getClassOrCreate('Foo\Foo'));

        $bar = $this->registry->getClassOrCreate('Bar\Foo');
        $this->assertSame($bar, $this->registry->getClassOrCreate('BAR\FoO'));
        $this->assertSame($bar, $this->registry->getClassOrCreate('Bar\Foo'));
    }

    public function testHasClass()
    {
        $this->registry->registerClass($foo = new \Scrutinizer\PhpAnalyzer\Model\Clazz('Foo'));
        $this->assertTrue($this->registry->hasClass('FoO'));
        $this->assertTrue($this->registry->hasClass('Foo'));

        $this->registry->getClassOrCreate('Bar');
        $this->assertFalse($this->registry->hasClass('BaR'));
        $this->assertFalse($this->registry->hasClass('Bar'));
    }

    public function testGetFunction()
    {
        $this->registry->registerFunction($foo = new \Scrutinizer\PhpAnalyzer\Model\GlobalFunction('foo'));
        $this->assertSame($foo, $this->registry->getFunction('FOo'));
        $this->assertSame($foo, $this->registry->getFunction('foo'));
    }

    public function testHasFunction()
    {
        $this->registry->registerFunction($foo = new \Scrutinizer\PhpAnalyzer\Model\GlobalFunction('Foo'));
        $this->assertTrue($this->registry->hasFunction('foo'));
        $this->assertTrue($this->registry->hasFunction('FoO'));
    }

    private function assertResolvedType($expectedType, $typeToResolve)
    {
        $resolvedType = $this->registry->resolveType($typeToResolve);
        $this->assertTrue($expectedType->equals($resolvedType), 'Expected '.$expectedType.', but got '.$resolvedType.'.');
        $this->assertEquals($expectedType->getAttributes(), $resolvedType->getAttributes());
    }
}