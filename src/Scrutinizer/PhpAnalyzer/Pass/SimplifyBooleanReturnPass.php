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
use JMS\PhpManipulator\PhpParser\BlockNode;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\TypedScopeCreator;
use Scrutinizer\PhpAnalyzer\Model\Comment;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Boolean Return Simplification
 *
 * This pass checks whether a boolean return value could be simplified::
 *
 *     // Instead of
 *     function foo(A $a) {
 *         if ($a->isValid()) {
 *             return true;
 *         } else {
 *             return false;
 *         }
 *     }
 *
 *     // it will suggest
 *     function foo(A $a) {
 *         return $a->isValid();
 *     }
 *
 * In the example above, we assumed that ``$a->isValid()`` already returns a boolean.
 * If that is not the case, it would suggest ``(boolean) $a->isValid()`` instead.
 *
 * @category checks
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class SimplifyBooleanReturnPass extends AstAnalyzerPass implements ConfigurablePassInterface
{
    use ConfigurableTrait;

    public function visit(NodeTraversal $t, \PHPParser_Node $node, \PHPParser_Node $parent = null)
    {
        if ($node instanceof \PHPParser_Node_Stmt_If
                && 0 === count($node->elseifs)
                && null !== $node->else
                && $this->returnsBooleanLiteral($node->stmts)
                && $this->returnsBooleanLiteral($node->else->stmts)) {
            $this->checkIfElseStatements($node, $node->stmts, $node->else->stmts);
        } else if ($node instanceof \PHPParser_Node_Stmt_If
                        && 0 === count($node->elseifs)
                        && null === $node->else
                        && ($next = $node->getAttribute('next')) instanceof \PHPParser_Node_Stmt_Return
                        && $this->returnsBooleanLiteral($node->stmts)
                        && $this->returnsBooleanLiteral($else = new BlockNode(array($next)))) {
            $this->checkIfElseStatements($node, $node->stmts, $else);
        }
    }

    protected function isEnabled()
    {
        return true === $this->getSetting('enabled');
    }

    private function checkIfElseStatements(\PHPParser_Node_Stmt_If $node, BlockNode $if, BlockNode $else)
    {
        $ifTrue = $this->returnsTrue($if);
        $elseTrue = $this->returnsTrue($else);

        if ($ifTrue === $elseTrue) {
            if (null === $node->else) {
                $this->phpFile->addComment($node->getLine(), Comment::error(
                    'suspicious_code.superfluous_if_return',
                    'This ``if`` statement and the following ``return`` statement are superfluous as you return always ``%return_value%``.',
                    array('return_value' => $ifTrue ? 'true' : 'false')));
            } else {
                $this->phpFile->addComment($node->getLine(), Comment::error(
                    'suspicious_code.superfluous_if_else',
                    'This ``if``-``else`` statement are superfluous as you return always ``%return_value%``.',
                    array('return_value' => $ifTrue ? 'true' : 'false')));
            }

            return;
        }

        $condType = $node->cond->getAttribute('type');
        $possiblyNeedsCast = !$condType || !$condType->isBooleanType();
        $cond = $node->cond;

        if ( ! $ifTrue) {
            if ($cond instanceof \PHPParser_Node_Expr_BooleanNot) {
                $cond = $cond->expr;
                $condType = $cond->getAttribute('type');
                $possiblyNeedsCast = !$condType || !$condType->isBooleanType();
            } else {
                $cond = new \PHPParser_Node_Expr_BooleanNot($cond);
                $possiblyNeedsCast = false;
            }
        }

        if ($possiblyNeedsCast) {
            $cond = new \PHPParser_Node_Expr_Cast_Bool($cond);
        }

        if (null !== $node->else) {
            $this->phpFile->addComment($node->getLine(), Comment::warning(
                'coding_style.simplify_boolean_return_of_if_else',
                 'The ``if``-``else`` statement can be simplified to ``return %expr%;``.',
                array('expr' => self::$prettyPrinter->prettyPrintExpr($cond))));
        } else {
            $this->phpFile->addComment($node->getLine(), Comment::warning(
                'coding_style.simplify_boolean_return_of_if_return',
                'This ``if`` statement, and the following ``return`` statement can be replaced with ``return %expr%;``.',
                array('expr' => self::$prettyPrinter->prettyPrintExpr($cond))));
        }
    }

    public function getConfiguration()
    {
        $tb = new TreeBuilder();
        $tb->root('simplify_boolean_return', 'array', new NodeBuilder())
            ->attribute('label', 'Boolean Return Simplification')
            ->canBeEnabled()
            ->attribute('help', <<<'EOF'
This checks whether boolean return values can be simplified.

For example

```php
function foo($foo) {
    if (!$foo) {
        return true;
    } else {
        return false;
    }
}
```

could be simplified to

```php
function foo($foo) {
    return !$foo;
}
```
EOF
                    )
        ;

        return $tb;
    }

    protected function getScopeCreator()
    {
        return new TypedScopeCreator($this->typeRegistry);
    }

    private function getBooleanReturn(BlockNode $node)
    {
        if (count($node) !== 1) {
            return null;
        }

        if (!$node[0] instanceof \PHPParser_Node_Stmt_Return) {
            return null;
        }

        if (!$node[0]->expr || !$node[0]->expr instanceof \PHPParser_Node_Expr_ConstFetch) {
            return null;
        }

        return strtolower($node[0]->expr->name);
    }

    private function returnsTrue(BlockNode $node)
    {
        return 'true' === $this->getBooleanReturn($node);
    }

    private function returnsBooleanLiteral(BlockNode $node)
    {
        $return = $this->getBooleanReturn($node);

        return 'true' === $return || 'false' === $return;
    }
}