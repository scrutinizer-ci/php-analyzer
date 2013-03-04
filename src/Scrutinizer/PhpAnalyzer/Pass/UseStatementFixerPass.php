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

use JMS\PhpManipulator\TokenStream\AbstractToken;

/**
 * Use Statement Fixes
 *
 * This pass performs some ``use`` statement related fixes.
 *
 * 1. Removing Unused Use Statements
 * 2. Re-ordering Use Statements Alphabetically (disabled by default)
 *
 * .. note ::
 *
 *     By default, this pass rewrites all ``use`` statements that import
 *     multiple namespaces (``use A, B;``) to separate statements (``use A; use B;``).
 *     You can turn this off with the ``preserve_multiple`` setting.
 *
 * @category fixes
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class UseStatementFixerPass extends FixingAnalyzerPass implements ConfigurablePassInterface
{
    use ConfigurableTrait;

    private $prettyPrinter;
    private $newContent;

    public function __construct()
    {
        parent::__construct();

        $this->prettyPrinter = new \PHPParser_PrettyPrinter_Zend();
        $this->prettyPrinter->setPreserveOriginalNames(true);
    }

    public function getConfiguration()
    {
        $tb = new \Symfony\Component\Config\Definition\Builder\TreeBuilder();
        $tb->root('use_statement_fixes', 'array', new \Scrutinizer\PhpAnalyzer\Config\NodeBuilder())
            ->canBeDisabled()
            ->children()
                ->booleanNode('remove_unused')->defaultTrue()->end()
                ->booleanNode('preserve_multiple')
                    ->info('Whether you would like multiple imports in one USE statement to be preserved, e.g. ``use A, B;``.')
                    ->defaultFalse()
                ->end()
                ->booleanNode('order_alphabetically')->defaultFalse()->end()
//                ->booleanNode('alias_qualified_names')->defaultFalse()->end()
            ->end()
        ;

        return $tb;
    }

    protected function isEnabled()
    {
        return $this->getSetting('enabled');
    }

    protected function getNewContent()
    {
        return $this->newContent;
    }

    protected function analyzeStream()
    {
//        $this->rewriteQualifiedNames();
        $this->rewriteImports();
    }

    private function rewriteQualifiedNames()
    {
        if ( ! $this->getSetting('alias_qualified_names')) {
            return;
        }

        /*
         * We do a two step analysis.
         *
         * First, we traverse the stream, and gather all imports for each namespace.
         * On the second, pass we perform the actual rewrite.
         *
         * During the rewrite, it could occur that we actually do not rewrite
         * every found import, for example if there is already an import with the
         * same name, or a class in the local namespace.
         */
        $token = $this->stream->nextToken;
        $namespaceToken = $token;
        $imports = array();
        while (null !== $token) {
            switch (true) {
                case $token->matches(T_NAMESPACE):
                    $namespaceToken->setAttribute('new_imports', $imports);
                    $namespaceToken = $token;
                    $imports = array();
                    break;

                case $token->matches(T_USE):
                    if ( ! $namespaceToken->hasAttribute('first_use_token')) {
                        $namespaceToken->setAttribute('first_use_token', $token);
                    }
                    break;

                case $token->matches(T_DOC_COMMENT):
                    $this->extractQualifiedNamesFromComment($token->getContent(), $imports);
                    break;

                // TODO
            }

            $token = $token->getNextToken();
        }
    }

    private function extractQualifiedNamesFromComment($comment, array &$imports)
    {
        // TODO
    }

    private function rewriteImports()
    {
        $this->newContent = '';

        /*
         * We perform the following steps to extract use statements, and preserve
         * the formatting as much as possible.
         *
         * 1. When reaching a T_USE (1), we scan all imports until the
         *    last ";" of the last T_USE that we can safely rewrite (2).
         * 2. Parse the imported namespaces and their aliases between (1) and (2).
         * 3. Process the imports (removing unused, re-ordering).
         * 4a. Add resulting imports to new content.
         * 4b. If line where the first marker is placed (1) is now empty, remove
         *     it. This is the case when all imports were removed.
         * 5. Continue with 1.
         *
         *     .
         *     .
         *     .
         * ...(1)use ...;
         *       use ...,
         *           ...;(2)
         *
         * (1): Marker for the first use statement of the current block
         * (2): Marker for the last ";" of the last use statement
         */
        while ($this->stream->moveNext()) {
            $imports = array();
            $lastToken = null;
            $writeMultiple = false;
            // Step 1
            if ($this->stream->node instanceof \PHPParser_Node_Stmt_Use
                    && $this->stream->token->matches(T_USE)
                       // Steps 2 + 3
                    && $this->isRewritable($this->stream->node, $this->stream->token, $imports, $lastToken, $writeMultiple)) {
                // Step 4b
                if (empty($imports)) {
                    $endOfLine = $lastToken->findNextToken('END_OF_LINE')->get();
                    $remainingContentOnLine = $lastToken->getContentBetweenIncluding($endOfLine);

                    // All imports of the scanned blocks have been removed. Check
                    // to see if the resulting line is empty and remove.
                    if ('' === trim($remainingContentOnLine) && $this->isLastLineEmpty()) {
                        $this->removeLastLine();
                        $this->stream->moveToToken($endOfLine);

                        // When there are no more USE statements, we often get two consecutive
                        // empty lines. To prevent that, we will remove the following if it is
                        // empty, and also the previous line is empty.
                        if ($this->isPreviousLineEmpty()
                                && (null !== $newEndOfLine = $endOfLine->findNextToken('END_OF_LINE')->getOrElse(null))
                                && '' === trim($endOfLine->getContentBetween($newEndOfLine))) {
                            $this->stream->moveToToken($newEndOfLine);
                        }

                    } else {
                        $this->stream->moveToToken($lastToken);
                    }

                    // Step 5
                    continue;
                }

                $this->sortImports($imports);

                // Step 4a
                if ($writeMultiple) {
                    $this->writeCombinedImports($imports, $this->stream->token);
                } else {
                    $this->writeSingleImports($imports, $this->stream->token->getLineIndentation());
                }

                $this->stream->moveToToken($lastToken);

                // Step 5
                continue;
            }

            // No use statement, or not rewritable.
            $this->newContent .= $this->stream->token->getContent();
        }
    }

    private function removeLastLine()
    {
        if (false !== $pos = strrpos($this->newContent, "\n")) {
            $this->newContent = substr($this->newContent, 0, $pos + 1);

            return;
        }

        $this->newContent = '';
    }

    private function isPreviousLineEmpty()
    {
        if (false === $pos = strrpos($this->newContent, "\n")) {
            return false;
        }

        if (false === $pos = strrpos($this->newContent, "\n", $pos - 1 - strlen($this->newContent))) {
            return false;
        }

        return '' === trim(substr($this->newContent, $pos + 1));
    }

    private function isLastLineEmpty()
    {
        if (false !== $pos = strrpos($this->newContent, "\n")) {
            return '' === trim(substr($this->newContent, $pos + 1));
        }

        return '' === trim($this->newContent);
    }

    private function writeSingleImports(array $queuedImports, $indentation)
    {
        $first = true;
        foreach ($queuedImports as $namespace => $alias) {
            if ( ! $first) {
                $this->newContent .= "\n".$indentation;
            }
            $first = false;

            $this->newContent .= 'use '.$namespace;
            if (null !== $alias) {
                $this->newContent .= ' as '.$alias;
            }
            $this->newContent .= ';';
        }
    }

    private function writeCombinedImports(array $imports, AbstractToken $startToken)
    {
        $this->newContent .= $startToken->getContent()
                            .$startToken->getWhitespaceAfter();

        // For following imports, we use an indentation to reach the
        // column of the first import, so that they are neatly aligned.
        //
        // ```
        //     use A,
        //         B,
        //         C;
        // ```
        $indentation = $startToken->findNextToken('NO_WHITESPACE')->get()->getIndentation();
        $first = true;
        foreach ($imports as $namespace => $alias) {
            if ( ! $first) {
                $this->newContent .= ",\n".$indentation;
            }
            $first = false;

            $this->newContent .= $namespace;
            if (null !== $alias) {
                $this->newContent .= ' as '.$alias;
            }
        }
        $this->newContent .= ';';
    }

    private function sortImports(array &$imports)
    {
        if ( ! $this->getSetting('order_alphabetically')) {
            return;
        }

        ksort($imports);
    }

    private function removeUnused(\PHPParser_Node $namespaceNode, array &$imports)
    {
        if ( ! $this->getSetting('remove_unused')) {
            return;
        }

        foreach ($imports as $namespace => $alias) {
            if (null === $alias) {
                if (false !== $pos = strrpos($namespace, '\\')) {
                    $alias = substr($namespace, $pos + 1);
                } else {
                    $alias = $namespace;
                }
            }

            if ( ! $this->shouldUseBeRemoved($namespaceNode, $alias)) {
                continue;
            }

            unset($imports[$namespace]);
        }
    }

    private function shouldUseBeRemoved(\PHPParser_Node $namespaceNode, $alias)
    {
        $printable = $this->getPrintableAst($namespaceNode);
        if ( ! is_array($printable)) {
            $printable = array($printable);
        }
        $content = $this->prettyPrinter->prettyPrint($printable);

        return preg_match('#\b'.preg_quote($alias, '#').'\b#', $content) === 0;
    }

    private function isRewritable(\PHPParser_Node $startNode, AbstractToken $token, array &$imports, AbstractToken &$lastToken = null, &$writeMultiple = false)
    {
        $namespaceNode = \Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil::findParent($startNode, 'PHPParser_Node_Stmt_Namespace')
                ?: $this->fixedFile->getAst();
        $preserveMultiple = $this->getSetting('preserve_multiple');

        $matchedAtLeastOne = false;
        $writeMultiple = false;
        while ($token->matches(T_USE)) {
            $currentImports = array();
            $currentLastToken = null;
            $currentMultiple = false;

            if ( ! $this->isSafeToRewrite($token, $currentImports, $currentLastToken, $currentMultiple)) {
                break;
            }

            // If we are supposed to preserve multiples, and we already matched
            // some single use statements, bail out here. We will come back when
            // reaching the T_USE of the multiple import statement.
            if ($currentMultiple && $matchedAtLeastOne && $preserveMultiple) {
                break;
            }

            $this->removeUnused($namespaceNode, $currentImports);

            $matchedAtLeastOne = true;
            $imports = array_merge($imports, $currentImports);
            $lastToken = $currentLastToken;

            // If we scanned a multiple imports use statement, and we are supposed
            // to preserve it, bail out here as we have already reached its end.
            if ($currentMultiple && $preserveMultiple) {
                $writeMultiple = true;

                return true;
            }

            $token = $lastToken->findNextToken('NO_WHITESPACE')->get();
        }

        return $matchedAtLeastOne;
    }

    /**
     * Checks whether the given token can safely be rewritten.
     *
     * We consider something safe to rewrite if there are no comments placed inside
     * the use statements.
     *
     * @param \JMS\PhpManipulator\TokenStream\AbstractToken $token
     *
     * @return boolean
     */
    private function isSafeToRewrite(AbstractToken $token, array &$imports = array(), AbstractToken &$lastToken = null, &$multiple = false)
    {
        if ( ! $token->matches(T_USE)) {
            throw new \LogicException(sprintf('Expected a T_USE token, but got "%s".', $token));
        }

        $next = $token;
        $isFirst = true;
        $multiple = false;
        do {
            if ( ! $isFirst) {
                $multiple = true;
            }
            $isFirst = false;

            $next = $next->findNextToken('NO_WHITESPACE')->get();
            if ( ! $next->matches(T_STRING)) {
                return false;
            }

            $endOfName = $next->findNextToken('END_OF_NAME')->get();
            $namespace = $next->getContentUntil($endOfName);
            if ($namespace[0] === '\\') {
                $namespace = substr($namespace, 1);
            }
            $next = $endOfName;

            if ($next->matches(T_WHITESPACE)) {
                $next = $next->findNextToken('NO_WHITESPACE')->get();
            }

            if ($next->matches(T_AS)) {
                $next = $next->findNextToken('NO_WHITESPACE')->get();

                if ( ! $next->matches(T_STRING)) {
                    return false;
                }

                $imports[$namespace] = $next->getContent();
                $next = $next->findNextToken('NO_WHITESPACE')->get();

                if ($next->matches(';')) {
                    $lastToken = $next;

                    return $this->isSafeEndToken($next);
                }
            } else {
                $imports[$namespace] = null;
            }

            if ($next->matches(';')) {
                $lastToken = $next;

                return $this->isSafeEndToken($next);
            }
        } while ($next->matches(','));

        return false;
    }

    private function isSafeEndToken(AbstractToken $token)
    {
        $nextNoWhitespace = $token->findNextToken('NO_WHITESPACE')->get();

        // If the end token is followed by a comment on the same same line,
        // we consider it unsafe for rewriting.
        if ($nextNoWhitespace->matches('COMMENT') && $token->getLine() === $nextNoWhitespace->getLine()) {
            return false;
        }

        return true;
    }

    /**
     * Cleans the AST of nodes which would deteriorate the accuracy of this check.
     *
     * Specifically, that means comments (not doc comments), and the use node
     * which we want to remove.
     *
     * @param \PHPParser_Node $node
     * @param string $useNode
     *
     * @return array|\PHPParser_Node
     */
    private function getPrintableAst(\PHPParser_Node $node)
    {
        $node = clone $node;
        foreach ($node as $name => $subNode) {
            if (is_array($subNode)) {
                foreach ($subNode as $k => $aSubNode) {
                    if (!$aSubNode instanceof \PHPParser_Node) {
                        continue;
                    }

                    $subNode[$k] = $this->getPrintableAst($aSubNode);
                }
            } else if ($subNode instanceof \PHPParser_Node) {
                $node->$name = $this->getPrintableAst($subNode);
            }
        }

        if ($node instanceof \JMS\PhpManipulator\PhpParser\BlockNode) {
            $nodes = array();
            foreach ($node as $subNode) {
                if ($subNode instanceof \PHPParser_Node_Stmt_Use) {
                    continue;
                }

                $nodes[] = $subNode;
            }

            return $nodes;
        }

        return $node;
    }
}