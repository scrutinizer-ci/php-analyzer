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

namespace Scrutinizer\PhpAnalyzer\DataFlow;

use Scrutinizer\PhpAnalyzer\ControlFlow\ControlFlowGraph;
use Scrutinizer\PhpAnalyzer\ControlFlow\GraphNode;
use Scrutinizer\PhpAnalyzer\Exception\MaxIterationsExceededException;

/**
 * Provides a framework for data flow analysis.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
abstract class DataFlowAnalysis
{
    const MAX_STEPS = 200000;

    const ATTR_FLOW_STATE_IN = 'flow_state_in';
    const ATTR_FLOW_STATE_OUT = 'flow_state_out';

    private $orderedWorkSet = array();
    private $nodeComparator;

    protected $cfg;
    protected $joinOp;

    public function __construct(ControlFlowGraph $graph, callable $joinOp)
    {
        $this->cfg = $graph;
        $this->joinOp = $joinOp;
        $this->nodeComparator = $this->getComparator();
    }

    public function getControlFlowGraph()
    {
        return $this->cfg;
    }

    public function getJoinOp()
    {
        return $this->joinOp;
    }

    public function getOrderedWorkSet()
    {
        return $this->orderedWorkSet;
    }

    public function getExitLatticeElement()
    {
        return $this->cfg->getImplicitReturn()->getAttribute(self::ATTR_FLOW_STATE_IN);
    }

    public function join(LatticeElementInterface $a, LatticeElementInterface $b)
    {
        return call_user_func($this->joinOp, array($a, $b));
    }

    /**
     * Whether this implement conducts a forward analysis.
     *
     * @return boolean
     */
    abstract protected function isForward();

    /**
     * Computes the output state for a given node and input state.
     *
     * @param object $node
     * @param LatticeElementInterface $lattice should be read-only
     *
     * @return LatticeElementInterface
     */
    abstract protected function flowThrough($node, LatticeElementInterface $lattice);

    /**
     * Gets the state of the initial estimation at each node.
     *
     * @return LatticeElementInterface Initial state.
     */
    abstract protected function createInitialEstimateLattice();

    /**
     * Gets the incoming state of the entry node.
     *
     * @return LatticeElementInterface Entry state.
     */
    abstract protected function createEntryLattice();

    /**
     * Computes a fixed-point solution.
     *
     * At the beginning each node gets assigned the initial estimate lattice. The entry node gets assigned the entry
     * lattice. After that, the initial work set is built, join operations are executed, and new lattices are calculated.
     *
     * This process repeats as long as we have not reached a fixed point solution. Should the calculation not converge
     * after the defined number of steps, an exception is thrown stating that no fixed point solution was found within
     * reasonable time.
     *
     * @param integer $maxSteps
     */
    public final function analyze($maxSteps = self::MAX_STEPS)
    {
        $this->initialize();
        $step = 0;

        while ( ! empty($this->orderedWorkSet)) {
            if ($step > $maxSteps) {
                throw new MaxIterationsExceededException(sprintf('Data Flow Analysis did not terminate after %d iterations.', $maxSteps));
            }

            $curNode = array_shift($this->orderedWorkSet);
            $this->joinInputs($curNode);

            if ($this->flow($curNode)) {
                // If there is a change in the current node, we want to grab the list
                // of nodes that this node affects.
                $nextNodes = $this->isForward() ? $this->cfg->getDirectedSuccNodes($curNode)
                    : $this->cfg->getDirectedPredNodes($curNode);

                foreach ($nextNodes as $nextNode) {
                    if ($this->cfg->isImplicitReturn($nextNode)) {
                        continue;
                    }

                    // There is no point in adding a node multiple times.
                    if (!in_array($nextNode, $this->orderedWorkSet, true)) {
                        $this->orderedWorkSet[] = $nextNode;
                    }
                }
                usort($this->orderedWorkSet, $this->nodeComparator);
            }

            $step += 1;
        }

        if ($this->isForward()) {
            $this->joinInputs($this->cfg->getImplicitReturn());
        }
    }

    protected final function addToWorkSet(GraphNode $node)
    {
        if (in_array($node, $this->orderedWorkSet, true)) {
            return;
        }

        $this->orderedWorkSet[] = $node;
        usort($this->orderedWorkSet, $this->nodeComparator);
    }

    protected final function clearWorkSet()
    {
        $this->orderedWorkSet = array();
    }

    protected function initialize()
    {
        $this->orderedWorkSet = array();
        foreach ($this->cfg->getDirectedGraphNodes() as $node) {
            $node->setAttribute(self::ATTR_FLOW_STATE_IN,  $this->createInitialEstimateLattice());
            $node->setAttribute(self::ATTR_FLOW_STATE_OUT, $this->createInitialEstimateLattice());
            $this->orderedWorkSet[] = $node;
        }

        $implicitReturn = $this->cfg->getImplicitReturn();
        $implicitReturn->setAttribute(self::ATTR_FLOW_STATE_IN,  $this->createInitialEstimateLattice());
        $implicitReturn->setAttribute(self::ATTR_FLOW_STATE_OUT, $this->createInitialEstimateLattice());

        usort($this->orderedWorkSet, $this->nodeComparator);
    }

    protected function joinInputs(GraphNode $node)
    {
        // forward analysis
        if ($this->isForward()) {
            // For the entry point, create an initial lattice state.
            if ($node === $this->cfg->getEntryPoint()) {
                $node->setAttribute(self::ATTR_FLOW_STATE_IN, $this->createEntryLattice());

                return;
            }

            $inNodes = $this->cfg->getDirectedPredNodes($node);
            $nbNodes = count($inNodes);

            // If only one in node, its out state is automatically this node's in state.
            if (1 === $nbNodes) {
                $node->setAttribute(self::ATTR_FLOW_STATE_IN, $inNodes[0]->getAttribute(self::ATTR_FLOW_STATE_OUT));
            } else if ($nbNodes > 1) {
                $values = array();
                foreach ($inNodes as $inNode) {
                    $values[] = $inNode->getAttribute(self::ATTR_FLOW_STATE_OUT);
                }

                $newInState = call_user_func($this->joinOp, $values);
                if (!$newInState instanceof LatticeElementInterface) {
                    throw new \RuntimeException(sprintf('The join operation must return a lattice element, but returned %s.', gettype($newInState)));
                }
                $node->setAttribute(self::ATTR_FLOW_STATE_IN, $newInState);
            }

            return;
        }

        // backward analysis
        $inNodes = $this->cfg->getDirectedSuccNodes($node);
        $nbNodes = count($inNodes);

        if (1 === $nbNodes) {
            if ($this->cfg->isImplicitReturn($inNodes[0])) {
                $node->setAttribute(self::ATTR_FLOW_STATE_OUT, $this->createEntryLattice());
            } else {
                $node->setAttribute(self::ATTR_FLOW_STATE_OUT, $inNodes[0]->getAttribute(self::ATTR_FLOW_STATE_IN));
            }
        } else if ($nbNodes > 1) {
            $values = array();
            foreach ($inNodes as $inNode) {
                $values[] = $inNode->getAttribute(self::ATTR_FLOW_STATE_IN);
            }

            $newOutState = call_user_func($this->joinOp, $values);
            if (!$newOutState instanceof LatticeElementInterface) {
                throw new \RuntimeException(sprintf('The join operation must return a lattice element, but returned %s.', gettype($newOutState)));
            }
            $node->setAttribute(self::ATTR_FLOW_STATE_OUT, $newOutState);
        }
    }

    protected function flow(GraphNode $node)
    {
        if ($this->isForward()) {
            $outBefore = $node->getAttribute(self::ATTR_FLOW_STATE_OUT);
            $outAfter = $this->flowThrough($node->getAstNode(), $node->getAttribute(self::ATTR_FLOW_STATE_IN));
            $node->setAttribute(self::ATTR_FLOW_STATE_OUT, $outAfter);

            return false === $outBefore->equals($outAfter);
        }

        $inBefore = $node->getAttribute(self::ATTR_FLOW_STATE_IN);
        $inAfter = $this->flowThrough($node->getAstNode(), $node->getAttribute(self::ATTR_FLOW_STATE_OUT));
        $node->setAttribute(self::ATTR_FLOW_STATE_IN, $inAfter);

        return false === $inBefore->equals($inAfter);
    }

    private function getComparator()
    {
        if ($this->isForward()) {
            return function(GraphNode $n1, GraphNode $n2) {
                return $n1->getAttribute('priority') - $n2->getAttribute('priority');
            };
        }

        return function(GraphNode $n1, GraphNode $n2) {
            return $n2->getAttribute('priority') - $n1->getAttribute('priority');
        };
    }
}