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
use Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\PhpType;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Reflection Usage Fixes
 *
 * This pass fixes several usages of the ``Reflection???`` classes. As such, it
 * complements the :doc:`Reflection Usage Checks </checks/reflection_usage_checks>` pass.
 *
 * @category fixes
 * @see ReflectionUsageCheckPass
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class ReflectionFixerPass extends FixingAnalyzerPass implements ConfigurablePassInterface
{
    use ConfigurableTrait;

    private $newContent;

    public function getConfiguration()
    {
        $tb = new TreeBuilder();
        $tb->root('reflection_fixes', 'array', new NodeBuilder())
            ->attribute('label', 'Rewrites certain reflection methods to use property access as this is faster and avoids certain PHP/APC bugs.')
            ->canBeEnabled()
        ;

        return $tb;
    }

    protected function isEnabled()
    {
        return true === $this->getSetting('enabled');
    }

    protected function getNewContent()
    {
        return $this->newContent;
    }

    protected function analyzeStream()
    {
        $this->newContent = '';

        $getNameType = $this->registry->createUnionType(array(
            $this->registry->getClassOrCreate('ReflectionZendExtension'),
            $this->registry->getClassOrCreate('ReflectionExtension'),
            $this->registry->getClassOrCreate('ReflectionFunction'),
            $this->registry->getClassOrCreate('ReflectionParameter'),
            $this->registry->getClassOrCreate('ReflectionClass'),
            $reflectionMethodType = $this->registry->getClassOrCreate('ReflectionMethod'),
        ));
        $reflectionPropertyType = $this->registry->getClassOrCreate('ReflectionProperty');

        $lastNode = null;
        while ($this->stream->moveNext()) {
            switch (true) {
                case $this->stream->node instanceof \PHPParser_Node_Expr_MethodCall
                        && $lastNode !== $this->stream->node:
                    if (NodeUtil::isCallToMethod($this->stream->node, 'getName')
                            && (null !== $type = $this->stream->node->var->getAttribute('type'))
                            && $this->isSubtype($type, $getNameType)) {
                        $this->newContent .= '->name';
                        $this->stream->skipUntil('END_OF_CALL');

                        break;
                    }

                    if (NodeUtil::isCallToMethod($this->stream->node, 'getDeclaringClass')
                            && (null !== $type = $this->stream->node->var->getAttribute('type'))
                            && ($this->isSubtype($type, $reflectionMethodType) || $this->isSubtype($type, $reflectionPropertyType))
                            && ($parent = $this->stream->node->getAttribute('parent'))
                            && NodeUtil::isCallToMethod($parent, 'getName')) {
                        $this->newContent .= '->class';
                        $this->stream->skipUntil('END_OF_CALL');
                        $this->stream->skipUntil('END_OF_CALL');

                        break;
                    }

                    $this->newContent .= $this->stream->token->getContent();

                    break;

                default:
                    $this->newContent .= $this->stream->token->getContent();
            }

            $lastNode = $this->stream->node;
        }
    }

    private function isSubtype(PhpType $a, PhpType $b)
    {
        if ($a->isUnknownType()) {
            return false;
        }

        if (null === $objType = $a->toMaybeObjectType()) {
            return false;
        }

        return $objType->isSubtypeOf($b);
    }
}
