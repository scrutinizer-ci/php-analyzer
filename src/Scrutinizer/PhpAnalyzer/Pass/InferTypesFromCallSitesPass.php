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
use Scrutinizer\PhpAnalyzer\PhpParser\Type\UnionTypeBuilder;
use Scrutinizer\PhpAnalyzer\Pass\AstAnalyzerPass;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\TypedScopeCreator;
use Scrutinizer\PhpAnalyzer\Model\AbstractFunction;
use Scrutinizer\PhpAnalyzer\Model\Parameter;

class InferTypesFromCallSitesPass extends AstAnalyzerPass
{
    public function enterScope(NodeTraversal $t)
    {
        $node = $t->getScopeRoot();

        $function = null;
        if ($node instanceof \PHPParser_Node_Stmt_ClassMethod) {
            $function = $t->getScope()->getTypeOfThis()->getMethod($node->name)->getMethod();
        } else if ($node instanceof \PHPParser_Node_Stmt_Function) {
            $function = $this->typeRegistry->getFunctionByNode($node);
        }

        if (null !== $function) {
            $this->inferTypesForFunction($function);
        }
    }

    protected function getScopeCreator()
    {
        return new TypedScopeCreator($this->typeRegistry);
    }

    private function inferTypesForFunction(AbstractFunction $function)
    {
        // Infer types for parameters.
        foreach ($function->getParameters() as $param) {
            if ( ! $param->getPhpType()->isUnknownType()) {
                continue;
            }

            $this->inferTypeForParameter($function, $param);
        }
    }

    private function inferTypeForParameter(AbstractFunction $function, Parameter $param)
    {
        $builder = new UnionTypeBuilder($this->typeRegistry);
        $index   = $param->getIndex();

        foreach ($function->getInCallSites() as $site) {
            $args = $site->getArgs();

            if (!isset($args[$index])) {
                continue;
            }

            $builder->addAlternate($args[$index]->getPhpType());
        }

        $newType = $builder->build();
        if (  ! $newType->isNoType()
                && ! $newType->isUnknownType()) {
            $param->setPhpType($newType);
        }
    }
}