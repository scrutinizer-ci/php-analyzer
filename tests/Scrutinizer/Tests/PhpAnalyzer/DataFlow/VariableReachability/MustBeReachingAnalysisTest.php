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

use Scrutinizer\PhpAnalyzer\ControlFlow\ControlFlowAnalysis;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil;
use JMS\PhpManipulator\PhpParser\ParseUtils;
use Scrutinizer\PhpAnalyzer\PhpParser\PreOrderCallback;
use Scrutinizer\PhpAnalyzer\PhpParser\Scope\SyntacticScopeCreator;
use Scrutinizer\PhpAnalyzer\DataFlow\VariableReachability\MustBeReachingDefAnalysis;

class MustBeReachingAnalysisTest extends \PHPUnit_Framework_TestCase
{
    /** @var MustBeReachingDefAnalysis */
    private $analysis;

    /** @var \PHPParser_Node */
    private $def;

    /** @var \PHPParser_Node */
    private $use;

    public function testStraightLine()
    {
        $this->assertMatch('D:$x=1; U:$x;');
        $this->assertMatch('$x=0; D:$x=1; U:$x;');
        $this->assertNotMatch('D:$x=1; $x=2; U:$x;');
        $this->assertMatch('$x=1; D:$x=2; U:$x;');
        $this->assertNotMatch('U:$x; D:$x=1;');
        $this->assertMatch('D:$x=1; $y=2; $y; U:$x;');
    }

    public function testIf()
    {
        $this->assertNotMatch('if($a) { D:$x=1; } else { $x=2; }; U:$x;');
        $this->assertNotMatch('if($a) { $x=1; } else { D:$x=2; }; U:$x;');
        $this->assertMatch('D:$x=1; if($a) { U:$x; } else { $x; };');
        $this->assertMatch('D:$x=1; if($a) { $x; } else { U:$x; };');
        $this->assertNotMatch('if($a) { D: $x=1; }; U:$x;');
    }

    /**
     * @group loops
     */
    public function testLoops()
    {
        $this->assertNotMatch('$x=0; while($a) {
                                    D:$x=1; }; U:$x;');
        $this->assertNotMatch('$x=0; for(;;) {
                                    D:$x=1; }; U:$x;');
        $this->assertMatch('D:$x=1; while($a) { U:$x; }');
        $this->assertMatch('D:$x=1; for(;;)  { U:$x; }');
    }

    public function testConditional()
    {
        $this->assertMatch('$x=0; D:($x=1)&&$y; U:$x;');
        $this->assertNotMatch('$x=0; D:$y&&($x=1); U:$x;');
    }

    public function testAssignInExpr()
    {
        $this->assertMatch('$x=0; D:foo(bar($x=1)); U:$x;');
        $this->assertMatch('$x=0; D:foo($bar + ($x = 1)); U:$x;');
    }

    public function testTernary()
    {
        $this->assertNotMatch('$x=0; D: $foo ? ($x = 1) : bar(); U:$x;');
        $this->assertNotMatch('$x=0; D: $foo ? ($x = 1) : ($x = 2); U:$x;');
    }

    public function testExprVariableReAssignment()
    {
        $this->assertMatch('$a = $b = 1; D: $x = $a + $b; U:$x;');
        $this->assertNotMatch('$a = $b = 1; D: $x = $a + $b; $a = 1; U:$x;');
        $this->assertNotMatch('$a = $b = 1; D: $x = $a + $b; f($b = 1); U:$x;');
        $this->assertMatch('$a = $b = 1; D: $x = $a + $b; $c = 1; U:$x;');

        // Even if the sub-expression is change conditionally
        $this->assertNotMatch('$a = $b = 1; D: $x = $a + $b; $c ? ($a = 1) : 0; U:$x;');
    }

    public function testMergeDefinitions()
    {
        $this->assertNotMatch('D:$y=$x+$x; if ($x) { $x = 1; }; U:$y;');
    }

    public function testMergesWithOneDefinition()
    {
        $this->assertNotMatch('while ($y) { if ($y) { echo $x; } else { D: $x = 1; } } U: $x;');
    }

    public function testRedefinitionUsingItself()
    {
        $this->assertMatch('$x=1; D: $x=$x+1; U:$x;');
        $this->assertNotMatch('$x=1; D: $x = $x+1; $x=1; U:$x;');
    }

    public function testMultipleDefinitionsWithDependence()
    {
        $this->assertMatch('$a = $b = 1; $x = $a;
                                      D: $x = $b; U: $x;');
        $this->assertMatch('$a = $b = 1; $x = $a;
                                      D: $x = $b; $a = 1; U: $x;');
        $this->assertNotMatch('$a = $b = 1; $x = $a;
                                         D: $x = $b; $b = 1; U: $x;');
    }

    public function testAssignmentOp()
    {
        $this->assertMatch('$x = 0; D: $x += 1; U: $x;');
        $this->assertMatch('$x = 0; D: $x *= 1; U: $x;');
        $this->assertNotMatch('D: $x = 0; $x += 1; U: $x;');
    }

    public function testIncAndDec()
    {
        $this->assertMatch('$x = 0; D:$x++; U:$x;');
        $this->assertMatch('$x = 0; D:$x--; U:$x;');
    }

    public function testFunctionParams1()
    {
        $this->computeDefUse('if ($param2) { D: $param1 = 1; U: $param1; }');
        $this->assertSame($this->def, $this->analysis->getDefiningNode('param1', $this->use));
    }

    public function testFunctionParams2()
    {
        $this->computeDefUse('if ($param2) { D: $param1 = 1; } U: $param1;');
        $this->assertNotSame($this->def, $this->analysis->getDefiningNode('param1', $this->use));
    }

    public function testException()
    {
        $this->assertMatch('try { f(); D: $x = 1; U: $x; } catch (\Exception $ex) { }');
        $this->assertMatch('try { D: $x = 1; f(); U: $x; } catch (\Exception $ex) { }');
        $this->assertMatch('D: $x = 1; try { f(); } catch (\Exception $ex) { } U: $x;');
        $this->assertNotMatch('D: $x = 1; try { f(); } catch (\Exception $ex) { $x = 2; } U: $x;');
        $this->assertNotMatch('$x = 1; try { f(); } catch (\Exception $ex) { D: $x = 2; } U: $x;');
        $this->assertMatch('try { D: $x = 0; f(); } catch (\Exception $ex) { } U: $x;');
        $this->assertMatch('try { D: $x = 0; f(); } catch (\Exception $ex) { U: $x; }');
        $this->assertNotMatch('try { D: $x = 0; f(); } catch (\Exception $x) { } U: $x;');
        $this->assertNotMatch('try { D: $x = 0; f(); } catch (\Exception $x) { U: $x; }');
        $this->assertMatch('try { $x = 0; f(); } catch (\Exception $x) { DP_Stmt_Catch: U: $x; }');
    }

    public function testListAssignment()
    {
        $this->assertMatch('$x = 1; D: list($x, $y) = array(1,2); U: $x;');
        $this->assertMatch('list($x) = array(1); D: $x = 1; U: $x;');
    }

    public function testAssignByRef()
    {
        $this->assertMatch('$x = $y = 1; D: $x &= $y; U: $x;');
    }

    public function testAnalysisAttachesDefiningExpr()
    {
        $this->assertMatch('$x=1; D: $x=2; U: f($x);');

        $this->assertInstanceOf('PHPParser_Node_Expr_FuncCall', $this->use);

        $defExpr = $this->use->args[0]->value->getAttribute('defining_expr');
        $this->assertNotNull($defExpr);
        $this->assertSame($this->def->expr, $defExpr);
    }

    public function testDefiningExprIsNotAttachedToAssigns()
    {
        $this->assertMatch('$x = 0; D:$x=1; U: $x;');
        $this->assertNull($this->def->var->getAttribute('defining_expr'));
    }

    private function assertMatch($code)
    {
        $this->computeDefUse($code);
        $this->assertSame($this->def, $foundDef = $this->analysis->getDefiningNode('x', $this->use),
            'Expected: '.\Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil::getStringRepr($this->def)
                .' - Found: '.\Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil::getStringRepr($foundDef));
    }

    private function assertNotMatch($code)
    {
        $this->computeDefUse($code);
        $this->assertNotSame($this->def, $this->analysis->getDefiningNode('x', $this->use),
            'Found definition was not expected: '.\Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil::getStringRepr($this->def));
    }

    private function computeDefUse($code)
    {
        $code = sprintf('<?php function foo($param1, $param2) { %s }', $code);

        $ast = ParseUtils::parse($code);
        $scope = (new SyntacticScopeCreator())->createScope($ast);

        $cfa = new ControlFlowAnalysis();
        $cfa->process($ast);
        $cfg = $cfa->getGraph();

        $this->analysis = new MustBeReachingDefAnalysis($cfg, $scope);
        $this->analysis->analyze();

        $this->def = $this->use = null;

        NodeTraversal::traverseWithCallback($ast, new PreOrderCallback(function(NodeTraversal $t, \PHPParser_Node $node) {
            if ( ! $node instanceof \PHPParser_Node_Stmt_Label) {
                return;
            }

            if ('D' === $node->name) {
                $this->def = $node->getAttribute('next');
            } else if ('U' === $node->name) {
                $this->use = $node->getAttribute('next');
            } else if (0 === strpos($node->name, 'DP_')) {
                $className = 'PHPParser_Node_'.substr($node->name, 3);

                $parent = $node;
                while (null !== $parent = $parent->getAttribute('parent')) {
                    if ($parent instanceof $className) {
                        $this->def = $parent;

                        return;
                    }
                }

                throw new \RuntimeException(sprintf('Did not find any parent of class "%s".', $className));
            }
        }));

        $this->assertNotNull($this->def, 'Did not find "D" (definition) label in the code.');
        $this->assertNotNull($this->use, 'Did not find "U" (usage) label in the code.');
    }
}