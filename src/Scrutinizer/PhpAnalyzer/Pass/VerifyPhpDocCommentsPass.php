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

use Scrutinizer\PhpAnalyzer\Analyzer;
use Scrutinizer\PhpAnalyzer\Config\NodeBuilder;
use Scrutinizer\PhpAnalyzer\Model\MethodContainer;
use Scrutinizer\PhpAnalyzer\Model\Parameter;
use Scrutinizer\PhpAnalyzer\PhpParser\DocCommentParser;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\ObjectType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\PhpType;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\TypedScopeCreator;
use Scrutinizer\PhpAnalyzer\Model\Comment;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class VerifyPhpDocCommentsPass extends AstAnalyzerPass implements ConfigurablePassInterface
{
    use ConfigurableTrait;

    /** @var DocCommentParser */
    private $parser;

    private $importedNamespaces;
    private $config;

    public function getConfiguration()
    {
        $tb = new TreeBuilder();
        $tb->root('verify_php_doc_comments', 'array', new NodeBuilder())
            ->attribute('label', 'Verify PHPDoc')
            ->attribute('type', 'choice')
            ->canBeEnabled()
            ->children()
                ->booleanNode('parameters')
                    ->defaultTrue()
                    ->attribute('label', 'Verify types for parameters (@param).')
                ->end()
                ->booleanNode('return')
                    ->defaultTrue()
                    ->attribute('label', 'Verify return types (@return).')
                ->end()
                ->booleanNode('suggest_more_specific_types')
                    ->defaultFalse()
                    ->attribute('label', 'Suggest more specific types')
                    ->attribute('help', 'For example, instead of just writing "array", it would suggest to use "array<Type>". '
                             .'Instead of writing "mixed", it would suggest "null|Object". This helps the type '
                             .'inference engine, and IDEs to better understand your code, and makes all checks more useful.')
                ->end()
                ->booleanNode('ask_for_return_if_not_inferrable')
                    ->defaultFalse()
                    ->attribute('label', 'Ask for @return doc comment if return type is not inferrable')
                    ->attribute('help', 'If the type inference engine is not able to infer a return type, it can ask the user to '
                             .'add a @return annotation to the method/function. This helps the type inference '
                             .'engine, and IDEs to better understand your code, and makes all checks more useful.')
                ->end()
                ->booleanNode('ask_for_param_type_annotation')
                    ->defaultFalse()
                    ->attribute('label', 'Ask for a @param annotation if the type is not inferrable')
                    ->attribute('help', 'If the type inference engine is not able to infer a parameter type, it can ask the user to '
                             .'add a @param annotation to the method/function. This helps the type inference engine, and IDEs '
                             .'to better understand your code, and makes all checks more useful.')
                ->end()
            ->end()
        ;

        return $tb;
    }

    public function setAnalyzer(Analyzer $analyzer)
    {
        parent::setAnalyzer($analyzer);
        $this->parser = new DocCommentParser($this->typeRegistry);
    }

    protected function isEnabled()
    {
        return true === $this->getSetting('enabled');
    }

    public function enterScope(NodeTraversal $t)
    {
        $node = $t->getScopeRoot();

        $function = null;
        if ($node instanceof \PHPParser_Node_Stmt_Function) {
            $function = $this->typeRegistry->getFunction($node->name);
            if (null !== $function) {
                $this->parser->setCurrentClassName(null);
                $this->parser->setImportedNamespaces($this->importedNamespaces = $function->getImportedNamespaces());
            }
        } else if ($node instanceof \PHPParser_Node_Stmt_ClassMethod) {
            $objType = $t->getScope()->getTypeOfThis()->toMaybeObjectType();
            if (null !== $objType) {
                /** @var $objType MethodContainer */
                $this->parser->setCurrentClassName($objType->getName());
                $this->parser->setImportedNamespaces($this->importedNamespaces = $objType->getImportedNamespaces());
                $function = $objType->getMethod($node->name);
            }
        }

        if (null !== $function) {
            if (null !== $returnType = $function->getReturnType()) {
                $this->verifyReturnType($returnType, $node);
            }

            foreach ($function->getParameters() as $param) {
                /** @var $param Parameter */

                if (null !== $paramType = $param->getPhpType()) {
                    $this->verifyParamType($paramType, $node, $param->getName());
                }
            }
        }

        return true;
    }

    protected function getScopeCreator()
    {
        return new TypedScopeCreator($this->typeRegistry);
    }

    private function verifyParamType(PhpType $type, \PHPParser_Node $node, $paramName)
    {
        if (null === $commentType = $this->parser->getTypeFromParamAnnotation($node, $paramName)) {
            if ($this->getSetting('ask_for_param_type_annotation')) {
               if ($type->isAllType() || $type->isUnknownType()) {
                    $this->phpFile->addComment($node->getLine(), Comment::warning(
                        'php_doc.param_type_all_type_non_commented',
                        'Please add a ``@param`` annotation for parameter ``$%parameter%`` which defines a more specific range of types; something like ``string|array``, or ``null|MyObject``.',
                        array('parameter' => $paramName))->varyIn(array()));
                } else if ($this->typeRegistry->getNativeType('array')->isSubtypeOf($type)) {
                    $this->phpFile->addComment($node->getLine(), Comment::warning(
                        'php_doc.param_type_array_type_not_inferrable',
                        'Please add a ``@param`` annotation for parameter ``$%parameter%`` which defines the array type; using ``array<SomeType>``, or ``SomeType[]``.',
                        array('parameter' => $paramName))->varyIn(array()));
                }
            }

            return;
        }

        if ( ! $this->getSetting('parameters')) {
            return;
        }

        // If the type is not a subtype of the annotated type, then there is an
        // error somewhere (could also be in the type inference engine).
        if ( ! $type->isSubtypeOf($commentType)) {
            if ($this->getSetting('suggest_more_specific_types') && $this->typeRegistry->getNativeType('array')->isSubtypeOf($type)) {
                $this->phpFile->addComment($this->getLineOfParam($node, $paramName), Comment::warning(
                    'php_doc.param_type_mismatch_with_generic_array',
                    'Should the type for parameter ``$%parameter%`` not be ``%expected_type%``? Also, consider making the array more specific, something like ``array<String>``, or ``String[]``.',
                    array('parameter' => $paramName, 'expected_type' => $type->getDocType($this->importedNamespaces))));
            } else {
                $this->phpFile->addComment($this->getLineOfParam($node, $paramName), Comment::warning(
                    'php_doc.param_type_mismatch',
                    'Should the type for parameter ``$%parameter%`` not be ``%expected_type%``?',
                    array('parameter' => $paramName, 'expected_type' => $type->getDocType($this->importedNamespaces))));
            }

            return;
        }

        if ( ! $this->getSetting('suggest_more_specific_types')) {
            return;
        }

        if ( ! $this->isMoreSpecificType($type, $commentType)) {
            if ($type->isAllType()) {
                $this->phpFile->addComment($this->getLineOfParam($node, $paramName), Comment::warning(
                    'php_doc.param_type_all_type_more_specific',
                    'Please define a more specific type for parameter ``$%parameter%``; consider using a union like ``null|Object``, or ``string|array``.',
                    array('parameter' => $paramName))->varyIn(array()));
            } else if ($this->typeRegistry->getNativeType('array')->isSubtypeOf($type)) {
                $this->phpFile->addComment($this->getLineOfParam($node, $paramName), Comment::warning(
                    'php_doc.param_type_array_element_type',
                    'Please define the element type for the array of parameter ``$%parameter%`` (using ``array<SomeType>``, or ``SomeType[]``).',
                    array('parameter' => $paramName))->varyIn(array()));
            }

            return;
        }

        $this->phpFile->addComment($this->getLineOfParam($node, $paramName), Comment::warning(
            'php_doc.param_type_more_specific',
            'Consider making the type for parameter ``$%parameter%`` a bit more specific; maybe use ``%actual_type%``.',
             array('parameter' => $paramName, $type->getDocType($this->importedNamespaces))));
    }

    private function verifyReturnType(PhpType $type, \PHPParser_Node $node)
    {
        if (null === $commentType = $this->parser->getTypeFromReturnAnnotation($node)) {
            if ($this->getSetting('ask_for_return_if_not_inferrable') && $type->isUnknownType()) {
                $this->phpFile->addComment($node->getLine(), Comment::warning(
                    'php_doc.return_type_not_inferrable',
                    'Please add a ``@return`` annotation.'));
            }

            return;
        }

        if (!$this->getSetting('return')) {
            return;
        }

        if ( ! $type->isSubtypeOf($commentType)) {
            if ($this->getSetting('suggest_more_specific_types') && $this->typeRegistry->getNativeType('array')->isSubtypeOf($type)) {
                $this->phpFile->addComment($this->getLineOfReturn($node), Comment::warning(
                    'php_doc.return_type_mismatch_with_generic_array',
                    'Should the return type not be ``%expected_type%``? Also, consider making the array more specific, something like ``array<String>``, or ``String[]``.',
                    array('expected_type' => $type->getDocType($this->importedNamespaces))));
            } else {
                $this->phpFile->addComment($this->getLineOfReturn($node), Comment::warning(
                    'php_doc.return_type_mismatch',
                    'Should the return type not be ``%expected_type%``?',
                    array('expected_type' => $type->getDocType($this->importedNamespaces))));
            }

            return;
        }

        if ( ! $this->getSetting('suggest_more_specific_types')) {
            return;
        }

        if ( ! $this->isMoreSpecificType($type, $commentType)) {
            if ($type->isAllType()) {
                $this->phpFile->addComment($this->getLineOfReturn($node), Comment::warning(
                    'php_doc.return_type_all_type_more_specific',
                    'Please define a more specific return type; maybe using a union like ``null|Object``, or ``string|array``.'));
            } else if ($this->typeRegistry->getNativeType('array')->isSubtypeOf($type)) {
                $this->phpFile->addComment($this->getLineOfReturn($node), Comment::warning(
                    'php_doc.return_type_array_element_type',
                    'Please define the element type for the returned array (using ``array<SomeType>``, or ``SomeType[]``).'));
            }

            return;
        }

        $this->phpFile->addComment($this->getLineOfReturn($node), Comment::warning(
            'php_doc.return_type_more_specific',
            'Consider making the return type a bit more specific; maybe use ``%actual_type%``.',
            array('actual_type' => $type->getDocType($this->importedNamespaces))));
    }

    private function isMoreSpecificType(PhpType $actualType, PhpType $annotatedType)
    {
        if ($actualType->equals($annotatedType)) {
            return false;
        }

        if ($actualType->isUnknownType()) {
            return false;
        }

        return $actualType->isSubtypeOf($annotatedType);
    }

    private function getLineOfParam(\PHPParser_Node $node, $paramName)
    {
        $regex = '/@param\s+(?:[^\s]+\s+\$'.preg_quote($paramName, '/').'|'.preg_quote($paramName, '/').'\s+[^\s]+)(.*)$/is';
        if (!preg_match($regex, $comment = $node->getDocComment(), $match)) {
            return $node->getLine() - 1;
        }

        return $node->getLine() - substr_count($match[1], "\n") - 1;
    }

    private function getLineOfReturn(\PHPParser_Node $node)
    {
        $comment = $node->getDocComment();

        return $node->getLine() - substr_count($comment, "\n", strripos($comment, '@return')) - 1;
    }
}