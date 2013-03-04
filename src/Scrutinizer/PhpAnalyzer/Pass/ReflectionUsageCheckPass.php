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
use Scrutinizer\PhpAnalyzer\Model\GlobalFunction;
use Scrutinizer\PhpAnalyzer\Model\Comment;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\PhpType;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Reflection Usage Checks
 *
 * This pass checks usage of several of the ``Reflection???`` classes, and suggests
 * to re-write method calls to property access where possible.
 *
 * Property accesses are always faster, but more importantly there are some random,
 * and hard to debug `issues <https://bugs.php.net/bug.php?id=61384>`_ with method
 * calls on some APC versions. So, using property accesses is a double win.
 *
 * This pass is complemented by the :doc:`Reflection Usage Fixer </fixes/reflection_usage_fixes>`
 * which automatically performs the suggested fixes in your source code.
 *
 * @category checks
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class ReflectionUsageCheckPass extends AstAnalyzerPass implements ConfigurablePassInterface
{
    use ConfigurableTrait;

    public function getConfiguration()
    {
        $tb = new TreeBuilder();
        $tb->root('reflection_checks', 'array', new NodeBuilder())
            ->canBeDisabled()
            ->attribute('label', 'Checks certain usages of Reflection related classes.');
        ;

        return $tb;
    }

    protected function isEnabled()
    {
        return true === $this->getSetting('enabled');
    }

    public function shouldTraverse(NodeTraversal $t, \PHPParser_Node $node, \PHPParser_Node $parent = null)
    {
        switch (true) {
            case $node instanceof \PHPParser_Node_Expr_MethodCall:
                return $this->shouldTraverseMethodCall($t, $node, $parent);

            case $node instanceof \PHPParser_Node_Expr_FuncCall:
                $this->shouldTraverseFunctionCall($t, $node, $parent);

                return true;

            default:
                return true;
        }
    }

    private function shouldTraverseFunctionCall(NodeTraversal $t, \PHPParser_Node_Expr_FuncCall $node, \PHPParser_Node $parent = null)
    {
        $function = $this->typeRegistry->getCalledFunctionByNode($node);
        if (null === $function) {
            return;
        }

        assert($function instanceof GlobalFunction);
        switch ($function->getName()) {
            case 'is_subclass_of':
                if ( ! isset($node->args[1])) {
                    return;
                }

                if ( ! $node->args[1]->value instanceof \PHPParser_Node_Scalar_String) {
                    $this->phpFile->addComment($node->getLine(), Comment::warning(
                            'reflection_usage.could_be_subclass_problem',
                            'Due to PHP Bug #53727, ``is_subclass_of`` might return inconsistent results on some PHP versions if ``%argument%`` can be an interface. If so, you could instead use ``ReflectionClass::implementsInterface``.',
                            array('argument' => self::$prettyPrinter->prettyPrintExpr($node->args[1]->value))));

                    return;
                }

                $class = $this->typeRegistry->getClass($node->args[1]->value->value);
                if (null === $class || $class->isInterface()) {
                    $this->phpFile->addComment($node->getLine(), Comment::warning(
                            'reflection_usage.is_subclass_problem',
                            'Due to PHP Bug #53727, ``is_subclass_of`` returns inconsistent results on some PHP versions for interfaces; you could instead use ``ReflectionClass::implementsInterface``.'));

                    return;
                }
        }
    }

    private function shouldTraverseMethodCall(NodeTraversal $t, \PHPParser_Node $node, \PHPParser_Node $parent = null)
    {
        $type = $node->var->getAttribute('type');
        if (null === $type || !is_string($node->name)) {
            return true;
        }

        if (!$type->toMaybeObjectType()) {
            return true;
        }

        $methodName = strtolower($node->name);

        $zendExtension = $this->typeRegistry->getClassOrCreate('ReflectionZendExtension');
        $extension     = $this->typeRegistry->getClassOrCreate('ReflectionExtension');
        $function      = $this->typeRegistry->getClassOrCreate('ReflectionFunction');
        $parameter     = $this->typeRegistry->getClassOrCreate('ReflectionParameter');
        $method        = $this->typeRegistry->getClassOrCreate('ReflectionMethod');
        $class         = $this->typeRegistry->getClassOrCreate('ReflectionClass');

        switch (true) {
            case $this->isSubtype($type, $zendExtension):
            case $this->isSubtype($type, $extension):
            case $this->isSubType($type, $function):
            case $this->isSubType($type, $parameter):
            case $this->isSubType($type, $method):
                if ('getname' === $methodName) {
                    $this->phpFile->addComment($node->getLine(), $this->createGetNameComment($node));

                    return false;
                }

                return true;

            case $this->isSubType($type, $class):
                if ('getname' === $methodName) {
                    if ($node->var instanceof \PHPParser_Node_Expr_MethodCall
                            && (null !== $innerType = $node->var->var->getAttribute('type'))
                            && ($this->isSubType($innerType, $this->typeRegistry->getClassOrCreate('ReflectionMethod'))
                                    || $this->isSubType($innerType, $this->typeRegistry->getClassOrCreate('ReflectionProperty')))) {
                        $propertyAccess = new \PHPParser_Node_Expr_PropertyFetch($node->var->var, 'class');

                        $this->phpFile->addComment($node->getLine(), Comment::warning(
                            'reflection_usage.declaring_class_name',
                            'Consider using ``%property_access%``. There is [an issue](https://bugs.php.net/bug.php?id=61384) with ``getName()`` and APC-enabled PHP versions.',
                            array('property_access' => self::$prettyPrinter->prettyPrintExpr($propertyAccess)))->varyIn(array()));
                    } else {
                        $this->phpFile->addComment($node->getLine(), $this->createGetNameComment($node));
                    }

                    return false;
                }

                return true;

            default:
                return true;
        }
    }

    private function createGetNameComment(\PHPParser_Node_Expr_MethodCall $node)
    {
        $propertyAccess = new \PHPParser_Node_Expr_PropertyFetch($node->var, 'name');

        return Comment::warning(
                   'reflection_usage.get_name',
                   'Consider using ``%property_access%``. There is [an issue](https://bugs.php.net/bug.php?id=61384) with ``getName()`` and APC-enabled PHP versions.',
                   array('property_access' => self::$prettyPrinter->prettyPrintExpr($propertyAccess)))->varyIn(array());
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