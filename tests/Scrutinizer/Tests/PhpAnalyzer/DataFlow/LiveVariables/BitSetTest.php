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

namespace Scrutinizer\Tests\PhpAnalyzer\DataFlow\LiveVariables;

use Scrutinizer\PhpAnalyzer\DataFlow\LiveVariableAnalysis\BitSet;

class BitSetTest extends \PHPUnit_Framework_TestCase
{
    private $set;

    public function testSet()
    {
        $this->assertBits('00000');
        $this->set->set(1);
        $this->assertBits('01000');
        $this->set->set(4);
        $this->assertBits('01001');
    }

    public function testPerformOr()
    {
        $set = new BitSet(5);
        $set->set(2);
        $this->assertBits('00100', $set);

        $this->set->set(1);
        $this->assertBits('01000');

        $this->set->performOr($set);
        $this->assertBits('01100');
        $this->assertBits('00100', $set);
    }

    public function testAndNot()
    {
        $set = new BitSet(5);
        $set->set(4);
        $this->assertBits('00001', $set);

        $this->set->set(4);
        $this->set->set(1);
        $this->assertBits('01001');

        $this->set->andNot($set);
        $this->assertBits('01000');
        $this->assertBits('00001', $set);
    }

    public function testEquals1()
    {
        $this->assertFalse($this->set->equals(new BitSet(1)));
    }

    public function testEquals2()
    {
        $set = new BitSet(5);
        $set->set(1);
        $this->set->set(1);

        $this->assertTrue($this->set->equals($set));
    }

    public function testEquals3()
    {
        $set = new BitSet(5);
        $set->set(2);

        $this->assertFalse($this->set->equals($set));
    }

    public function testCount()
    {
        $this->assertEquals(5, count($this->set));
    }

    private function assertBits($bits, BitSet $set = null)
    {
        $this->assertSame($bits, (string) ($set ?: $this->set));
    }

    protected function setUp()
    {
        $this->set = new BitSet(5);
    }
}