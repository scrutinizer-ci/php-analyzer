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

namespace Scrutinizer\PhpAnalyzer\ControlFlow;

class GraphReachability implements EdgeCallbackInterface
{
    const ATTR_REACHABILITY = 'reachability';

    const REACHABLE = 1;
    const UNREACHABLE = 2;

    private $graph;

    public static function computeWithEntry(ControlFlowGraph $graph, \PHPParser_Node $entry)
    {
        $r = new self($graph);
        $r->compute($entry);

        return $r;
    }

    public function __construct(ControlFlowGraph $graph)
    {
        $this->graph = $graph;
    }

    public function compute(\PHPParser_Node $entry)
    {
        foreach ($this->graph->getDirectedGraphNodes() as $node) {
            $node->setAttribute(self::ATTR_REACHABILITY, self::UNREACHABLE);
        }

        $this->graph->getEntryPoint()->setAttribute(self::ATTR_REACHABILITY, self::REACHABLE);

        $t = new FixedPointGraphTraversal($this);
        $t->computeFixedPointWithEntry($this->graph, $entry);
    }

    public function recompute(\PHPParser_Node $reachableNode)
    {
        $newReachable = $this->graph->getNode($reachableNode);
        assert('$newReachable->getAttribute(self::ATTR_REACHABILITY) === self::UNREACHABLE');

        $newReachable->setAttribute(self::ATTR_REACHABILITY, self::REACHABLE);

        $t = new FixedPointGraphTraversal($this);
        $t->computeFixedPointWithEntry($this->graph, $reachableNode);
    }

    public function traverseEdge(\PHPParser_Node $source, $edgeType, \PHPParser_Node $dest = null)
    {
        if ($this->graph->getNode($source)->getAttribute(self::ATTR_REACHABILITY) === self::REACHABLE) {
            $destNode = $this->graph->getNode($dest);

            // We need to check whether the node has already been declared as reachable
            // in order to not run in endless cycles if the graph is circular.
            if (self::UNREACHABLE === $destNode->getAttribute(self::ATTR_REACHABILITY)) {
                $destNode->setAttribute(self::ATTR_REACHABILITY, self::REACHABLE);

                return true;
            }
        }

        return false;
    }
}