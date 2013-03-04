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

namespace Scrutinizer\PhpAnalyzer\DataFlow\TypeInference;

/**
 * Small helper class for handling the outcome of binary operations.
 *
 * We do keep track of two sets of boolean literals:
 *
 *     1. toBooleanOutcomes: These are boolean literals as converted from any types.
 *     2. booleanValues: These are boolean literals from just boolean types.
 *
 * TODO: Verify that toBooleanOutcomes, and booleanValues are not necessary anymore,
 *       and remove accordingly.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class BooleanOutcomePair
{
    public $toBooleanOutcomes;
    public $booleanValues;

    private $inference;

    // The scope if only half of the expression executed, when applicable.
    private $leftScope;

    // The scope when the whole expression executed.
    private $rightScope;

    // The scope when we don't know how much of the expression is executed.
    private $joinedScope;

    /**
     * Creates the boolean outcome pair.
     *
     * This is used when we determine the outcome of short-circuiting binary
     * operations such as &&, and, ||, or.
     *
     * @param boolean $condition
     *
     * @return BooleanOutcomePair
     */
    public static function fromPairs(TypeInference $inference, BooleanOutcomePair $left, BooleanOutcomePair $right, $condition)
    {
        return new self($inference,
                self::getBooleanOutcomes($left->toBooleanOutcomes, $right->toBooleanOutcomes, $condition),
                self::getBooleanOutcomes($left->booleanValues, $right->booleanValues, $condition),
                $left->getJoinedFlowScope(),
                $right->getJoinedFlowScope());
    }

    /**
     * Determines the boolean literal set that is possible for short-circuiting binary operations.
     *
     * @param array<boolean> $leftValues possible boolean values for left expression
     * @param array<boolean> $rightValues possible boolean values for right expression
     * @param boolean $condition the boolean value that causes the right side to be evaluated
     *
     * @return array<boolean> the possible set for the entire expression
     */
    private static function getBooleanOutcomes(array $leftValues, array $rightValues, $condition)
    {
        if (!in_array(!$condition, $leftValues, true)) {
            return $rightValues;
        }

        if (!in_array(!$condition, $rightValues, true)) {
            $rightValues[] = !$condition;
        }

        return $rightValues;
    }

    public function __construct(TypeInference $inference, array $toBooleanOutcomes, array $booleanValues, LinkedFlowScope $leftScope, LinkedFlowScope $rightScope)
    {
        $this->inference = $inference;
        $this->toBooleanOutcomes = $toBooleanOutcomes;
        $this->booleanValues = $booleanValues;
        $this->leftScope = $leftScope;
        $this->rightScope = $rightScope;
    }

    /**
     * Gets the safe estimated scope without knowing if all of the
     * subexpressions will be evaluated.
     */
    public function getJoinedFlowScope()
    {
        if (null === $this->joinedScope) {
            if ($this->leftScope === $this->rightScope) {
                $this->joinedScope = $this->rightScope;
            } else {
                $this->joinedScope = $this->inference->join($this->leftScope, $this->rightScope);
            }
        }

        return $this->joinedScope;
    }

    /**
     * Gets the outcome scope if we do know the outcome of the entire
     * expression.
     *
     * @param \PHPParser_Node $nodeType
     * @param boolean $outcome
     */
    public function getOutcomeFlowScope(\PHPParser_Node $node = null, $outcome)
    {
        if ((($node instanceof \PHPParser_Node_Expr_BooleanAnd
                || $node instanceof \PHPParser_Node_Expr_LogicalAnd) && $outcome)
                || (($node instanceof \PHPParser_Node_Expr_BooleanOr
                        || $node instanceof \PHPParser_Node_Expr_LogicalOr) && !$outcome)) {
            // We know that the whole expression must have executed.
            return $this->rightScope;
        }

        return $this->getJoinedFlowScope();
    }
}
