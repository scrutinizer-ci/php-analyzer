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

use Psr\Log\LoggerInterface;
use Scrutinizer\PhpAnalyzer\ControlFlow\ControlFlowGraph;
use Scrutinizer\PhpAnalyzer\ControlFlow\GraphEdge;
use Scrutinizer\PhpAnalyzer\DataFlow\BinaryJoinOp;
use Scrutinizer\PhpAnalyzer\DataFlow\BranchedForwardDataFlowAnalysis;
use Scrutinizer\PhpAnalyzer\DataFlow\LatticeElementInterface;
use JMS\PhpManipulator\PhpParser\BlockNode;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil;
use Scrutinizer\PhpAnalyzer\PhpParser\PreOrderCallback;
use Scrutinizer\PhpAnalyzer\PhpParser\Scope\Scope;

/**
 * Calculates must-be reachability of variable definitions.
 *
 * It allows us to trace back each variable to its defining statement.
 *
 * In contrast to a maybe-reaching analysis, this analysis only takes paths into account which we can determine for sure,
 * and where no alternative paths exist.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class MustBeReachingDefAnalysis extends BranchedForwardDataFlowAnalysis
{
    /** The scope of the function/method we are analyzing. */
    private $scope;

    private $logger;

    /**
     * Returns the join operation for this data-flow analysis.
     *
     * The algorithm simply checks whether the definition for a variable differs in both lattices.
     * If that is the case, we reset the definition of the variable as it means that the variable
     * has more than one possible definition.
     *
     * @return callable
     */
    public static function createJoinOperation()
    {
        return new BinaryJoinOp(function(DefinitionLattice $a, DefinitionLattice $b) {
            $result = new DefinitionLattice();

            foreach ($a as $var) {
                if (null === $a[$var]) {
                    $result[$var] = null;
                    continue;
                }

                if ( ! isset($b[$var])) {
                    $result[$var] = $a[$var];
                    continue;
                }

                if (null === $b[$var] || false === $a[$var]->equals($b[$var])) {
                    $result[$var] = null;
                    continue;
                }

                $result[$var] = $a[$var];
            }

            foreach ($b as $var) {
                if (isset($result[$var])) {
                    continue;
                }

                $result[$var] = $b[$var];
            }

            return $result;
        });
    }

    public function __construct(ControlFlowGraph $cfg, Scope $scope)
    {
        parent::__construct($cfg, self::createJoinOperation());

        $this->scope = $scope;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    protected function createEntryLattice()
    {
        return new DefinitionLattice($this->scope->getVars());
    }

    protected function createInitialEstimateLattice()
    {
        return new DefinitionLattice();
    }

    /**
     * Returns the defining node for a given variable.
     *
     * @param string $varName The name of the variable to get the definition for.
     * @param \PHPParser_Node $usageNode The cfg node where the variable is used.
     *
     * @return \PHPParser_Node|null The node that must be defining the variable if available.
     */
    public function getDefiningNode($varName, \PHPParser_Node $usageNode)
    {
        if ( ! $this->cfg->hasNode($usageNode)) {
            throw new \InvalidArgumentException('$usageNode must be part of the control flow graph.');
        }

        if (null === $var = $this->scope->getVar($varName)) {
            return null;
        }

        $graphNode = $this->cfg->getNode($usageNode);
        $definition = $graphNode->getAttribute(self::ATTR_FLOW_STATE_IN)[$var];

        return null === $definition ? null : $definition->getNode();
    }

    protected function branchedFlowThrough($node, LatticeElementInterface $input)
    {
        $result = array();
        $conditionalOutput = null;
        foreach ($this->cfg->getOutEdges($node) as $edge) {
            switch ($edge->getType()) {
                case GraphEdge::TYPE_ON_FALSE:
                case GraphEdge::TYPE_ON_TRUE:
                case GraphEdge::TYPE_ON_EX:
                    if (null === $conditionalOutput) {
                        $conditionalOutput = clone $input;
                        $this->computeMustDef($node, $node, $conditionalOutput, true);
                    }
                    $output = clone $conditionalOutput;
                    break;

                default:
                    $output = clone $input;
                    $this->computeMustDef($node, $node, $output, false);
            }

            $result[] = $output;
        }

        return $result;
    }

    private function computeMustDef(\PHPParser_Node $node, \PHPParser_Node $cfgNode, DefinitionLattice $output, $conditional)
    {
        switch (true) {
            case $node instanceof BlockNode:
            case NodeUtil::isScopeCreator($node);
                return;

            case $node instanceof \PHPParser_Node_Stmt_While:
            case $node instanceof \PHPParser_Node_Stmt_Do:
            case $node instanceof \PHPParser_Node_Stmt_For:
            case $node instanceof \PHPParser_Node_Stmt_If:
                if (null !== $cond = NodeUtil::getConditionExpression($node)) {
                    $this->computeMustDef($cond, $cfgNode, $output, $conditional);
                }

                return;

            case $node instanceof \PHPParser_Node_Stmt_Foreach:
                if (null !== $node->keyVar
                        && $node->keyVar instanceof \PHPParser_Node_Expr_Variable
                        && is_string($node->keyVar->name)) {
                    $this->addToDefIfLocal($node->keyVar->name, $cfgNode, $node->expr, $output);
                }

                if ($node->valueVar instanceof \PHPParser_Node_Expr_Variable
                        && is_string($node->valueVar->name)) {
                    $this->addToDefIfLocal($node->valueVar->name, $cfgNode, $node->expr, $output);
                }

                return;

            case $node instanceof \PHPParser_Node_Stmt_Catch:
                $this->addToDefIfLocal($node->var, $cfgNode, null, $output);
                return;

            case $node instanceof \PHPParser_Node_Expr_BooleanAnd:
            case $node instanceof \PHPParser_Node_Expr_BooleanOr:
            case $node instanceof \PHPParser_Node_Expr_LogicalAnd:
            case $node instanceof \PHPParser_Node_Expr_LogicalOr:
                $this->computeMustDef($node->left, $cfgNode, $output, $conditional);
                $this->computeMustDef($node->right, $cfgNode, $output, true);

                return;

            case $node instanceof \PHPParser_Node_Expr_Ternary:
                $this->computeMustDef($node->cond, $cfgNode, $output, $conditional);
                if (null !== $node->if) {
                    $this->computeMustDef($node->if, $cfgNode, $output, true);
                }
                $this->computeMustDef($node->else, $cfgNode, $output, true);

                return;

            case NodeUtil::isName($node):
                if ((null !== $var = $this->scope->getVar($node->name))
                        && isset($output[$var])
                        && null !== $output[$var]) {
                    $node->setAttribute('defining_expr', $output[$var]->getExpr());
                }

                return;

            default:
                if (NodeUtil::isAssignmentOp($node)) {
                    if ($node instanceof \PHPParser_Node_Expr_AssignList) {
                        $this->computeMustDef($node->expr, $cfgNode, $output, $conditional);

                        foreach ($node->vars as $var) {
                            if (null === $var) {
                                continue;
                            }

                            $this->addToDefIfLocal($var->name, $conditional ? null: $cfgNode, $node->expr, $output);
                        }

                        return;
                    }

                    if (NodeUtil::isName($node->var)) {
                        $this->computeMustDef($node->expr, $cfgNode, $output, $conditional);
                        $this->addToDefIfLocal($node->var->name, $conditional ? null : $cfgNode,
                            $node->expr, $output);

                        return;
                    }
                }

                if (($node instanceof \PHPParser_Node_Expr_PostDec
                        || $node instanceof \PHPParser_Node_Expr_PostInc
                        || $node instanceof \PHPParser_Node_Expr_PreDec
                        || $node instanceof \PHPParser_Node_Expr_PreInc
                        ) && NodeUtil::isName($node->var)) {
                    $this->addToDefIfLocal($node->var->name, $conditional ? null : $cfgNode, null, $output);

                    return;
                }

                foreach ($node as $subNode) {
                    if (is_array($subNode)) {
                        foreach ($subNode as $aSubNode) {
                            if ( ! $aSubNode instanceof \PHPParser_Node) {
                                continue;
                            }

                            $this->computeMustDef($aSubNode, $cfgNode, $output, $conditional);
                        }
                    } else if ($subNode instanceof \PHPParser_Node) {
                        $this->computeMustDef($subNode, $cfgNode, $output, $conditional);
                    }
                }
        }
    }

    private function addToDefIfLocal($name, \PHPParser_Node $node = null, \PHPParser_Node $rValue = null, DefinitionLattice $definitions)
    {
        $var = $this->scope->getVar($name);

        if (null === $var) {
            return;
        }

        // Invalidate other definitions if they depended on this variable as it has been changed.
        foreach ($definitions as $otherVar) {
            if (null === $otherDef = $definitions[$otherVar]) {
                continue;
            }

            if ($otherDef->dependsOn($var)) {
                $definitions[$otherVar] = null;
            }
        }

        // The node can be null if we are dealing with a conditional definition. For conditional
        // definitions, this analysis cannot do much.
        if (null === $node) {
            $definitions[$var] = null;

            return;
        }

        $definition = new Definition($node, $rValue);
        if (null !== $rValue) {
            NodeTraversal::traverseWithCallback($rValue, new PreOrderCallback(function($t, \PHPParser_Node $node) use ($definition) {
                if (NodeUtil::isScopeCreator($node)) {
                    return false;
                }

                if ( ! NodeUtil::isName($node)) {
                    return;
                }

                if (null === $var = $this->scope->getVar($node->name)) {
                    if (null !== $this->logger) {
                        $this->logger->debug(sprintf('Could not find variable "%s" in the current scope. '
                            .'This could imply an error in SyntacticScopeCreator.', $node->name));
                    }

                    // We simply assume that the variable was declared so that we can properly
                    // invalidate the definition if it is changed. For this analysis, it does not
                    // matter whether it actually exists. A later pass will add a warning anyway.
                    $var = $this->scope->declareVar($node->name);
                }

                $definition->addDependentVar($var);
            }));
        }

        $definitions[$var] = $definition;
    }
}
