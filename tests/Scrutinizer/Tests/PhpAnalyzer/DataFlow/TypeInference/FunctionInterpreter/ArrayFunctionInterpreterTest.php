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

use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\FunctionInterpreter\ArrayFunctionInterpreter;
use Scrutinizer\PhpAnalyzer\Model\GlobalFunction;

class ArrayFunctionInterpreterTest extends \PHPUnit_Framework_TestCase
{
    private $registry;
    private $interpreter;

    /**
     * @dataProvider getArrayPopTests
     */
    public function testArrayPop($inputType, $expectedType)
    {
        $this->assertReturn($this->createFunction('array_pop'),
                            array($inputType),
                            $expectedType);
    }

    public function getArrayPopTests()
    {
        $tests = array();

        $tests[] = array('array', 'unknown');
        $tests[] = array('array<integer>', 'integer|null');
        $tests[] = array('array<integer|string>', 'integer|string|null');

        return $tests;
    }

    /**
     * @dataProvider getArrayChangeKeyCaseTests
     */
    public function testArrayChangeKeyCase($inputType, $expectedType)
    {
        $this->assertReturn($this->createFunction('array_change_key_case'),
                            array($inputType),
                            $expectedType);
    }

    public function getArrayChangeKeyCaseTests()
    {
        $tests = array();

        $tests[] = array('array', 'array');
        $tests[] = array('array<integer>', 'array<integer>');
        $tests[] = array('array<integer,string>', 'array<integer,string>');
        $tests[] = array('array|integer', 'array');

        return $tests;
    }

    /**
     * @dataProvider getArrayKeysTests
     */
    public function testArrayKeys($inputType, $expectedType)
    {
        $this->assertReturn($this->createFunction('array_keys'),
                            array($inputType),
                            $expectedType);
    }

    public function getArrayKeysTests()
    {
        $tests = array();

        $tests[] = array('array', 'array<integer,integer|string>');
        $tests[] = array('array<string>', 'array<integer>');
        $tests[] = array('array<string,object<Foo>>', 'array<integer,string>');
        $tests[] = array('array<integer,string>|array<integer,object<Foo>>', 'array<integer,integer>');
        $tests[] = array('array|string', 'array<integer,integer|string>');

        return $tests;
    }

    /**
     * @dataProvider getArrayValuesTests
     */
    public function testArrayValues($inputType, $expectedType)
    {
        $this->assertReturn($this->createFunction('array_values'),
                            array($inputType),
                            $expectedType);
    }

    public function getArrayValuesTests()
    {
        $tests = array();

        $tests[] = array('array', 'array<integer,unknown>');
        $tests[] = array('array<string>', 'array<integer,string>');
        $tests[] = array('array<integer,object<Foo>>', 'array<integer,object<Foo>>');
        $tests[] = array('array<string>|array<object<Foo>>', 'array<integer,string|object<Foo>>');
        $tests[] = array('array|integer|string', 'array<integer,unknown>');

        return $tests;
    }

    /**
     * @dataProvider getArrayUniqueTests
     */
    public function testArrayUnique($inputType, $expectedType)
    {
        $this->assertReturn($this->createFunction('array_unique'),
                            array($inputType),
                            $expectedType);
    }

    public function getArrayUniqueTests()
    {
        $tests = array();

        $tests[] = array('array', 'array');
        $tests[] = array('array<string>', 'array<string>');
        $tests[] = array('array|string', 'array');
        $tests[] = array('array<string>|array<boolean>', 'array<string>|array<boolean>');

        return $tests;
    }

    /**
     * @dataProvider getCurrentAndSimilarTests
     */
    public function testCurrentAndSimilar($inputType, $expectedType)
    {
        $this->assertReturn('current', array($inputType), $expectedType);
        $this->assertreturn('pos', array($inputType), $expectedType);
        $this->assertreturn('next', array($inputType), $expectedType);
        $this->assertreturn('prev', array($inputType), $expectedType);
        $this->assertreturn('reset', array($inputType), $expectedType);
        $this->assertreturn('end', array($inputType), $expectedType);
    }

    public function getCurrentAndSimilarTests()
    {
        $tests = array();

        $tests[] = array('array', 'unknown');
        $tests[] = array('array<string>', 'string|false');
        $tests[] = array('array<integer|string>', 'string|integer|false');
        $tests[] = array('array<string>|array<double>', 'string|double|false');
        $tests[] = array('string', null);
        $tests[] = array('string|integer', null);

        return $tests;
    }

    /**
     * @dataProvider getArrayMergeReplaceTests
     */
    public function testArrayMergeReplace($inputTypes, $expectedType)
    {
        $this->assertReturn('array_merge', $inputTypes, $expectedType);
        $this->assertReturn('array_replace', $inputTypes, $expectedType);
    }

    public function getArrayMergeReplaceTests()
    {
        $tests = array();

        $tests[] = array(array(), 'null');
        $tests[] = array(array('array'), 'array');
        $tests[] = array(array('array<string>', 'array<double>'), 'array<string|double>');
        $tests[] = array(array('array<string>|array<object<Foo>>', 'array<object<Bar>>'), 'array<string|object<Foo>|object<Bar>>');

        return $tests;
    }

    /**
     * @dataProvider getArrayPadTests
     */
    public function testArrayPad($inputType, $paddingType, $expectedType)
    {
        $this->assertReturn('array_pad', array($inputType, 'none', $paddingType), $expectedType);
    }

    public function getArrayPadTests()
    {
        $tests = array();

        $tests[] = array('array', 'double', 'array');
        $tests[] = array('array<string>', 'string', 'array<string>');
        $tests[] = array('array<string,string>', 'string', 'array<integer|string,string>');
        $tests[] = array('array<string>|array<string,integer>', 'string', 'array<string|integer,string|integer>');

        return $tests;
    }

    /**
     * @dataProvider getArraySearchTests
     */
    public function testArraySearch($inputType, $expectedType)
    {
        $this->assertReturn('array_search', array('null', $inputType), $expectedType);
    }

    public function getArraySearchTests()
    {
        $tests = array();

        $tests[] = array('array', 'string|integer|false');
        $tests[] = array('array<string>', 'integer|false');
        $tests[] = array('array<string,string>|array<string,boolean>', 'string|false');

        return $tests;
    }

    public function testUnknownFunction()
    {
        $this->assertNull($this->interpreter->getPreciserFunctionReturnTypeKnowingArguments(
                $this->createFunction('foo'), array(), array()));
    }

    private function createFunction($name, $returnType = 'all')
    {
        $func = new GlobalFunction($name);
        $func->setReturnType($this->registry->resolveType($returnType));

        return $func;
    }

    private function assertReturn($function, array $inputTypes, $expectedType)
    {
        if (is_string($function)) {
            $function = $this->createFunction($function);
        }

        foreach ($inputTypes as &$type) {
            $type = $this->registry->resolveType($type);
        }

        $restrictedType = $this->interpreter->getPreciserFunctionReturnTypeKnowingArguments($function, array(), $inputTypes);

        if (null === $expectedType) {
            $this->assertNull($restrictedType);
        } else {
            $expectedType = $this->registry->resolveType($expectedType);

            $this->assertNotNull($restrictedType);
            $this->assertTrue($expectedType->equals($restrictedType), 'Failed to assert that "'.$restrictedType.'" equals expected type "'.$expectedType."'.");
        }
    }

    protected function setUp()
    {
        $this->registry = new TypeRegistry();
        $this->interpreter = new ArrayFunctionInterpreter($this->registry);
    }
}