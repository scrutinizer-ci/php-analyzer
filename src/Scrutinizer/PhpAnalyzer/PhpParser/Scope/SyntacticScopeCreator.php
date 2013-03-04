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

namespace Scrutinizer\PhpAnalyzer\PhpParser\Scope;

use Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil;
use \PHPParser_Node as Node;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\PhpType;

class SyntacticScopeCreator implements ScopeCreatorInterface
{
    /** @var Scope */
    private $scope;

    public function createScope(Node $node, Scope $parent = null)
    {
        $scope = $this->scope = new Scope($node, $parent);
        $this->scanRoot($node, $parent);
        $this->scope = null;

        return $scope;
    }

    private function scanRoot(Node $node, Scope $parent = null)
    {
        if (NodeUtil::isScopeCreator($node)) {
            // declare function/method parameters
            foreach ($node->params as $param) {
                assert($param instanceof \PHPParser_Node_Param);

                $var = $this->declareVar($param->name, null, true, $param);
                $var->setReference($param->byRef);
            }

            // declare uses from parent scope
            if ($node instanceof \PHPParser_Node_Expr_Closure) {
                foreach ($node->uses as $use) {
                    assert($use instanceof \PHPParser_Node_Expr_ClosureUse);

                    if (null !== $parent && $parent->isDeclared($use->var)) {
                        $var = $parent->getVar($use->var);
                        $newVar = $this->declareVar($use->var, $var->getType(), $var->isTypeInferred(), $use);
                        $newVar->setReference($use->byRef);

                        continue;
                    }

                    $var = $this->declareVar($use->var, null, true, $use);
                    $var->setReference($use->byRef);
                }
            }

            // Declare $this for non-static methods.
            if ($node instanceof \PHPParser_Node_Stmt_ClassMethod
                    && ($node->type & \PHPParser_Node_Stmt_Class::MODIFIER_STATIC) === 0) {
                $this->declareVar('this');
            }

            // Declare $this for Closures (since PHP 5.4) inside non-static methods.
            if ($node instanceof \PHPParser_Node_Expr_Closure) {
                $curParent = $parent;
                while (null !== $curParent) {
                    if ($curParent->getRootNode() instanceof \PHPParser_Node_Stmt_ClassMethod) {
                        // If the method is declared static, then $this is not available in the
                        // Closure, just like $this is not available in a static method.
                        if (($curParent->getRootNode()->type & \PHPParser_Node_Stmt_Class::MODIFIER_STATIC) !== 0) {
                            break;
                        }

                        $this->declareVar('this');
                        break;
                    }

                    $curParent = $curParent->getParentScope();
                }
            }

            // body
            if ($node->stmts) {
                $this->scanVars($node->stmts, $node);
            }
        } else {
            foreach ($node as $subNode) {
                if ($subNode instanceof \PHPParser_Node) {
                    $this->scanVars($subNode, $node);
                } else if (is_array($subNode)) {
                    foreach ($subNode as $aSubNode) {
                        if ($aSubNode instanceof \PHPParser_Node) {
                            $this->scanVars($aSubNode, $node);
                        }
                    }
                }
            }
        }
    }

    private function scanVars(Node $node, Node $parent)
    {
        switch (true) {
            case $node instanceof \PHPParser_Node_Stmt_StaticVar:
                $this->declareVar($node->name, null, true, $node);

                return;

            case $node instanceof \PHPParser_Node_Expr_Variable:
                if (($parent instanceof \PHPParser_Node_Expr_Assign
                        || $parent instanceof \PHPParser_Node_Expr_AssignRef)
                            && is_string($node->name)
                            && $parent->var === $node) {
                    $this->declareVar($node->name, null, true, $node);
                }

                // PHP automatically creates a variable (without issuing a warning) if an item is added
                // to a variable without the variable being declared as array previously.
                // ``function() { $a[] = 'foo'; }`` is perfectly permissible, and will create ``$a``.
                if ($parent instanceof \PHPParser_Node_Expr_ArrayDimFetch
                        && $parent->var === $node) {
                    $parentsParent = $parent->getAttribute('parent');

                    if (($parentsParent instanceof \PHPParser_Node_Expr_Assign
                            || $parentsParent instanceof \PHPParser_Node_Expr_AssignRef)
                                && is_string($node->name)
                                && $parentsParent->var === $parent) {
                        // TODO: We should better track which variables have been initialized
                        //       and which have not. This can be done by adding an undefined
                        //       type for those that have not.
                        if (!$this->scope->isDeclared($node->name)) {
                            $node->setAttribute('array_initializing_variable', true);
                        }

                        $this->declareVar($node->name, null, true, $node);
                    }
                }

                if ($parent instanceof \PHPParser_Node_Arg
                        && $parent->getAttribute('param_expects_ref', false)
                        && is_string($node->name)) {
                    $this->declareVar($node->name, null, true, $node);
                }

                return;

            case $node instanceof \PHPParser_Node_Expr_AssignList:
                foreach ($node->vars as $var) {
                    if ($var instanceof \PHPParser_Node_Expr_Variable
                            && is_string($var->name)) {
                        $this->declareVar($var->name, null, true, $var);
                    }
                }

                $this->scanVars($node->expr, $node);

                return;

            case $node instanceof \PHPParser_Node_Stmt_Trait:
            case $node instanceof \PHPParser_Node_Stmt_Interface:
            case $node instanceof \PHPParser_Node_Stmt_Class:
            case NodeUtil::isScopeCreator($node):
                return; // do not examine their children as they belong to a different scope

            case $node instanceof \PHPParser_Node_Stmt_Catch:
                $this->declareVar($node->var, null, true, $node);
                $this->scanVars($node->stmts, $node);

                return;

            case $node instanceof \PHPParser_Node_Stmt_Foreach:
                if ($node->keyVar !== null
                        && $node->keyVar instanceof \PHPParser_Node_Expr_Variable
                        && is_string($node->keyVar->name)) {
                    $this->declareVar($node->keyVar->name, null, true, $node->keyVar);
                }

                if ($node->valueVar instanceof \PHPParser_Node_Expr_Variable
                        && is_string($node->valueVar->name)) {
                    $this->declareVar($node->valueVar->name, null, true, $node->valueVar);
                }

                $this->scanVars($node->stmts, $node);

                return;

            default:
                foreach ($node as $subNode) {
                    if (is_array($subNode)) {
                        foreach ($subNode as $aSubNode) {
                            if (!$aSubNode instanceof \PHPParser_Node) {
                                continue;
                            }
                            $this->scanVars($aSubNode, $node);
                        }
                    } else if ($subNode instanceof \PHPParser_Node) {
                        $this->scanVars($subNode, $node);
                    }
                }
        }
    }

    /**
     * Declares a variable.
     *
     * @param string $name
     * @param array $types
     * @param boolean $typesInferred
     */
    private function declareVar($name, PhpType $type = null, $typeInferred = true, \PHPParser_Node $nameNode = null)
    {
        if ($this->scope->isDeclared($name)) {
            return $this->scope->getVar($name);
        }

        $var = $this->scope->declareVar($name, $type, $typeInferred);

        if ($nameNode) {
            $var->setNameNode($nameNode);
        }

        return $var;
    }
}