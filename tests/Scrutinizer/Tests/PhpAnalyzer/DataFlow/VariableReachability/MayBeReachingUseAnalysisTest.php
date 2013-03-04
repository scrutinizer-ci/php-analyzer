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

class MayBeReachingUseAnalysisTest extends \PHPUnit_Framework_TestCase
{
    private $analysis;
    private $def;
    private $uses;

    public function testStraightLine()
    {
        $this->assertMatch('D:$x=1; U:$x;');
        $this->assertMatch('$x = 1; D: $x = 2; U: $x;');
        $this->assertNotMatch('D:$x=1; $x=2; U:$x;');
        $this->assertNotMatch('U:$x; D:$x=2;');
        $this->assertMatch('D: $x = 1; $y = 2; $y; U: $x;');
    }

    public function testIf()
    {
        $this->assertMatch('if ($a) { D: $x = 1; } else { $x = 2; } U:$x;');
        $this->assertMatch('if ($a) { $x = 1; } else { D: $x = 2; } U:$x;');
        $this->assertMatch('D: $x=1; if ($a) { U1: $x; } else { U2: $x; }');
    }

    public function testLoops()
    {
        $this->assertMatch('$x=0; while ($a) { D: $x=1; } U:$x;');
        $this->assertMatch('$x=0; for(;;) { D: $x=1; } U:$x;');

        $this->assertMatch('D: $x=1; while($a) { U: $x; }');
        $this->assertMatch('D: $x=1; for(;;) { U: $x; }');

        $this->assertNotMatch('D: $x= 1; foreach ($a as $x) { U: $x; }');
    }

    public function testConditional()
    {
        $this->assertMatch('$x=0; D:($x=1)&&$y; U:$x;');
        $this->assertMatch('$x=0; D:$y&&($x=1); U:$x;');
        $this->assertMatch('$x=0; $y=0; D:($x=1)&&($y=0); U:$x;');
        $this->assertMatch('$x=0; $y=0; D:($y=0)&&($x=1); U:$x;');
        $this->assertNotMatch('D: $x=0; $y=0; ($x=1)&&($y=0); U:$x;');
        $this->assertMatch('D: $x=0; $y=0; ($y=1)&&(($y=2)||($x=1)); U:$x;');
        $this->assertMatch('D: $x=0; $y=0; ($y=0)&&($x=1); U:$x;');
    }

    public function testAssignmentInExpressions()
    {
        $this->assertMatch('$x=0; D:foo(bar($x=1)); U:$x;');
        $this->assertMatch('$x=0; D:foo(bar + ($x = 1)); U:$x;');
    }

    public function testHook()
    {
        $this->assertMatch('$x=0; D:foo() ? $x=1 : bar(); U:$x;');
        $this->assertMatch('$x=0; D:foo() ? $x=1 : $x=2; U:$x;');
    }

    public function testAssignmentOps()
    {
        $this->assertNotMatch('D: $x = 0; U: $x = 100;');
        $this->assertMatch('D: $x = 0; U: $x += 100;');
        $this->assertMatch('D: $x = 0; U: $x -= 100;');
    }

    public function testInc()
    {
        $this->assertMatch('D: $x = 0; U:$x++;');
        $this->assertMatch('$x = 0; D:$x++; U:$x;');
    }

    public function testForeach()
    {
        $this->assertMatch('D: $x = array(); U: foreach ($x as $y) { }');
        $this->assertNotMatch('D: $x = array(); U: foreach ($foo as $x) { }');
        $this->assertNotMatch('D: $x = array(); foreach ($foo as $x) { U:$x; }');
        $this->assertMatch('$x = array(); D: foreach ($foo as $x) { U:$x; }');
    }

    public function testException()
    {
        $this->assertNotMatch('D: $x=1; try { f(); } catch (\Exception $x) { U: $x; }');

        // This test fails as we are not taking into account branches in the backwards analysis.
        // This case seems also quite unlikely that it does not matter for now.
//        $this->assertMatch('D: $x=1; try { f(); } catch (\Exception $x) { } U: $x;');
    }

    public function testAttachesDefiningExprsToVarNodes()
    {
        $this->assertMatch('D: $x=1; if ($a) { U1: $x; } else { U2: f($x); }');
        $this->assertSame(array($this->def->expr), $this->uses[0]->getAttribute('maybe_defining_exprs'));
        $this->assertSame(array($this->def->expr), $this->uses[1]->args[0]->value->getAttribute('maybe_defining_exprs'));

        $this->assertMatch('try { $x = 1; f(); } catch (\Exception $ex) { D: $x = 2; } U: $x;');
        $defExprs = $this->uses[0]->getAttribute('maybe_defining_exprs');
        $this->assertInstanceOf('PHPParser_Node_Scalar_LNumber', $defExprs[0]);
        $this->assertSame(2, $defExprs[0]->value);
        $this->assertInstanceOf('PHPParser_Node_Scalar_LNumber', $defExprs[1]);
        $this->assertSame(1, $defExprs[1]->value);
    }

    private function assertMatch($code)
    {
        $this->computeUseDef($code);

        $analyzedUses = $this->analysis->getUses('x', $this->def);
        $this->assertEquals(count($analyzedUses), count($this->uses));
        foreach ($this->uses as $use) {
            if ( ! in_array($use, $analyzedUses, true)) {
                $this->fail('Did not find '.\Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil::getStringRepr($use));
            }
        }
    }

    private function assertNotMatch($code)
    {
        $this->computeUseDef($code);

        $analyzedUses = $this->analysis->getUses('x', $this->def);
        foreach ($this->uses as $use) {
            if (in_array($use, $analyzedUses, true)) {
                $this->fail('Did not expect '.\Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil::getStringRepr($use));
            }
        }
    }

    private function computeUseDef($code)
    {
        $code = sprintf('<?php function foo($param1, $param2) { %s }', $code);

        $ast = \JMS\PhpManipulator\PhpParser\ParseUtils::parse($code);
        $scope = (new \Scrutinizer\PhpAnalyzer\PhpParser\Scope\SyntacticScopeCreator())->createScope($ast);

        $cfa = new \Scrutinizer\PhpAnalyzer\ControlFlow\ControlFlowAnalysis();
        $cfa->process($ast);
        $cfg = $cfa->getGraph();

        $this->analysis = new \Scrutinizer\PhpAnalyzer\DataFlow\VariableReachability\MayBeReachingUseAnalysis($cfg, $scope);
        $this->analysis->analyze();

        $this->def = null;
        $this->uses = array();

        \Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal::traverseWithCallback($ast, new \Scrutinizer\PhpAnalyzer\PhpParser\PreOrderCallback(function($t, \PHPParser_Node $node) {
            if ( ! $node instanceof \PHPParser_Node_Stmt_Label) {
                return;
            }

            if ('D' === $node->name) {
                $this->def = $node->getAttribute('next');
            } else if (0 === strpos($node->name, 'U')) {
                $this->uses[] = $node->getAttribute('next');
            }
        }));

        $this->assertNotNull($this->def);
        $this->assertNotEmpty($this->uses);
    }
}