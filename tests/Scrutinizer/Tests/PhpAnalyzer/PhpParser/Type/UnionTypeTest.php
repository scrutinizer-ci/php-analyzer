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

use Scrutinizer\PhpAnalyzer\PhpParser\Type\PhpType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;

class UnionTypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider getEqualsTests
     */
    public function testEquals(PhpType $a, PhpType $b, $expectedOutcome)
    {
        $this->assertSame($expectedOutcome, $a->equals($b));
    }

    public function getEqualsTests()
    {
        $registry = new TypeRegistry();

        $tests = array();
        $tests[] = array(
            $registry->createUnionType(array('integer', 'string')),
            $registry->createUnionType(array('string', 'integer')),
            true
        );
        $tests[] = array(
            $registry->createUnionType(array('number', 'string')),
            $registry->createUnionType(array('string', 'number')),
            true
        );

        return $tests;
    }
}