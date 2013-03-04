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

namespace Scrutinizer\PhpAnalyzer\PhpParser;

use Scrutinizer\PhpAnalyzer\ControlFlow\ControlFlowAnalysis;
use Scrutinizer\PhpAnalyzer\ControlFlow\ControlFlowGraph;
use Scrutinizer\PhpAnalyzer\PhpParser\Scope\Scope;
use Scrutinizer\PhpAnalyzer\PhpParser\Scope\ScopeCreatorInterface;
use Scrutinizer\PhpAnalyzer\PhpParser\Scope\SyntacticScopeCreator;
use Scrutinizer\PhpAnalyzer\PhpParser\Traversal\CallbackInterface;
use Scrutinizer\PhpAnalyzer\PhpParser\Traversal\ScopedCallbackInterface;

class NodeTraversal
{
    private $callback;
    private $curNode;
    private $scopes;
    private $scopeRoots;
    private $cfgs;
    private $scopeCreator;

    public static function traverseWithCallback(\PHPParser_Node $root, CallbackInterface $callback, ScopeCreatorInterface $scopeCreator = null)
    {
        $t = new self($callback, $scopeCreator);
        $t->traverse($root);
    }

    public function __construct(CallbackInterface $callback, ScopeCreatorInterface $scopeCreator = null)
    {
        $this->callback = $callback;
        $this->scopeCreator = $scopeCreator ?: new SyntacticScopeCreator();
        $this->scopes = new \SplStack();
        $this->scopeRoots = new \SplDoublyLinkedList();
        $this->scopeRoots->setIteratorMode(\SplDoublyLinkedList::IT_MODE_LIFO | \SplDoublyLinkedList::IT_MODE_KEEP);
        $this->cfgs = new \SplStack();
    }

    public function getCurrentNode()
    {
        return $this->curNode;
    }

    /**
     * Returns the current scope.
     *
     * @return Scope
     */
    public function getScope()
    {
        $scope = $this->scopes->isEmpty() ? null : $this->scopes->top();
        if ($this->scopeRoots->isEmpty()) {
            return $scope;
        }

        // We create the scopes from bottom to top. It is also necessary to build all scopes which are on the stack
        // since each scope might depend on its predecessor (a Closure might depend on variables of the scope it
        // was declared in).
        $this->scopeRoots->setIteratorMode(\SplDoublyLinkedList::IT_MODE_FIFO | \SplDoublyLinkedList::IT_MODE_DELETE);
        foreach ($this->scopeRoots as $scopeRoot) {
            $scope = $this->scopeCreator->createScope($scopeRoot, $scope);
            $this->scopes->push($scope);
        }
        $this->scopeRoots->setIteratorMode(\SplDoublyLinkedList::IT_MODE_LIFO | \SplDoublyLinkedList::IT_MODE_KEEP);

        return $scope;
    }

    /**
     * @return ControlFlowGraph
     */
    public function getControlFlowGraph()
    {
        if ($cfg = $this->cfgs->top()) {
            return $cfg;
        }

        $cfa = new ControlFlowAnalysis();
        $cfa->process($this->getScopeRoot());
        $this->cfgs->pop();
        $this->cfgs->push($cfg = $cfa->getGraph());

        return $cfg;
    }

    /**
     * @return \PHPParser_Node
     */
    public function getScopeRoot()
    {
        if ($this->scopeRoots->isEmpty()) {
            return $this->scopes->top()->getRootNode();
        }

        return $this->scopeRoots->top();
    }

    public function getScopeDepth()
    {
        return $this->scopes->count() + $this->scopeRoots->count();
    }

    public function hasScope()
    {
        return !$this->scopeRoots->isEmpty() || !$this->scopes->isEmpty();
    }

    public function traverse(\PHPParser_Node $root)
    {
        $this->curNode = $root;
        $this->pushScopeWithRoot($root);
        $this->traverseBranch($root);
        $this->popScope();
    }

    private function traverseBranch(\PHPParser_Node $node, \PHPParser_Node $parent = null)
    {
        $this->curNode = $node;
        if (!$this->callback->shouldTraverse($this, $node, $parent)) {
            return;
        }

        switch (true) {
            case NodeUtil::isScopeCreator($node):
                $this->traverseScopeCreator($node, $parent);
                break;

            default:
                foreach ($node as $subNode) {
                    // Sometimes we have an array for example when using multiple CATCH clauses.
                    // This does not apply to statements though which are wrapped in BlockNode
                    // instances.
                    if (is_array($subNode)) {
                        foreach ($subNode as $aSubNode) {
                            // in case of NameNodes we end up in this branch, so we also need to
                            // check that we do not have a string here
                            if (!$aSubNode instanceof \PHPParser_Node) {
                                continue;
                            }

                            $this->traverseBranch($aSubNode, $node);
                        }

                        continue;
                    }

                    // Sometimes we get strings, simply skip everything that's not a node
                    // at this point.
                    if (!$subNode instanceof \PHPParser_Node) {
                        continue;
                    }

                    $this->traverseBranch($subNode, $node);
                }
        }

        $this->curNode = $node;
        $this->callback->visit($this, $node, $parent);
    }

    private function traverseScopeCreator(\PHPParser_Node $node, \PHPParser_Node $parent = null)
    {
        assert(NodeUtil::isScopeCreator($node));

        $this->curNode = $node;
        $isScopeActive = $this->getScopeRoot() === $node;

        if (!$isScopeActive) {
            $this->pushScopeWithRoot($node);
        }

        // args
        foreach ($node->params as $param) {
            $this->traverseBranch($param, $node);
        }

        // uses from parent scope
        if ($node instanceof \PHPParser_Node_Expr_Closure) {
            foreach ($node->uses as $use) {
                $this->traverseBranch($use, $node);
            }
        }

        // body
        // For abstract methods the stmts property is null, which we need to check here.
        if (null !== $node->stmts) {
            $this->traverseBranch($node->stmts, $node);
        }

        if (!$isScopeActive) {
            $this->popScope();
        }
    }

    private function pushScopeWithRoot(\PHPParser_Node $node)
    {
        assert(null !== $this->curNode);

        $this->scopeRoots->push($node);
        $this->cfgs->push(null);

        if ($this->callback instanceof ScopedCallbackInterface) {
            $this->callback->enterScope($this);
        }
    }

    private function popScope()
    {
        if ($this->callback instanceof ScopedCallbackInterface) {
            $this->callback->exitScope($this);
        }

        if ($this->scopeRoots->isEmpty()) {
            $this->scopes->pop();
        } else {
            $this->scopeRoots->pop();
        }

        $this->cfgs->pop();
    }
}