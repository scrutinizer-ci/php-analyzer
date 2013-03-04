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

use Scrutinizer\PhpAnalyzer\ControlFlow\ControlFlowGraph;
use Scrutinizer\PhpAnalyzer\ControlFlow\FixedPointGraphTraversal;

class FixedPointGraphTraversalTest extends \PHPUnit_Framework_TestCase
{
    public function testComputeFixedPoint()
    {
        $graph = new ControlFlowGraph($entry = $this->getAstNode());

        $node1 = $this->getAstNode();
        $graph->connect($entry, null, $node1);

        $node2 = $this->getAstNode();
        $graph->connect($node1, null, $node2);

        $node3 = $this->getAstNode();
        $graph->connect($node3, null, $node2);

        $node4 = $this->getAstNode();
        $graph->connect($node2, null, $node4);

        $callback = $this->getMock('Scrutinizer\PhpAnalyzer\ControlFlow\EdgeCallbackInterface');
        $t = new FixedPointGraphTraversal($callback);

        $callback->expects($this->at(0))
            ->method('traverseEdge')
            ->with($entry, null, $node1)
            ->will($this->returnValue(true));
        $callback->expects($this->at(1))
            ->method('traverseEdge')
            ->with($node1, null, $node2)
            ->will($this->returnValue(false));

        $t->computeFixedPointWithEntry($graph, $entry);
    }

    private function getAstNode()
    {
        return $this->getMock('PHPParser_Node');
    }
}