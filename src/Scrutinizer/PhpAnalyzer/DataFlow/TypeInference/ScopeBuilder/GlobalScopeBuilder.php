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

class GlobalScopeBuilder extends AbstractScopeBuilder
{
    public function shouldTraverse(NodeTraversal $t, \PHPParser_Node $node, \PHPParser_Node $parent = null)
    {
        if ($node instanceof \PHPParser_Node_Stmt_Namespace) {
            $this->importedClasses = array('' => $node->name ? implode("\\", $node->name->parts) : '');
        } else if ($node instanceof \PHPParser_Node_Stmt_UseUse) {
            $this->importedClasses[$node->alias] = implode("\\", $node->name->parts);
        }

        return parent::shouldTraverse($t, $node, $parent);
    }
}