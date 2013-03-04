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

use JMS\PhpManipulator\PhpParser\BlockNode;

class ControlFlowGraph
{
    /**
     * Node used to mark an implicit return when control is transferred outside the scope of this graph.
     *
     * @var GraphNode
     */
    private $implicitReturn;
    private $entryPoint;

    /**
     * @var \SplObjectStorage
     */
    private $nodes;

    /**
     * @param object $entryPoint
     */
    public function __construct($entryPoint)
    {
        $this->nodes = new \SplObjectStorage();
        $this->implicitReturn = $this->createGraphNode();
        $this->entryPoint = $this->createGraphNode($entryPoint);
    }

    public function getImplicitReturn()
    {
        return $this->implicitReturn;
    }

    public function getEntryPoint()
    {
        return $this->entryPoint;
    }

    public function getNodes()
    {
        return $this->nodes;
    }

    public function getDirectedGraphNodes()
    {
        $nodes = array();
        foreach ($this->nodes as $astNode) {
            $nodes[] = $this->nodes[$astNode];
        }

        return $nodes;
    }

    /**
     * Returns an array of predecessor nodes.
     *
     * @param GraphNode $destNode
     *
     * @return array
     */
    public function getDirectedPredNodes(GraphNode $destNode)
    {
        $nodes = array();
        foreach ($destNode->getInEdges() as $edge) {
            $nodes[] = $edge->getSource();
        }

        return $nodes;
    }

    public function getDirectedSuccNodes(GraphNode $sourceNode)
    {
        $nodes = array();
        foreach ($sourceNode->getOutEdges() as $edge) {
            $nodes[] = $edge->getDest();
        }

        return $nodes;
    }

    public function getOutEdges($value)
    {
        assert('is_object($value)');

        if (!isset($this->nodes[$value])) {
            return array();
        }

        return $this->nodes[$value]->getOutEdges();
    }

    public function getInEdges($value)
    {
        assert('is_object($value)');

        if (!isset($this->nodes[$value])) {
            return array();
        }

        return $this->nodes[$value]->getInEdges();
    }

    public function getNode($value)
    {
        if (null === $value) {
            return $this->implicitReturn;
        }

        assert('is_object($value)');

        return isset($this->nodes[$value]) ? $this->nodes[$value] : null;
    }

    public function hasNode($value)
    {
        assert('is_object($value)');

        return isset($this->nodes[$value]);
    }

    public function isImplicitReturn(GraphNode $node)
    {
        return $node === $this->implicitReturn;
    }

    public function connectToImplicitReturn($sourceValue, $edgeType)
    {
        $this->connect($sourceValue, $edgeType, null);
    }

    public function connect($sourceValue, $edgeType, $destValue)
    {
        $source = $this->createGraphNode($sourceValue);
        $dest = $this->createGraphNode($destValue);
        $edge = new GraphEdge($source, $edgeType, $dest);

        $source->addOutEdge($edge);
        $dest->addInEdge($edge);
    }

    public function connectIfNotConnected($sourceValue, $edgeType, $destValue)
    {
        $source = $this->createGraphNode($sourceValue);
        $dest = $this->createGraphNode($destValue);

        if ($this->isConnected($source, $dest, $edgeType)) {
            return;
        }

        $edge = new GraphEdge($source, $edgeType, $dest);
        $source->addOutEdge($edge);
        $dest->addInEdge($edge);
    }

    public function isConnected(GraphNode $a, GraphNode $b, $edgeType)
    {
        foreach ($a->getOutEdges() as $edge) {
            if ($edge->getType() !== $edgeType) {
                continue;
            }

            if ($edge->getDest() === $b) {
                return true;
            }
        }

        return false;
    }

    private function createGraphNode($value = null)
    {
        if (null === $value) {
            if (null === $this->implicitReturn) {
                $this->implicitReturn = new GraphNode();
            }

            return $this->implicitReturn;
        }

        if (!isset($this->nodes[$value])) {
            $this->nodes[$value] = new GraphNode($value);
        }

        return $this->nodes[$value];
    }
}