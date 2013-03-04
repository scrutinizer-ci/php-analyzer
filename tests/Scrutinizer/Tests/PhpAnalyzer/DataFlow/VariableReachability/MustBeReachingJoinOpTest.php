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

namespace Scrutinizer\Tests\PhpAnalyzer\DataFlow\VariableReachability;

use Scrutinizer\PhpAnalyzer\DataFlow\VariableReachability\Definition;
use Scrutinizer\PhpAnalyzer\DataFlow\VariableReachability\DefinitionLattice;

class MustBeReachingJoinOpTest extends \PHPUnit_Framework_TestCase
{
    private $op;
    private $scope;
    private $var;
    private $a;
    private $b;

    public function testNullInBoth()
    {
        $this->a[$this->var] = null;
        $this->b[$this->var] = null;
        $this->assertNull($this->computeJoin());
    }

    public function testJoinSameInAAndB()
    {
        $this->a[$this->var] =
        $this->b[$this->var] = new Definition($this->getMock('PHPParser_Node'));
        $this->assertSame($this->a[$this->var], $this->computeJoin());
    }

    public function testJoinExistsInBAndNullInA()
    {
        $this->a[$this->var] = null;
        $this->b[$this->var] = new Definition($this->getMock('PHPParser_Node'));
        $this->assertNull($this->computeJoin());
    }

    public function testJoinExistsOnlyInB()
    {
        $this->b[$this->var] = new Definition($this->getMock('PHPParser_Node'));
        $this->assertSame($this->b[$this->var], $this->computeJoin());
    }

    public function testJoinExistsInAAndNullInB()
    {
        $this->a[$this->var] = new Definition($this->getMock('PHPParser_Node'));
        $this->b[$this->var] = null;

        $this->assertNull($this->computeJoin());
    }

    public function testJoinExistsOnlyInA()
    {
        $this->a[$this->var] = new Definition($this->getMock('PHPParser_Node'));

        $this->assertSame($this->a[$this->var], $this->computeJoin());
    }

    public function testJoinDiffDefinitions()
    {
        $this->a[$this->var] = new Definition($this->getMock('PHPParser_Node'));
        $this->b[$this->var] = new Definition($this->getMock('PHPParser_Node'));

        $this->assertNull($this->computeJoin());
    }

    private function computeJoin()
    {
        return call_user_func($this->op, array($this->a, $this->b))[$this->var];
    }

    protected function setUp()
    {
        $this->op = \Scrutinizer\PhpAnalyzer\DataFlow\VariableReachability\MustBeReachingDefAnalysis::createJoinOperation();
        $this->scope = new \Scrutinizer\PhpAnalyzer\PhpParser\Scope\Scope($this->getMock('PHPParser_Node'));
        $this->var = new \Scrutinizer\PhpAnalyzer\PhpParser\Scope\Variable('x', null, $this->scope);
        $this->a = new DefinitionLattice();
        $this->b = new DefinitionLattice();
    }
}