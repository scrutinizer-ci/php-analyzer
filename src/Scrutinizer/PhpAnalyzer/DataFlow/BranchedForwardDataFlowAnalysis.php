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

use Scrutinizer\PhpAnalyzer\ControlFlow\GraphNode;

/**
 * Forward flowing data flow analysis which takes branches (TRUE, FALSE, EX, UNCOND) into account.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
abstract class BranchedForwardDataFlowAnalysis extends DataFlowAnalysis
{
    protected function initialize()
    {
        $this->clearWorkSet();

        foreach ($this->cfg->getDirectedGraphNodes() as $node) {
            $outEdgeCount = count($node->getOutEdges());
            $outLattices = array();
            for ($i=0; $i < $outEdgeCount; $i++) {
                $outLattices[] = $this->createInitialEstimateLattice();
            }

            $node->setAttribute(self::ATTR_FLOW_STATE_IN, $this->createInitialEstimateLattice());
            $node->setAttribute(self::ATTR_FLOW_STATE_OUT, $outLattices);
            $this->addToWorkSet($node);
        }
    }

    protected final function isForward()
    {
        return true;
    }

    protected final function flowThrough($node, LatticeElementInterface $input)
    {
        throw new \LogicException('Use branchedFlowThrough() when conducting a branched forward data flow analysis.');
    }

    /**
     * Computes, and returns an array of outputs for each branch edge.
     *
     * @param object $node
     * @param LatticeElementInterface $input
     *
     * @return LatticeElementInterface[] A list of output values depending on the edge's branch type.
     */
    abstract protected function branchedFlowThrough($node, LatticeElementInterface $input);

    protected final function flow(GraphNode $node)
    {
        $outBefore = $node->getAttribute(self::ATTR_FLOW_STATE_OUT);
        $outAfter = $this->branchedFlowThrough($node->getAstNode(), $node->getAttribute(self::ATTR_FLOW_STATE_IN));
        $node->setAttribute(self::ATTR_FLOW_STATE_OUT, $outAfter);
        assert('count($outBefore) === count($outAfter)');

        for ($i=0,$c=count($outBefore); $i<$c; $i++) {
            if (false === $outBefore[$i]->equals($outAfter[$i])) {
                return true;
            }
        }

        return false;
    }

    protected function joinInputs(GraphNode $node)
    {
        if ($this->cfg->getEntryPoint() === $node) {
            $node->setAttribute(self::ATTR_FLOW_STATE_IN, $this->createEntryLattice());

            return;
        }

        $predNodes = $this->cfg->getDirectedPredNodes($node);
        if (!$predNodes) {
            return;
        }

        $values = array();
        foreach ($predNodes as $predNode) {
            $succNodes = $this->cfg->getDirectedSuccNodes($predNode);
            $index = array_search($node, $succNodes, true);

            $outStates = $predNode->getAttribute(self::ATTR_FLOW_STATE_OUT);
            $values[] = $outStates[$index];
        }

        if (!$values) {
            return;
        }

        $newInState = call_user_func($this->joinOp, $values);
        $node->setAttribute(self::ATTR_FLOW_STATE_IN, $newInState);
    }
}