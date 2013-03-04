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
use Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal;
use Scrutinizer\PhpAnalyzer\Pass\AstAnalyzerPass;
use Scrutinizer\PhpAnalyzer\Model\Comment;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * PHPUnit Assertion Usage
 *
 * This pass checks the usage of assertions in unit tests, and will suggest more
 * specific assertions if available::
 *
 *     // instead of
 *     $this->assertTrue(count($foo) > 0);
 *
 *     // it would suggest to use
 *     $this->assertGreaterThan(0, $foo);
 *
 * The reasoning behind this is that more specific assertions will lead to better
 * error messages if the assertion fails, which in turn saves you debugging time.
 *
 * @category checks
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class PhpunitAssertionsPass extends AstAnalyzerPass implements ConfigurablePassInterface
{
    use ConfigurableTrait;

    public function getConfiguration()
    {
        $tb = new TreeBuilder();
        $tb->root('phpunit_checks', 'array', new NodeBuilder())
            ->canBeEnabled()
            ->attribute('label', 'Checks usage of PHPUnit specific features, such as assertions.')
        ;

        return $tb;
    }

    protected function isEnabled()
    {
        return true === $this->getSetting('enabled');
    }

    public function visit(NodeTraversal $t, \PHPParser_Node $node, \PHPParser_Node $parent = null)
    {
        $objType = null;
        if ($node instanceof \PHPParser_Node_Expr_MethodCall) {
            $objType = $node->var->getAttribute('type');
        } else if ($node instanceof \PHPParser_Node_Expr_StaticCall) {
            $objType = $node->class->getAttribute('type');
        }

        if (!$objType) {
            return;
        }

        if (!is_string($node->name)) {
            return;
        }

        if (!$objType->isSubTypeOf($this->typeRegistry->getClassOrCreate('PHPUnit_Framework_TestCase'))) {
            return;
        }

        switch (strtolower($node->name)) {
            case 'asserttrue':
                $this->optimizeAssertTrue($node);
                break;

            case 'assertfalse':
                $this->optimizeAssertFalse($node);
                break;
        }
    }

    private function optimizeAssertTrue(\PHPParser_Node $node)
    {
        if (!isset($node->args[0])) {
            return;
        }
        $arg = $node->args[0]->value;

        $betterExpr = null;
        switch (true) {
            case $arg instanceof \PHPParser_Node_Expr_Greater:
                $betterExpr = clone $node;
                $betterExpr->name = 'assertGreaterThan';
                $betterExpr->args = $this->createArgs($arg->right, $arg->left);
                break;

            case $arg instanceof \PHPParser_Node_Expr_GreaterOrEqual:
                $betterExpr = clone $node;
                $betterExpr->name = 'assertGreaterThanOrEqual';
                $betterExpr->args = $this->createArgs($arg->right, $arg->left);
                break;

            case $arg instanceof \PHPParser_Node_Expr_Smaller:
                $betterExpr = clone $node;
                $betterExpr->name = 'assertLessThan';
                $betterExpr->args = $this->createArgs($arg->left, $arg->right);
                break;

            case $arg instanceof \PHPParser_Node_Expr_SmallerOrEqual:
                $betterExpr = clone $node;
                $betterExpr->name = 'assertLessThanOrEqual';
                $betterExpr->args = $this->createArgs($arg->left, $arg->right);
                break;
        }

        if ($betterExpr) {
            if (isset($node->args[1])) {
                $this->phpFile->addComment($node->getLine(), Comment::warning(
                    'phpunit.more_specific_assertion_for_true_with_custom_message',
                    'Instead of ``assertTrue()`` consider using ``%expr%``.',
                    array('expr' => self::$prettyPrinter->prettyPrintExpr($betterExpr))));
            } else {
                $this->phpFile->addComment($node->getLine(), Comment::warning(
                    'phpunit.more_specific_assertion_for_true',
                    'Instead of ``assertTrue()`` use ``%expr%``. This will lead to a better error message when the test fails.',
                    array('expr' => self::$prettyPrinter->prettyPrintExpr($betterExpr))));
            }
        }
    }

    private function optimizeAssertFalse(\PHPParser_Node $node)
    {
        if (!isset($node->args[0])) {
            return;
        }
        $arg = $node->args[0]->value;

        $betterExpr = null;
        switch (true) {
            case $arg instanceof \PHPParser_Node_Expr_Greater:
                $betterExpr = clone $node;
                $betterExpr->name = 'assertLessThanOrEqual';
                $betterExpr->args = $this->createArgs($arg->right, $arg->left);
                break;

            case $arg instanceof \PHPParser_Node_Expr_GreaterOrEqual:
                $betterExpr = clone $node;
                $betterExpr->name = 'assertLessThan';
                $betterExpr->args = $this->createArgs($arg->right, $arg->left);
                break;

            case $arg instanceof \PHPParser_Node_Expr_Smaller:
                $betterExpr = clone $node;
                $betterExpr->name = 'assertGreaterThanOrEqual';
                $betterExpr->args = $this->createArgs($arg->left, $arg->right);
                break;

            case $arg instanceof \PHPParser_Node_Expr_SmallerOrEqual:
                $betterExpr = clone $node;
                $betterExpr->name = 'assertGreaterThan';
                $betterExpr->args = $this->createArgs($arg->left, $arg->right);
                break;
        }

        if ($betterExpr) {
            if (isset($node->args[1])) {
                $this->phpFile->addComment($node->getLine(), Comment::warning(
                    'phpunit.more_specific_assertion_for_false_with_custom_message',
                    'Instead of ``assertFalse()`` consider using ``%expr%``.',
                    array('expr' => self::$prettyPrinter->prettyPrintExpr($betterExpr))));
            } else {
                $this->phpFile->addComment($node->getLine(), Comment::warning(
                    'phpunit.more_specific_assertion_for_false',
                    'Instead of ``assertFalse()`` use ``%expr%``. This will lead to a better error message when the test fails.',
                    array('expr' => self::$prettyPrinter->prettyPrintExpr($betterExpr))));
            }
        }
    }

    private function createArgs()
    {
        return array_map(function($v) { return new \PHPParser_Node_Arg($v); }, func_get_args());
    }
}