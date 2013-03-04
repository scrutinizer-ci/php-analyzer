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

class GraphNode
{
    private $astNode;
    private $inEdges = array();
    private $outEdges = array();
    private $attributes = array();

    /**
     * @param object $node
     */
    public function __construct($node = null)
    {
        $this->astNode = $node;
    }

    public function getAstNode()
    {
        return $this->astNode;
    }

    /**
     * @return GraphEdge[]
     */
    public function getInEdges()
    {
        return $this->inEdges;
    }

    /**
     * @return GraphEdge[]
     */
    public function getOutEdges()
    {
        return $this->outEdges;
    }

    public function addInEdge(GraphEdge $edge)
    {
        $this->inEdges[] = $edge;
    }

    public function addOutEdge(GraphEdge $edge)
    {
        $this->outEdges[] = $edge;
    }

    public function setAttribute($key, $value)
    {
        $this->attributes[$key] = $value;
    }

    public function hasAttribute($key)
    {
        return array_key_exists($key, $this->attributes);
    }

    public function getAttribute($key, $default = null)
    {
        return array_key_exists($key, $this->attributes) ? $this->attributes[$key] : null;
    }

    public function removeAttribute($key)
    {
        unset($this->attributes[$key]);
    }
}