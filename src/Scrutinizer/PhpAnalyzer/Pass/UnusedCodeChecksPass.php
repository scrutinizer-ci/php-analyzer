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

use Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal;
use Scrutinizer\PhpAnalyzer\Pass\AstAnalyzerPass;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\TypedScopeCreator;
use Scrutinizer\PhpAnalyzer\Model\Comment;

class UnusedCodeChecksPass extends AstAnalyzerPass
{
    public function visit(NodeTraversal $t, \PHPParser_Node $node, \PHPParser_Node $parent = null)
    {
        if ($node instanceof \PHPParser_Node_Stmt_ClassMethod) {
            $this->checkUnusedMethod($t, $node);
        }
    }

    protected function getScopeCreator()
    {
        return new TypedScopeCreator($this->typeRegistry);
    }

    private function checkUnusedMethod(NodeTraversal $t, \PHPParser_Node_Stmt_ClassMethod $node)
    {
        if ((\PHPParser_Node_Stmt_Class::MODIFIER_PRIVATE & $node->type) === 0) {
            return;
        }

        $classType = $t->getScope()->getTypeOfThis()->restrictByNotNull();
        if (($method = $classType->toMaybeObjectType()->getMethod($node->name))
                && 0 === count($method->getInMethodCallSites())) {
            $this->phpFile->addComment($node->getLine(), Comment::warning(
                'cleanup.unused_method',
                'This method is unused, and could be removed.'));
        }
    }
}