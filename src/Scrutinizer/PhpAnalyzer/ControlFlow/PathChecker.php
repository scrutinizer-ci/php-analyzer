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
 * Checks the path between two nodes for the given predicate.
 *
 * The implementation of discoverBackEdges follows the DFS-Visit algorithm in
 * "Introduction to Algorithms" by Cormen, Leiseron, Rivest, and Stein, 2nd
 * ed., on page 541. The calculation of back edges is described on page 546.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class PathChecker
{
    // No yet visited.
    const STATUS_WHITE = 'white';

    // Being visited.
    const STATUS_GRAY = 'gray';

    // Finished visiting.
    const STATUS_BLACK = 'black';

    const STATUS_BACK_EDGE = 'back_edge';
    const STATUS_VISITED_EDGE = 'visited_edge';

    const ATTR_STATUS = 'check_path_status';

    private $graph;
    private $start;
    private $end;
    private $nodePredicate;
    private $edgePredicate;
    private $inclusive;

    /**
     * Given a graph G with nodes A and B, this algorithm determines if all paths
     * from A to B contain at least one node satisfying a given predicate.
     *
     * Note that nodePredicate is not necessarily called for all nodes in G nor is
     * edgePredicate called for all edges in G.
     *
     * Also note that this algorithm does not assess whether there actually is a path
     * from A to B, but all edges if not ignored by the edge predicate are assumed
     * to form a path from A to B.
     *
     * @param ControlFlowGraph $graph Graph G to analyze.
     * @param GraphNode $a The node A.
     * @param GraphNode $b The node B.
     * @param callable $nodePredicate Predicate which at least one node on each path from an
     *     A node to B (inclusive) must match.
     * @param callable $edgePredicate Edges to consider as part of the graph. Edges in
     *     graph that don't match edgePredicate will be ignored.
     * @param boolean $inclusive Includes node A and B in the test for the node predicate.
     */
    public function __construct(ControlFlowGraph $graph, GraphNode $a, GraphNode $b, $nodePredicate = null, $edgePredicate = null, $inclusive = true)
    {
        if (null === $nodePredicate) {
            $nodePredicate = function() { return true; };
        }

        if (!is_callable($nodePredicate)) {
            throw new \InvalidArgumentException('$nodePredicate is not callable.');
        }

        if (null === $edgePredicate) {
            $edgePredicate = function() { return true; };
        }

        if (!is_callable($edgePredicate)) {
            throw new \InvalidArgumentException('$edgePredicate is not callable.');
        }

        $this->graph = $graph;
        $this->start = $a;
        $this->end = $b;
        $this->nodePredicate = $nodePredicate;
        $this->edgePredicate = $edgePredicate;
        $this->inclusive = $inclusive;
    }

    /**
     * @return boolean true iff all paths contain at least one node that satisfy the
     *     predicate
     */
    public function allPathsSatisfyPredicate()
    {
        $this->setUp();
        $result = $this->checkAllPathsWithoutBackEdges($this->start, $this->end);
        $this->tearDown();

        return $result;
    }

    public function somePathsSatisfyPredicate()
    {
        $this->setUp();
        $result = $this->checkSomePathsWithoutBackEdges($this->start, $this->end);

        return $result;
    }

    private function setUp()
    {
        foreach ($this->graph->getDirectedGraphNodes() as $node) {
            $node->setAttribute(self::ATTR_STATUS, self::STATUS_WHITE);

            foreach ($node->getOutEdges() as $e) {
                $e->removeAttribute(self::ATTR_STATUS);
            }
        }

        $this->discoverBackEdges($this->start);
    }

    private function discoverBackEdges(GraphNode $u)
    {
        $u->setAttribute(self::ATTR_STATUS, self::STATUS_GRAY);

        foreach ($u->getOutEdges() as $e) {
            if ( ! call_user_func($this->edgePredicate, $e)) {
                continue;
            }

            $v = $e->getDest();
            switch ($v->getAttribute(self::ATTR_STATUS)) {
                case self::STATUS_WHITE:
                    $this->discoverBackEdges($v);
                    break;

                case self::STATUS_GRAY:
                    $e->setAttribute(self::ATTR_STATUS, self::STATUS_BACK_EDGE);
                    break;
            }
        }

        $u->setAttribute(self::ATTR_STATUS, self::STATUS_BLACK);
    }

    /**
     * Verify that all non-looping paths from $a to $b pass
     * through at least one node where $nodePredicate is true.
     *
     * @param GraphNode $a
     * @param GraphNode $b
     */
    private function checkAllPathsWithoutBackEdges(GraphNode $a, GraphNode $b)
    {
        if (call_user_func($this->nodePredicate, $a->getAstNode())
                && ($this->inclusive || ($a !== $this->start && $a !== $this->end))) {
            return true;
        }

        if ($a === $b) {
            return false;
        }

        foreach ($a->getOutEdges() as $e) {
            if ($e->getAttribute(self::ATTR_STATUS) === self::STATUS_BACK_EDGE) {
                continue;
            }

            if ($e->getAttribute(self::ATTR_STATUS) === self::STATUS_VISITED_EDGE) {
                continue;
            }
            $e->setAttribute(self::ATTR_STATUS, self::STATUS_VISITED_EDGE);

            if (!call_user_func($this->edgePredicate, $e)) {
                continue;
            }

            $next = $e->getDest();
            if (!$this->checkAllPathsWithoutBackEdges($next, $b)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Verify that some non-looping paths from $a to $b pass
     * through at least one node where $nodePredicate is true.
     *
     * @param GraphNode $a
     * @param GraphNode $b
     */
    private function checkSomePathsWithoutBackEdges(GraphNode $a, GraphNode $b)
    {
        if (call_user_func($this->nodePredicate, $a->getAstNode())
                && ($this->inclusive || ($a !== $this->start && $a !== $this->end))) {
            return true;
        }

        if ($a === $b) {
            return false;
        }

        foreach ($a->getOutEdges() as $e) {
            if ($e->getAttribute(self::ATTR_STATUS) === self::STATUS_BACK_EDGE) {
                continue;
            }

            if ($e->getAttribute(self::ATTR_STATUS) === self::STATUS_VISITED_EDGE) {
                continue;
            }
            $e->setAttribute(self::ATTR_STATUS, self::STATUS_VISITED_EDGE);

            if (!call_user_func($this->edgePredicate, $e)) {
                continue;
            }

            $next = $e->getDest();
            if ($this->checkSomePathsWithoutBackEdges($next, $b)) {
                return true;
            }
        }

        return false;
    }
}