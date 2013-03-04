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

use Scrutinizer\PhpAnalyzer\DataFlow\LiveVariableAnalysis\LiveVariableLattice;

class LiveVariableLatticeTest extends \PHPUnit_Framework_TestCase
{
    private $lattice;

    public function testEquals()
    {
        $lattice = new LiveVariableLattice(5);

        $mock1 = $this->getMock('Scrutinizer\PhpAnalyzer\DataFlow\LiveVariableAnalysis\BitSet');
        $mock2 = $this->getMock('Scrutinizer\PhpAnalyzer\DataFlow\LiveVariableAnalysis\BitSet');

        $mock1->expects($this->once())
            ->method('equals')
            ->with($mock2)
            ->will($this->returnValue(true));

        $this->lattice->liveSet = $mock1;
        $lattice->liveSet = $mock2;

        $this->assertTrue($this->lattice->equals($lattice));
    }

    public function testEquals2()
    {
        $lattice = $this->getMock('Scrutinizer\PhpAnalyzer\DataFlow\LatticeElementInterface');

        $this->assertFalse($this->lattice->equals($lattice));
    }

    public function testClone()
    {
        $lattice = clone $this->lattice;
        $this->assertNotSame($this->lattice->liveSet, $lattice->liveSet);

        $lattice->liveSet->set(1);
        $this->assertFalse($this->lattice->liveSet->equals($lattice->liveSet));
    }

    protected function setUp()
    {
        $this->lattice = new LiveVariableLattice(5);
    }
}