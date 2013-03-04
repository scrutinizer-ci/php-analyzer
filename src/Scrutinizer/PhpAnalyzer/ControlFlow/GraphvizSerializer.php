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

use Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil;

class GraphvizSerializer
{
    private $ids;
    private $idCount;

    public function serialize(ControlFlowGraph $graph)
    {
        $dot = "digraph G {\n";

        $this->ids = new \SplObjectStorage();
        $this->idCount = 0;

        $dot .= "    ".$this->getId($graph->getImplicitReturn())." [shape=box,label=\"implicit return\",style=filled]\n";
        $entryPoint = $graph->getEntryPoint();

        $nodes = $graph->getNodes();
        foreach ($nodes as $astNode) {
            $node = $nodes[$astNode];
            $id = $this->getId($node);

            $dot .= sprintf('    %s [shape=box,label="%s"', $id, str_replace('"', '\\"', NodeUtil::getStringRepr($astNode)));

            if ($node === $entryPoint) {
                $dot .= ",style=filled";
            }

            $dot .= "]\n";

            foreach ($node->getOutEdges() as $edge) {
                $dot .= sprintf("    %s -> %s", $id, $this->getId($edge->getDest()));

                switch ($edge->getType()) {
                    case GraphEdge::TYPE_ON_FALSE:
                        $dot .= ' [label="false"]';
                        break;

                    case GraphEdge::TYPE_ON_TRUE:
                        $dot .= ' [label="true"]';
                        break;

                    case GraphEdge::TYPE_ON_EX:
                        $dot .= ' [label="exception"]';
                        break;
                }

                $dot .= "\n";
            }
        }

        $dot .= "}";

        return $dot;
    }

    private function getId(GraphNode $node)
    {
        if (!isset($this->ids[$node])) {
            $this->ids[$node] = ++$this->idCount;
        }

        return "B".$this->ids[$node];
    }
}