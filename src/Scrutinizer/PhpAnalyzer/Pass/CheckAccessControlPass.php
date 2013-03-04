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

use Scrutinizer\PhpAnalyzer\AnalyzerAwareInterface;
use Scrutinizer\PhpAnalyzer\Config\NodeBuilder;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal;
use Scrutinizer\PhpAnalyzer\Pass\AstAnalyzerPass;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\TypedScopeCreator;
use Scrutinizer\PhpAnalyzer\Model\ClassMethod;
use Scrutinizer\PhpAnalyzer\Model\Comment;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Access Control Checks
 *
 * This pass checks that all property accesses, and method calls are allowed from
 * the context they are performed in::
 *
 *     class A
 *     {
 *         private $a;
 *     }
 *
 *     $a = new A();
 *     $a->a; // not permitted
 *
 * @category checks
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class CheckAccessControlPass extends AstAnalyzerPass implements AnalyzerAwareInterface, ConfigurablePassInterface
{
    use ConfigurableTrait;

    public function getConfiguration()
    {
        $tb = new TreeBuilder();
        $tb->root('check_access_control', 'array', new NodeBuilder())
            ->attribute('label', 'Access Control related Checks')
            ->canBeDisabled()
        ;

        return $tb;
    }

    public function visit(NodeTraversal $t, \PHPParser_Node $node, \PHPParser_Node $parent = null)
    {
        if ( ! $this->getSetting('enabled')) {
            return;
        }

        $this->checkCall($t, $node);
        $this->checkPropertyFetch($t, $node);
    }

    protected function getScopeCreator()
    {
        return new TypedScopeCreator($this->typeRegistry);
    }

    private function checkPropertyFetch(NodeTraversal $t, \PHPParser_Node $node)
    {
        if ( ! $node instanceof \PHPParser_Node_Expr_StaticPropertyFetch
                && ! $node instanceof \PHPParser_Node_Expr_PropertyFetch) {
            return;
        }

        if (null === $property = $this->typeRegistry->getFetchedPropertyByNode($node)) {
            return;
        }

        if ($property->isPublic()) {
            return;
        }

        $thisType = $t->getScope()->getTypeOfThis();
        $objType = $property->getClass();

        if ($property->isPrivate()) {
            // For private properties, we need to get the type of the class
            // where it was defined.
            $objType = $property->getDeclaringClassType();

            if (null === $thisType || ! $thisType->equals($objType)) {
                $this->phpFile->addComment($node->getLine(), $this->createInAccessiblePropertyError($property->getName(), $objType->getName(), 'private'));

                return;
            }

            // Check that ``static`` is not used with private properties.
            if ($node instanceof \PHPParser_Node_Expr_StaticPropertyFetch
                    && $node->class instanceof \PHPParser_Node_Name
                    && 1 === count($node->class->parts)
                    && 'static' === strtolower($node->class->parts[0])) {
                $this->phpFile->addComment($node->getLine(), Comment::error(
                        'access_control.static_with_private_property',
                        'Since ``$%property_name%`` is declared private, accessing it with ``static`` will lead to errors in possible sub-classes; you can either use ``self``, or increase the visibility of ``$%property_name%`` to at least protected.',
                        array('property_name' => $property->getName())));
            }

            return;
        }

        if ($property->isProtected()) {
            if (null === $thisType || ! $thisType->isSubTypeOf($objType)) {
                $this->phpFile->addComment($node->getLine(), $this->createInAccessiblePropertyError($property->getName(), $objType->getName(), 'protected'));
            }

            return;
        }
    }

    private function checkCall(NodeTraversal $t, \PHPParser_Node $node)
    {
        if ( ! $node instanceof \PHPParser_Node_Expr_StaticCall
                && ! $node instanceof \PHPParser_Node_Expr_MethodCall) {
            return;
        }

        $method = $this->typeRegistry->getCalledFunctionByNode($node);
        if ( ! $method instanceof ClassMethod) {
            return;
        }
        $type = $method->getClass();

        if ($method->isPublic()) {
            return;
        }

        $thisType = $t->getScope()->getTypeOfThis();

        if ($method->isPrivate()) {
            $type = $method->getDeclaringClassType();

            if (null === $thisType || ! $thisType->equals($type)) {
                $this->phpFile->addComment($node->getLine(), $this->createInAccessibleMethodError($method->getMethod()->getName(), $type->getName(), 'private'));

                return;
            }

            // Check that ``static`` is not used with private methods.
            if ($node instanceof \PHPParser_Node_Expr_StaticCall
                    && $node->class instanceof \PHPParser_Node_Name
                    && 1 === count($node->class->parts)
                    && 'static' === strtolower($node->class->parts[0])) {
                $this->phpFile->addComment($node->getLine(), Comment::error(
                        'access_control.static_with_private_method',
                        'Since ``%method_name%()`` is declared private, calling it with ``static`` will lead to errors in possible sub-classes. You can either use ``self``, or increase the visibility of ``%method_name%()`` to at least protected.',
                        array('method_name' => $method->getMethod()->getName())));
            }

            return;
        }

        if ($method->isProtected()) {
            if (null === $thisType || ! $thisType->isSubTypeOf($type)) {
                $this->phpFile->addComment($node->getLine(), $this->createInAccessibleMethodError($method->getMethod()->getName(), $type->getName(), 'protected'));
            }

            return;
        }

        throw new \LogicException('Invalid state.');
    }

    private function createInAccessibleMethodError($methodName, $className, $visibility)
    {
        return Comment::error(
            'access_control.inaccessible_method',
            'The method "%method_name%()" cannot be called from this context as it is declared %visibility% in class "%class_name%"?',
            array('method_name' => $methodName, 'class_name' => $className, 'visibility' => $visibility));
    }

    private function createInAccessiblePropertyError($propertyName, $className, $visibility)
    {
        return Comment::error(
            'access_control.inaccessible_property',
            'The property "%property_name%" cannot be accessed from this context as it is declared %visibility% in class "%class_name%"?',
            array('property_name' => $propertyName, 'class_name' => $className, 'visibility' => $visibility));
    }
}