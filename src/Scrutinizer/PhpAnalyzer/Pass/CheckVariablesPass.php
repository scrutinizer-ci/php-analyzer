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
use Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil;
use Scrutinizer\PhpAnalyzer\Pass\AstAnalyzerPass;
use Scrutinizer\PhpAnalyzer\Model\Comment;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Variable Checks
 *
 * This pass performs syntactical variable checks::
 *
 *     function foo() {
 *         echo $a; // $a was not defined.
 *     }
 *
 *     function bar($artificial) {
 *         if ($artifical) { // will suggest to rename to $artificial
 *             // do something
 *         }
 *     }
 *
 * It also performs a limited analysis to check that arrays have been initialized
 * before they are written to::
 *
 *     function foo() {
 *         $a[] = 'foo'; // will suggest to add $a = array(); before
 *     }
 *
 * At the moment, this does not perform a full-blown data flow analysis, so currently
 * it will not catch things that only may be defined, like in this example::
 *
 *     function foo($a) {
 *         if ($a || 'foo' === $b = some_function()) {
 *             echo $b; // $b is not always defined
 *         }
 *     }
 *
 * @category checks
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class CheckVariablesPass extends AstAnalyzerPass implements ConfigurablePassInterface
{
    use ConfigurableTrait;

    public function getConfiguration()
    {
        $tb = new TreeBuilder();
        $tb->root('check_variables', 'array', new NodeBuilder())
            ->attribute('label', 'Variable Checks')
            ->canBeDisabled()
        ;

        return $tb;
    }

    protected function isEnabled()
    {
        return $this->getSetting('enabled');
    }

    public function visit(NodeTraversal $t, \PHPParser_Node $node, \PHPParser_Node $parent = null)
    {
        // Consider only non-dynamic variables in this pass.
        if ( ! $node instanceof \PHPParser_Node_Expr_Variable || ! is_string($node->name)) {
            return;
        }

        // We ignore PHP globals.
        if (NodeUtil::isSuperGlobal($node)) {
            return;
        }

        // Ignore variables which are defined here.
        if ($parent instanceof \PHPParser_Node_Expr_Assign && $parent->var === $node) {
            return;
        }

        if ( ! $t->getScope()->isDeclared($node->name)) {
            if ($bestName = CheckForTyposPass::getMostSimilarName($node->name, $t->getScope()->getVarNames())) {
                $this->phpFile->addComment($node->getLine(), Comment::error(
                    'typos.mispelled_variable_name',
                    'The variable ``%offending_variable_name%`` does not exist. Did you mean ``%closest_variable_name%``?',
                    array('offending_variable_name' => '$'.$node->name, 'closest_variable_name' => '$'.$bestName)));
            } else {
                $this->phpFile->addComment($node->getLine(), Comment::warning(
                    'usage.undeclared_variable',
                    'The variable ``%variable_name%`` does not exist. Did you forget to declare it?',
                    array('variable_name' => '$'.$node->name)));
            }

            return;
        }

        if ($parent instanceof \PHPParser_Node_Expr_ArrayDimFetch
                && $node->getAttribute('array_initializing_variable')
                && $parent->var === $node) {
            $this->phpFile->addComment($node->getLine(), Comment::warning(
                    'usage.non_initialized_array',
                    '``%array_fetch_expr%`` was never initialized. Although not strictly required by PHP, it is generally a good practice to add ``%array_fetch_expr% = array();`` before regardless.',
                    array('array_fetch_expr' => self::$prettyPrinter->prettyPrintExpr($node))));
        }
    }
}