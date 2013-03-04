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

namespace Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\ReverseInterpreter;

use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\FlowScopeInterface;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\LinkedFlowScope;

interface ReverseInterpreterInterface
{
    /**
     * Calculates a precise version of the scope knowing the outcome of the
     * condition.
     *
     *  @param \PHPParser_Node $condition the condition's expression
     *  @param LinkedFlowScope $blindScope the scope without knowledge about the outcome of the
     *  condition
     *  @param boolean $outcome the outcome of the condition
     *
     *  @return FlowScopeInterface
     */
    function getPreciserScopeKnowingConditionOutcome(\PHPParser_Node $condition,
        LinkedFlowScope $blindScope, $outcome);
}