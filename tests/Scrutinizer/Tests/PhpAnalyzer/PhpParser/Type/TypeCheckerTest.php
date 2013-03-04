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

use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeChecker;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;

class TypeCheckerTest extends \PHPUnit_Framework_TestCase
{
    private $typeRegistry;
    private $checker;

    /**
     * @dataProvider getMayBePassedTests
     */
    public function testMayBePassed($expectedType, $actualType, $expectedOutcome, $level = 'lenient')
    {
        $this->checker->setLevel($level);

        $expectedType = $this->typeRegistry->resolveType($expectedType);
        $actualType = $this->typeRegistry->resolveType($actualType);

        $this->assertSame($expectedOutcome, $this->checker->mayBePassed($expectedType, $actualType));
    }

    public function getMayBePassedTests()
    {
        $tests = array();

        // Strict Tests
        $tests[] = array('integer', 'integer', true, 'strict');
        $tests[] = array('integer', 'double', false, 'strict');
        $tests[] = array('integer', 'string', false, 'strict');

        // TODO: This should be removed once we have support for parameterized types, and anonymous classes.
        $tests[] = array('object<Foo>', 'object<PHPUnit_Framework_MockObject_MockObject>', true, 'strict');

        // Lenient Tests
        $tests[] = array('integer', 'integer', true);
        $tests[] = array('integer', 'double', true);
        $tests[] = array('integer', 'string', true);
        $tests[] = array('integer', 'double|null', true);
        $tests[] = array('integer', 'string|null', true);
        $tests[] = array('integer', 'array', false);
        $tests[] = array('integer', 'double|array', false);
        $tests[] = array('double|integer', 'integer', true);
        $tests[] = array('double|integer', 'string', true);
        $tests[] = array('integer|string', 'string', true);
        $tests[] = array('string', 'integer', true);
        $tests[] = array('string', 'double', true);
        $tests[] = array('double', 'string', true);
        $tests[] = array('double', 'integer', true);

        // Allow the generic array to be passed in place of more specific arrays in lenient mode.
        $tests[] = array('array<integer>', 'array', true);
        $tests[] = array('array<string,integer>', 'array', true);
        $tests[] = array('array<integer>', 'array<string>', true);
        $tests[] = array('array<integer>', 'integer', false);
        $tests[] = array('array<object<Foo>>', 'array<string>', false);
        $tests[] = array('array', 'array<string>', true);
        $tests[] = array('array', 'array<object<Foo>>', true);

        return $tests;
    }

    protected function setUp()
    {
        $this->typeRegistry = new TypeRegistry();
        $this->checker = new TypeChecker($this->typeRegistry);
    }
}