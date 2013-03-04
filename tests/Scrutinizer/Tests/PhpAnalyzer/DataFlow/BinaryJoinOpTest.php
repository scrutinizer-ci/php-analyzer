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

namespace Scrutinizer\Tests\PhpAnalyzer\DataFlow;

use Scrutinizer\PhpAnalyzer\DataFlow\BinaryJoinOp;

class BinaryJoinOpTest extends \PHPUnit_Framework_TestCase
{
    public function testApply()
    {
        $a = $this->createLattice();
        $b = $this->createLattice();
        $c = $this->createLattice();
        $bc = $this->createLattice();
        $abc = $this->createLattice();

        $callable = function($paramA, $paramB) use ($a, $b, $c, $bc, $abc) {
            static $count = -1;
            $count += 1;

            switch ($count) {
                case 0:
                    $this->assertSame($b, $paramA);
                    $this->assertSame($c, $paramB);

                    return $bc;

                case 1:
                    $this->assertSame($a, $paramA);
                    $this->assertSame($bc, $paramB);

                    return $abc;

                default:
                    $this->fail('Unexpected third call to join operation.');
            }
        };
        $callable->bindTo($this);
        $op = new BinaryJoinOp($callable);

        $this->assertSame($abc, call_user_func($op, array($a, $b, $c)));
    }

    public function testApplyWithOneLattice()
    {
        $a = $this->createLattice();
        $callable = function() {
            $this->fail('Callable was not expected to be called.');
        };
        $callable->bindTo($this);

        $this->assertSame($a, call_user_func(new BinaryJoinOp($callable), array($a)));
    }

    private function createLattice()
    {
        return $this->getMock('Scrutinizer\PhpAnalyzer\DataFlow\LatticeElementInterface');
    }
}