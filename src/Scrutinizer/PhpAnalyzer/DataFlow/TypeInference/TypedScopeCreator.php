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

namespace Scrutinizer\PhpAnalyzer\DataFlow\TypeInference;

use Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil;
use Scrutinizer\PhpAnalyzer\PhpParser\Scope\Scope;
use Scrutinizer\PhpAnalyzer\PhpParser\Scope\ScopeCreatorInterface;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\ScopeBuilder\GlobalScopeBuilder;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\ScopeBuilder\LocalScopeBuilder;

/**
 * Creates the symbol table of variables available in the current scope and their types.
 *
 * Scopes created by this class are very different from scopes created by the SyntacticScopeCreator. These scopes have
 * type information, and include some qualified names in addition to variables.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
final class TypedScopeCreator implements ScopeCreatorInterface
{
    private $typeRegistry;

    public function __construct(TypeRegistry $registry)
    {
        $this->typeRegistry = $registry;
    }

    public function createScope(\PHPParser_Node $node, Scope $parent = null)
    {
        $thisType = $this->getThisType($node, $parent);

        // Constructing the global scope is very different than constructing inner scopes, because only global scopes
        // can contain named classes that show up in the type registry.
        $newScope = null;
        if (null === $parent) {
            $newScope = new Scope($node, null, $thisType);

            $builder = new GlobalScopeBuilder($newScope, $this->typeRegistry);
            NodeTraversal::traverseWithCallback($node, $builder);
        } else {
            $newScope = new Scope($node, $parent, $thisType);
            $builder = new LocalScopeBuilder($newScope, $this->typeRegistry);
            NodeTraversal::traverseWithCallback($node, $builder);
        }

        if (null !== $thisType) {
            $newScope->declareVar('this', $thisType);
        }

        return $newScope;
    }

    private function getThisType(\PHPParser_Node $node, Scope $parentScope = null)
    {
        $parent = $node;
        while (null !== $parent) {
            // As of PHP 5.4, closures inherit the this type of their parent scope. Since there is currently no way to
            // configure the PHP version that we build against, we will simply always infer the ThisType. Other passes,
            // can then perform checks whether ``$this`` may be accessed, allowing us to produce better error messages.
            if ($parent instanceof \PHPParser_Node_Expr_Closure) {
                if (null === $parentScope) {
                    return null;
                }

                return $parentScope->getTypeOfThis();
            }

            if (NodeUtil::isMethodContainer($parent)) {
                $name = implode("\\", $parent->namespacedName->parts);

                return $this->typeRegistry->getClassOrCreate($name);
            }

            $parent = $parent->getAttribute('parent');
        }

        return null;
    }
}