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

namespace Scrutinizer\Tests\PhpAnalyzer\ControlFlow;

use Scrutinizer\PhpAnalyzer\ControlFlow\ControlFlowAnalysis;
use JMS\PhpManipulator\PhpParser\BlockNode;

class ControlFlowAnalysisTest extends \PHPUnit_Framework_TestCase
{
    /** @var ControlFlowAnalysis */
    private $cfa;

    /**
     * @group foo
     */
    public function testCfgDoesNotTraverseExpressionTrees()
    {
        $expr = new \PHPParser_Node_Expr_Assign(new \PHPParser_Node_Expr_Variable('foo'), new \PHPParser_Node_Scalar_String('foo'));
        $this->normalizeAst(array($expr));
        $this->cfa->process($expr);

        $graph = $this->cfa->getGraph();
        $this->assertSame($expr, $graph->getEntryPoint()->getAstNode());
        $this->assertSame(1, count($out = $graph->getOutEdges($expr)));
        $this->assertSame($out[0]->getDest(), $graph->getImplicitReturn());
        $this->assertSame(1, count($graph->getNodes()));
    }

    /**
     * @dataProvider getComputeFollowNodeTests
     * @param \PHPParser_Node $fromNode
     * @param \PHPParser_Node $parentNode
     * @param \PHPParser_Node $expectedNode
     */
    public function testComputeFollowNode($message, \PHPParser_Node $fromNode, \PHPParser_Node $expectedNode = null)
    {
        $ref = new \ReflectionMethod($this->cfa, 'computeFollowNode');
        $ref->setAccessible(true);

        $this->assertSame($expectedNode, $ref->invoke($this->cfa, $fromNode), $message);
    }

    public function getComputeFollowNodeTests()
    {
        $tests = array();

        $if = new \PHPParser_Node_Stmt_If($cond = $this->getMockForAbstractClass('PHPParser_Node_Expr', array(array())), array(
            'stmts' => $blockNode = new BlockNode(array(
                $onTrue = new \PHPParser_Node_Stmt_Echo(array(new \PHPParser_Node_Scalar_String('foo'))),
            )),
            'elseifs' => array(
                new \PHPParser_Node_Stmt_ElseIf($cond, array(
                    'stmts' => $emptyBlock = new BlockNode(array()),
                )),
            ),
            'else' => new \PHPParser_Node_Stmt_Else(array(
                $onFalse = new \PHPParser_Node_Stmt_Echo(array(new \PHPParser_Node_Scalar_String('bar'))),
            )),
        ));
        $this->normalizeAst(array($if));

        $tests[] = array('Follow node of stmts is whatever follows the if', $onTrue, null);
        $tests[] = array('Follow node of else branch is whatever follows the if', $onFalse, null);
        $tests[] = array('Follow node of blocks is their next sibling', $blockNode, null);
        $tests[] = array('Follow node of empty blocks is whatever follows their parent', $emptyBlock, null);


        $function = new \PHPParser_Node_Stmt_Function('foo', array(
            'stmts' => $functionBlock = new BlockNode(array(
                $try = new \PHPParser_Node_Stmt_TryCatch(array(), array(
                    $catch1 = new \PHPParser_Node_Stmt_Catch(new \PHPParser_Node_Name(array('Foo')), 'ex'),
                    $catch2 = new \PHPParser_Node_Stmt_Catch(new \PHPParser_Node_Name(array('Bar')), 'ex'),
                )),
                $echo = new \PHPParser_Node_Stmt_Echo(array(new \PHPParser_Node_Scalar_String('foo'))),
            ))
        ));
        $tryBlock = $try->stmts = new BlockNode(array(
            $throw = new \PHPParser_Node_Stmt_Throw(new \PHPParser_Node_Expr_Variable('foo'))
        ));
        $catch1Block = $catch1->stmts = new BlockNode(array());
        $catch2Block = $catch2->stmts = new BlockNode(array());
        $this->normalizeAst(array($function));

        $tests[] = array('Follow node of function block is null', $functionBlock, null);
        $tests[] = array('Follow node of try block is echo', $tryBlock, $echo);
        $tests[] = array('Follow node of catch1 block is echo', $catch1Block, $echo);
        $tests[] = array('Follow node of catch2 block is echo', $catch2Block, $echo);
        $tests[] = array('Follow node of try is echo', $try, $echo);

        return $tests;
    }

    private function normalizeAst($ast)
    {
        $traverser = new \PHPParser_NodeTraverser();
        $traverser->addVisitor(new \PHPParser_NodeVisitor_NodeConnector());

        return $traverser->traverse($ast);
    }

    protected function setUp()
    {
        $this->cfa = new ControlFlowAnalysis();
    }
}