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

namespace Scrutinizer\PhpAnalyzer\DataFlow\VariableReachability;

/**
 * Keeps track of the different uses for each variable.
 *
 * Also, this class contains comparison logic with other lattices.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class UseLattice implements \Scrutinizer\PhpAnalyzer\DataFlow\LatticeElementInterface, \ArrayAccess, \IteratorAggregate
{
    private $uses;
    private $varNodes;

    public function __construct()
    {
        $this->uses = new \SplObjectStorage();
        $this->varNodes = new \SplObjectStorage();
    }

    public function __clone()
    {
        $this->uses = clone $this->uses;
        $this->varNodes = clone $this->varNodes;
    }

    public function offsetExists($offset)
    {
        return isset($this->uses[$offset]);
    }

    public function offsetSet($offset, $value)
    {
        throw new \LogicException('offsetSet() is not available; please use addUse() instead.');
    }

    public function offsetGet($offset)
    {
        return $this->uses[$offset];
    }

    public function offsetUnset($offset)
    {
        // We do not remove variable nodes here, as this CFG node might be referenced by another
        // variable. This should not matter too much as the variables per CFG node are fixed anyway.
        unset($this->uses[$offset]);
    }

    public function getUsingVarNodes(\Scrutinizer\PhpAnalyzer\PhpParser\Scope\Variable $var)
    {
        if ( ! isset($this->uses[$var]) || null === $this->uses[$var]) {
            return array();
        }

        $varNodes = array();
        foreach ($this->uses[$var] as $cfgNode) {
            foreach ($this->varNodes[$cfgNode] as $varNode) {
                if (in_array($varNode, $varNodes, true)) {
                    continue;
                }

                $varNodes[] = $varNode;
            }
        }

        return $varNodes;
    }

    public function add(UseLattice $that)
    {
        foreach ($that as $var) {
            $currentUses = isset($this[$var]) ? $this[$var] : array();
            foreach ($that->uses[$var] as $cfgNode) {
                if ( ! in_array($cfgNode, $currentUses, true)) {
                    $currentUses[] = $cfgNode;
                }

                foreach ($that->varNodes[$cfgNode] as $varNode) {
                    $this->addVarNode($cfgNode, $varNode);
                }
            }
            $this->uses[$var] = $currentUses;
        }
    }

    public function getIterator()
    {
        return new \IteratorIterator($this->uses);
    }

    public function addUse(\Scrutinizer\PhpAnalyzer\PhpParser\Scope\Variable $var, \PHPParser_Node $cfgNode, \PHPParser_Node $varNode)
    {
        if ( ! isset($this->uses[$var])) {
            $this->uses[$var] = array($cfgNode);
            $this->addVarNode($cfgNode, $varNode);

            return;
        }

        if (in_array($cfgNode, $this->uses[$var], true)) {
            $this->addVarNode($cfgNode, $varNode);

            return;
        }

        $uses = $this->uses[$var];
        $uses[] = $cfgNode;
        $this->uses[$var] = $uses;

        $this->addVarNode($cfgNode, $varNode);
    }

    private function addVarNode(\PHPParser_Node $cfgNode, \PHPParser_Node $varNode)
    {
        $currentVars = isset($this->varNodes[$cfgNode]) ? $this->varNodes[$cfgNode] : array();
        if (in_array($varNode, $currentVars, true)) {
            return;
        }

        $currentVars[] = $varNode;
        $this->varNodes[$cfgNode] = $currentVars;
    }

    public function equals(\Scrutinizer\PhpAnalyzer\DataFlow\LatticeElementInterface $that)
    {
        if ( ! $that instanceof UseLattice) {
            return false;
        }

        if (count($this->uses) !== count($that->uses)) {
            return false;
        }

        foreach ($this->uses as $var) {
            if ( ! isset($that->uses[$var])) {
                return false;
            }

            $thisUses = $this->uses[$var];
            $thatUses = $that->uses[$var];
            if (count($thisUses) !== count($thatUses)) {
                return false;
            }

            foreach ($thisUses as $node) {
                if ( ! in_array($node, $thatUses, true)) {
                    return false;
                }
            }
        }

        return true;
    }
}