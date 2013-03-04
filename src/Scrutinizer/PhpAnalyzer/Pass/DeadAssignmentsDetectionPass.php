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

namespace Scrutinizer\PhpAnalyzer\Pass;

use Scrutinizer\PhpAnalyzer\Config\NodeBuilder;
use Scrutinizer\PhpAnalyzer\ControlFlow\ControlFlowGraph;
use Scrutinizer\PhpAnalyzer\DataFlow\DataFlowAnalysis;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil;
use Scrutinizer\PhpAnalyzer\DataFlow\LiveVariableAnalysis\LiveVariablesAnalysis;
use Scrutinizer\PhpAnalyzer\Model\Comment;

/**
 * Dead Assignments Detection
 *
 * This pass performs a backward dataflow analysis, commonly referred to as
 * `live variable analysis <http://en.wikipedia.org/wiki/Live_variable_analysis>`_, to
 * detect dead assignments::
 *
 *     function foo($foo) {
 *         $a = 'foo'; // $a is never used, it's assignment is "dead".
 *
 *         echo $foo;
 *     }
 *
 * It also detects dead assignments in ``list()`` assignments.
 *
 * @category checks
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class DeadAssignmentsDetectionPass extends AstAnalyzerPass implements ConfigurablePassInterface
{
    use ConfigurableTrait;

    private $liveness;

    public function getConfiguration()
    {
        $tb = new \Symfony\Component\Config\Definition\Builder\TreeBuilder();
        $tb->root('dead_assignments', 'array', new NodeBuilder())
            ->attribute('label', 'Dead Assignment Detection')
            ->canBeDisabled()
        ;

        return $tb;
    }

    protected function isEnabled()
    {
        return true === $this->getSetting('enabled');
    }

    public function enterScope(NodeTraversal $t)
    {
        $scope = $t->getScope();
        $cfg = $t->getControlFlowGraph();

        $this->liveness = new LiveVariablesAnalysis($cfg, $scope);
        $this->liveness->analyze();

        $this->tryRemoveDeadAssignments($t, $cfg);
    }

    private function tryRemoveDeadAssignments(NodeTraversal $t, ControlFlowGraph $cfg)
    {
        $nodes = $cfg->getDirectedGraphNodes();

        foreach ($nodes as $cfgNode) {
            $inState = $cfgNode->getAttribute(DataFlowAnalysis::ATTR_FLOW_STATE_IN);
            $outState = $cfgNode->getAttribute(DataFlowAnalysis::ATTR_FLOW_STATE_OUT);
            $n = $cfgNode->getAstNode();

            if (null === $n) {
                continue;
            }

            switch (true) {
                case $n instanceof \PHPParser_Node_Stmt_If:
                case $n instanceof \PHPParser_Node_Stmt_ElseIf:
                case $n instanceof \PHPParser_Node_Stmt_While:
                case $n instanceof \PHPParser_Node_Stmt_Do:
                case $n instanceof \PHPParser_Node_Stmt_For:
                case $n instanceof \PHPParser_Node_Stmt_Switch:
                case $n instanceof \PHPParser_Node_Stmt_Case:
                    if (null !== $condition = NodeUtil::getConditionExpression($n)) {
                        $this->tryRemoveAssignment($t, $condition, null, $inState, $outState);
                    }
                    continue 2;

                case $n instanceof \PHPParser_Node_Stmt_Return:
                    if (null !== $n->expr) {
                        $this->tryRemoveAssignment($t, $n->expr, null, $inState, $outState);
                    }
                    continue 2;
            }

            $this->tryRemoveAssignment($t, $n, null, $inState, $outState);
        }
    }

    private function tryRemoveAssignment(NodeTraversal $t, \PHPParser_Node $n, \PHPParser_Node $exprRoot = null, $inState, $outState)
    {
        $parent = $n->getAttribute('parent');

        if (NodeUtil::isAssignmentOp($n)) {
            $lhs = $n->var;
            $rhs = $n->expr;

            if ($n instanceof \PHPParser_Node_Expr_AssignList) {
                $i = 0;
                foreach ($n->vars as $var) {
                    if (null === $var) {
                        $i += 1;

                        continue;
                    }

                    $newNode = new \PHPParser_Node_Expr_Assign($var, new \PHPParser_Node_Expr_ArrayDimFetch($rhs, new \PHPParser_Node_Scalar_LNumber($i)), $n->getLine());
                    $newNode->setAttribute('is_list_assign', true);

                    $this->tryRemoveAssignment($t,
                        $newNode,
                        $exprRoot,
                        $inState,
                        $outState);
                    $i += 1;
                }

                return;
            }

            // Recurse first. Example: dead_x = dead_y = 1; We try to clean up dead_y first.
            if (null !== $rhs) {
                $this->tryRemoveAssignment($t, $rhs, $exprRoot, $inState, $outState);
                $rhs = $lhs->getAttribute('next');
            }

            $scope = $t->getScope();
            if (!$lhs instanceof \PHPParser_Node_Expr_Variable
                    || !is_string($lhs->name)) {
                return;
            }

            if (!$scope->isDeclared($lhs->name)) {
                return;
            }

            if (in_array($lhs->name, NodeUtil::$superGlobalNames, true)) {
                return;
            }

            $var = $scope->getVar($lhs->name);
            $escaped = $this->liveness->getEscapedLocals();
            if (isset($escaped[$var])) {
                return; // Local variable that might be escaped due to closures.
            }

            // If we have an identity assignment such as a=a, always remove it
            // regardless of what the liveness results because it does not
            // change the result afterward.
            if (null !== $rhs && $rhs instanceof \PHPParser_Node_Expr_Variable
                    && $var->getName() === $rhs->name
                    && $n instanceof \PHPParser_Node_Expr_Assign) {
                $this->phpFile->addComment($n->getLine(), Comment::warning(
                    'dead_assignment.assignment_to_itself',
                    'Why assign ``%variable%`` to itself?',
                    array('variable' => '$'.$rhs->name)));

                return;
            }

            // At the moment we miss some dead assignments if a variable is killed,
            // and defined in the same node of the control flow graph.
            // Example: $a = 'foo'; return $a = 'bar';

            if ($outState->isLive($var)) {
                return; // Variable is not dead.
            }

            // Assignments to references do not need to be used in the
            // current scope.
            if ($var->isReference()) {
                return;
            }

//             if ($inState->isLive($var)
//                    /*&& $this->isVariableStillLiveWithinExpression($n, $exprRoot, $var->getName()) */) {
                // The variable is killed here but it is also live before it.
                // This is possible if we have say:
                //    if ($X = $a && $a = $C) {..} ; .......; $a = $S;
                // In this case we are safe to remove "$a = $C" because it is dead.
                // However if we have:
                //    if ($a = $C && $X = $a) {..} ; .......; $a = $S;
                // removing "a = C" is NOT correct, although the live set at the node
                // is exactly the same.
                // TODO: We need more fine grain CFA or we need to keep track
                // of GEN sets when we recurse here. Maybe add the comment anyway, and let the user decide?
//                 return;
//             }

            if ($n instanceof \PHPParser_Node_Expr_Assign) {
                if ($n->getAttribute('is_list_assign', false)) {
                    $this->phpFile->addComment($n->getLine(), Comment::warning(
                        'dead_assignment.unnecessary_list_assign',
                        'The assignment to ``$%variable%`` is dead. Consider omitting it like so ``list($first,,$third)``.',
                        array('variable' => $var->getName())));

                    return;
                }

                $this->phpFile->addComment($n->getLine(), Comment::warning(
                    'dead_assignment.unnecessary_var_assign',
                    'The assignment to ``$%variable%`` is dead and can be removed.',
                    array('variable' => $var->getName())));

                return;
            }
        }
    }
}