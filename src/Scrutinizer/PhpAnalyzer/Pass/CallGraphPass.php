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
use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;
use Scrutinizer\PhpAnalyzer\Pass\AstAnalyzerPass;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\TypedScopeCreator;
use Scrutinizer\PhpAnalyzer\Model\AbstractFunction;
use Scrutinizer\PhpAnalyzer\Model\CallGraph\Argument;
use Scrutinizer\PhpAnalyzer\Model\CallGraph\CallSite;

class CallGraphPass extends AstAnalyzerPass
{
    public function shouldTraverse(NodeTraversal $t, \PHPParser_Node $node, \PHPParser_Node $parent = null)
    {
        if ($node instanceof \PHPParser_Node_Stmt_Function) {
            $function = $this->typeRegistry->getFunctionByNode($node, TypeRegistry::LOOKUP_CACHE_ONLY);
            if ($function) {
                $this->removeOutCallSites($function);

                // We need to copy over all the current in-call-sites to the non-cached function.
                $nonCachedFunction = $this->typeRegistry->getFunctionByNode($node);
                $this->copyInCallSites($function, $nonCachedFunction);
            }
        } else if ($node instanceof \PHPParser_Node_Stmt_Class) {
            $class = $this->typeRegistry->getClassByNode($node, TypeRegistry::LOOKUP_CACHE_ONLY);
            if ($class) {
                $nonCachedClass = $this->typeRegistry->getClassByNode($node);

                foreach ($class->getMethods() as $method) {
                    // If the method has not been declared in this class, then its out call sites
                    // are not affected.
                    if ($method->isInherited()) {
                        continue;
                    }

                    $this->removeOutCallSites($method->getMethod());

                    if ($nonCachedMethod = $nonCachedClass->getMethod($method->getName())) {
                        $this->copyInCallSites($method->getMethod(), $nonCachedMethod->getMethod());
                    }
                }
            }
        }

        return true;
    }

    public function visit(NodeTraversal $t, \PHPParser_Node $node, \PHPParser_Node $parent = null)
    {
        $target = null;

        if ($node instanceof \PHPParser_Node_Expr_FuncCall && $node->name instanceof \PHPParser_Node_Name) {
            $target = $this->typeRegistry->getFunction(implode("\\", $node->name->parts));
        } else if ($objType = $this->getReturnType($node)) {
            $objType->restrictByNotNull();
            $objType = $objType->toMaybeObjectType();
            if ($objType && null !== $method = $objType->getMethod($node->name)) {
                $target = $method->getMethod();
            }

            // TODO: We should also go through unions here to see if there are object types
            //       for which we can add calls.
        }

        if ($target && null !== $source = $this->getSource($t)) {
            $site = CallSite::create($source, $target, $this->createArgs($node->args));
            $site->setAstNode($node);
        }
    }

    protected function getScopeCreator()
    {
        return new TypedScopeCreator($this->typeRegistry);
    }

    private function getReturnType(\PHPParser_Node $node)
    {
        switch (true) {
            case $node instanceof \PHPParser_Node_Expr_MethodCall:
                if ( ! is_string($node->name)) {
                    return null;
                }

                return $node->var->getAttribute('type');

            case $node instanceof \PHPParser_Node_Expr_StaticCall:
                if ( ! is_string($node->name)) {
                    return null;
                }

                return $node->class->getAttribute('type');
        }

        return null;
    }

    private function createArgs(array $args)
    {
        $rs = array();
        foreach ($args as $index => $arg) {
            $rs[$index] = new Argument($index);
            $rs[$index]->setAstNode($arg);

            if ($type = $arg->getAttribute('type')) {
                $rs[$index]->setPhpType($type);
            } else {
                $rs[$index]->setPhpType($this->typeRegistry->getNativeType('unknown'));
            }
        }

        return $rs;
    }

    /**
     * Returns the originating function, or method.
     *
     * @param NodeTraversal $t
     *
     * @return AbstractFunction|null
     */
    private function getSource(NodeTraversal $t)
    {
        $root = $t->getScopeRoot();

        if ($root instanceof \PHPParser_Node_Stmt_Function) {
            return $this->typeRegistry->getFunctionByNode($root);
        } else if ($root instanceof \PHPParser_Node_Stmt_ClassMethod) {
            // If the originating object was not part of this packages' dependencies, or we
            // have not scanned it for some other reason, we have to bail out here.
            if (null === $thisObject = $t->getScope()->getTypeOfThis()->toMaybeObjectType()) {
                return null;
            }

            // This could be the case if the same class has been defined more than once.
            // We had such cases for example when people add fixtures for code generation to their
            // packages, and do not ensure that class names are unique.
            if (null === $classMethod = $thisObject->getMethod($root->name)) {
                return null;
            }

            return $classMethod->getMethod();
        }

        return null;
    }

    private function copyInCallSites(AbstractFunction $function, AbstractFunction $nonCachedFunction)
    {
        foreach ($function->getInCallSites() as $site) {
            $source = $site->getSource();
            $args   = $site->getArgs()->getValues();
            $args = array_map(function($arg) { return clone $arg; }, $args);

            $source->removeCallSite($site);
            CallSite::create($source, $nonCachedFunction, $args);
        }
    }

    private function removeOutCallSites(AbstractFunction $function)
    {
        foreach ($function->getOutCallSites() as $site) {
            $site->getTarget()->removeCallSite($site);
        }
    }
}