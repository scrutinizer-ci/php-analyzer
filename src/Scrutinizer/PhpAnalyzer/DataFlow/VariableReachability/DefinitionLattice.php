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

use Scrutinizer\PhpAnalyzer\DataFlow\LatticeElementInterface;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil;
use Scrutinizer\PhpAnalyzer\PhpParser\Scope\Variable;

/**
 * Definition Lattice element.
 *
 * This class contains the comparison logic.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class DefinitionLattice implements LatticeElementInterface, \ArrayAccess, \IteratorAggregate
{
    private $definitions;

    /**
     * @param Variable[] $vars the initial set of variables
     */
    public function __construct(array $vars = array())
    {
        $this->definitions = new \SplObjectStorage();

        foreach ($vars as $var) {
            assert($var instanceof Variable);

            // Every variable in the scope is defined once in the beginning of the
            // function: all the declared variable are undefined, all functions
            // have been assigned and all arguments have their value from the caller.
            $this->definitions[$var] = new Definition($var->getScope()->getRootNode());
        }
    }

    public function getIterator()
    {
        return new \IteratorIterator($this->definitions);
    }

    public function offsetExists($offset)
    {
        return isset($this->definitions[$offset]);
    }

    public function offsetGet($offset)
    {
        return $this->definitions[$offset];
    }

    public function offsetSet($offset, $value)
    {
        $this->definitions[$offset] = $value;
    }

    public function offsetUnset($offset)
    {
        unset($this->definitions[$offset]);
    }

    public function getDefinitions()
    {
        return $this->definitions;
    }

    public function __clone()
    {
        $this->definitions = clone $this->definitions;
    }

    public function equals(LatticeElementInterface $that)
    {
        if ( ! $that instanceof DefinitionLattice) {
            return false;
        }

        if (count($this->definitions) !== count($that->definitions)) {
            return false;
        }

        foreach ($this->definitions as $var) {
            if ( ! isset($that->definitions[$var])) {
                return false;
            }

            $thisDef = $this->definitions[$var];
            $thatDef = $that->definitions[$var];
            if (null === $thisDef ^ null === $thatDef) {
                return false;
            } else if (null !== $thisDef && false === $thisDef->equals($thatDef)) {
                return false;
            }
        }

        return true;
    }

    public function __toString()
    {
        $str = 'DefinitionLattice(';

        $defs = array();
        foreach ($this->definitions as $var) {
            $defs[$var->getName()] = (null === $this->definitions[$var]) ? null
                : NodeUtil::getStringRepr($this->definitions[$var]->getNode());
        }
        $str .= json_encode($defs).')';

        return $str;
    }
}