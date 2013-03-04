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

interface EdgeCallbackInterface
{
    /**
     * Update the state of the destination node when the given edge is traversed.
     *
     * For the fixed-point computation to work, only the destination node may be modified.
     * The source node and the edge must not be modified.
     *
     * @param \PHPParser_Node $source The start node.
     * @param integer $edgeType The edge.
     * @param \PHPParser_Node $destination The end node.
     *
     * @return boolean Whether the state of the destination node changed.
     */
    function traverseEdge(\PHPParser_Node $source, $edgeType, \PHPParser_Node $destination);
}