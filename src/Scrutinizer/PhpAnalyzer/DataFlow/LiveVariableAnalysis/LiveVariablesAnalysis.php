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

namespace Scrutinizer\PhpAnalyzer\DataFlow\LiveVariableAnalysis;

use Scrutinizer\PhpAnalyzer\ControlFlow\ControlFlowGraph;
use Scrutinizer\PhpAnalyzer\ControlFlow\GraphEdge;
use Scrutinizer\PhpAnalyzer\DataFlow\DataFlowAnalysis;
use Scrutinizer\PhpAnalyzer\DataFlow\LatticeElementInterface;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil;
use Scrutinizer\PhpAnalyzer\PhpParser\Scope\Scope;
use JMS\PhpManipulator\PhpParser\BlockNode;

class LiveVariablesAnalysis extends DataFlowAnalysis
{
    private $scope;
    private $escaped;

    public function __construct(ControlFlowGraph $graph, Scope $scope)
    {
        parent::__construct($graph, function(array $in) {
            /** @var $in LiveVariableLattice[] */

            $result = clone $in[0];
            for ($i=1,$c=count($in); $i<$c; $i++) {
                $result->liveSet->performOr($in[$i]->liveSet);
            }

            return $result;
        });

        $this->scope = $scope;
        $this->escaped = new \SplObjectStorage();
    }

    public function isForward()
    {
        return false;
    }

    public function getVarIndex($varName)
    {
        $var = $this->scope->getVar($varName);
        if (null === $var) {
            throw new \InvalidArgumentException(sprintf('There is no variable named "%s" in the current scope. Available variables: %s', $varName, implode(', ', $this->scope->getVarNames())));
        }

        return $var->getIndex();
    }

    public function getEscapedLocals()
    {
        return $this->escaped;
    }

    protected function createEntryLattice()
    {
        return new LiveVariableLattice($this->scope->getVarCount());
    }

    protected function createInitialEstimateLattice()
    {
        return new LiveVariableLattice($this->scope->getVarCount());
    }

    protected function flowThrough($node, LatticeElementInterface $input)
    {
        /** @var $node \PHPParser_Node */
        /** @var $input LiveVariableLattice */

        $gen = new BitSet(count($input->liveSet));
        $kill = new BitSet(count($input->liveSet));

        // Make kills conditional if the node can end abruptly by an exception.
        $conditional = false;
        foreach ($this->cfg->getOutEdges($node) as $edge) {
            /** @var $edge GraphEdge */

            if (GraphEdge::TYPE_ON_EX === $edge->getType()) {
                $conditional = true;
            }
        }

        $this->computeGenKill($node, $gen, $kill, $conditional);

        $result = clone $input;
        // L_in = L_out - Kill + Gen
        $result->liveSet->andNot($kill);
        $result->liveSet->performOr($gen);

        return $result;
    }

    /**
     * Computes the GEN and KILL set.
     *
     * @param boolean $conditional
     */
    private function computeGenKill(\PHPParser_Node $node, BitSet $gen, BitSet $kill, $conditional)
    {
        switch (true) {
            case $node instanceof \PHPParser_Node_Expr_Closure:
                foreach ($node->uses as $use) {
                    assert($use instanceof \PHPParser_Node_Expr_ClosureUse);
                    $this->addToSetIfLocal($use->var, $gen);
                }

                return;

            case $node instanceof BlockNode:
            case NodeUtil::isScopeCreator($node):
                return;

            case $node instanceof \PHPParser_Node_Stmt_While:
            case $node instanceof \PHPParser_Node_Stmt_Do:
            case $node instanceof \PHPParser_Node_Stmt_If:
                $this->computeGenKill($node->cond, $gen, $kill, $conditional);

                return;

            case $node instanceof \PHPParser_Node_Stmt_Foreach:
                if (null !== $node->keyVar) {
                    if ($node->keyVar instanceof \PHPParser_Node_Expr_Variable) {
                        $this->addToSetIfLocal($node->keyVar, $kill);
                        $this->addToSetIfLocal($node->keyVar, $gen);
                    } else {
                        $this->computeGenKill($node->keyVar, $gen, $kill, $conditional);
                    }
                }

                if ($node->valueVar instanceof \PHPParser_Node_Expr_Variable) {
                    $this->addToSetIfLocal($node->valueVar, $kill);
                    $this->addToSetIfLocal($node->valueVar, $gen);
                } else {
                    $this->computeGenKill($node->valueVar, $gen, $kill, $conditional);
                }

                $this->computeGenKill($node->expr, $gen, $kill, $conditional);

                return;

            case $node instanceof \PHPParser_Node_Stmt_For:
                foreach ($node->cond as $cond) {
                    $this->computeGenKill($cond, $gen, $kill, $conditional);
                }

                return;

            case $node instanceof \PHPParser_Node_Expr_BooleanAnd:
            case $node instanceof \PHPParser_Node_Expr_BooleanOr:
                $this->computeGenKill($node->left, $gen, $kill, $conditional);
                // May short circuit.
                $this->computeGenKill($node->right, $gen, $kill, true);

                return;

            case $node instanceof \PHPParser_Node_Expr_Ternary:
                $this->computeGenKill($node->cond, $gen, $kill, $conditional);
                // Assume both sides are conditional.
                if (null !== $node->if) {
                    $this->computeGenKill($node->if, $gen, $kill, true);
                }
                $this->computeGenKill($node->else, $gen, $kill, true);

                return;

            case $node instanceof \PHPParser_Node_Param:
                $this->markAllParametersEscaped();

                return;

            case $node instanceof \PHPParser_Node_Expr_Variable:
                if ( ! is_string($node->name)) {
                    $this->addAllToSetIfLocal($gen);

                    return;
                }

                $this->addToSetIfLocal($node->name, $gen);

                return;

            case $node instanceof \PHPParser_Node_Expr_Include:
                $this->computeGenKill($node->expr, $gen, $kill, $conditional);

                if ($node->type === \PHPParser_Node_Expr_Include::TYPE_INCLUDE
                        || $node->type === \PHPParser_Node_Expr_Include::TYPE_REQUIRE) {
                    $this->addAllToSetIfLocal($gen);
                }

                return;

            case NodeUtil::isMaybeFunctionCall($node, 'get_defined_vars'):
                $this->addAllToSetIfLocal($gen);
                break;

            case NodeUtil::isMaybeFunctionCall($node, 'compact'):
                foreach ($node->args as $arg) {
                    $this->computeGenKill($arg, $gen, $kill, $conditional);
                }

                $varNames = array();
                $isDynamic = false;
                foreach ($node->args as $arg) {
                    if ($arg->value instanceof \PHPParser_Node_Scalar_String) {
                        $varNames[] = $arg->value->value;

                        continue;
                    }

                    if ($arg->value instanceof \PHPParser_Node_Expr_Array) {
                        foreach ($arg->value->items as $item) {
                            assert($item instanceof \PHPParser_Node_Expr_ArrayItem);

                            if ($item->value instanceof \PHPParser_Node_Scalar_String) {
                                $varNames[] = $item->value->value;

                                continue;
                            }

                            $isDynamic = true;
                            break 2;
                        }

                        continue;
                    }

                    $isDynamic = true;
                    break;
                }

                $this->addAllToSetIfLocal($gen, $isDynamic ? null : $varNames);

                return;

            case $node instanceof \PHPParser_Node_Expr_AssignList:
                foreach ($node->vars as $var) {
                    if (!$var instanceof \PHPParser_Node_Expr_Variable) {
                        continue;
                    }

                    if (!$conditional) {
                        $this->addToSetIfLocal($var->name, $kill);
                    }

                    $this->addToSetIfLocal($var->name, $gen);
                }

                $this->computeGenKill($node->expr, $gen, $kill, $conditional);

                return;

            // AssignList is already handled in the previous CASE block.
            case NodeUtil::isAssignmentOp($node)
                    && $node->var instanceof \PHPParser_Node_Expr_Variable:
                if ($node->var->name instanceof \PHPParser_Node_Expr) {
                    $this->computeGenKill($node->var->name, $gen, $kill, $conditional);
                }

                if ( ! $conditional) {
                    $this->addToSetIfLocal($node->var->name, $kill);
                }

                if ( ! $node instanceof \PHPParser_Node_Expr_Assign) {
                    // Assignments such as a += 1 reads a first.
                    $this->addToSetIfLocal($node->var->name, $gen);
                }

                $this->computeGenKill($node->expr, $gen, $kill, $conditional);

                return;

            default:
                foreach ($node as $subNode) {
                    if (is_array($subNode)) {
                        foreach ($subNode as $aSubNode) {
                            if (!$aSubNode instanceof \PHPParser_Node) {
                                continue;
                            }

                            $this->computeGenKill($aSubNode, $gen, $kill, $conditional);
                        }

                        continue;
                    } else if (!$subNode instanceof \PHPParser_Node) {
                        continue;
                    }

                    $this->computeGenKill($subNode, $gen, $kill, $conditional);
                }
        }
    }

    private function addAllToSetIfLocal(BitSet $set, array $vars = null)
    {
        foreach ($vars ?: $this->scope->getVarNames() as $name) {
            $this->addToSetIfLocal($name, $set);
        }
    }

    private function addToSetIfLocal($varName, BitSet $set)
    {
        if (!is_string($varName)) {
            return;
        }

        if (false === $this->scope->isDeclared($varName)) {
            return;
        }

        $var = $this->scope->getVar($varName);
        if (!isset($this->escaped[$var])) {
            $set->set($var->getIndex());
        }
    }

    private function markAllParametersEscaped()
    {
        $lp = $this->scope->getRootNode()->params;
        foreach ($lp as $param) {
            $this->escaped->attach($this->scope->getVar($param->name));
        }
    }
}