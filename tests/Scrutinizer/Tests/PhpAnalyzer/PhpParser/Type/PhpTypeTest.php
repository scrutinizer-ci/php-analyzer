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

use Scrutinizer\PhpAnalyzer\PhpParser\Type\NamedType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;
use Scrutinizer\PhpAnalyzer\Model\Clazz;
use Scrutinizer\PhpAnalyzer\Model\InterfaceC;

class PhpTypeTest extends BaseTypeTest
{
    /** @var TypeRegistry */
    protected $registry;

    /**
     * @dataProvider getEqualsTests
     * @group equals
     */
    public function testEquals($a, $b, $expected, array $extends = array())
    {
        $this->setUpInheritance($extends);

        $a = $this->registry->resolveType($a);
        $b = $this->registry->resolveType($b);

        $this->assertSame($expected, $a->equals($b), sprintf('Could not verify equality of $a (%s - %s) and $b (%s - %s).', get_class($a), $a, get_class($b), $b));
        $this->assertSame($expected, $b->equals($a), sprintf('Could not verify equality of $b (%s - %s) and $a (%s - %s).', get_class($b), $b, get_class($a), $a));
    }

    public function getEqualsTests()
    {
        $tests = array();

        $tests[] = array('string', 'string', true);
        $tests[] = array('string', 'boolean', false);
        $tests[] = array('object', 'object', true);
        $tests[] = array('array', 'array', true);
        $tests[] = array('array', 'array<integer|string,unknown>', true);
        $tests[] = array('array', 'array<integer|string,all>', false);
        $tests[] = array('array<integer|string,unknown>', 'array<integer|string,all>', false);
        $tests[] = array('array', 'array<string|integer,unknown,{"foo":type(unknown)}>', false);
        $tests[] = array('array<integer,string,{"foo":type(string)}>', 'array<integer,string>', false);
        $tests[] = array('array<integer,string|integer,{"foo":type(string)}>', 'array<integer,string|integer,{"foo":type(integer)}>', false);
        $tests[] = array('array<integer,all>', 'array<integer,all>', true);
        $tests[] = array('array<integer|string,integer,{"foo":integer}>', 'array<integer|string,integer>', false);
        $tests[] = array('object<Foo>', 'object<Foo>', true);
        $tests[] = array('object<Foo>', 'object<Foo>', true, array('Foo' => 'Bar'));
        $tests[] = array('this<Foo>', 'object<Foo>', true);
        $tests[] = array('this<Foo>', 'object<Foo>', true, array('Foo' => 'Bar'));
        $tests[] = array('this<Foo>', 'this<Foo>', true);
        $tests[] = array('this<Foo>', 'this<Foo>', true, array('Foo' => 'Bar'));
        $tests[] = array('all', 'all', true);
        $tests[] = array('unknown', 'unknown', true);

        $tests[] = array('boolean', 'boolean', true);
        $tests[] = array('boolean', 'false', false);
        $tests[] = array('false', 'false', true);

        foreach ($tests as $test) {
            if ($test[0] === $test[1]) {
                continue;
            }

            $tmp = $test[0];
            $test[0] = $test[1];
            $test[1] = $tmp;
            $tests[] = $test;
        }

        return $tests;
    }

    /**
     * @group equals
     */
    public function testEqualsWithNamedAndResolvedType()
    {
        $a = $this->registry->getClassOrCreate('Foo');
        $this->assertInstanceOf('Scrutinizer\PhpAnalyzer\PhpParser\Type\NamedType', $a);

        $b = new \Scrutinizer\PhpAnalyzer\Model\Clazz('Foo');
        $b->setNormalized(true);
        $this->assertTrue($a->equals($b));
        $this->assertTrue($b->equals($a));

        $c = new \Scrutinizer\PhpAnalyzer\Model\InterfaceC('Foo');
        $c->setNormalized(true);
        $this->assertTrue($a->equals($c));
        $this->assertTrue($c->equals($a));
    }

    /**
     * @group equals
     */
    public function testEqualsWithResolvedNamedAndResolvedType()
    {
        $a = new NamedType($this->registry, 'Foo');
        $b = new \Scrutinizer\PhpAnalyzer\Model\Clazz('Foo');
        $b->setNormalized(true);
        $a->setReferencedType($b);
        $this->assertTrue($a->equals($b));
        $this->assertTrue($b->equals($a));

        $a = new NamedType($this->registry, 'Foo');
        $c = new \Scrutinizer\PhpAnalyzer\Model\Clazz('Foo');
        $c->setNormalized(true);
        $a->setReferencedType($c);
        $this->assertTrue($c->equals($a));
        $this->assertTrue($a->equals($c));
    }

    /**
     * @dataProvider getTestForEqualityTests
     * @group testForEquality
     */
    public function testTestForEquality($a, $b, $expected, array $extends = array())
    {
        $this->setUpInheritance($extends);

        $a = $this->registry->resolveType($a);
        $b = $this->registry->resolveType($b);
        $actual = $a->testForEquality($b);

        $this->assertNotNull($actual);
        $this->assertEquals($expected, (string) $actual, sprintf('Could not verify equality result for $a (%s - %s) with $b (%s - %s).', get_class($a), $a, get_class($b), $b));
    }

    public function getTestForEqualityTests()
    {
        $tests = array();

        // Resource Type
        $tests[] = array('resource', 'boolean', 'unknown');
        $tests[] = array('resource', 'integer', 'unknown');
        $tests[] = array('resource', 'double', 'unknown');
        $tests[] = array('resource', 'string', 'unknown');
        $tests[] = array('resource', 'array', 'false');
        $tests[] = array('resource', 'object', 'false');
        $tests[] = array('resource', 'null', 'false');
        $tests[] = array('resource', 'object|array', 'false');
        $tests[] = array('resource', 'boolean|array', 'unknown');

        // Boolean Type
        $tests[] = array('boolean', 'boolean', 'unknown');
        $tests[] = array('boolean', 'false', 'unknown');
        $tests[] = array('false', 'false', 'true');

        // Callable Type
        // We could have modeled callable as a true super-set of the string, array, and
        // object types. However, since not all strings, arrays, and objects are valid
        // callbacks, we chose to use a bit more strict rules. Namely, there is no valid
        // callback which would evaluate to true with any of the below types. The empty
        // strings, or a string consisting solely of integers are not valid callbacks.
        $tests[] = array('integer', 'callable', 'false');
        $tests[] = array('callable', 'integer', 'false');
        $tests[] = array('double', 'callable', 'false');
        $tests[] = array('callable', 'double', 'false');
        $tests[] = array('null', 'callable', 'false');
        $tests[] = array('callable', 'null', 'false');
        $tests[] = array('boolean', 'callable', 'false');
        $tests[] = array('callable', 'boolean', 'false');
        $tests[] = array('resource', 'callable', 'false');
        $tests[] = array('callable', 'resource', 'false');

        //// Object Types
        // Tests that are unknown because they depend on runtime values.
        $tests[] = array('object<Foo>', 'object<Foo>', 'unknown');
        $tests[] = array('object<Foo>', 'object<Foo>', 'unknown', array('Foo' => 'Bar'));
        $tests[] = array('this<Foo>', 'object<Foo>', 'unknown');
        $tests[] = array('this<Foo>', 'object<Foo>', 'unknown', array('Foo' => 'Bar'));
        $tests[] = array('this<Foo>', 'this<Foo>', 'unknown');
        $tests[] = array('this<Foo>', 'this<Foo>', 'unknown', array('Foo' => 'Bar'));

        // Tests that are unknown because they depend on not resolved types.
        $tests[] = array('object<Foo>', 'object<Bar>', 'unknown');
        $tests[] = array('this<Foo>', 'object<Bar>', 'unknown');
        $tests[] = array('this<Foo>', 'this<Bar>', 'unknown');

        // Tests for which we know they are always false.
        $tests[] = array('object<Foo>', 'object<Moo>', 'false', array('Foo' => 'Bar', 'Moo' => 'Boo'));
        $tests[] = array('this<Foo>', 'object<Moo>', 'false', array('Foo' => 'Bar', 'Moo' => 'Boo'));

        // A == B is equal to B == A
        foreach ($tests as $test) {
            if ($test[0] === $test[1]) {
                continue;
            }

            $tmp = $test[0];
            $test[0] = $test[1];
            $test[1] = $tmp;
            $tests[] = $test;
        }

        return $tests;
    }

    /**
     * @group leastSupertype
     * @dataProvider getLeastSuperTypeTests
     */
    public function testGetLeastSuperTypeDefault($a, $b, $expected, array $extends = array())
    {
        $this->setUpInheritance($extends);

        $a = $this->registry->resolveType($a);
        $b = $this->registry->resolveType($b);
        $expected = $this->registry->resolveType($expected);

        $this->assertTrue($expected->equals($actual = $a->getLeastSuperType($b)), sprintf('Expected type "%s" does not equal actual type "%s".', $expected, $actual));
        $this->assertTrue($expected->equals($actual = $b->getLeastSuperType($a)), sprintf('Expected type "%s" does not equal actual type "%s".', $expected, $actual));
    }

    public function getLeastSuperTypeTests()
    {
        $tests = array();

        // A, B, Expected Type
        $tests[] = array('boolean', 'double', 'boolean|double');
        $tests[] = array('boolean', 'boolean', 'boolean');
        $tests[] = array('boolean', 'false', 'boolean');
        $tests[] = array('false', 'false', 'false');

        $tests[] = array('array<string>', 'array<integer>', 'array<integer|string>');
        $tests[] = array('array<string>', 'array', 'array');
        $tests[] = array('array<string,string,{"foo":type(string)}>',
                         'array<string,integer,{"foo":type(integer)}>',
                         'array<string,integer|string,{"foo":type(string|integer)}>');
        $tests[] = array('array<string,string,{"foo":type(string)}>',
                         'array<string,string>',
                         'array<string,string>|array<string,string,{"foo":type(string)}>');
        $tests[] = array('array<string,string,{"foo":type(string)}>',
                         'array<string,string,{"bar":type(string)}>',
                         'array<string,string,{"foo":type(string)}>|array<string,string,{"bar":type(string)}>');

        // Unknown-Type Array, and All-Type Array
        $tests[] = array('array', 'array<string|integer,all>', 'array');
        $tests[] = array('array<string|integer,unknown>', 'array<string|integer,all>', 'array');
        $tests[] = array('array', 'array<integer,all>', 'array');
        $tests[] = array('array', 'array<string>', 'array');

        return $tests;
    }

    /**
     * @dataProvider getGreatestSubtypeTests
     * @group greatestSubtype
     */
    public function testGetGreatestSubtype($a, $b, $expected, array $extends = array())
    {
        $this->setUpInheritance($extends);

        $a = $this->registry->resolveType($a);
        $b = $this->registry->resolveType($b);
        $expected = $this->registry->resolveType($expected);

        $this->assertTrue($expected->equals($actual = $a->getGreatestSubtype($b)), sprintf('Expected type "%s" does not equal actual type "%s".', $expected, $actual));
        $this->assertTrue($expected->equals($actual = $b->getGreatestSubtype($a)), sprintf('Expected type "%s" does not equal actual type "%s".', $expected, $actual));
    }

    /**
     * The greatest subtype of type A and B is also known as
     * the infimum, meet, or greatest lower bound, and typically
     * denoted with A ^ B.
     *
     * When looking at the type hierarchy, this is the type where both type
     * paths meet. The worst case, is to meet at the bottom No type.
     *
     * @return array
     */
    public function getGreatestSubtypeTests()
    {
        $tests = array();

        // A, B, Expected Type
        $tests[] = array('boolean', 'boolean', 'boolean');
        $tests[] = array('boolean', 'false', 'false');
        $tests[] = array('false', 'false', 'false');
        $tests[] = array('unknown', 'boolean', 'unknown');
        $tests[] = array('integer', 'double', 'none');
        $tests[] = array('array', 'array<string>', 'array<string>');
        $tests[] = array('callable', 'integer', 'none');
        $tests[] = array('callable', 'object', 'object');

        // Unions
        $tests[] = array('string|integer', 'string', 'string');
        $tests[] = array('string|integer', 'double', 'none');

        // Objects
        $tests[] = array('object<Foo>', 'object<Bar>', 'none');
        $tests[] = array('object<Foo>', 'object<Foo>', 'object<Foo>');
        $tests[] = array('object', 'object<Foo>', 'object<Foo>');
        $tests[] = array('object<Foo>', 'object<Bar>', 'object<Foo>', array('Foo' => 'Bar'));
        $tests[] = array('object<Foo>|object<Bar>', 'object<Baz>', 'object<Foo>|object<Bar>', array('Foo' => 'Baz', 'Bar' => 'Baz'));
        $tests[] = array('this<Foo>', 'object<Bar>', 'none');
        $tests[] = array('this<Foo>', 'this<Bar>', 'none');
        $tests[] = array('object', 'this<Foo>', 'object<Foo>');
        $tests[] = array('this<Foo>', 'this<Bar>', 'object<Foo>', array('Foo' => 'Bar'));
        $tests[] = array('this<Foo>', 'object<Bar>', 'object<Foo>', array('Foo' => 'Bar'));
        $tests[] = array('object<Foo>', 'this<Bar>', 'object<Foo>', array('Foo' => 'Bar'));
        $tests[] = array('object<Foo>|this<Bar>', 'object<Baz>', 'object<Foo>|object<Bar>', array('Foo' => 'Baz', 'Bar' => 'Baz'));
        $tests[] = array('this<Foo>|object<Bar>', 'object<Baz>', 'object<Foo>|object<Bar>', array('Foo' => 'Baz', 'Bar' => 'Baz'));
        $tests[] = array('object<Foo>|object<Bar>', 'this<Baz>', 'object<Foo>|object<Bar>', array('Foo' => 'Baz', 'Bar' => 'Baz'));
        $tests[] = array('this<Foo>|this<Bar>', 'object<Baz>', 'object<Foo>|object<Bar>', array('Foo' => 'Baz', 'Bar' => 'Baz'));
        $tests[] = array('this<Foo>|this<Bar>', 'this<Baz>', 'object<Foo>|object<Bar>', array('Foo' => 'Baz', 'Bar' => 'Baz'));

        // Unknown
        $tests[] = array('unknown', 'integer', 'unknown');
        $tests[] = array('unknown', 'array', 'unknown');
        $tests[] = array('unknown', 'none', 'unknown');
        $tests[] = array('unknown', 'all', 'unknown');
        $tests[] = array('unknown', 'object', 'unknown');
        $tests[] = array('unknown', 'object<Foo>', 'unknown');
        $tests[] = array('unknown', 'object<Foo>', 'unknown', array('Foo' => 'Bar'));
        $tests[] = array('unknown', 'this<Foo>', 'unknown');
        $tests[] = array('unknown', 'this<Foo>', 'unknown', array('Foo' => 'Bar'));

        // A ^ B = B ^ A
        foreach ($tests as $test) {
            if ($test[1] === $test[0]) {
                continue;
            }

            $tmp = $test[1];
            $test[1] = $test[0];
            $test[0] = $tmp;
            $tests[] = $test;
        }

        return $tests;
    }

    /**
     * This test differs from the other isSubTypeOf tests in that we need
     * to check the behavior of NamedType resolved against, Clazz instances.
     *
     * @group isSubTypeOf
     */
    public function testIsSubTypeOfWithForObjectTypes()
    {
        $this->registry->registerClass($foo = new Clazz('Foo'));
        $this->registry->registerClass($bar = new Clazz('Bar'));
        $foo->setSuperClasses(array('Bar'));
        $foo->setNormalized(true);
        $bar->setNormalized(true);
        $named = new NamedType($this->registry, 'Bar');
        $named->setReferencedType($bar);

        $this->assertTrue($foo->isSubTypeOf($named));
        $this->assertFalse($named->isSubTypeOf($foo));
    }

    /**
     * @dataProvider getSubTypeTests
     * @group isSubTypeOf
     */
    public function testIsSubTypeOf($a, $b, $expectedOutcome, array $extends = array())
    {
        $this->setUpInheritance($extends);
        $a = $this->registry->resolveType($a);
        $b = $this->registry->resolveType($b);

        $this->assertSame($expectedOutcome, $a->isSubTypeOf($b), sprintf('Could not verify that $a (%s - %s) is a subtype of $b (%s - %s).', get_class($a), $a, get_class($b), $b));
    }

    public function getSubTypeTests()
    {
        $tests = array();

        // Every other type is a subtype of the Unkown type, and the Unknown
        // type is a subtype of all other types
        $tests[] = array('integer', 'unknown', true);
        $tests[] = array('unknown', 'integer', true);
        $tests[] = array('double', 'unknown', true);
        $tests[] = array('unknown', 'double', true);
        $tests[] = array('string', 'unknown', true);
        $tests[] = array('unknown', 'string', true);
        $tests[] = array('array', 'unknown', true);
        $tests[] = array('unknown', 'array', true);
        $tests[] = array('array<integer>', 'unknown', true);
        $tests[] = array('unknown', 'array<integer>', true);
        $tests[] = array('object', 'unknown', true);
        $tests[] = array('unknown', 'object', true);
        $tests[] = array('object<Foo>', 'unknown', true);
        $tests[] = array('this<Foo>', 'unknown', true);
        $tests[] = array('unknown', 'object<Foo>', true);
        $tests[] = array('unknown', 'this<Foo>', true);
        $tests[] = array('number', 'unknown', true);
        $tests[] = array('unknown', 'number', true);
        $tests[] = array('all', 'unknown', true);
        $tests[] = array('unknown', 'all', true);
        $tests[] = array('callable', 'unknown', true);
        $tests[] = array('unknown', 'callable', true);
        $tests[] = array('none', 'unknown', true);
        $tests[] = array('unknown', 'none', true);
        $tests[] = array('null', 'unknown', true);
        $tests[] = array('unknown', 'null', true);
        $tests[] = array('resource', 'unknown', true);
        $tests[] = array('unknown', 'resource', true);

        // Every other type is a subtype of the all type.
        $tests[] = array('integer', 'all', true);
        $tests[] = array('all', 'integer', false);
        $tests[] = array('double', 'all', true);
        $tests[] = array('all', 'double', false);
        $tests[] = array('string', 'all', true);
        $tests[] = array('all', 'string', false);
        $tests[] = array('array', 'all', true);
        $tests[] = array('all', 'array', false);
        $tests[] = array('array<integer>', 'all', true);
        $tests[] = array('all', 'array<integer>', false);
        $tests[] = array('object', 'all', true);
        $tests[] = array('all', 'object', false);
        $tests[] = array('object<Foo>', 'all', true);
        $tests[] = array('this<Foo>', 'all', true);
        $tests[] = array('all', 'object<Foo>', false);
        $tests[] = array('all', 'this<Foo>', false);
        $tests[] = array('number', 'all', true);
        $tests[] = array('all', 'number', false);
        $tests[] = array('callable', 'all', true);
        $tests[] = array('all', 'callable', false);
        $tests[] = array('none', 'all', true);
        $tests[] = array('all', 'none', false);
        $tests[] = array('null', 'all', true);
        $tests[] = array('all', 'null', false);
        $tests[] = array('resource', 'all', true);
        $tests[] = array('all', 'resource', false);
        $tests[] = array('all', 'all', true);

        // The None type is a subtype of every other type
        $tests[] = array('none', 'unknown', true);
        $tests[] = array('none', 'integer', true);
        $tests[] = array('none', 'double', true);
        $tests[] = array('none', 'string', true);
        $tests[] = array('none', 'array', true);
        $tests[] = array('none', 'array<integer>', true);
        $tests[] = array('none', 'object', true);
        $tests[] = array('none', 'object<Foo>', true);
        $tests[] = array('none', 'this<Foo>', true);
        $tests[] = array('none', 'number', true);
        $tests[] = array('none', 'callable', true);
        $tests[] = array('none', 'null', true);
        $tests[] = array('none', 'resource', true);

        // Boolean Types
        $tests[] = array('boolean', 'false', false);
        $tests[] = array('false', 'boolean', true);
        $tests[] = array('boolean', 'boolean', true);
        $tests[] = array('false', 'false', true);

        // NoObject type is a supertype of every other object type.
        $tests[] = array('object', 'object', true);
        $tests[] = array('object<Foo>', 'object', true);
        $tests[] = array('this<Foo>', 'object', true);
        $tests[] = array('object', 'object<Foo>', false);
        $tests[] = array('object', 'this<Foo>', false);
        $tests[] = array('object<Bar>', 'object', true, array('Bar' => ''));
        $tests[] = array('this<Bar>', 'object', true, array('Bar' => ''));
        $tests[] = array('object', 'object<Bar>', false);
        $tests[] = array('object', 'this<Bar>', false);
        $tests[] = array('object', 'object<Bar>', false, array('Bar' => ''));
        $tests[] = array('object', 'this<Bar>', false, array('Bar' => ''));

        // An Object type O1 is a subtype of Object type O2 if O1 has a parent
        // of O2, or implements an interface of O2.
        $tests[] = array('object<Foo>', 'object<Bar>', true, array('Foo' => 'Bar'));
        $tests[] = array('object<Foo>', 'object<Baz>', true, array('Foo' => 'Bar', 'Bar' => 'Baz'));
        $tests[] = array('object<Foo>', 'object<Bar>', false, array('Foo' => 'Baz', 'Bar' => 'Baz'));
        $tests[] = array('this<Foo>', 'object<Bar>', true, array('Foo' => 'Bar'));
        $tests[] = array('this<Foo>', 'object<Baz>', true, array('Foo' => 'Bar', 'Bar' => 'Baz'));
        $tests[] = array('this<Foo>', 'object<Bar>', false, array('Foo' => 'Baz', 'Bar' => 'Baz'));
        $tests[] = array('object<Foo>', 'this<Bar>', true, array('Foo' => 'Bar'));
        $tests[] = array('object<Foo>', 'this<Baz>', true, array('Foo' => 'Bar', 'Bar' => 'Baz'));
        $tests[] = array('object<Foo>', 'this<Bar>', false, array('Foo' => 'Baz', 'Bar' => 'Baz'));
        $tests[] = array('this<Foo>', 'this<Bar>', true, array('Foo' => 'Bar'));
        $tests[] = array('this<Foo>', 'this<Baz>', true, array('Foo' => 'Bar', 'Bar' => 'Baz'));
        $tests[] = array('this<Foo>', 'this<Bar>', false, array('Foo' => 'Baz', 'Bar' => 'Baz'));

        // If a no resolved type is present, then object subtype checks always fail.
        // This will generate some false-positives if not all classes are present in a
        // review which should regularly be the case though (unless someone has a filter
        // which excludes them). If we change this, then we need to re-visit all the other
        // methods (getGreatestSubType, getLeastSuperType, etc.) to consider no resolved
        // types specially.
        $tests[] = array('object<Foo>', 'object<Bar>', false);
        $tests[] = array('object<Foo>', 'object<Bar>', false, array('Foo' => 'Moo'));
        $tests[] = array('object<Foo>', 'object<Bar>', false, array('Bar' => 'Baz'));
        $tests[] = array('this<Foo>', 'object<Bar>', false);
        $tests[] = array('object<Foo>', 'this<Bar>', false);
        $tests[] = array('this<Foo>', 'this<Bar>', false);

        // Each Array type is a subtype of the generic Array type.
        $tests[] = array('array<integer>', 'array', true);
        $tests[] = array('array<integer,object<Foo>>', 'array', true);
        $tests[] = array('array<integer|string, boolean>', 'array', true);
        $tests[] = array('array', 'array', true);
        $tests[] = array('array<integer,all>', 'array<integer,all>', true);

        // The generic Array type is not a subtype of a more concrete Array type.
        $tests[] = array('array', 'array<integer>', false);
        $tests[] = array('array', 'array<integer|string,string>', false);

        // Array with known item types.
        $tests[] = array('array<string,integer|string,{"foo":type(integer)}>',
                         'array<string,integer|string,{"foo":type(string)}>', false); // Known item is is not a subtype.
        $tests[] = array('array<string,integer|string,{"foo":type(string)}>',
                         'array<string,integer|string>', true);
        $tests[] = array('array<string,integer|string>',
                         'array<string,integer|string,{"foo":type(string)}>', false);
        $tests[] = array('array<string,string,{"foo":type(string)}>',
                         'array<string,string,{"bar":type(string)}>',
                         false);


        // An Array type A1 is a subtype of Array type A2 if both A1's key type,
        // and A1's element type are subtypes of A2's key and element types
        // respectively.
        $tests[] = array('array<integer>', 'array<integer>', true);
        $tests[] = array('array<integer,string>', 'array<integer|string,string>', true);
        $tests[] = array('array<integer,string|object<Foo>>', 'array<integer,string>', false);
        $tests[] = array('array<integer,string|this<Foo>>', 'array<integer,string>', false);

        // Unknown-Type array and All-Type array
        $tests[] = array('array<integer|string,all>', 'array', true);
        $tests[] = array('array<integer|string,all>', 'array<integer|string,unknown>', true);
        $tests[] = array('array<integer|string,unknown>', 'array<integer|string,all>', true);
        $tests[] = array('array', 'array<integer|string,all>', true);

        // String, Array, and Object types are subtypes of the Callable type.
        $tests[] = array('object', 'callable', true);
        $tests[] = array('callable', 'object', false);
        $tests[] = array('array', 'callable', true);
        $tests[] = array('callable', 'array', false);
        $tests[] = array('string', 'callable', true);
        $tests[] = array('callable', 'string', false);
        $tests[] = array('object<Foo>', 'callable', true);
        $tests[] = array('this<Foo>', 'callable', true);
        $tests[] = array('callable', 'object<Foo>', false);
        $tests[] = array('callable', 'this<Foo>', false);
        $tests[] = array('object<Foo>', 'callable', true, array('Foo'));
        $tests[] = array('this<Foo>', 'callable', true, array('Foo'));
        $tests[] = array('callable', 'object<Foo>', false, array('Foo'));
        $tests[] = array('callable', 'this<Foo>', false, array('Foo'));
        $tests[] = array('array<integer,boolean>', 'callable', true);
        $tests[] = array('callable', 'array<integer,boolean>', false);

        // A union type is a subtype of a type U if all the union's alternates
        // are a subtype of that type U.
        $tests[] = array('integer|double', 'integer', false);
        $tests[] = array('integer|double', 'double', false);
        $tests[] = array('integer|double', 'integer|double', true);
        $tests[] = array('integer|double', 'integer|double|string', true);
        $tests[] = array('object<Foo>|object<Bar>', 'object<Foo>|object<Bar>', true);
        $tests[] = array('object<Foo>|object<Bar>', 'object<Foo>|object<Bar>', true, array('Foo' => 'Baz'));
        $tests[] = array('object<Foo>|object<Bar>', 'object<Foo>|object<Bar>', true, array('Foo' => 'Baz', 'Bar' => 'Boo'));
        $tests[] = array('this<Foo>|object<Bar>', 'object<Foo>|object<Bar>', true);
        $tests[] = array('this<Foo>|object<Bar>', 'object<Foo>|object<Bar>', true, array('Foo' => 'Baz'));
        $tests[] = array('this<Foo>|object<Bar>', 'object<Foo>|object<Bar>', true, array('Foo' => 'Baz', 'Bar' => 'Boo'));
        $tests[] = array('object<Foo>|this<Bar>', 'object<Foo>|object<Bar>', true);
        $tests[] = array('object<Foo>|this<Bar>', 'object<Foo>|object<Bar>', true, array('Foo' => 'Baz'));
        $tests[] = array('object<Foo>|this<Bar>', 'object<Foo>|object<Bar>', true, array('Foo' => 'Baz', 'Bar' => 'Boo'));

        // A type U is a subtype of a union type if it is a subtype of one of
        // its alternates.
        $tests[] = array('integer', 'integer|double', true);
        $tests[] = array('integer', 'double|string', false);

        // If types are equal, they are subtypes of eachother.
        $tests[] = array('integer', 'integer', true);
        $tests[] = array('double', 'double', true);
        $tests[] = array('string', 'string', true);
        $tests[] = array('array', 'array', true);
        $tests[] = array('unknown', 'unknown', true);
        $tests[] = array('none', 'none', true);
        $tests[] = array('object', 'object', true);
        $tests[] = array('null', 'null', true);
        $tests[] = array('callable', 'callable', true);
        $tests[] = array('all', 'all', true);

        // Unrelated types (non-exhaustive).
        $tests[] = array('integer', 'double', false);
        $tests[] = array('double', 'integer', false);
        $tests[] = array('callable', 'integer', false);
        $tests[] = array('integer', 'callable', false);
        $tests[] = array('resource', 'integer', false);
        $tests[] = array('integer', 'resource', false);
        $tests[] = array('string', 'resource', false);
        $tests[] = array('resource', 'string', false);
        $tests[] = array('string', 'object', false);
        $tests[] = array('object', 'string', false);

        return $tests;
    }

    public function testIsSubtypeWithInterfaceAndClass()
    {
        $bar = new \Scrutinizer\PhpAnalyzer\Model\InterfaceC('Bar');
        $bar->setNormalized(true);

        $foo = new Clazz('Foo');
        $foo->setNormalized(true);

        $this->assertFalse($foo->isSubtypeOf($bar));

        $foo->addImplementedInterface('bar');
        $this->assertTrue($foo->isSubtypeOf($bar));

        $namedBar = new NamedType($this->registry, 'Bar');
        $this->assertTrue($foo->isSubtypeOf($namedBar));
    }

    public function testIsSubtypeWithResolvedNamedTypes()
    {
        $bar = $this->registry->getClassOrCreate('Bar\Baz');
        $bar->setReferencedType(new InterfaceC('Bar\Baz'));
        $bar->getReferencedType()->setNormalized(true);

        $foo = $this->registry->getClassOrCreate('Foo\Moo');
        $foo->setReferencedType(new Clazz('Foo\Moo'));
        $foo->getReferencedType()->setNormalized(true);

        $this->assertFalse($foo->isSubtypeOf($bar));

        $foo->getReferencedType()->addImplementedInterface('Bar\Baz');
        $this->assertTrue($foo->isSubtypeOf($bar));
    }

    public function testIsSubtypeWithPartiallyResolvedTypes()
    {
        $interface = $this->registry->getClassOrCreate('I');
        $interface->setReferencedType(new InterfaceC('I'));
        $interface->getReferencedType()->setNormalized(true);

        $class = new Clazz('C');
        $this->assertTrue($class->isSubtypeOf($interface), 'Non-normalized types are always subtypes.');

        $class->addImplementedInterface('I');
        $this->assertTrue($class->isSubtypeOf($interface));
    }

    public function testIsSubtypeWithNamedTypes()
    {
        $bar = new \Scrutinizer\PhpAnalyzer\Model\InterfaceC('Bar');
        $bar->setNormalized(true);

        $foo = new Clazz('Foo');
        $foo->setNormalized(true);

        $this->assertFalse($foo->isSubtypeOf($bar));

        $foo->addImplementedInterface('Bar');
        $namedBar = NamedType::createResolved($this->registry, $bar);
        $this->assertTrue($foo->isSubTypeOf($namedBar));
        $this->assertTrue($foo->isSubTypeOf($bar));

        $namedFoo = NamedType::createResolved($this->registry, $foo);
        $this->assertTrue($namedFoo->isSubtypeOf($namedBar));
        $this->assertTrue($namedFoo->isSubtypeOf($bar));

        $unresolvedNamedBar = new NamedType($this->registry, 'Bar');
        $this->assertTrue($foo->isSubtypeOf($unresolvedNamedBar));
        $this->assertTrue($namedFoo->isSubtypeOf($unresolvedNamedBar));

        $baz = new InterfaceC('Baz');
        $baz->setNormalized(true);
        $this->assertFalse($baz->isSubtypeOf($bar));
        $this->assertFalse($baz->isSubtypeOf($namedBar));
        $this->assertFalse($baz->isSubtypeOf($unresolvedNamedBar));

        $baz->addExtendedInterface('Bar');
        $this->assertTrue($baz->isSubtypeOf($bar));
        $this->assertTrue($baz->isSubtypeOf($namedBar));
        $this->assertTrue($baz->isSubtypeOf($unresolvedNamedBar));
    }

    /**
     * @dataProvider getDocTypeTests
     */
    public function testGetDocType($type, $expectedDocType, array $importedNamespaces = array(), array $extends = array())
    {
        $this->setUpInheritance($extends);
        $type = $this->registry->resolveType($type);

        $this->assertEquals($expectedDocType, $type->getDocType($importedNamespaces));
    }

    public function getDocTypeTests()
    {
        $tests = array();

        // Primitive type tests.
        $tests[] = array('boolean', 'boolean');
        $tests[] = array('integer', 'integer');
        $tests[] = array('double', 'double');
        $tests[] = array('string', 'string');
        $tests[] = array('array', 'array');
        $tests[] = array('resource', 'resource');
        $tests[] = array('callable', 'callable');
        $tests[] = array('string|integer', 'string|integer');
        $tests[] = array('string|null', 'string|null');

        // Array type tests.
        $tests[] = array('array<string>', 'array<string>');
        $tests[] = array('array<integer|string>', 'array<integer|string>');
        $tests[] = array('array<string|integer,string>', 'array<*,string>');
        $tests[] = array('array<string,string|integer>', 'array<string,string|integer>');
        $tests[] = array('array<unknown>', 'array');
        $tests[] = array('array<all>', 'array');

        // Object type tests.
        $tests[] = array('object<Foo\Bar>', 'Foo\Bar');
        $tests[] = array('object<Foo\Bar>', 'Bar', array('' => 'Foo'));
        $tests[] = array('object<Foo\Bar>', 'Bar', array('Bar' => 'Foo\Bar'));
        $tests[] = array('object<Foo\Bar\Baz>', 'Bar\Baz', array('Bar' => 'Foo\Bar'));
        $tests[] = array('object<ReflectionClass>', '\ReflectionClass', array('' => 'Foo'));
        $tests[] = array('this<Foo>', 'Foo');

        return $tests;
    }

    /**
     * @dataProvider getTypesUnderTests
     * @group typesUnder
     */
    public function testGetTypesUnder($method, $a, $b, $expectedRestrictedA, $expectedRestrictedB, array $extends = array())
    {
        $this->setUpInheritance($extends);

        $a = $this->registry->resolveType($a);
        $b = $this->registry->resolveType($b);

        list($restrictedA, $restrictedB) = $a->{'getTypesUnder'.$method}($b);

        if (null === $expectedRestrictedA) {
            $this->assertNull($restrictedA, sprintf('Expected null, but found type "%s".', $restrictedA));
        } else {
            $this->assertNotNull($restrictedA);

            $expectedRestrictedA = $this->registry->resolveType($expectedRestrictedA);
            $this->assertTrue($expectedRestrictedA->equals($restrictedA), sprintf('Failed to assert that expected type "%s" equals actual type "%s".', $expectedRestrictedA, $restrictedA));
        }

        if (null === $expectedRestrictedB) {
            $this->assertNull($restrictedB, sprintf('Expected null, but found type "%s".', $restrictedB));
        } else {
            $this->assertNotNull($restrictedB);

            $expectedRestrictedB = $this->registry->resolveType($expectedRestrictedB);
            $this->assertTrue($expectedRestrictedB->equals($restrictedB), sprintf('Failed to assert that expected type "%s" equals actual type "%s".', $expectedRestrictedB, $restrictedB));
        }
    }

    public function getTypesUnderTests()
    {
        // Available Comparisons:
        // - Equality = loose comparison
        // - Inequality = loose comparison
        // - ShallowEquality = strict comparison
        // - ShallowInequality = strict comparison

        // Structure
        // Comparison (see above) | Type A | Type B | Restricted A | Restricted B | Inheritance
        $tests = array();

        $tests[] = array('Equality', 'false', 'false|object<B>', 'false', 'false');
        $tests[] = array('Equality', 'false', 'false|object<B>', 'false', 'false', array('B' => 'A'));
        $tests[] = array('ShallowEquality', 'false', 'false|object<B>', 'false', 'false');
        $tests[] = array('ShallowEquality', 'false', 'false|object<B>', 'false', 'false', array('B' => 'A'));
        $tests[] = array('Inequality', 'false', 'false|object<B>', 'false', 'object<B>');
        $tests[] = array('Inequality', 'false', 'false|object<B>', 'false', 'object<B>', array('B' => 'A'));
        $tests[] = array('ShallowInequality', 'false', 'false|object<B>', 'false', 'object<B>');
        $tests[] = array('ShallowInequality', 'false', 'false|object<B>', 'false', 'object<B>', array('B' => 'A'));

        // true is not added here since at the moment, we regard true as a boolean, and do not have a true type.
        foreach (array('false', 'null') as $nativeType) {
            $tests[] = array('Equality', $nativeType, $nativeType, $nativeType, $nativeType);
            $tests[] = array('ShallowEquality', $nativeType, $nativeType, $nativeType, $nativeType);
            $tests[] = array('Inequality', $nativeType, $nativeType, null, null);
            $tests[] = array('ShallowInequality', $nativeType, $nativeType, null, null);
        }

        return $tests;
    }

    public function testAddAttribute()
    {
        $type = $this->getAbstractType();
        $this->assertNotSame($type, $newType = $type->addAttribute('foo', 'bar'));
        $this->assertSame($newType, $newType->addAttribute('foo', 'bar'));
        $this->assertNotSame($newType, $newType->addAttribute('foo', 'moo'));
        $this->assertNotSame($newType, $newType->addAttribute('bar', 'moo'));
    }

    protected function setUp()
    {
        $this->registry = new TypeRegistry();
    }

    private function getAbstractType()
    {
        return $this->getMockBuilder('Scrutinizer\PhpAnalyzer\PhpParser\Type\PhpType')
                    ->setConstructorArgs(array($this->registry))
                    ->getMockForAbstractClass();
    }

    private function setUpInheritance(array $extends = array())
    {
        if ( ! $extends) {
            return;
        }

        foreach ($extends as $class => $extendedClass) {
            if ($this->registry->hasClass($class)) {
                continue;
            }

            $this->setUpClass($class, $extends);
        }
    }

    private function setUpClass($class, array $extends)
    {
        $class = new Clazz($class);
        if (isset($extends[$class->getName()])) {
            $parentClass = $this->setUpClass($extends[$class->getName()], $extends);
            $class->setSuperClasses(array_merge(array($parentClass->getName()), $parentClass->getSuperClasses()));
            $class->setSuperClass($parentClass->getName());
        }

        $class->setNormalized(true);
        $this->registry->registerClass($class);

        return $class;
    }
}