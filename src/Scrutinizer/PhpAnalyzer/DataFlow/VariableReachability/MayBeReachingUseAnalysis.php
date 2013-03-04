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

use Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil;

/**
 * Analyzes the may-be usage of the definition of variables.
 *
 * For a given variable defining node, this allows us to find all the nodes where the variable
 * with the given definition is being used. If the variable is being overwritten, the overwritten
 * instances are not being returned.
 *
 * NOTE: We should migrate this to a BranchforwardDataFlowAnalysis keeping track of possible
 *       definitions. This is necessary to take into account ON_EX edges.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class MayBeReachingUseAnalysis extends \Scrutinizer\PhpAnalyzer\DataFlow\DataFlowAnalysis
{
    /** @var \Scrutinizer\PhpAnalyzer\PhpParser\Scope\Scope */
    private $scope;

    public static function createJoinOperation()
    {
        return function(array $values) {
            $result = new UseLattice();
            foreach ($values as $lattice) {
                assert($lattice instanceof UseLattice);
                $result->add($lattice);
            }

            return $result;
        };
    }

    public function __construct(\Scrutinizer\PhpAnalyzer\ControlFlow\ControlFlowGraph $cfg, \Scrutinizer\PhpAnalyzer\PhpParser\Scope\Scope $scope)
    {
        parent::__construct($cfg, self::createJoinOperation());
        $this->scope = $scope;
    }

    public function getUses($name, \PHPParser_Node $defNode)
    {
        if (null === $graphNode = $this->cfg->getNode($defNode)) {
            throw new \InvalidArgumentException('$defNode must be part of the control flow graph.');
        }

        if (null === $var = $this->scope->getVar($name)) {
            return array();
        }

        $outState = $graphNode->getAttribute(self::ATTR_FLOW_STATE_OUT);

        return isset($outState[$var]) ? $outState[$var] : array();
    }

    protected function isForward()
    {
        return false;
    }

    protected function createEntryLattice()
    {
        return new UseLattice();
    }

    protected function createInitialEstimateLattice()
    {
        return new UseLattice();
    }

    protected function flowThrough($node, \Scrutinizer\PhpAnalyzer\DataFlow\LatticeElementInterface $input)
    {
        assert($node instanceof \PHPParser_Node);
        assert($input instanceof UseLattice);

        $output = clone $input;
        $this->computeMayUse($node, $node, $output, false);

        return $output;
    }

    /**
     * @param boolean $conditional
     */
    private function computeMayUse(\PHPParser_Node $node, \PHPParser_Node $cfgNode, UseLattice $output, $conditional)
    {
        switch (true) {
            case $node instanceof \JMS\PhpManipulator\PhpParser\BlockNode:
            case NodeUtil::isScopeCreator($node):
                return;

            case NodeUtil::isName($node):
                $this->addToUseIfLocal($node, $cfgNode, $output);

                return;

            case $node instanceof \PHPParser_Node_Stmt_Catch:
                $this->computeMayUse($node->stmts, $cfgNode, $output, $conditional);
                $this->addDefToVarNodes($node->var, $node, $node, $output);
                $this->removeFromUseIfLocal($node->var, $output);

                return;

            case $node instanceof \PHPParser_Node_Stmt_While:
            case $node instanceof \PHPParser_Node_Stmt_Do:
            case $node instanceof \PHPParser_Node_Stmt_If:
            case $node instanceof \PHPParser_Node_Stmt_ElseIf:
            case $node instanceof \PHPParser_Node_Stmt_For:
                if (null !== $cond = NodeUtil::getConditionExpression($node)) {
                    $this->computeMayUse($cond, $cfgNode, $output, $conditional);
                }

                return;

            case $node instanceof \PHPParser_Node_Stmt_Foreach:
                if ( ! $conditional) {
                    if (null !== $node->keyVar && NodeUtil::isName($node->keyVar)) {
                        $this->removeFromUseIfLocal($node->keyVar->name, $output);
                    }
                    if (NodeUtil::isName($node->valueVar)) {
                        $this->removeFromUseIfLocal($node->valueVar->name, $output);
                    }
                }

                $this->computeMayUse($node->expr, $cfgNode, $output, $conditional);

                return;

            case $node instanceof \PHPParser_Node_Expr_BooleanAnd:
            case $node instanceof \PHPParser_Node_Expr_LogicalAnd:
            case $node instanceof \PHPParser_Node_Expr_BooleanOr:
            case $node instanceof \PHPParser_Node_Expr_LogicalOr:
                $this->computeMayUse($node->right, $cfgNode, $output, true);
                $this->computeMayUse($node->left, $cfgNode, $output, $conditional);

                return;

            case $node instanceof \PHPParser_Node_Expr_Ternary:
                $this->computeMayUse($node->else, $cfgNode, $output, true);
                if (null !== $node->if) {
                    $this->computeMayUse($node->if, $cfgNode, $output, true);
                }
                $this->computeMayUse($node->cond, $cfgNode, $output, $conditional);

                return;

            default:
                if (NodeUtil::isAssignmentOp($node)) {
                    if ($node instanceof \PHPParser_Node_Expr_AssignList) {
                        foreach ($node->vars as $var) {
                            if (null === $var) {
                                continue;
                            }

                            if ( ! $conditional) {
                                $this->removeFromUseIfLocal($var->name, $output);
                            }
                        }
                        $this->computeMayUse($node->expr, $cfgNode, $output, $conditional);

                        return;
                    }

                    if (NodeUtil::isName($node->var)) {
                        $this->addDefToVarNodes($node->var->name, $node->var, $node->expr, $output);

                        if ( ! $conditional) {
                            $this->removeFromUseIfLocal($node->var->name, $output);
                        }

                        // Handle the cases where we assign, and read at the same time, e.g.
                        // ``$a += 5``.
                        if ( ! $node instanceof \PHPParser_Node_Expr_Assign
                                && ! $node instanceof \PHPParser_Node_Expr_AssignRef) {
                            $this->addToUseIfLocal($node->var, $cfgNode, $output);
                        }

                        $this->computeMayUse($node->expr, $cfgNode, $output, $conditional);

                        return;
                    }

                    return;
                }

                $inOrder = array();
                foreach ($node as $subNode) {
                    if (is_array($subNode)) {
                        foreach ($subNode as $aSubNode) {
                            if ( ! $aSubNode instanceof \PHPParser_Node) {
                                continue;
                            }

                            $inOrder[] = $aSubNode;
                        }
                    } else if ($subNode instanceof \PHPParser_Node) {
                        $inOrder[] = $subNode;
                    }
                }

                foreach (array_reverse($inOrder) as $subNode) {
                    $this->computeMayUse($subNode, $cfgNode, $output, $conditional);
                }
        }
    }

    private function addDefToVarNodes($name, \PHPParser_Node $defNode, \PHPParser_Node $defExpr, UseLattice $uses)
    {
        if (null === $var = $this->scope->getVar($name)) {
            return;
        }

        $maybeUsingVarNodes = $uses->getUsingVarNodes($var);
        $defNode->setAttribute('maybe_using_vars', $maybeUsingVarNodes);

        foreach ($maybeUsingVarNodes as $varNode) {
            $maybeDefiningExprs = $varNode->getAttribute('maybe_defining_exprs', array());
            if (in_array($defExpr, $maybeDefiningExprs, true)) {
                continue;
            }

            $maybeDefiningExprs[] = $defExpr;
            $varNode->setAttribute('maybe_defining_exprs', $maybeDefiningExprs);
        }
    }

    private function addToUseIfLocal(\PHPParser_Node $varNode, \PHPParser_Node $cfgNode, UseLattice $uses)
    {
        if ( ! NodeUtil::isName($varNode)) {
            return;
        }
        $name = $varNode->name;

        $var = $this->scope->getVar($name);
        if (null === $var) {
            return;
        }

        $uses->addUse($var, $cfgNode, $varNode);
    }

    private function removeFromUseIfLocal($name, UseLattice $uses)
    {
        $var = $this->scope->getVar($name);
        if (null === $var) {
            return;
        }

        unset($uses[$var]);
    }
}