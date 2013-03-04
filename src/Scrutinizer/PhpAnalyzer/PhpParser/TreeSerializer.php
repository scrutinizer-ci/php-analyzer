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

namespace Scrutinizer\PhpAnalyzer\PhpParser;

class TreeSerializer
{
    private $idCache;
    private $idCount;

    public function serialize(\PHPParser_Node $node)
    {
        $this->idCache = new \SplObjectStorage();
        $this->idCount = 0;

        $dot = "digraph tree {\n";

        $dot .= '    '.$this->getId($node).' [label="'.$this->getLabel($node).'", style=filled]'."\n";
        $dot .= $this->serializeChildren($node);

        $dot .= "}\n";

        return $dot;
    }

    private function serializeChildren(\PHPParser_Node $node)
    {
        $dot = '';
        foreach ($node as $subNode) {
            if (is_array($subNode)) {
                foreach ($subNode as $aSubNode) {
                    if (!$aSubNode instanceof \PHPParser_Node) {
                        continue;
                    }
                    $dot .= '    '.$this->getId($aSubNode).' [label="'.$this->getLabel($aSubNode).'"]'."\n";
                    $dot .= '    '.$this->getId($node).' -> '.$this->getId($aSubNode)."\n";
                    $dot .= $this->serializeChildren($aSubNode);
                }
            } else if ($subNode instanceof \PHPParser_Node) {
                $dot .= '    '.$this->getId($subNode).' [label="'.$this->getLabel($subNode).'"]'."\n";
                $dot .= '    '.$this->getId($node).' -> '.$this->getId($subNode)."\n";
                $dot .= $this->serializeChildren($subNode);
            }
        }

        return $dot;
    }

    private function getLabel(\PHPParser_Node $node)
    {
        $label = get_class($node);

        if ($type = $node->getAttribute('type')) {
            $label .= ' ('.$type->getDisplayName().')';
        }

        return str_replace('"', '\\"', $label);
    }

    private function getId(\PHPParser_Node $node)
    {
        if (isset($this->idCache[$node])) {
            return $this->idCache[$node];
        }

        return $this->idCache[$node] = 'N'.($this->idCount++);
    }
}