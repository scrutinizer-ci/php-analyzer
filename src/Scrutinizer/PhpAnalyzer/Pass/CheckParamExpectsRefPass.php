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

use Scrutinizer\PhpAnalyzer\Config\NodeBuilder;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil;
use Scrutinizer\PhpAnalyzer\Pass\AstAnalyzerPass;
use Scrutinizer\PhpAnalyzer\Model\Comment;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Parameter Reference Check
 *
 * Checks parameters of method calls if they expect a reference, and if so asserts
 * that the passed argument is a valid reference.
 *
 * Valid References are:
 *
 * - Variables, and Properties
 * - Array Elements (``$x[$y]``)
 * - New Expressions (``new A()``)
 *
 * @category checks
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class CheckParamExpectsRefPass extends AstAnalyzerPass implements ConfigurablePassInterface
{
    use ConfigurableTrait;

    public function getConfiguration()
    {
        $tb = new TreeBuilder();
        $tb->root('parameter_reference_check', 'array', new NodeBuilder())
            ->attribute('label', 'Parameter Reference Check')
            ->canBeDisabled()
        ;

        return $tb;
    }

    protected function isEnabled()
    {
        return $this->getSetting('enabled');
    }

    public function visit(NodeTraversal $t, \PHPParser_Node $node, \PHPParser_Node $parent = null)
    {
        if ( ! NodeUtil::isCallLike($node)) {
            return;
        }

        if (null === $function = $this->typeRegistry->getCalledFunctionByNode($node)) {
            return;
        }

        foreach ($function->getParameters() as $param) {
            if ( ! $param->isPassedByRef()) {
                continue;
            }

            $index = $param->getIndex();
            if ( ! isset($node->args[$index])) {
                continue;
            }

            $arg = $node->args[$index];
            if ( ! NodeUtil::canBePassedByRef($arg->value)) {
                $this->phpFile->addComment($arg->getLine(), Comment::error(
                    'usage.non_referencable_arg',
                    '``%argument%`` cannot be passed to ``%function_name%()`` as the parameter ``$%parameter_name%`` expects a reference.',
                    array('argument' => self::$prettyPrinter->prettyPrintExpr($arg->value),
                          'function_name' => $function->getName(),
                          'parameter_name' => $param->getName())
                ));
            }
        }
    }
}