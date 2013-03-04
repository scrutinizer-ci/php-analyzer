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
use Scrutinizer\PhpAnalyzer\AnalyzerAwareInterface;
use Scrutinizer\PhpAnalyzer\ArgumentChecker\ArgumentCheckerInterface;
use Scrutinizer\PhpAnalyzer\ArgumentChecker\DefaultArgumentChecker;
use Scrutinizer\PhpAnalyzer\ArgumentChecker\OverloadedCoreFunctionChecker;
use Scrutinizer\PhpAnalyzer\ArgumentChecker\PhpUnitAssertionChecker;
use Scrutinizer\PhpAnalyzer\Config\NodeBuilder;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\TypedScopeCreator;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\PhpType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeChecker;
use Scrutinizer\PhpAnalyzer\Pass\AstAnalyzerPass;
use Scrutinizer\PhpAnalyzer\Model\AbstractFunction;
use Scrutinizer\PhpAnalyzer\Model\Comment;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Miscellaneous Usage Context Checks
 *
 * This pass performs a couple of context sensitive usage checks.
 *
 * 1. Method Calls on Non-Objects
 * ------------------------------
 *
 * .. code-block :: php
 *
 *     class A
 *     {
 *         private $b;
 *
 *         public function __construct(B $b = null)
 *         {
 *             $this->b = $b;
 *         }
 *
 *         public function foo()
 *         {
 *             $this->b->bar(); // possible method call on null
 *         }
 *     }
 *
 * 2. Foreach Expression, and Value Variable
 * -----------------------------------------
 * Checks that the expression is traversable, and if the variable is passed as
 * reference, it also ensures that the expression can be used as a reference.
 *
 * .. code-block :: php
 *
 *     foreach ($expression as &$variable) { }
 *
 * 3. Missing Argument on Function/Method Calls
 * --------------------------------------------
 * Checks whether a function, or method call misses a required argument.
 *
 * .. code-block :: php
 *
 *     function foo($a) { }
 *     foo(); // missing argument
 *
 * 4. Argument Type Check
 * ----------------------
 * Checks whether an argument type can be passed to a function/method. Currently,
 * two different levels are supported:
 *
 * 1. **Strict**: The passed type must be a sub-type of the expected type.
 * 2. **Lenient** (default): In lenient mode, we make a few exceptions to the strict sub-type requirement.
 *    For example, we treat string, integer, and double types as interchangable. So, if a string is
 *    expected, an integer, or a double would also be acceptable.
 *
 * .. tip ::
 *
 *     If you have an existing code-base, lenient mode is most likely what you want. If
 *     you start a new project, using strict type checking is recommended.
 *
 * @category checks
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class CheckUsageContextPass extends AstAnalyzerPass implements ConfigurablePassInterface, AnalyzerAwareInterface
{
    use ConfigurableTrait;

    /** @var TypeChecker */
    private $typeChecker;

    /** @var ArgumentCheckerInterface */
    private $argumentChecker;

    public function setAnalyzer(Analyzer $analyzer)
    {
        parent::setAnalyzer($analyzer);

        $this->typeChecker = new TypeChecker($this->typeRegistry);

        $this->argumentChecker = new OverloadedCoreFunctionChecker($this->typeRegistry, $this->typeChecker);
        $this->argumentChecker->append(new PhpUnitAssertionChecker($this->typeRegistry, $this->typeChecker));
        $this->argumentChecker->append(new DefaultArgumentChecker($this->typeRegistry, $this->typeChecker));
    }

    private function afterConfigSet()
    {
        $this->typeChecker->setLevel($this->getSetting('argument_type_checks'));
    }

    public function getConfiguration()
    {
        $tb = new TreeBuilder();
        $tb->root('check_usage_context', 'array', new NodeBuilder())
            ->attribute('label', 'Usage Context')
            ->canBeDisabled()
            ->children()
                ->arrayNode('method_call_on_non_object')
                    ->addDefaultsIfNotSet()
                    ->canBeDisabled()
                    ->children()
                        ->booleanNode('ignore_null_pointer')
                            ->info('Does not display a warning when a type such as "object|null" is encountered.')
                            ->defaultTrue()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('foreach')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->booleanNode('value_as_reference')
                            ->defaultTrue()
                            ->attribute('label', 'Check whether the foreach expression supports a value reference.')
                        ->end()
                        ->booleanNode('traversable')
                            ->defaultTrue()
                            ->attribute('label', 'Check whether the foreach expression is traversable.')
                        ->end()
                    ->end()
                ->end()
                ->booleanNode('missing_argument')
                    ->defaultTrue()
                    ->attribute('label', 'Check whether a method/function call misses a required argument.')
                ->end()
                ->enumNode('argument_type_checks')
                    ->defaultValue('lenient')
                    ->attribute('label', 'Check types of Arguments.')
                    ->values(array('disabled', 'lenient', 'strict'))
        ;

        return $tb;
    }

    protected function isEnabled()
    {
        return true === $this->getSetting('enabled');
    }

    public function shouldTraverse(NodeTraversal $t, \PHPParser_Node $node, \PHPParser_Node $parent = null)
    {
        if ($node instanceof \PHPParser_Node_Expr_MethodCall) {
            $objType = $node->var->getAttribute('type');
            if ($this->getSetting('method_call_on_non_object.enabled') && $objType) {
                // By default, we ignore null as it simply occurs too often, and is annoying otherwise.
                if ($this->getSetting('method_call_on_non_object.ignore_null_pointer')) {
                    $objType = $objType->restrictByNotNull();
                }

                if ( ! $objType->matchesObjectContext()) {
                    $this->phpFile->addComment($node->var->getLine(), Comment::error(
                        'usage.method_call_on_non_object',
                        'The method ``%method_expr%`` cannot be called on ``%variable_expr%`` (of type ``%type_name%``).',
                        array('method_expr' => is_string($node->name) ? $node->name : self::$prettyPrinter->prettyPrintExpr($node->name),
                              'variable_expr' => self::$prettyPrinter->prettyPrintExpr($node->var),
                              'type_name' => (string) $objType)));
                }
            }

            if ($objType && is_string($node->name)) {
                $this->checkMethodParameters($node, $objType, $node->name, $node->args);
            }
        } else if ($node instanceof \PHPParser_Node_Expr_StaticCall) {
            $objType = $node->class->getAttribute('type');

            if ($objType && is_string($node->name)) {
                $this->checkMethodParameters($node, $objType, $node->name, $node->args);
            }
        } else if ($node instanceof \PHPParser_Node_Expr_FuncCall) {
            $this->checkFunctionParameters($node);
        } else if ($node instanceof \PHPParser_Node_Stmt_Foreach) {
            $this->checkForeach($node);
        }

        return true;
    }

    public function checkForeach(\PHPParser_Node_Stmt_Foreach $node)
    {
        if ($this->getSetting('foreach.value_as_reference') && $node->byRef && !NodeUtil::canBePassedByRef($node->expr)) {
            $this->phpFile->addComment($node->expr->getLine(), Comment::error(
                'usage_context.foreach_expr_no_reference',
                'The expression ``%expr%`` cannot be used as a reference.',
                array('expr' => self::$prettyPrinter->prettyPrintExpr($node->expr))));
        }

        $this->checkTraversable($node);
    }

    private function checkTraversable(\PHPParser_Node_Stmt_Foreach $node)
    {
        if ( ! $this->getSetting('foreach.traversable')) {
            return;
        }

        $exprType = $node->expr->getAttribute('type');
        if ( ! $exprType || $exprType->isAllType() || $exprType->isTraversable()) {
            return;
        }

        if ( ! $exprType->isUnionType()) {
            $this->phpFile->addComment($node->expr->getLine(), Comment::error(
                'usage_context.foreach_expr_not_traversable',
                'The expression ``%expr%`` of type ``%type%`` is not traversable.',
                array('expr' => self::$prettyPrinter->prettyPrintExpr($node->expr), 'type' => (string) $exprType)));

            return;
        }

        $this->phpFile->addComment($node->expr->getLine(), Comment::error(
            'usage_context.foreach_expr_not_guaranteed_to_be_traversable',
            'The expression ``%expr%`` of type ``%type%`` is not guaranteed to be traversable. How about adding an additional type check?',
            array('expr' => self::$prettyPrinter->prettyPrintExpr($node->expr), 'type' => (string) $exprType)));
    }

    private function checkFunctionParameters(\PHPParser_Node_Expr_FuncCall $node)
    {
        if (!$node->name instanceof \PHPParser_Node_Name) {
            return;
        }

        $funcName = implode("\\", $node->name->parts);
        $function = $this->typeRegistry->getFunction($funcName);
        if (!$function) {
            return;
        }

        $this->checkParameters($node, $function, $node->args);
    }

    private function checkMethodParameters(\PHPParser_Node $node, PhpType $objType, $methodName, array $args)
    {
        $objType = $objType->restrictByNotNull()->toMaybeObjectType();
        if (!$objType) {
            return;
        }

        if (!$method = $objType->getMethod($methodName)) {
            return;
        }

        $this->checkParameters($node, $method->getMethod(), $args, $objType);
    }

    private function checkParameters(\PHPParser_Node $node, AbstractFunction $function, array $args, \Scrutinizer\PhpAnalyzer\Model\MethodContainer $clazz = null)
    {
        $missingArgs = $this->argumentChecker->getMissingArguments($function, $args, $clazz);
        if ( ! empty($missingArgs)) {
            if (count($missingArgs) > 1) {
                $this->phpFile->addComment($node->getLine(), Comment::error(
                    'usage.missing_multiple_required_arguments',
                    'The call to ``%function_name%()`` misses some required arguments starting with ``$%parameter_name%``.',
                    array('function_name' => $function->getName(), 'parameter_name' => reset($missingArgs))));
            } else {
                $this->phpFile->addComment($node->getLine(), Comment::error(
                     'usage.missing_required_argument',
                     'The call to ``%function_name%()`` misses a required argument ``$%parameter_name%``.',
                     array('function_name' => $function->getName(), 'parameter_name' => reset($missingArgs))));
            }
        }

        if ('disabled' !== $this->getSetting('argument_type_checks')) {
            $argTypes = array();
            foreach ($args as $arg) {
                $argTypes[] = $arg->getAttribute('type') ?: $this->typeRegistry->getNativeType('unknown');
            }

            $mismatchedTypes = $this->argumentChecker->getMismatchedArgumentTypes($function, $argTypes, $clazz);
            foreach ($mismatchedTypes as $index => $type) {
                $this->phpFile->addComment($args[$index]->getLine(), Comment::warning(
                    'usage.argument_type_mismatch',
                    '``%expr%`` of type ``%expr_type%`` is not a sub-type of ``%expected_type%``.',
                    array('expr' => self::$prettyPrinter->prettyPrintExpr($args[$index]->value),
                          'expr_type' => (string) $argTypes[$index],
                          'expected_type' => (string) $type)));
            }
        }
    }

    protected function getScopeCreator()
    {
        return new TypedScopeCreator($this->typeRegistry);
    }
}