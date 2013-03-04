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

/**
 * Utility class for fixed-point computations on the control flow graph.
 *
 * The edges over the control flow graph are traversed until it reaches a steady state.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class FixedPointGraphTraversal
{
    private $callback;

    public function __construct(EdgeCallbackInterface $callback)
    {
        $this->callback = $callback;
    }

    public function computeFixedPointForGraph(ControlFlowGraph $graph)
    {
        $nodes = array();
        foreach ($graph->getNodes() as $astNode) {
            $nodes[] = $astNode;
        }

        $this->computeFixedPoint($graph, $nodes);
    }

    public function computeFixedPointWithEntry(ControlFlowGraph $graph, \PHPParser_Node $entry)
    {
        $entrySet = array();
        $entrySet[] = $entry;

        $this->computeFixedPoint($graph, $entrySet);
    }

    public function computeFixedPoint(ControlFlowGraph $graph, array $entrySet)
    {
        $cycleCount = 0;
        $nodeCount = count($graph->getNodes());

        // Choose a bail-out heristically in case the computation doesn't converge
        $maxIterations = max($nodeCount * $nodeCount * $nodeCount, 100);

        $workSet = array();
        foreach ($entrySet as $n) {
            $workSet[] = $graph->getNode($n);
        }

        while (count($workSet) > 0) {
            $source = array_shift($workSet);
            $sourceValue = $source->getAstNode();

            $outEdges = $source->getOutEdges();
            foreach ($outEdges as $edge) {
                $destNode = $edge->getDest();
                $destValue = $destNode->getAstNode();

                if ($this->callback->traverseEdge($sourceValue, $edge->getType(), $destValue)
                        && ! in_array($destNode, $workSet, true)) {
                    $workSet[] = $destNode;
                }
            }

            $cycleCount += 1;

            if ($cycleCount > $maxIterations) {
                throw new \RuntimeException(sprintf('Fixed point computation is not halting. Aborting after %d iterations.', $cycleCount));
            }
        }
    }
}