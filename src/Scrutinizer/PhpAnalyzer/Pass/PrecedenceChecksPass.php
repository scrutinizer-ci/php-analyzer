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

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Scrutinizer\PhpAnalyzer\Config\NodeBuilder;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil;
use Scrutinizer\PhpAnalyzer\Model\Comment;
use JMS\PhpManipulator\TokenStream\AbstractToken;

/**
 * Precedence Checks
 *
 * Assignments in Conditions
 * -------------------------
 * If you make use of assignments in conditions, it sometimes happens that you add a new condition and forget to take
 * precedence into account. As a result, you might easily change the result of the assignment to something that you did
 * not intend::
 *
 *     if ($foo = $this->getFoo()) { }
 *
 *     // is extended to
 *     if ($foo = $this->getFoo() && $foo->isEnabled()) { }
 *
 * This pass verifies that all assignments in conditions which involve ``&&``, ``||``, ``and``, ``or`` have explicit
 * parentheses set, or flags them if not.
 *
 * For the above example that means that this check would suggest to change the assignment to one of the following
 * versions::
 *
 *     // Accepted Version 1
 *     if (($foo = $this->getFoo()) && $foo->isEnabled()) { }
 *
 *     // Accepted Version 2
 *     if ($foo = ($this->getFoo() && $foo->isEnabled())) { }
 *
 * Comparison Involving the Result of Bit Operations
 * -------------------------------------------------
 * If you compare the result of a bitwise operations and place it on the right side of a comparison operator, the
 * resulting expression is likely not intended, but might go unnoticed::
 *
 *     // Returns always int(0).
 *     return 0 === $foo & 4;
 *     return (0 === $foo) & 4;
 *
 *     // More likely intended return: true/false
 *     return 0 === ($foo & 4);
 *
 *
 * @category checks
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class PrecedenceChecksPass extends TokenAndAstStreamAnalyzerPass implements ConfigurablePassInterface
{
    use ConfigurableTrait;

    public function getConfiguration()
    {
        $tb = new TreeBuilder();
        $tb->root('precedence_checks', 'array', new NodeBuilder())
            ->canBeDisabled()
            ->info('Checks Common Precedence Mistakes')
            ->children()
                ->booleanNode('assignment_in_condition')->defaultTrue()->end()
                ->booleanNode('comparison_of_bit_result')->defaultTrue()->end()
            ->end()
        ;

        return $tb;
    }

    protected function isEnabled()
    {
        return $this->getSetting('enabled');
    }

    protected function analyzeStream()
    {
        $lastNode = null;
        while ($this->stream->moveNext()) {
            if ($this->stream->node === $lastNode) {
                continue;
            }
            $lastNode = $this->stream->node;

            if ($this->stream->node instanceof \PHPParser_Node_Expr_Assign
                    && $this->getSetting('assignment_in_condition')) {
                $expr = $this->stream->node->expr;
                if ( ! $expr instanceof \PHPParser_Node_Expr_BooleanAnd
                        && ! $expr instanceof \PHPParser_Node_Expr_BooleanOr
                        && ! $expr instanceof \PHPParser_Node_Expr_LogicalAnd
                        && ! $expr instanceof \PHPParser_Node_Expr_LogicalOr
                        && ! $expr instanceof \PHPParser_Node_Expr_LogicalXor) {
                    continue;
                }

                if ( ! $this->isInsideCondition($this->stream->node)) {
                    continue;
                }

                $exprStartToken = $this->stream->token->findNextToken('NO_WHITESPACE_OR_COMMENT')->get();
                if ( ! $exprStartToken->matches('(')) {
                    $this->phpFile->addComment($this->stream->node->getLine(), Comment::warning(
                        'precedence.possibly_wrong_assign_in_cond',
                        'Consider adding parentheses for clarity. Current Interpretation: ``%current_assign%``, Probably Intended Meaning: ``%alternative_assign%``',
                        array(
                            'current_assign' => self::$prettyPrinter->prettyPrintExpr($this->stream->node->var)
                                                    .' = ('.self::$prettyPrinter->prettyPrintExpr($this->stream->node->expr).')',
                            'alternative_assign' => '('.self::$prettyPrinter->prettyPrintExpr($this->stream->node->var)
                                                    .' = '.self::$prettyPrinter->prettyPrintExpr($this->stream->node->expr->left).')'
                                                    .' '.NodeUtil::getBinOp($this->stream->node->expr).' '
                                                    .self::$prettyPrinter->prettyPrintExpr($this->stream->node->expr->right),
                        )
                    ));
                }
            }

            if (($this->stream->node instanceof \PHPParser_Node_Expr_BitwiseAnd
                    || $this->stream->node instanceof \PHPParser_Node_Expr_BitwiseOr
                    || $this->stream->node instanceof \PHPParser_Node_Expr_BitwiseXor)
                        && $this->getSetting('comparison_of_bit_result')) {
                $this->checkBitwiseOperation();
            }
        }
    }

    private function checkBitwiseOperation()
    {
        if ( ! NodeUtil::isEqualityExpression($this->stream->node->left)) {
            return;
        }

        /*
         * When this method is called, the token stream points to the bitwise operation already.
         *
         * 0 === foo() & 5;
         *             ^
         * -----------(1)
         *
         * (1) The position of the token stream when this method is called.
         *
         * We now check that the equality expression on the left side (in the case above, it is an Identical node) ends
         * with a parenthesis, and the ending parenthesis is started somewhere on the left side side of the equality
         * operator (===).
         */

        /** @var AbstractToken $closingParenthesis */
        $closingParenthesis = $this->stream->token->findPreviousToken('NO_WHITESPACE_OR_COMMENT')->get();
        if ($closingParenthesis->matches(')')) {
            $startToken = $this->stream->node->left->left->getAttribute('start_token');

            // The left operator of the equality sign is not assigned an explicit start token, so we do not know where
            // it started. We can just bail out here, and hope that the closing parenthesis is indeed started on the left
            // side. This needs to be improved in the SimultaneousTokenAndAstStream over time so that we ideally always
            // have a start token available.
            if (null === $startToken) {
                return;
            }

            // The closing parenthesis was started somewhere on the left side, good !
            if ($closingParenthesis->isClosing($startToken->findPreviousToken('NO_WHITESPACE_OR_COMMENT')->get())) {
                return;
            }
        }

        $this->phpFile->addComment($this->stream->node->getLine(), Comment::warning(
            'precedence.possibly_wrong_comparison_of_bitop_result',
            'Consider adding parentheses for clarity. Current Interpretation: ``%current_compare%``, Probably Intended Meaning: ``%alternative_compare%``',
            array(
                'current_compare' => '('.self::$prettyPrinter->prettyPrintExpr($this->stream->node->left).') '
                                        .NodeUtil::getBitOp($this->stream->node).' '
                                        .self::$prettyPrinter->prettyPrintExpr($this->stream->node->right),
                'alternative_compare' => self::$prettyPrinter->prettyPrintExpr($this->stream->node->left->left).' '
                                        .NodeUtil::getEqualOp($this->stream->node->left).' ('
                                        .self::$prettyPrinter->prettyPrintExpr($this->stream->node->left->right)
                                        .' '.NodeUtil::getBitOp($this->stream->node).' '
                                        .self::$prettyPrinter->prettyPrintExpr($this->stream->node->right).')',
            )
        ));
    }

    private function isInsideCondition(\PHPParser_Node $node)
    {
        $previous = $node;
        while (null !== $parent = $previous->getAttribute('parent')) {
            if ($parent instanceof \PHPParser_Node_Stmt_If
                    || $parent instanceof \PHPParser_Node_Stmt_ElseIf
                    || $parent instanceof \PHPParser_Node_Stmt_While
                    || $parent instanceof \PHPParser_Node_Stmt_Do) {
                // Verifies that we are traversing up from inside the condition, and not from
                // inside the statement block.
                return $parent->cond === $previous;
            }

            $previous = $parent;
        }

        return false;
    }
}