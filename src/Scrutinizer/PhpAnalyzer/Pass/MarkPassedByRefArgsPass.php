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
use Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil;

/**
 * Marks arguments which are passed by reference.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class MarkPassedByRefArgsPass extends AstAnalyzerPass
{
    public function visit(NodeTraversal $t, \PHPParser_Node $node, \PHPParser_Node $parent = null)
    {
        if ( ! NodeUtil::isCallLike($node)) {
            return;
        }

        if (null === $function = $this->typeRegistry->getCalledFunctionByNode($node)) {
            return;
        }

        foreach ($function->getParameters() as $param) {
            $index = $param->getIndex();
            if ( ! isset($node->args[$index])) {
                continue;
            }

            $node->args[$index]->setAttribute('param_expects_ref', $param->isPassedByRef());
        }
    }
}