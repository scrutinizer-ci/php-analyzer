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

namespace Scrutinizer\PhpAnalyzer\ControlFlow;

use JMS\PhpManipulator\PhpParser\BlockNode;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil;
use Scrutinizer\PhpAnalyzer\PhpParser\Traversal\CallbackInterface;

/**
 * Creates the control flow graph for PHP code.
 *
 * The graph is computed in a single pass without performing inter-procedural flow analysis.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
class ControlFlowAnalysis implements CallbackInterface
{
    /** @var ControlFlowGraph */
    private $graph;
    
    /** @var integer */
    private $astPositionCounter;
    
    /** @var integer */
    private $priorityCounter;

    /** @var \PHPParser_Node */
    private $root;
    
    /** @var \SplStack<\PHPParser_Node> */
    private $exceptionHandlers;

    /**
     * Computes the control flow graph starting at the given root node.
     * 
     * @param \PHPParser_Node $node
     * 
     * @return ControlFlowGraph
     */
    public static function computeGraph(\PHPParser_Node $node)
    {
        $cfa = new self();
        $cfa->process($node);

        return $cfa->getGraph();
    }

    /**
     * Computes the fall-through node for the given node.
     * 
     * Most of the time, this will equal the given node itself. Notable exceptions are
     * FOR, and DO nodes.
     *
     * @return \PHPParser_Node
     */
    private static function computeFallThrough(\PHPParser_Node $node) {
        if ($node instanceof \PHPParser_Node_Stmt_For && $node->init) {
            return self::computeFallThrough($node->init[0]);
        }

        if ($node instanceof \PHPParser_Node_Stmt_Do) {
            return self::computeFallThrough($node->stmts);
        }

        return $node;
    }

    public function getGraph()
    {
        return $this->graph;
    }

    public function process(\PHPParser_Node $root)
    {
        $this->root = $root;
        $this->astPositionCounter = 0;
        $this->exceptionHandlers = new \SplStack();

        $this->graph = new ControlFlowGraph(self::computeFallThrough($root));
        NodeTraversal::traverseWithCallback($root, $this);

        $this->assignPriorities();
    }

    /**
     * This method will stop traversal depending on the parent node.
     *
     * Principally, we are only interested in adding edges between nodes that change control flow. Notable ones are
     * loops (WHILE, FOR, etc.) and IF-ELSE statements; other statements typically transfer control to their next sibling.
     *
     * With regard to expression trees, we currently do not perform any sort of control flow within them, even if there
     * are short circuiting operators or conditionals. Instead, we synthesize lattices up when performing data flow
     * analysis by finding the meet at each expression node.
     *
     * @param NodeTraversal $t
     * @param \PHPParser_Node $node
     * @param \PHPParser_Node $parent
     *
     * @return boolean
     */
    public function shouldTraverse(NodeTraversal $t, \PHPParser_Node $node, \PHPParser_Node $parent = null)
    {
        $node->setAttribute('position', $this->astPositionCounter++);

        if ($node instanceof \PHPParser_Node_Stmt_TryCatch) {
            $this->exceptionHandlers->push($node);

            return true;
        }

        if (null !== $parent) {
            // Do not traverse into classes, interfaces, or traits as control flow never gets passed in.
            if (NodeUtil::isMethodContainer($parent)) {
                return false;
            }

            // Generally, the above also applies to closures except when they are the scope's root.
            if (NodeUtil::isScopeCreator($parent) && $parent !== $this->root) {
                return false;
            }

            // Skip conditions.
            if ($parent instanceof \PHPParser_Node_Stmt_For || $parent instanceof \PHPParser_Node_Stmt_Foreach) {
                return $parent->stmts === $node;
            }

            // Skip conditions.
            if ($parent instanceof \PHPParser_Node_Stmt_If
                    || $parent instanceof \PHPParser_Node_Stmt_ElseIf
                    || $parent instanceof \PHPParser_Node_Stmt_While
                    || $parent instanceof \PHPParser_Node_Stmt_Do
                    || $parent instanceof \PHPParser_Node_Stmt_Switch
                    || $parent instanceof \PHPParser_Node_Stmt_Case) {
                return $parent->cond !== $node;
            }

            // Skip exception type.
            if ($parent instanceof \PHPParser_Node_Stmt_Catch) {
                return $parent->stmts === $node;
            }

            // Skip expressions, see above.
            if ($parent instanceof \PHPParser_Node_Expr && ! $parent instanceof \PHPParser_Node_Expr_Closure) {
                return false;
            }

            if ($parent instanceof \PHPParser_Node_Stmt_Continue
                    || $parent instanceof \PHPParser_Node_Stmt_Break
                    || $parent instanceof \PHPParser_Node_Stmt_Return
                    || $parent instanceof \PHPParser_Node_Stmt_Echo
                    || $parent instanceof \PHPParser_Node_Stmt_Use
                    || $parent instanceof \PHPParser_Node_Stmt_Unset
                    || $parent instanceof \PHPParser_Node_Stmt_Declare
                    || $parent instanceof \PHPParser_Node_Stmt_Global
                    || $parent instanceof \PHPParser_Node_Stmt_Static
                    || $parent instanceof \PHPParser_Node_Stmt_StaticVar
                    || $parent instanceof \PHPParser_Node_Stmt_Throw) {
                return false;
            }

            // Skip parameters.
            if (NodeUtil::isScopeCreator($parent) && $node !== $parent->stmts) {
                return false;
            }

            // If we are reaching the CATCH, or FINALLY node, they current exception handler cannot catch anymore
            // exceptions, and we therefore can remove it.
            // TODO: Add Support for FINALLY (PHP 5.5)
            if ($parent instanceof \PHPParser_Node_Stmt_TryCatch && $parent->catches[0] === $node
                    && $parent === $this->exceptionHandlers->top()) {
                $this->exceptionHandlers->pop();
            }
        }

        return true;
    }

    private function assignPriorities()
    {
        $this->priorityCounter = 0;
        $entry = $this->graph->getEntryPoint();
        $this->prioritizeFromEntryNode($entry);

        // At this point, all reachable nodes have been given a priority. There might still unreachable nodes which do
        // not yet have been assigned a priority. Presumably, it does not matter anyway what priority they get since
        // should not happen in real code.
        foreach ($nodes = $this->graph->getNodes() as $astNode) {
            /** @var $candidate GraphNode */
            $candidate = $nodes[$astNode];
            if (null !== $candidate->getAttribute('priority')) {
                continue;
            }

            $candidate->setAttribute('priority', ++$this->priorityCounter);
        }

        // The implicit return is always last.
        $this->graph->getImplicitReturn()->setAttribute('priority', ++$this->priorityCounter);
    }

    private function prioritizeFromEntryNode(GraphNode $entry)
    {
        $worklist = array();
        $worklist[] = array($entry, $entry->getAstNode()->getAttribute('position', 0));

        while ( ! empty($worklist)) {
            list($current,) = array_shift($worklist);
            /** @var $current GraphNode */

            if (null !== $current->getAttribute('priority')) {
                continue;
            }

            $current->setAttribute('priority', ++$this->priorityCounter);
            foreach ($current->getOutEdges() as $edge) {
                /** @var $edge GraphEdge */
                $destNode = $edge->getDest();

                // Implicit return is always the last node.
                if ($this->graph->isImplicitReturn($destNode)) {
                    $position = $this->astPositionCounter + 1;
                } else {
                    $position = $destNode->getAstNode()->getAttribute('position');
                    if (null === $position) {
                        throw new \RuntimeException(NodeUtil::getStringRepr($destNode->getAstNode()).' has no position.');
                    }
                }

                $worklist[] = array($destNode, $position);
            }

            usort($worklist, function($a, $b) {
                return $a[1] - $b[1];
            });
        }
    }

    public function visit(NodeTraversal $t, \PHPParser_Node $node, \PHPParser_Node $parent = null)
    {
        switch (true) {
            case NodeUtil::isScopeCreator($node):
                $this->handleScopeCreator($node);
                break;

            case $node instanceof \PHPParser_Node_Stmt_Namespace:
                $this->handleNamespace($node);
                break;

            case $node instanceof \PHPParser_Node_Stmt_If:
                $this->handleIf($node);
                break;

            case $node instanceof \PHPParser_Node_Stmt_Else:
            case $node instanceof \PHPParser_Node_Stmt_ElseIf:
                // these are already handled by the IF statement
                return;

            case $node instanceof \PHPParser_Node_Stmt_While:
                $this->handleWhile($node);
                break;

            case $node instanceof \PHPParser_Node_Stmt_Do:
                $this->handleDo($node);
                break;

            case $node instanceof \PHPParser_Node_Stmt_For:
                $this->handleFor($node);
                break;

            case $node instanceof \PHPParser_Node_Stmt_Foreach:
                $this->handleForeach($node);
                break;

            case $node instanceof \PHPParser_Node_Stmt_Switch:
                $this->handleSwitch($node);
                break;

            case $node instanceof \PHPParser_Node_Stmt_Case:
                $this->handleCase($node);
                break;

            case $node instanceof BlockNode:
                $this->handleStmtList($node);
                break;

            case $node instanceof \PHPParser_Node_Stmt_Throw:
                $this->handleThrow($node);
                break;

            case $node instanceof \PHPParser_Node_Stmt_TryCatch:
                $this->handleTryCatch($node);
                break;

            case $node instanceof \PHPParser_Node_Stmt_Catch:
                $this->handleCatch($node);
                break;

            case $node instanceof \PHPParser_Node_Stmt_Break:
                $this->handleBreak($node);
                break;

            case $node instanceof \PHPParser_Node_Stmt_Continue:
                $this->handleContinue($node);
                break;

            case $node instanceof \PHPParser_Node_Stmt_Return:
                $this->handleReturn($node);
                break;

            case $node instanceof \PHPParser_Node_Stmt_Goto:
                $this->handleGoto($node);
                break;

            case $node instanceof \PHPParser_Node_Stmt:
                $this->handleStmt($node);
                break;
            
            case $node instanceof \PHPParser_Node_Expr_Exit:
                $this->handleExit($node);
                break;

            case $node instanceof \PHPParser_Node_Expr:
                $this->handleExpr($node);
                break;
        }
    }
    
    private function handleExit(\PHPParser_Node_Expr_Exit $node)
    {
        $this->graph->connectToImplicitReturn($node, GraphEdge::TYPE_UNCOND);
    }

    private function handleExpr(\PHPParser_Node_Expr $node)
    {
        $this->graph->connectIfNotConnected($node, GraphEdge::TYPE_UNCOND, $this->computeFollowNode($node));
        $this->connectToPossibleExceptionHandler($node, $node);
    }

    private function handleNamespace(\PHPParser_Node_Stmt_Namespace $node)
    {
        $this->graph->connectIfNotConnected($node, GraphEdge::TYPE_UNCOND, $node->stmts);
    }

    private function handleScopeCreator(\PHPParser_node $node)
    {
        // Go to next sibling unless the node is the scope's root.
        if ($node !== $this->root) {
            $this->graph->connectIfNotConnected($node, GraphEdge::TYPE_UNCOND, $this->computeFollowNode($node));

            return;
        }

        $this->graph->connectIfNotConnected($node, GraphEdge::TYPE_UNCOND, $node->stmts);
    }

    private function handleGoto(\PHPParser_Node_Stmt_Goto $node)
    {
        $parent = $node->getAttribute('parent');

        while ( ! NodeUtil::isScopeCreator($parent)) {
            $newParent = $parent->getAttribute('parent');
            if (null === $newParent) {
                break;
            }

            $parent = $newParent;
        }

        $nodes = \PHPParser_Finder::create(array($parent))->find('Stmt_Label[name='.$node->name.']');
        if (!$nodes) {
            return;
        }

        $this->graph->connectIfNotConnected($node, GraphEdge::TYPE_UNCOND, $nodes[0]);
    }

    private function handleStmtList(BlockNode $node)
    {
        foreach ($node as $child) {
            if ($child instanceof \PHPParser_Node) {
                $this->graph->connectIfNotConnected($node, GraphEdge::TYPE_UNCOND, self::computeFallThrough($child));

                return;
            }
        }

        $this->graph->connectIfNotConnected($node, GraphEdge::TYPE_UNCOND, $this->computeFollowNode($node));
    }

    private function handleStmt(\PHPParser_Node $node)
    {
        $this->graph->connectIfNotConnected($node, GraphEdge::TYPE_UNCOND, $this->computeFollowNode($node));
        $this->connectToPossibleExceptionHandler($node, $node);
    }

    private function handleTryCatch(\PHPParser_Node_Stmt_TryCatch $node)
    {
        $this->graph->connectIfNotConnected($node, GraphEdge::TYPE_UNCOND, self::computeFallThrough($node->stmts));

        for ($i=0,$c=count($node->catches); $i<$c; $i++) {
            $isLast = $i+1 === $c;

            if ($isLast) {
                if (false === $this->exceptionHandlers->isEmpty()) {
                    $this->graph->connectIfNotConnected($node->catches[$i], GraphEdge::TYPE_ON_FALSE, $this->exceptionHandlers->top()->catches[0]);
                }

                continue;
            }

            $this->graph->connectIfNotConnected($node->catches[$i], GraphEdge::TYPE_ON_FALSE, $node->catches[$i+1]);
        }
    }

    private function handleCatch(\PHPParser_Node_Stmt_Catch $node)
    {
        $this->graph->connectIfNotConnected($node, GraphEdge::TYPE_ON_TRUE, self::computeFallThrough($node->stmts));
    }

    private function handleBreak(\PHPParser_Node_Stmt_Break $node)
    {
        if (null === $parent = $this->findContinueOrBreakTarget($node)) {
            // The BREAK target was not found. This normally indicates some sort of error, but
            // could also mean that the number of break levels was dynamic (on some versions of PHP).
            return;
        }

        $this->graph->connectIfNotConnected($node, GraphEdge::TYPE_UNCOND, $this->computeFollowNode($parent));
    }

    private function handleContinue(\PHPParser_Node_Stmt_Continue $node)
    {
        if (null === $parent = $this->findContinueOrBreakTarget($node)) {
            // Same as as for BREAK, see above.
            return;
        }

        if ($parent instanceof \PHPParser_Node_Stmt_For && $parent->loop) {
            $this->graph->connectIfNotConnected($node, GraphEdge::TYPE_UNCOND, $parent->loop[0]);

            return;
        }

        // continue has the same effect like break on a SWITCH statement.
        if ($parent instanceof \PHPParser_Node_Stmt_Switch) {
            $this->graph->connectIfNotConnected($node, GraphEdge::TYPE_UNCOND, $this->computeFollowNode($parent));

            return;
        }

        // By default, just connect to the parent which will handle the continuing flow.
        $this->graph->connectIfNotConnected($node, GraphEdge::TYPE_UNCOND, $parent);
    }

    private function findContinueOrBreakTarget(\PHPParser_Node $node)
    {
        if (null !== $node->num && !$node->num instanceof \PHPParser_Node_Scalar_LNumber) {
            return null;
        }

        // Continously look up the ancestor tree for the BREAK target, and connect to it.
        $curNb = 0;
        $num = null === $node->num ? 1 : $node->num->value;
        if (0 === $num) {
            $num = 1;
        }

        $parent = $node->getAttribute('parent');
        while (null !== $parent) {
            if ($parent instanceof \PHPParser_Node_Stmt_For
                    || $parent instanceof \PHPParser_Node_Stmt_Foreach
                    || $parent instanceof \PHPParser_Node_Stmt_While
                    || $parent instanceof \PHPParser_Node_Stmt_Do
                    || $parent instanceof \PHPParser_Node_Stmt_Switch) {
                $curNb += 1;

                if ($curNb >= $num) {
                    break;
                }
            }

            $parent = $parent->getAttribute('parent');
        }

        return $parent;
    }

    private function handleReturn(\PHPParser_Node_Stmt_Return $node)
    {
        $this->graph->connectIfNotConnected($node, GraphEdge::TYPE_UNCOND, null);

        if ($node->expr) {
            $this->connectToPossibleExceptionHandler($node, $node->expr);
        }
    }

    private function handleThrow(\PHPParser_Node_Stmt_Throw $node)
    {
        $this->connectToPossibleExceptionHandler($node, $node);
    }

    private function handleSwitch(\PHPParser_Node_Stmt_Switch $node)
    {
        // Transfer to the first non-DEFAULT CASE. If there are none, transfer
        // to the DEFAULT.
        if ($node->cases) {
            $this->graph->connectIfNotConnected($node, GraphEdge::TYPE_UNCOND, $node->cases[0]);
        } else {
            $this->graph->connectIfNotConnected($node, GraphEdge::TYPE_UNCOND, $this->computeFollowNode($node));
        }

        $this->connectToPossibleExceptionHandler($node, $node->cond);
    }

    private function handleCase(\PHPParser_Node_Stmt_Case $node)
    {
        if (null !== $node->cond) {
            // Case is a bit tricky... First it goes into the body if condition is true.
            $this->graph->connectIfNotConnected($node, GraphEdge::TYPE_ON_TRUE, $node->stmts);

            // Look for the next CASE
            $next = $node->getAttribute('next');
            while (null !== $next && !$next instanceof \PHPParser_Node_Stmt_Case) {
                $next = $next->getAttribute('next');
            }

            if (null !== $next) {
                $this->graph->connectIfNotConnected($node, GraphEdge::TYPE_ON_FALSE, $next);
            } else { // No more cases, and no default found; go to the follow of SWITCH
                $this->graph->connectIfNotConnected($node, GraphEdge::TYPE_ON_FALSE, $this->computeFollowNode($node));
            }

            $this->connectToPossibleExceptionHandler($node, $node->cond);

            return;
        }

        // DEFAULT CASE
        $this->graph->connectIfNotConnected($node, GraphEdge::TYPE_UNCOND, self::computeFallThrough($node->stmts));
    }

    /**
     * Handles a for (init; cond; iter) { body }
     *
     * @param \PHPParser_Node_Stmt_For $node
     */
    private function handleFor(\PHPParser_Node_Stmt_For $node)
    {
        if ($node->init) {
            // After initialization, we transfer to the FOR which is in charge of
            // checking the condition (for the first time).
            for ($i=0, $c=count($node->init); $i<$c; $i++) {
                if ($i+1 === $c) { // The last init condition is connected to the FOR loop.
                    $this->graph->connectIfNotConnected($node->init[$i], GraphEdge::TYPE_UNCOND, $node);
                } else {
                    $this->graph->connectIfNotConnected($node->init[$i], GraphEdge::TYPE_UNCOND, $node->init[$i+1]);
                }

                $this->connectToPossibleExceptionHandler($node->init[$i], $node->init[$i]);
            }
        }

        // The edge that transfer control to the beginning of the loop body.
        $this->graph->connectIfNotConnected($node, GraphEdge::TYPE_ON_TRUE, self::computeFallThrough($node->stmts));

        // the edge to the end of the loop.
        $this->graph->connectIfNotConnected($node, GraphEdge::TYPE_ON_FALSE, $this->computeFollowNode($node));

        if ($node->loop) {
            // The end of the body will have a unconditional branch to our iter
            // (handled by calling computeFollowNode of the last instruction of the body. Our iter will jump to the forNode
            // again to another condition check.)
            $this->graph->connectIfNotConnected($node->loop[0], GraphEdge::TYPE_UNCOND, $node);

            foreach ($node->loop as $loop) {
                $this->connectToPossibleExceptionHandler($loop, $loop);
            }
        }

        foreach ($node->cond as $cond) {
            $this->connectToPossibleExceptionHandler($node, $cond);
        }
    }

    private function handleForeach(\PHPParser_Node_Stmt_Foreach $node)
    {
        // The edge that transfer control to the beginning of the loop body.
        $this->graph->connectIfNotConnected($node, GraphEdge::TYPE_ON_TRUE, self::computeFallThrough($node->stmts));

        // the edge to the end of the loop
        $this->graph->connectIfNotConnected($node, GraphEdge::TYPE_ON_FALSE, $this->computeFollowNode($node));

        $this->connectToPossibleExceptionHandler($node, $node->expr);
    }

    private function handleWhile(\PHPParser_Node_Stmt_While $node)
    {
        // Control goes to the first statement if the condition evaluates to true.
        $this->graph->connectIfNotConnected($node, GraphEdge::TYPE_ON_TRUE, self::computeFallThrough($node->stmts));

        // Control goes to the following node if the condition evaluates to false.
        $this->graph->connectIfNotConnected($node, GraphEdge::TYPE_ON_FALSE, $this->computeFollowNode($node));
        $this->connectToPossibleExceptionHandler($node, $node->cond);
    }

    private function handleDo(\PHPParser_Node_Stmt_Do $node)
    {
        // The first edge can be the initial iteration as well as the iterations after.
        $this->graph->connectIfNotConnected($node, GraphEdge::TYPE_ON_TRUE, self::computeFallThrough($node->stmts));

        // the edge that leaves the do loop if the condition fails.
        $this->graph->connectIfNotConnected($node, GraphEdge::TYPE_ON_FALSE, $this->computeFollowNode($node));
        $this->connectToPossibleExceptionHandler($node, $node->cond);
    }

    private function handleIf(\PHPParser_Node_Stmt_If $node)
    {
        $this->graph->connectIfNotConnected($node, GraphEdge::TYPE_ON_TRUE, self::computeFallThrough($node->stmts));

        $lastIf = $node;

        if ($node->elseifs) {
            foreach ($node->elseifs as $elseIf) {
                $this->graph->connectIfNotConnected($lastIf, GraphEdge::TYPE_ON_FALSE, $elseIf);
                $this->graph->connectIfNotConnected($elseIf, GraphEdge::TYPE_ON_TRUE, self::computeFallThrough($elseIf->stmts));
                $lastIf = $elseIf;
            }
        }

        if (null === $node->else) {
            $this->graph->connectIfNotConnected($lastIf, GraphEdge::TYPE_ON_FALSE, $this->computeFollowNode($node));
        } else {
            $this->graph->connectIfNotConnected($lastIf, GraphEdge::TYPE_ON_FALSE, self::computeFallThrough($node->else->stmts));
        }

        $this->connectToPossibleExceptionHandler($node, $node->cond);
    }

    /**
     * Determines if the subtree might throw an exception.
     *
     * @return boolean
     */
    public static function mayThrowException(\PHPParser_Node $node = null)
    {
        if (null === $node) {
            return false;
        }

        // TODO: This list is probably incomplete
        if ($node instanceof \PHPParser_Node_Expr_StaticCall
                || $node instanceof \PHPParser_Node_Expr_FuncCall
                || $node instanceof \PHPParser_Node_Expr_MethodCall
                || $node instanceof \PHPParser_Node_Expr_New
                || $node instanceof \PHPParser_Node_Stmt_Throw
                || $node instanceof \PHPParser_Node_Expr_Include
                || $node instanceof \PHPParser_Node_Expr_Clone) {
            return true;
        }

        if ($node instanceof \PHPParser_Node_Expr_Closure) {
            return false;
        }

        if ($node instanceof \PHPParser_Node_Expr_Array) {
            foreach ($node->items as $item) {
                if (self::mayThrowException($item->key) || self::mayThrowException($item->value)) {
                    return true;
                }
            }

            return false;
        }

        foreach ($node as $subNode) {
            if (!$subNode instanceof \PHPParser_Node) {
                continue;
            }

            if (is_array($subNode)) {
                foreach ($subNode as $aSubNode) {
                    if (!$aSubNode instanceof \PHPParser_Node) {
                        continue;
                    }

                    if (self::mayThrowException($aSubNode)) {
                        return true;
                    }
                }

                continue;
            }

            if (self::mayThrowException($subNode)) {
                return true;
            }
        }

        return false;
    }

    private function connectToPossibleExceptionHandler(\PHPParser_Node $cfgNode, \PHPParser_Node $target) {
        if (!self::mayThrowException($target)) {
            return;
        }

        if ($this->exceptionHandlers->isEmpty()) {
            return;
        }

        foreach ($this->exceptionHandlers as $handler) {
            assert($handler instanceof \PHPParser_Node_Stmt_TryCatch);

            $catches = $handler->catches;
            $this->graph->connectIfNotConnected($cfgNode, GraphEdge::TYPE_ON_EX, $catches[0]);
            break;
        }
    }

    /**
     * @param \PHPParser_Node $fromNode The original source node
     * @param \PHPParser_Node $node The node for which follow should be computed
     */
    private function computeFollowNode(\PHPParser_Node $fromNode, \PHPParser_Node $node = null)
    {
        if (null === $node) {
            $node = $fromNode;
        }

       /*
        * This is the case where:
        *
        * 1. Parent is null implies that we are transferring control to the end of
        * the script.
        *
        * 2. Parent is a function implies that we are transferring control back to
        * the caller of the function.
        *
        * 3. If the node is a return statement, we should also transfer control
        * back to the caller of the function.
        *
        * 4. If the node is root then we have reached the end of what we have been
        * asked to traverse.
        *
        * In all cases we should transfer control to a "symbolic return" node.
        * This will make life easier for DFAs.
        */
        $parent = $node->getAttribute('parent');
        if (null === $parent
                || $parent instanceof \PHPParser_Node_Stmt_ClassMethod
                || $parent instanceof \PHPParser_Node_Stmt_Function
                || $parent instanceof \PHPParser_Node_Expr_Closure
                || $node === $this->root) {
            return null;
        }

        // If we are just before a IF/WHILE/DO/FOR:
        switch (true) {
            // The follow() of any of the path from IF would be what follows IF.
            case $parent instanceof \PHPParser_Node_Stmt_If:
                return $this->computeFollowNode($fromNode, $parent);

            case $parent instanceof \PHPParser_Node_Stmt_Case:
                // After the body of a CASE, the control goes to the body of the next
                // CASE, without having to go to the next CASE condition.
                $next = $parent->getAttribute('next');
                while (null !== $next && !$next instanceof \PHPParser_Node_Stmt_Case) {
                    $next = $next->getAttribute('next');
                }

                if (null !== $next) {
                    return $next->stmts;
                }

                return $this->computeFollowNode($fromNode, $parent);

            case $parent instanceof \PHPParser_Node_Stmt_Foreach:
            case $parent instanceof \PHPParser_Node_Stmt_While:
            case $parent instanceof \PHPParser_Node_Stmt_Do:
                return $parent;

            case $parent instanceof \PHPParser_Node_Stmt_For:
                if ($parent->loop) {
                    return $parent->loop[0];
                }

                return $this->computeFollowNode($fromNode, $parent);

            // TODO: Add support fo FINALLY.
            case $parent instanceof \PHPParser_Node_Stmt_TryCatch:
                return $this->computeFollowNode($fromNode, $parent);
        }

        $nextSibling = $node->getAttribute('next');
        if (null !== $nextSibling) {
            return self::computeFallThrough($nextSibling);
        }

        // If there are no more siblings, control is transfered up the AST.
        return $this->computeFollowNode($fromNode, $parent);
    }
}