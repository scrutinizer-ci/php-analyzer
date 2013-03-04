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

namespace Scrutinizer\Tests\PhpAnalyzer\DataFlow\TypeInference\FunctionInterpreter;

use Scrutinizer\PhpAnalyzer\PhpParser\Type\PhpType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\FunctionInterpreter\CoreFunctionInterpreter;
use Scrutinizer\PhpAnalyzer\Model\GlobalFunction;

class CoreFunctionInterpreterTest extends \PHPUnit_Framework_TestCase
{
    /** @var TypeRegistry */
    private $registry;

    /** @var CoreFunctionInterpreter */
    private $interpreter;

    public function testReturnsNullForUnknownFunction()
    {
        $this->assertNull($this->interpreter->getPreciserFunctionReturnTypeKnowingArguments(
                new GlobalFunction('fofoofofofodkfjkdfkdjfkd'), array(), array()));
    }

    public function testUnserialize()
    {
        $this->assertTypeEquals('unknown', $this->interpreter->getPreciserFunctionReturnTypeKnowingArguments(
            new GlobalFunction('unserialize'), array(), array()));
    }

    /**
     * @dataProvider getMinMaxTests
     */
    public function testMinMax(array $passedTypes, $expectedType)
    {
        $resolvedTypes = array_map(array($this->registry, 'resolveType'), $passedTypes);

        $max = new GlobalFunction('max');
        $restrictedType = $this->interpreter->getPreciserFunctionReturnTypeKnowingArguments($max, array(), $resolvedTypes);
        $this->assertTypeEquals($expectedType, $restrictedType);

        $min = new GlobalFunction('min');
        $restrictedType = $this->interpreter->getPreciserFunctionReturnTypeKnowingArguments($min, array(), $resolvedTypes);
        $this->assertTypeEquals($expectedType, $restrictedType);
    }

    public function getMinMaxTests()
    {
        $tests = array();

        // If a single array is passed, the highest/lowest value in that array is returned.
        $tests[] = array(array('array'), 'unknown');
        $tests[] = array(array('array<integer>'), 'integer');
        $tests[] = array(array('array<object<Foo>>'), 'object<Foo>');
        $tests[] = array(array('string'), null);

        // If multiple values are passed, one of these values will be returned. So,
        // we need to built a union over these types.
        $tests[] = array(array('integer', 'string'), 'integer|string');
        $tests[] = array(array('array', 'string'), 'array|string'); // Technically, this will always return an array.
                                                                    // As an array is always greater than a string. We should
                                                                    // cover this at some point.
        $tests[] = array(array('boolean', 'integer'), 'boolean|integer');

        return $tests;
    }

    public function testVersionCompare()
    {
        $f = new GlobalFunction('version_compare');

        $restrictedType = $this->interpreter->getPreciserFunctionReturnTypeKnowingArguments($f, array(), array());
        $this->assertTypeEquals('integer|boolean', $restrictedType);

        $restrictedType = $this->interpreter->getPreciserFunctionReturnTypeKnowingArguments($f, array(), array(null));
        $this->assertTypeEquals('integer|boolean', $restrictedType);

        $restrictedType = $this->interpreter->getPreciserFunctionReturnTypeKnowingArguments($f, array(), array(null, null));
        $this->assertTypeEquals('integer', $restrictedType);

        $restrictedType = $this->interpreter->getPreciserFunctionReturnTypeKnowingArguments($f, array(), array(null, null, null));
        $this->assertTypeEquals('boolean', $restrictedType);

        $restrictedType = $this->interpreter->getPreciserFunctionReturnTypeKnowingArguments($f, array(), array(null, null, null, null));
        $this->assertTypeEquals('integer|boolean', $restrictedType);
    }

    public function testVarExport()
    {
        $f = new GlobalFunction('var_export');
        $restrictedType = $this->interpreter->getPreciserFunctionReturnTypeKnowingArguments(
            $f,
            array(null, new \PHPParser_Node_Expr_ConstFetch(new \PHPParser_Node_Name(array('true')))),
            array('all', 'boolean')
        );
        $this->assertTypeEquals('string', $restrictedType);

        $this->assertTypeEquals(null, $this->interpreter->getPreciserFunctionReturnTypeKnowingArguments($f, array(), array()));
    }

    /**
     * @dataProvider getPregMatchAliases
     */
    public function testPregMatchAndAliases($name)
    {
        $function = new GlobalFunction($name);

        $restrictedType = $this->interpreter->getPreciserFunctionReturnTypeKnowingArguments($function,
                array(), array(null, null, $this->registry->getNativeType('string')));
        $this->assertNotNull($restrictedType);
        $this->assertTrue($this->registry->getNativeType('string')->equals($restrictedType));

        $restrictedType = $this->interpreter->getPreciserFunctionReturnTypeKnowingArguments($function,
                array(), array(null, null, $this->registry->getNativeType('all')));
        $this->assertNull($restrictedType);

        $restrictedType = $this->interpreter->getPreciserFunctionReturnTypeKnowingArguments($function,
                array(), array(null, null, $this->registry->resolveType('string|integer')));
        $this->assertNotNull($restrictedType);
        $this->assertTrue($this->registry->getNativeType('string')->equals($restrictedType));

        $restrictedType = $this->interpreter->getPreciserFunctionReturnTypeKnowingArguments($function,
                array(), array(null, null, $this->registry->getNativeType('array')));
        $this->assertNotNull($restrictedType);
        $this->assertTrue($this->registry->resolveType('array<string>')->equals($restrictedType));

        $restrictedType = $this->interpreter->getPreciserFunctionReturnTypeKnowingArguments($function,
                array(), array(null, null, $this->registry->getNativeType('unknown')));
        $this->assertNotNull($restrictedType);
        $this->assertTrue($this->registry->resolveType('unknown')->equals($restrictedType));
    }

    public function getPregMatchAliases()
    {
        return array(
            array('preg_filter'),
            array('preg_replace'),
            array('preg_replace_callback'),
            array('str_replace'),
        );
    }

    protected function setUp()
    {
        $this->registry = new TypeRegistry();
        $this->interpreter = new CoreFunctionInterpreter($this->registry);
    }

    private function assertTypeEquals($expectedType, PhpType $restrictedType = null)
    {
        if (null === $expectedType) {
            $this->assertNull($restrictedType);
        } else {
            $resolvedExpectedType = $this->registry->resolveType($expectedType);

            $this->assertNotNull($restrictedType);
            $this->assertTrue($resolvedExpectedType->equals($restrictedType), sprintf('Failed to assert that expected type "%s" equals the actual type "%s".', $resolvedExpectedType, $restrictedType));
        }
    }
}