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

use Scrutinizer\PhpAnalyzer\PhpParser\Scope\Variable;

/**
 * Represents a local variable definition.
 *
 * It contains a reference to the actual parser node which defines the variable
 * as well as a reference to any other `Variable`s that it depends on.
 *
 * For example, given the code ``$a = $b + foo($c);``, the node would be
 * PHPParser_Node_Expr_Assign, the dependent variable would be ``$b``
 * and ``$c``.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class Definition
{
    private $node;
    private $expr;
    private $dependentVariables;

    public function __construct(\PHPParser_Node $node, \PHPParser_Node $expr = null)
    {
        $this->node = $node;
        $this->expr = $expr;
        $this->dependentVariables = array();
    }

    public function getExpr()
    {
        return $this->expr;
    }

    public function getDependentVariables()
    {
        return $this->dependentVariables;
    }

    public function dependsOn(Variable $var)
    {
        return in_array($var, $this->dependentVariables, true);
    }

    public function addDependentVar(Variable $var)
    {
        if (in_array($var, $this->dependentVariables, true)) {
            return;
        }

        $this->dependentVariables[] = $var;
    }

    public function getNode()
    {
        return $this->node;
    }

    public function equals(Definition $that)
    {
        return $this->node === $that->node;
    }
}