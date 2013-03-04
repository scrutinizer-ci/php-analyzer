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

use Scrutinizer\PhpAnalyzer\ControlFlow\ControlFlowAnalysis;
use Scrutinizer\PhpAnalyzer\ControlFlow\ControlFlowGraph;
use Scrutinizer\PhpAnalyzer\DataFlow\DataFlowAnalysis;
use JMS\PhpManipulator\PhpParser\NormalizingNodeVisitor;
use Scrutinizer\PhpAnalyzer\PhpParser\Scope\Scope;
use Scrutinizer\PhpAnalyzer\PhpParser\Scope\SyntacticScopeCreator;
use Scrutinizer\PhpAnalyzer\DataFlow\LiveVariableAnalysis\LiveVariablesAnalysis;

class LiveVariablesAnalysisTest extends \PHPUnit_Framework_TestCase
{
    /** @var LiveVariablesAnalysis */
    private $liveness;

    public function testStraightLine()
    {
        // A sample of simple straight line of code with different liveness changes.
        $this->assertNotLiveBeforeX('X: $a = null;', 'a');
        $this->assertNotLiveAfterX('X: $a = null;', 'a');
        $this->assertNotLiveAfterX('X: $a = "foo";', 'a');
        $this->assertLiveAfterX('X: $a = 1; echo $a;', 'a');
//        $this->assertNotLiveBeforeX('X: $a = 1; echo $a;', 'a');
        $this->assertLiveBeforeX('$a = null; X: $a;', 'a');
        $this->assertLiveBeforeX('$a = null; X: $a = $a + 1;', 'a');
        $this->assertLiveBeforeX('$a = null; X: $a++;', 'a');
        $this->assertNotLiveAfterX('$a = $b = null; X: $b($a); $b();', 'a');
        $this->assertLiveBeforeX('$a = $b = null; X: $b($a);', 'b');
    }

    public function testProperties()
    {
        // Reading property of a local variable makes that variable live.
        $this->assertLiveBeforeX('$a = $b = null; X: $a->p;', 'a');

        // Assigning to a property doesn't kill "a", it makes it live instead.
        $this->assertLiveBeforeX('$a = $b = null; X: $a->p = 1; $b();', 'a');
        $this->assertLiveBeforeX('$a = $b = null; X: $a->p->q = 1; $b();', 'a');

        // An "a" in a different context.
        $this->assertNotLiveAfterX('$a = $b = null; X: $b->p->q->a = 1;', 'a');

        $this->assertLiveBeforeX('$a = $b = null; X: $b->p->q = $a;', 'a');
    }

    public function testConditions()
    {
        // Reading the condition makes the variable live.
        $this->assertLiveBeforeX('$a = $b = null; X: if ($a) { }', 'a');
        $this->assertLiveBeforeX('$a = $b = null; X: if ($a || $b) {}', 'a');
        $this->assertLiveBeforeX('$a = $b = null; X: if ($b || $a) {}', 'a');
        $this->assertLiveBeforeX('$a = $b = null; X: if ($b || $b($a)) { }', 'a');
        $this->assertNotLiveAfterX('$a = $b = null; X: $b(); if ($a) { }', 'b');

        // We can kill within a condition as well.
//         $this->assertNotLiveAfterX('$a = $b = null; X: $a(); if ($a = $b) { } $a();', 'a');
    }

    private function assertNotLiveBeforeX($src, $var)
    {
        $state = $this->getFlowStateAtX($src);

        $this->assertNotNull($state, 'Label X should be in the input program.');
        $this->assertFalse($state['in']->isLive($this->liveness->getVarIndex($var)), 'Variable '.$var.' should not be live before X.');
    }

    private function assertNotLiveAfterX($src, $var)
    {
        $state = $this->getFlowStateAtX($src);

        $this->assertNotNull($state, 'Label X should be in the input program.');
        $this->assertFalse($state['out']->isLive($this->liveness->getVarIndex($var)), 'Variable "'.$var.'" should not be live after X.');
    }

    private function assertLiveBeforeX($src, $var)
    {
        $state = $this->getFlowStateAtX($src);

        $this->assertNotNull($state, 'The input program should contain label X.');
        $this->assertTrue($state['in']->isLive($this->liveness->getVarIndex($var)), 'Variable '.$var.' should be live before X.');
    }

    private function assertLiveAfterX($src, $var)
    {
        $state = $this->getFlowStateAtX($src);

        $this->assertNotNull($state, 'The input program should contain label X.');
        $this->assertTrue($state['out']->isLive($this->liveness->getVarIndex($var)), 'Variable '.$var.' should be live after X.');
    }

    private function getFlowStateAtX($src, ControlFlowGraph $cfg = null)
    {
        if (is_string($src)) {
            $this->liveness = $this->computeLiveness($src);

            $cfg = $this->liveness->getControlFlowGraph();
            $node = $cfg->getEntryPoint()->getAstNode();
        } else if ($src instanceof \PHPParser_Node) {
            assert(null !== $cfg);
            $node = $src;
        } else {
            throw new \InvalidArgumentException('Invalid arguments passed.');
        }

        if ($node instanceof \PHPParser_Node_Stmt_Label) {
            if ($node->name === 'X') {
                $graphNode = $cfg->getNode($node->getAttribute('next'));

                return array('in'  => $graphNode->getAttribute(DataFlowAnalysis::ATTR_FLOW_STATE_IN),
                             'out' => $graphNode->getAttribute(DataFlowAnalysis::ATTR_FLOW_STATE_OUT));
            }
        }

        foreach ($node as $subNode) {
            if (is_array($subNode)) {
                foreach ($subNode as $aSubNode) {
                    if (!$aSubNode instanceof \PHPParser_Node) {
                        continue;
                    }

                    $state = $this->getFlowStateAtX($aSubNode, $cfg);
                    if (null !== $state) {
                        return $state;
                    }
                }

                continue;
            } else if (!$subNode instanceof \PHPParser_Node) {
                continue;
            }

            $state = $this->getFlowStateAtX($subNode, $cfg);
            if (null !== $state) {
                return $state;
            }
        }

        return null;
    }

    private function computeLiveness($src)
    {
        $src = '<?php class Foo { public function foo($param1, $param2) { '.$src.' } }';
        $parser = new \PHPParser_Parser();
        $lexer = new \PHPParser_Lexer($src);
        $ast = $parser->parse($lexer);

        $traverser = new \PHPParser_NodeTraverser();
        $traverser->addVisitor(new \PHPParser_NodeVisitor_NameResolver());
        $traverser->addVisitor(new NormalizingNodeVisitor());
        $ast = $traverser->traverse($ast);

        $traverser = new \PHPParser_NodeTraverser();
        $traverser->addVisitor(new \PHPParser_NodeVisitor_NodeConnector());
        $traverser->traverse($ast);

        $scopeCreator = new SyntacticScopeCreator();
        $scope = $scopeCreator->createScope($ast[0]->stmts[0], new Scope($ast[0]));

        $cfa = new ControlFlowAnalysis();
        $cfa->process($ast[0]->stmts[0]);
        $cfg = $cfa->getGraph();

        $lva = new LiveVariablesAnalysis($cfg, $scope);
        $lva->analyze();

        return $lva;
    }
}