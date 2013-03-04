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
use Scrutinizer\PhpAnalyzer\ControlFlow\GraphReachability;
use JMS\PhpManipulator\PhpParser\BlockNode;

class GraphReachabilityTest extends \PHPUnit_Framework_TestCase
{
    private $graph;

    public function testCompute()
    {
        $ast = new BlockNode(array(
            $goto = new \PHPParser_Node_Stmt_Goto('foo'),
            $echo = new \PHPParser_Node_Stmt_Echo(array(new \PHPParser_Node_Scalar_String('boo'))),
            $label = new \PHPParser_Node_Stmt_Label('foo'),
        ));
        $this->compute($ast);

        $this->assertReachable($ast);
        $this->assertReachable($goto);
        $this->assertNotReachable($echo);
        $this->assertReachable($label);
    }

    private function assertReachable(\PHPParser_Node $node)
    {
        $graphNode = $this->graph->getNode($node);

        $this->assertNotNull($graphNode, 'Graph node does not exist for given AST node.');
        $this->assertTrue($graphNode->hasAttribute(GraphReachability::ATTR_REACHABILITY), 'Reachability attribute does not exist on node.');
        $this->assertSame(GraphReachability::REACHABLE, $graphNode->getAttribute(GraphReachability::ATTR_REACHABILITY), 'Node is not reachable, but was expected to be reachable.');
    }

    private function assertNotReachable(\PHPParser_Node $node)
    {
        $graphNode = $this->graph->getNode($node);

        $this->assertNotNull($graphNode, 'Graph node does not exist for given AST node.');
        $this->assertTrue($graphNode->hasAttribute(GraphReachability::ATTR_REACHABILITY), 'Reachability attribute does not exist on node.');
        $this->assertSame(GraphReachability::UNREACHABLE, $graphNode->getAttribute(GraphReachability::ATTR_REACHABILITY), 'Node is reachable, but was expected to be unreachable.');
    }

    private function compute(\PHPParser_Node $ast)
    {
        $r = new GraphReachability($graph = $this->getCfg($ast));
        $r->compute($ast);
    }

    private function getCfg(\PHPParser_Node $ast)
    {
        $traverser = new \PHPParser_NodeTraverser();
        $traverser->addVisitor(new \PHPParser_NodeVisitor_NodeConnector());
        $traverser->traverse(array($ast));

        $cfa = new ControlFlowAnalysis();
        $cfa->process($ast);

        return $this->graph = $cfa->getGraph();
    }
}