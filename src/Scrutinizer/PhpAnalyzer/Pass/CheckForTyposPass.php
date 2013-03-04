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
use Scrutinizer\PhpAnalyzer\Pass\AstAnalyzerPass;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\TypedScopeCreator;
use Scrutinizer\PhpAnalyzer\Model\Property;
use Scrutinizer\PhpAnalyzer\Model\Comment;

/**
 * Method, and Property Existence Checks
 *
 * This pass checks whether a called method, or an accessed property exists. It
 * will also try to detect typos if non-existent, and suggest alternatives::
 *
 *     class A
 *     {
 *         private $emptyProperty;
 *     }
 *
 *     $a = new A();
 *     $a->emtpyProperty = 'foo'; // will suggest to fix the properties name
 *
 * @category checks
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class CheckForTyposPass extends AstAnalyzerPass implements ConfigurablePassInterface
{
    use ConfigurableTrait;

    private $t;

    public function getConfiguration()
    {
        $tb = new \Symfony\Component\Config\Definition\Builder\TreeBuilder();
        $tb->root('typo_checks', 'array', new NodeBuilder())
            ->attribute('label', 'Check for Typos')
            ->canBeDisabled()
        ;

        return $tb;
    }

    public function visit(NodeTraversal $t, \PHPParser_Node $node, \PHPParser_Node $parent = null)
    {
        if ( ! $this->getSetting('enabled')) {
            return;
        }

        $this->t = $t;

        switch (true) {
            case $node instanceof \PHPParser_Node_Expr_PropertyFetch:
                $this->checkForMissingProperty($node);
                break;

            case $node instanceof \PHPParser_Node_Expr_StaticCall:
            case $node instanceof \PHPParser_Node_Expr_MethodCall:
                $this->checkForMissingMethod($node);
                break;

            // TODO: Check for static property access/static method calls
        }
    }

    protected function getScopeCreator()
    {
        return new TypedScopeCreator($this->analyzer->getTypeRegistry());
    }

    private function checkForMissingMethod(\PHPParser_Node $node)
    {
        assert($node instanceof \PHPParser_Node_Expr_StaticCall
                   || $node instanceof \PHPParser_Node_Expr_MethodCall);

        if ( ! is_string($node->name)) {
            return;
        }

        // TODO: Add support for union types. Currently, these are not checked as
        //       getCalledClassByNode only returns a single object type.
        if (null === $objType = $this->typeRegistry->getCalledClassByNode($node)) {
            return;
        }

        if ( ! $objType->isNormalized() || $objType->hasMethod($node->name)) {
            return;
        }

        // TODO: Remove once anonymous classes are supported.
        if ($objType->isSubTypeOf($this->typeRegistry->getClassOrCreate(
                'PHPUnit_Framework_MockObject_MockObject'))) {
            return;
        }

        if (null !== $bestName = $this->getMostSimilarName($node->name, $objType->getMethodNames())) {
            $this->phpFile->addComment($node->getLine(), Comment::error(
                'typos.misspelled_method_name',
                'The method "%offending_method_name%()" does not exist. Did you maybe mean "%closest_method_name%()"?',
                array('offending_method_name' => $node->name, 'closest_method_name' => $bestName)));
        } else {
            if ($node instanceof \PHPParser_Node_Expr_MethodCall && $objType->hasMethod('__call')) {
                $this->phpFile->addComment($node->getLine(), Comment::warning(
                    'strict.maybe_undocumented_call_capability',
                    'The method ``%method_name%`` does not exist on ``%type%``? Since you implemented ``__call``, maybe consider adding a [@method annotation](http://www.phpdoc.org/docs/latest/for-users/tags/method.html).',
                    array('method_name' => $node->name, 'type' => $objType->getDisplayName())));

                return;
            }

            if ($node instanceof \PHPParser_Node_Expr_StaticCall && $objType->hasMethod('__callStatic')) {
                $this->phpFile->addComment($node->getLine(), Comment::warning(
                    'strict.maybe_undocumented_static_call_capability',
                    'The method ``%method_name%`` does not exist on ``%type%``? Since you implemented ``__callStatic``, maybe consider adding a [@method annotation](http://www.phpdoc.org/docs/latest/for-users/tags/method.html).',
                    array('method_name' => $node->name, 'type' => $objType->getDisplayName())));

                return;
            }

            $this->phpFile->addComment($node->getLine(), Comment::error(
                'typos.non_existent_method',
                'The method "%method_name%()" does not exist on ``%type%``?',
                array('method_name' => $node->name, 'type' => $objType->getDisplayName())));
        }
    }

    private function checkForMissingProperty(\PHPParser_Node_Expr_PropertyFetch $node)
    {
        if (!is_string($node->name)) {
            return;
        }

        if (!$objType = $node->var->getAttribute('type')) {
            return;
        }

        $objType = $objType->restrictByNotNull()->toMaybeObjectType();
        if (!$objType) {
            // TODO: Add support to check on union types.
            return;
        }

        if ($objType->isInterface()) {
            $this->phpFile->addComment($node->getLine(), Comment::warning(
                'types.property_access_on_interface',
                'Accessing "%property_name%" on the interface "%interface_name%" suggest that you code against a concrete implementation. How about adding an ``instanceof`` check?',
                array('property_name' => $node->name, 'interface_name' => $objType->getName())));

            return;
        }

        if (!$objType->isNormalized() || $objType->hasProperty($node->name)) {
            return;
        }

        // Ignore all property accesses on ``stdClass`` objects. Currently, we have
        // no way to describe, or track RECORD types. As such, any messages related to
        // stdClass are likely wrong, but at least there are too many false-positives
        // for now. So, we just disable this.
        if ($objType->isSubtypeOf($this->typeRegistry->getClassOrCreate('stdClass'))) {
            return;
        }

        // Ignore all property reads on ``SimpleXMLElement`` objects. The reasoning
        // behind this is similar to the exception for ``stdClass`` objects above.
        // We simply have no reliable way to describe the structure of these objects
        // for now. So, we disable checks for them to avoid a flood of false-positives.
        if ( ! \Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil::isAssignmentOp($node->getAttribute('parent'))
                && $objType->isSubtypeOf($this->typeRegistry->getClassOrCreate('SimpleXMLElement'))) {
            return;
        }

        // Property accesses inside isset() are safe, do not make any noise just yet.
        if ($node->getAttribute('parent') instanceof \PHPParser_Node_Expr_Isset) {
            return;
        }

        if (null !== $bestName = $this->getMostSimilarName($node->name, $objType->getPropertyNames())) {
            $this->phpFile->addComment($node->getLine(), Comment::error(
                'typos.mispelled_property_name',
                'The property "%offending_property_name%" does not exist. Did you mean "%closest_property_name%"?',
                array('offending_property_name' => $node->name, 'closest_property_name' => $bestName)));
        } else {
            // Ignore additional accesses to this property.
            $objType->addProperty(new Property($node->name));

            if (\Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil::isAssignmentOp($node->getAttribute('parent'))) {
                if ($objType->hasMethod('__set')) {
                    $this->phpFile->addComment($node->getLine(), Comment::warning(
                        'strict.maybe_undocument_property_write_capability',
                        'The property ``%property_name%`` does not exist. Since you implemented ``__set``, maybe consider adding a [@property annotation](http://www.phpdoc.org/docs/latest/for-users/tags/property.html).',
                        array('property_name' => $node->name)));

                    return;
                }
            } else if ($objType->hasMethod('__get')) {
                $this->phpFile->addComment($node->getLine(), Comment::warning(
                    'strict.maybe_undocument_property_read_capability',
                    'The property ``%property_name%`` does not exist. Since you implemented ``__get``, maybe consider adding a [@property annotation](http://www.phpdoc.org/docs/latest/for-users/tags/property.html).',
                    array('property_name' => $node->name)));

                return;
            }

            $thisType = $this->t->getScope()->getTypeOfThis();
            if ($thisType && $thisType->equals($objType)) {
                $this->phpFile->addComment($node->getLine(), Comment::warning(
                    'strict.undeclared_property',
                    'The property "%property_name%" does not exist. Did you maybe forget to declare it?',
                    array('property_name' => $node->name)));
            } else {
                $this->phpFile->addComment($node->getLine(), Comment::error(
                    'typos.non_existent_property',
                    'The property "%property_name%" does not exist.',
                    array('property_name' => $node->name)));
            }
        }
    }

    public static function getMostSimilarName($name, array $availableNames)
    {
        $similarity = array();
        foreach ($availableNames as $aName) {
            similar_text($name, $aName, $percentage);
            $similarity[$aName] = $percentage;
        }

        arsort($similarity);
        $bestPercentage = reset($similarity);

        if ($bestPercentage < 90) {
            return null;
        }

        return key($similarity);
    }
}