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

use Scrutinizer\PhpAnalyzer\Pass\AnalysisPassInterface;
use Scrutinizer\PhpAnalyzer\Config\NodeBuilder;
use Scrutinizer\PhpAnalyzer\ControlFlow\GraphReachability;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal;
use Scrutinizer\PhpAnalyzer\PhpParser\Traversal\AbstractScopedCallback;
use Scrutinizer\PhpAnalyzer\Model\Comment;
use Scrutinizer\PhpAnalyzer\Model\File;
use Scrutinizer\PhpAnalyzer\Model\PhpFile;

/**
 * Unreachable Code Check
 *
 * This pass checks whether code is reachable by analyzing the control flow graph
 * of the program. At the moment, this does not perform any kind of data flow
 * analysis.
 *
 * @category checks
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class CheckUnreachableCodePass extends AbstractScopedCallback implements AnalysisPassInterface, ConfigurablePassInterface
{
    use ConfigurableTrait;

    private $file;
    private $prettyPrinter;

    public function __construct()
    {
        $this->prettyPrinter = new \PHPParser_PrettyPrinter_Zend();
    }

    public function getConfiguration()
    {
        $tb = new \Symfony\Component\Config\Definition\Builder\TreeBuilder();
        $tb->root('unreachable_code', 'array', new NodeBuilder())
            ->attribute('label', 'Un-reachable Code')
            ->canBeDisabled()
        ;

        return $tb;
    }

    public function analyze(File $file)
    {
        if (!$file instanceof PhpFile) {
            return;
        }

        if ( ! $this->getSetting('enabled')) {
            return;
        }

        $this->file = $file;
        NodeTraversal::traverseWithCallback($file->getAst(), $this);
    }

    public function enterScope(NodeTraversal $t)
    {
        $r = new GraphReachability($cfg = $t->getControlFlowGraph());
        $r->compute($cfg->getEntryPoint()->getAstNode());
    }

    public function shouldTraverse(NodeTraversal $t, \PHPParser_Node $node, \PHPParser_Node $parent = null)
    {
        $graphNode = $t->getControlFlowGraph()->getNode($node);

        if (null !== $graphNode && GraphReachability::UNREACHABLE === $graphNode->getAttribute(GraphReachability::ATTR_REACHABILITY)) {
            // Only report error when there are some line number informations.
            // There are synthetic nodes with no line number informations, nodes
            // introduced by other passes (although not likely since this pass should
            // be executed early) or some PHPParser bug.
            if (-1 !== $node->getLine()) {
                $this->file->addComment($node->getLine(), Comment::warning(
                    'usage.unreachable_code',
                    '``%unreachable_code%`` is not reachable.',
                    array('unreachable_code' => $this->prettyPrinter->prettyPrint(array($node)))));

                // From now on, we are going to assume the user fixed the error and not
                // give more warnings related to code sections reachable from this node.
                $r = new GraphReachability($t->getControlFlowGraph());
                $r->recompute($node);

                // Saves time by not traversing children.
                return false;
            }
        }

        return true;
    }
}