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

namespace Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\ScopeBuilder;

use Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal;

class LocalScopeBuilder extends AbstractScopeBuilder
{
    public function shouldTraverse(NodeTraversal $t, \PHPParser_Node $node, \PHPParser_Node $parent = null)
    {
        if (!isset($this->importedClasses[''])) {
            $this->gatherNamespaceInfo($node);
        }

        return parent::shouldTraverse($t, $node, $parent);
    }

    private function gatherNamespaceInfo(\PHPParser_Node $node)
    {
        // search for a namespace node
        $namespaceNode = $node;
        while (!$namespaceNode instanceof \PHPParser_Node_Stmt_Namespace) {
            $node = $namespaceNode->getAttribute('parent');
            if (!$node) {
                break;
            }

            $namespaceNode = $node;
        }

        if (!$namespaceNode instanceof \PHPParser_Node_Stmt_Namespace) {
            $this->importedClasses[''] = '';
        } else {
            $this->importedClasses[''] = null === $namespaceNode->name ? '' : implode("\\", $namespaceNode->name->parts);
        }

        $this->scanUseStmts($namespaceNode);
    }

    private function scanUseStmts(\PHPParser_Node $node)
    {
        if ($node instanceof \PHPParser_Node_Stmt_UseUse) {
            $this->importedClasses[$node->alias] = implode("\\", $node->name->parts);
        }

        // For these nodes we will bail-out early as they cannot contain use
        // statements anyway. This list is not complete, but should still speed
        // up the search significantly.
        if ($node instanceof \PHPParser_Node_Stmt_Class
                || $node instanceof \PHPParser_Node_Stmt_Function
                || $node instanceof \PHPParser_Node_Stmt_Interface
                || $node instanceof \PHPParser_Node_Stmt_Trait
                || $node instanceof \PHPParser_Node_Expr_Closure) {
            return;
        }

        // Scan children for use statements.
        foreach ($node as $subNode) {
            if (is_array($subNode)) {
                foreach ($subNode as $aSubNode) {
                    if (!$aSubNode instanceof \PHPParser_Node) {
                        continue;
                    }

                    $this->scanUseStmts($aSubNode);
                }
            } else if ($subNode instanceof \PHPParser_Node) {
                $this->scanUseStmts($subNode);
            }
        }
    }
}