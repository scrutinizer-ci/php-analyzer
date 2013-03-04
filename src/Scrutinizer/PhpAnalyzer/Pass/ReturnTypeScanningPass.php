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
use Scrutinizer\PhpAnalyzer\PhpParser\Type\PhpType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\UnionTypeBuilder;
use Scrutinizer\PhpAnalyzer\Pass\AstAnalyzerPass;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\TypedScopeCreator;
use Scrutinizer\PhpAnalyzer\Pass\RepeatedPass;
use Scrutinizer\PhpAnalyzer\Pass\RepeatedPassAwareInterface;
use Scrutinizer\PhpAnalyzer\Model\ContainerMethodInterface;
use Scrutinizer\PhpAnalyzer\Model\GlobalFunction;

/**
 * Determines the return type of functions/methods by looking at their exit points.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class ReturnTypeScanningPass extends AstAnalyzerPass implements RepeatedPassAwareInterface
{
    private $repeatedPass;

    public function setRepeatedPass(RepeatedPass $repeatedPass)
    {
        $this->repeatedPass = $repeatedPass;
    }

    public function enterScope(NodeTraversal $t)
    {
        $scope = $t->getScope();
        $root = $scope->getRootNode();

        if (!$root instanceof \PHPParser_Node_Stmt_Function
                && !$root instanceof \PHPParser_Node_Stmt_ClassMethod) {
            return;
        }

        // Bail out on abstract methods.
        if ($root instanceof \PHPParser_Node_Stmt_ClassMethod
                && ($root->type & \PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT) !== 0) {
            return;
        }

        // Bail out on methods defined on interfaces.
        if ($root instanceof \PHPParser_Node_Stmt_ClassMethod
                && $root->getAttribute('parent')->getAttribute('parent') instanceof \PHPParser_Node_Stmt_Interface) {
            return;
        }

        // Bail out on built-in functions marked by the @jms-builtin annotation.
        // For these, we will solely infer types from doc comments.
        if (false !== strpos($root->getDocComment(), '@jms-builtin')) {
            return;
        }

        // Same as above, but for methods of classes marked with @jms-builtin.
        if ($root instanceof \PHPParser_Node_Stmt_ClassMethod) {
            $maybeClass = $root->getAttribute('parent')->getAttribute('parent');
            if ($maybeClass instanceof \PHPParser_Node_Stmt_Class
                    && false !== strpos($maybeClass->getDocComment(), '@jms-builtin')) {
                return;
            }
        }

        $cfg = $t->getControlFlowGraph();

        $builder = new UnionTypeBuilder($this->typeRegistry);
        foreach ($cfg->getNode(null)->getInEdges() as $edge) {
            $sourceNode = $edge->getSource()->getAstNode();

            if (!$sourceNode instanceof \PHPParser_Node_Stmt_Return) {
                $builder->addAlternate($this->typeRegistry->getNativeType('null'));
                continue;
            }

            // If there is no type information available, we cannot make any
            // assumptions for this function/method.
            if (!$type = $sourceNode->getAttribute('type')) {
                return;
            }

            $builder->addAlternate($type);
        }

        $type = $builder->build();
        if ($type->isUnknownType()) {
            return;
        }

        $function = $this->typeRegistry->getFunctionByNode($root);
        if ($function instanceof GlobalFunction) {
            if ($this->hasTypeChanged($type, $function->getReturnType())) {
                $this->repeatedPass->repeat();
            }

            $function->setReturnType($type);
        } else if ($function instanceof ContainerMethodInterface) {
            $method = $function->getMethod();

            if ($this->hasTypeChanged($type, $method->getReturnType())) {
                $this->repeatedPass->repeat();
            }

            $method->setReturnType($type);
        }
    }

    private function hasTypeChanged(PhpType $newType, PhpType $currentType = null)
    {
        if (null === $currentType) {
            return true;
        }

        return false === $newType->equals($currentType);
    }

    protected function getScopeCreator()
    {
        return new TypedScopeCreator($this->typeRegistry);
    }
}