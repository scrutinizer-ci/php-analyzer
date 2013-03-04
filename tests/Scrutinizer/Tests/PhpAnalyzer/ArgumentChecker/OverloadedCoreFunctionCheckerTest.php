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

namespace Scrutinizer\Tests\PhpAnalyzer\ArgumentChecker;

class OverloadedCoreFunctionCheckerTest extends \PHPUnit_Framework_TestCase
{
    private $registry;
    private $typeChecker;
    private $argumentChecker;

    /**
     * @dataProvider getMissingArgumentsTests
     */
    public function testGetMissingArguments($functionName, array $rawArgs, array $expectedMissingArgs)
    {
        $args = $this->resolveArguments($rawArgs);
        $function = new \Scrutinizer\PhpAnalyzer\Model\GlobalFunction($functionName);

        $missingArgs = $this->argumentChecker->getMissingArguments($function, $args);
        $this->assertEquals($expectedMissingArgs, $missingArgs);
    }

    public function getMissingArgumentsTests()
    {
        $tests = array();

        $tests[] = array('asdfasdfasdfa', array(), array());
        $tests[] = array('asdfasdfasdfa', array('asdf'), array());

        $tests[] = array('array_udiff', array(), array('array1', 'array2', 'data_compare_func'));
        $tests[] = array('array_udiff_assoc', array(), array('array1', 'array2', 'data_compare_func'));
        $tests[] = array('array_uintersect_assoc', array(), array('array1', 'array2', 'data_compare_func'));
        $tests[] = array('array_uintersect', array(), array('array1', 'array2', 'data_compare_func'));
        $tests[] = array('array_udiff', array('foo'), array('array2', 'data_compare_func'));
        $tests[] = array('array_udiff_assoc', array('foo', 'bar'), array('data_compare_func'));
        $tests[] = array('array_uintersect_assoc', array('foo', 'bar'), array('data_compare_func'));
        $tests[] = array('array_uintersect', array('foo', 'bar'), array('data_compare_func'));
        $tests[] = array('array_udiff', array('foo', 'bar'), array('data_compare_func'));
        $tests[] = array('array_udiff_assoc', array('foo', 'bar'), array('data_compare_func'));
        $tests[] = array('array_uintersect_assoc', array('foo', 'bar'), array('data_compare_func'));
        $tests[] = array('array_uintersect', array('foo', 'bar'), array('data_compare_func'));
        $tests[] = array('array_udiff', array('foo', 'bar', 'baz'), array());
        $tests[] = array('array_udiff_assoc', array('foo', 'bar', 'baz'), array());
        $tests[] = array('array_uintersect_assoc', array('foo', 'bar', 'baz'), array());
        $tests[] = array('array_uintersect', array('foo', 'bar', 'baz'), array());
        $tests[] = array('array_udiff', array('foo', 'bar', 'baz', 'boo'), array());
        $tests[] = array('array_udiff_assoc', array('foo', 'bar', 'baz', 'boo'), array());
        $tests[] = array('array_uintersect_assoc', array('foo', 'bar', 'baz', 'boo'), array());
        $tests[] = array('array_uintersect', array('foo', 'bar', 'baz', 'boo'), array());

        $tests[] = array('array_udiff_uassoc', array(), array('array1', 'array2', 'data_compare_func', 'key_compare_func'));
        $tests[] = array('array_udiff_uassoc', array('foo'), array('array2', 'data_compare_func', 'key_compare_func'));
        $tests[] = array('array_udiff_uassoc', array('foo', 'bar'), array('data_compare_func', 'key_compare_func'));
        $tests[] = array('array_udiff_uassoc', array('foo', 'bar', 'baz'), array('key_compare_func'));
        $tests[] = array('array_udiff_uassoc', array('foo', 'bar', 'baz', 'boo'), array());
        $tests[] = array('array_uintersect_uassoc', array(), array('array1', 'array2', 'data_compare_func', 'key_compare_func'));
        $tests[] = array('array_uintersect_uassoc', array('foo'), array('array2', 'data_compare_func', 'key_compare_func'));
        $tests[] = array('array_uintersect_uassoc', array('foo', 'bar'), array('data_compare_func', 'key_compare_func'));
        $tests[] = array('array_uintersect_uassoc', array('foo', 'bar', 'baz'), array('key_compare_func'));
        $tests[] = array('array_uintersect_uassoc', array('foo', 'bar', 'baz', 'boo'), array());

        return $tests;
    }

    /**
     * @dataProvider getMismatchedArgumentTypesTests
     */
    public function testGetMismatchedArgumentTypes($functionName, array $rawTypes, array $expectedMismatchedTypes)
    {
        $function = new \Scrutinizer\PhpAnalyzer\Model\GlobalFunction($functionName);
        $types = array_map(array($this->registry, 'resolveType'), $rawTypes);
        $expectedMismatchedTypes = array_map(array($this->registry, 'resolveType'), $expectedMismatchedTypes);

        $mismatchedTypes = $this->argumentChecker->getMismatchedArgumentTypes($function, $types);
        $this->assertCount(count($expectedMismatchedTypes), $mismatchedTypes);
        foreach ($expectedMismatchedTypes as $i => $expectedType) {
            $this->assertArrayHasKey($i, $mismatchedTypes);
            $this->assertTrue($expectedType->equals($mismatchedTypes[$i]), sprintf('Failed to assert that expected type "%s" equals actual type "%s" for index %d (0-based).', $expectedType, $mismatchedTypes[$i], $i));
        }
    }

    public function getMismatchedArgumentTypesTests()
    {
        $tests = array();

        $tests[] = array('asasddfdfdf', array(), array());
        $tests[] = array('fgdfgdfdf', array('string'), array());

        $tests[] = array('array_udiff', array('array', 'callable'), array());
        $tests[] = array('array_udiff', array('string', 'array', 'callable'), array('array'));
        $tests[] = array('array_udiff', array('array', 'string', 'callable'), array(1 => 'array'));
        $tests[] = array('array_udiff', array('array', 'array', 'boolean'), array(2 => 'callable'));
        $tests[] = array('array_udiff', array('array', 'array', 'callable'), array());
        $tests[] = array('array_udiff', array('array', 'array', 'array', 'callable'), array());
        $tests[] = array('array_udiff', array('array', 'array', 'array', 'string', 'string'), array(3 => 'array'));

        $tests[] = array('array_udiff_assoc', array('array', 'callable'), array());
        $tests[] = array('array_udiff_assoc', array('string', 'array', 'callable'), array('array'));
        $tests[] = array('array_udiff_assoc', array('array', 'string', 'callable'), array(1 => 'array'));
        $tests[] = array('array_udiff_assoc', array('array', 'array', 'boolean'), array(2 => 'callable'));
        $tests[] = array('array_udiff_assoc', array('array', 'array', 'callable'), array());
        $tests[] = array('array_udiff_assoc', array('array', 'array', 'array', 'callable'), array());
        $tests[] = array('array_udiff_assoc', array('array', 'array', 'array', 'string', 'string'), array(3 => 'array'));

        $tests[] = array('array_uintersect_assoc', array('array', 'callable'), array());
        $tests[] = array('array_uintersect_assoc', array('string', 'array', 'callable'), array('array'));
        $tests[] = array('array_uintersect_assoc', array('array', 'string', 'callable'), array(1 => 'array'));
        $tests[] = array('array_uintersect_assoc', array('array', 'array', 'boolean'), array(2 => 'callable'));
        $tests[] = array('array_uintersect_assoc', array('array', 'array', 'callable'), array());
        $tests[] = array('array_uintersect_assoc', array('array', 'array', 'array', 'callable'), array());
        $tests[] = array('array_uintersect_assoc', array('array', 'array', 'array', 'string', 'string'), array(3 => 'array'));

        $tests[] = array('array_uintersect', array('array', 'callable'), array());
        $tests[] = array('array_uintersect', array('string', 'array', 'callable'), array('array'));
        $tests[] = array('array_uintersect', array('array', 'string', 'callable'), array(1 => 'array'));
        $tests[] = array('array_uintersect', array('array', 'array', 'boolean'), array(2 => 'callable'));
        $tests[] = array('array_uintersect', array('array', 'array', 'callable'), array());
        $tests[] = array('array_uintersect', array('array', 'array', 'array', 'callable'), array());
        $tests[] = array('array_uintersect', array('array', 'array', 'array', 'string', 'string'), array(3 => 'array'));

        $tests[] = array('array_udiff_uassoc', array('string', 'array', 'string'), array());
        $tests[] = array('array_udiff_uassoc', array('string', 'string', 'callable', 'string'), array('array', 'array'));
        $tests[] = array('array_udiff_uassoc', array('array', 'string', 'string', 'string'), array(1 => 'array'));
        $tests[] = array('array_udiff_uassoc', array('array', 'array', 'array', 'array', 'string', 'string'), array());
        $tests[] = array('array_udiff_uassoc', array('array', 'string', 'array', 'boolean', 'array'), array(1 => 'array', 3 => 'callable'));

        $tests[] = array('array_uintersect_uassoc', array('string', 'array', 'string'), array());
        $tests[] = array('array_uintersect_uassoc', array('string', 'string', 'callable', 'string'), array('array', 'array'));
        $tests[] = array('array_uintersect_uassoc', array('array', 'string', 'string', 'string'), array(1 => 'array'));
        $tests[] = array('array_uintersect_uassoc', array('array', 'array', 'array', 'array', 'string', 'string'), array());
        $tests[] = array('array_uintersect_uassoc', array('array', 'string', 'array', 'boolean', 'array'), array(1 => 'array', 3 => 'callable'));

        return $tests;
    }

    protected function setUp()
    {
        $this->registry = new \Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry();
        $this->typeChecker = new \Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeChecker($this->registry);
        $this->argumentChecker = new \Scrutinizer\PhpAnalyzer\ArgumentChecker\OverloadedCoreFunctionChecker($this->registry, $this->typeChecker);
    }

    private function resolveArguments(array $rawArgs)
    {
        $args = array();
        foreach ($rawArgs as $rawArg) {
            if (is_string($rawArg)) {
                $args[] = new \PHPParser_Node_Arg(new \PHPParser_Node_Scalar_String($rawArg));

                continue;
            }

            throw new \LogicException(sprintf('Could not resolve argument %s.', json_encode($rawArg)));
        }

        return $args;
    }
}