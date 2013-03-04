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
use Scrutinizer\PhpAnalyzer\ControlFlow\ControlFlowAnalysis;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\NamedType;
use Scrutinizer\PhpAnalyzer\Model\Comment;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

/**
 * Suspicious Code Checks
 *
 * This pass checks for certain, usually suspicious code constructs which might
 * indicate hidden errors in the program:
 *
 * 1. Empty Catch Blocks Without Comment
 * -------------------------------------
 *
 * .. code-block :: php
 *
 *     try {
 *         $foo->bar();
 *     } catch (\Exception $ex) { }
 *
 * In this case, it would suggest to add a comment to the catch block.
 *
 * 2. Fall-through in Switches without Comment
 * -------------------------------------------
 *
 * .. code-block :: php
 *
 *     switch ($foo) {
 *         case 'bar':
 *             if ($x) {
 *                 // ...
 *                 break;
 *             }
 *     }
 *
 * Also in this case, it would suggest to add a comment to the case block.
 *
 * 3. Assignment of Null Return Value
 * ----------------------------------
 * This checks whether an expression that always returns null is assigned to
 * a variable, or property::
 *
 *     function foo($foo) {
 *         echo $foo;
 *     }
 *     $a = foo(); // This assignment would be flagged.
 *
 * 4. Instance-Of with Non-Existent Class
 * --------------------------------------
 * This checks whether the class in an ``instanceof`` expression actually exists.
 * These bugs are pretty hard to debug, and do not produce any runtime error.
 *
 * 5. Catch Block with Non-Existent Class
 * --------------------------------------
 * This is the same as check 4), but for classes in the catch block.
 *
 * 6. Overriding Closure Use
 * -------------------------
 * This checks if you override a variable that was included from the outer-scope.
 * Typically, that suggests that you should include this variable as a reference::
 *
 *     function foo($a, $b) {
 *         return function() use ($a, &$b) {
 *             $a = 'foo'; // would be flagged
 *             $b = 'bar'; // ok
 *         }
 *     }
 *
 * 7. Parameter and Closure Use Conflict
 * -------------------------------------
 * This is almost certainly an error in your program::
 *
 *     function foo($a) {
 *         return function($a) use ($a) { }
 *     }
 *
 * 8. Use Statement Alias Conflict
 * -------------------------------
 * This checks whether the alias of a use statement conflicts with a class in the
 * current namespaces of the same name. These issues are hard to detect as they
 * only happen if both files are imported, and might go unnoticed for a while::
 *
 *     // Foo/A.php
 *     namespace Foo;
 *
 *     class A { }
 *
 *     // Bar/A.php
 *     namespace Bar;
 *
 *     class A { }
 *
 *     // Bar/B.php
 *     namespace Bar;
 *
 *     use Foo\A; // Conflicts with Bar\A.
 *
 *     class B
 *     {
 *         public function __construct(A $a) { }
 *     }
 *
 * @category checks
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class SuspiciousCodePass extends AstAnalyzerPass implements ConfigurablePassInterface
{
    use ConfigurableTrait;

    public function getConfiguration()
    {
        $tb = new TreeBuilder();
        $tb->root('suspicious_code', 'array', new NodeBuilder())
            ->attribute('label', 'Check Suspicious Code')
            ->attribute('type', 'choice')
            ->canBeDisabled()
            ->children()
                ->booleanNode('overriding_parameter')
                    ->defaultFalse()
                    ->attribute('label', 'Overriding Parameter')
                ->end()
                ->booleanNode('overriding_closure_use')
                    ->defaultTrue()
                    ->attribute('label', 'Overriding Closure Use')
                ->end()
                ->booleanNode('parameter_closure_use_conflict')
                    ->defaultTrue()
                    ->attribute('label', 'Conflict Between Parameter and Closure Use')
                ->end()
                ->booleanNode('parameter_multiple_times')
                    ->defaultTrue()
                    ->attribute('label', 'Parameter name used multiple times')
                ->end()
                ->booleanNode('non_existent_class_in_instanceof_check')
                    ->defaultTrue()
                    ->attribute('label', 'Non-existent class in instanceof check')
                ->end()
                ->booleanNode('non_existent_class_in_catch_clause')
                    ->defaultTrue()
                    ->attribute('label', 'Non-existent class in catch clause')
                ->end()
                ->booleanNode('assignment_of_null_return')
                    ->defaultTrue()
                    ->attribute('label', 'Assignment of null return type (e.g. assignment of a function which always returns null to a variable)')
                ->end()
                ->booleanNode('non_commented_switch_fallthrough')
                    ->defaultTrue()
                    ->attribute('label', 'Non-commented fall-through in a switch loop')
                ->end()
                ->booleanNode('non_commented_empty_catch_block')
                    ->defaultTrue()
                    ->attribute('label', 'Non-commented empty catch block')
                ->end()
                ->booleanNode('overriding_private_members')
                    ->defaultTrue()
                    ->attribute('label', 'Overriding private class members')
                ->end()
                ->booleanNode('use_statement_alias_conflict')
                    ->defaultTrue()
                    ->attribute('label', 'Conflicts between use statements.')
                ->end()
                ->booleanNode('precedence_in_condition_assignment')
                    ->defaultTrue()
                    ->attribute('label', 'Detects precedence mistakes in assignments in conditions.')
                ->end()
            ->end()
        ;

        return $tb;
    }

    protected function isEnabled()
    {
        return true === $this->getSetting('enabled');
    }

    public function visit(NodeTraversal $t, \PHPParser_Node $node, \PHPParser_Node $parent = null)
    {
        switch (true) {
            case $node instanceof \PHPParser_Node_Expr_StaticCall:
            case $node instanceof \PHPParser_Node_Expr_MethodCall:
            case $node instanceof \PHPParser_Node_Expr_FuncCall:
                $this->handleCall($node);
                break;

            case $node instanceof \PHPParser_Node_Expr_Instanceof:
                $this->handleInstanceOf($node);
                break;

            case $node instanceof \PHPParser_Node_Stmt_Catch:
                $this->handleEmptyCatch($node);
                $this->handleCatchWithNonExistentClass($node);
                break;

            case $node instanceof \PHPParser_Node_Stmt_Case:
                $this->handleCase($node);
                break;

            case $node instanceof \PHPParser_Node_Expr_Assign:
                $this->handleAssign($t, $node);
                break;

            case $node instanceof \PHPParser_Node_Stmt_Property:
                if (0 !== ($node->type & \PHPParser_Node_Stmt_Class::MODIFIER_PRIVATE)) {
                    foreach ($node->props as $propertyNode) {
                        $this->handlePrivateProperty($propertyNode);
                    }
                }
                break;

            case $node instanceof \PHPParser_Node_Stmt_Function:
            case $node instanceof \PHPParser_Node_Stmt_ClassMethod:
            case $node instanceof \PHPParser_Node_Expr_Closure:
                $this->handleParams($node->params);

                if ($node instanceof \PHPParser_Node_Expr_Closure) {
                    $this->handleClosure($node);
                } else if ($node instanceof \PHPParser_Node_Stmt_ClassMethod
                                && 0 !== ($node->type & \PHPParser_Node_Stmt_Class::MODIFIER_PRIVATE)) {
                    $this->handlePrivateMethod($node);
                }

                break;

            case $node instanceof \PHPParser_Node_Stmt_UseUse:
                $this->handleUse($node);
                break;
        }
    }

    private function handleUse(\PHPParser_Node_Stmt_UseUse $use)
    {
        if ( ! $this->getSetting('use_statement_alias_conflict')) {
            return;
        }

        $namespace = null;
        $parent = $use->getAttribute('parent');
        while (null !== $parent) {
            if ($parent instanceof \PHPParser_Node_Stmt_Namespace) {
                $namespace = $parent->name ? implode('\\', $parent->name->parts) : null;
                break;
            }

            $parent = $parent->getAttribute('parent');
        }

        $localClass = ($namespace ? $namespace.'\\' : '').$use->alias;
        if (strtolower($localClass) === strtolower(implode('\\', $use->name->parts))) {
            return;
        }

        if ($this->typeRegistry->hasClass($localClass)) {
            $this->phpFile->addComment($use->name->getLine(), Comment::error(
                'suspicious_code.use_statement_alias_conflict',
                'This use statement conflicts with another class in this namespace, ``%local_class%``.',
                array('local_class' => $localClass)));
        }
    }

    private function handlePrivateMethod(\PHPParser_Node_Stmt_ClassMethod $methodNode)
    {
        if ( ! $this->getSetting('overriding_private_members')) {
            return;
        }

        $classNode = $methodNode->getAttribute('parent')->getAttribute('parent');
        $class = $this->typeRegistry->getClassByNode($classNode);
        if ( ! $class->isClass()) {
            return;
        }

        if ((null !== $superClass = $class->getSuperClassType())
                && (null !== $superClass = $superClass->toMaybeObjectType())
                && $superClass->hasMethod($methodNode->name)
                && $superClass->getMethod($methodNode->name)->isPrivate()) {
            $this->phpFile->addComment($methodNode->getLine(), Comment::warning(
                'suspicious_code.overriding_private_method',
                'Consider using a different method name as you override a private method of the parent class.'));
        }
    }

    private function handlePrivateProperty(\PHPParser_Node_Stmt_PropertyProperty $propertyNode)
    {
        if ( ! $this->getSetting('overriding_private_members')) {
            return;
        }

        $class = $this->typeRegistry->getClassByNode($propertyNode->getAttribute('parent')->getAttribute('parent')->getAttribute('parent'));
        if ( ! $class->isClass()) {
            return;
        }

        if ((null !== $superClass = $class->getSuperClassType())
                && (null !== $superClass = $superClass->toMaybeObjectType())
                && $superClass->hasProperty($propertyNode->name)
                && $superClass->getProperty($propertyNode->name)->isPrivate()) {
            $this->phpFile->addComment($propertyNode->getLine(), Comment::warning(
                'suspicious_code.overriding_private_property',
                'Consider using a different property name as you override a private property of the parent class.'));
        }
    }

    private function handleAssign(NodeTraversal $t, \PHPParser_Node_Expr_Assign $node)
    {
        if (!$node->var instanceof \PHPParser_Node_Expr_Variable
                || !is_string($node->var->name)) {
            return;
        }

        $scope = $t->getScope();
        if (null === $var = $scope->getVar($node->var->name)) {
            return;
        }

        if ($var->isReference()) {
            return;
        }

        $nameNode = $var->getNameNode();
        if ($nameNode instanceof \PHPParser_Node_Param
                && $this->getSetting('overriding_parameter')) {
            $this->phpFile->addComment($node->getLine(), Comment::warning(
                'suspicious_code.assignment_to_parameter',
                'Consider using a different name than the parameter ``$%param_name%``. This often makes code more readable.',
                array('param_name' => $node->var->name)));
        } else if ($nameNode instanceof \PHPParser_Node_Expr_ClosureUse
                       && $this->getSetting('overriding_closure_use')) {
            $this->phpFile->addComment($node->getLine(), Comment::warning(
                'suspicious_code.assignment_to_closure_import',
                'Consider using a different name than the imported variable ``$%variable_name%``, or did you forget to import by reference?',
                array('variable_name' => $node->var->name)));
        }
    }

    private function handleClosure(\PHPParser_Node_Expr_Closure $node)
    {
        $names = array_map(function(\PHPParser_Node_Expr_ClosureUse $use) {
            return $use->var;
        }, $node->uses);

        foreach ($node->params as $param) {
            if (in_array($param->name, $names, true)
                    && $this->getSetting('parameter_closure_use_conflict')) {
                $this->phpFile->addComment($param->getLine(), Comment::warning(
                    'suspicious_code.param_name_with_imported_var_conflict',
                    'The parameter name ``$%param_name%`` conflicts with one of the imported variables.',
                    array('param_name' => $param->name)));
            }
        }
    }

    private function handleParams(array $params)
    {
        $names = array();
        foreach ($params as $param) {
            assert($param instanceof \PHPParser_Node_Param);

            if (in_array($param->name, $names, true) && $this->getSetting('parameter_multiple_times')) {
                $this->phpFile->addComment($param->getLine(), Comment::warning(
                    'suspicious_code.param_name_used_multiple_times',
                    'You cannot use the parameter ``$%param_name%`` multiple times.',
                    array('param_name' => $param->name)));
            }
            $names[] = $param->name;
        }
    }

    private function handleInstanceOf(\PHPParser_Node_Expr_Instanceof $node)
    {
        if (!$this->getSetting('non_existent_class_in_instanceof_check')) {
            return;
        }

        if (!$classType = $node->class->getAttribute('type')) {
            return;
        }

        if ($classType instanceof NamedType
                && !$this->typeRegistry->getClass($classType->getReferenceName())) {
            $this->phpFile->addComment($node->class->getLine(), Comment::warning(
                'suspicious_code.non_existent_class_for_instanceof',
                'The class "%class_name%" does not exist. Did you forget a USE statement, or did you not list all dependencies?',
                array('class_name' => $classType->getReferenceName())));
        }
    }

    private function handleCatchWithNonExistentClass(\PHPParser_Node_Stmt_Catch $node)
    {
        if (!$this->getSetting('non_existent_class_in_catch_clause')) {
            return;
        }

        if (!$classType = $node->type->getAttribute('type')) {
            return;
        }

        if ($classType instanceof NamedType
                && !$this->typeRegistry->getClass($classType->getReferenceName())) {
            $this->phpFile->addComment($node->type->getLine(), Comment::warning(
                'suspicious_code.non_existent_class_for_catch',
                'The class "%class_name%" does not exist. Did you forget a USE statement, or did you not list all dependencies?',
                array('class_name' => $classType->getReferenceName())));
        }
    }

    private function handleCall(\PHPParser_Node $node)
    {
        if (!$this->getSetting('assignment_of_null_return')) {
            return;
        }

        if (!$returnType = $node->getAttribute('type')) {
            return;
        }

        if (!$returnType->isNullType()) {
            return;
        }

        if (!$node->getAttribute('parent') instanceof \PHPParser_Node_Expr_Assign) {
            return;
        }

        $this->phpFile->addComment($node->getAttribute('parent')->getLine(), Comment::warning(
            'suspicious_code.assignment_of_null_return',
            'The assignment to ``%assignment_expr%`` looks wrong as ``%call_expr%`` always returns null.',
            array('assignment_expr' => self::$prettyPrinter->prettyPrintExpr($node->getAttribute('parent')->var),
                  'call_expr' => self::$prettyPrinter->prettyPrintExpr($node))));
    }

    private function handleCase(\PHPParser_Node_Stmt_Case $node)
    {
        if (!$this->getSetting('non_commented_switch_fallthrough')) {
            return;
        }

        if (0 === count($node->stmts)) {
            return;
        }

        // Check if the next statement is a CASE statement.
        $nextCase = $node->getAttribute('next');
        if (!$nextCase instanceof \PHPParser_Node_Stmt_Case) {
            return;
        }

        // If the last statement of the CASE node is a BREAK/CONTINUE/RETURN
        // THROW/GOTO statement, then we can always rule out a FALL-THROUGH. The
        // opposite, i.e. when there is none, is not true though.
        foreach ($node->stmts as $lastStmt);
        if ($lastStmt instanceof \PHPParser_Node_Stmt_Break
                || $lastStmt instanceof \PHPParser_Node_Stmt_Continue
                || $lastStmt instanceof \PHPParser_Node_Stmt_Return
                || $lastStmt instanceof \PHPParser_Node_Stmt_Throw
                || $lastStmt instanceof \PHPParser_Node_Stmt_Goto) {
            return;
        }

        $cfa = new ControlFlowAnalysis();
        $cfa->process($node);
        $graph = $cfa->getGraph();

//         $serializer = new GraphvizSerializer();
//         echo $serializer->serialize($graph);

        // In case of a fallthrough, the control flow falls through to the statements block
        // of the next CASE statement. If that node is not part of the control flow graph,
        // then the control flow cannot reach from this starting node.
        if ( ! $graph->hasNode($nextCase->stmts)) {
            return;
        }

        foreach ($node->stmts as $lastChild);
        $previousLineEnd = $lastChild->getLine();

        if ($previousLineEnd === $nextCase->getLine()) {
            $this->phpFile->addComment($previousLineEnd, Comment::warning(
                'coding_style.unreadable_case',
                'Consider moving this CASE statement to a new line.'));

            return;
        }

        $code = explode("\n", $this->phpFile->getContent());

        $foundComment = false;
        for ($i = $previousLineEnd, $c = $nextCase->getLine(); $i<$c; $i++) {
            if (empty($code[$i])) {
                continue;
            }

            $lineTokens = token_get_all('<?php '.$code[$i]);
            foreach ($lineTokens as $token) {
                if (!is_array($token)) {
                    continue;
                }

                if (T_CASE === $token[0] || T_DEFAULT === $token[0]) {
                    break 2;
                }

                if (T_COMMENT === $token[0] || T_DOC_COMMENT === $token[0]) {
                    $foundComment = true;
                    break 2;
                }
            }
        }

        // TODO: Improve where the comment is being applied. If there is only one fall-through
        //       it would be nice to add the comment directly where the fall-through is
        //       happening. If there are multiple fall-throughs, then we can apply the
        //       comment at the end of the entire block as is the current behavior.

        if (!$foundComment) {
            $this->phpFile->addComment($previousLineEnd, Comment::warning(
                'suspicious_code.non_empty_switch_fallthrough',
                'Consider adding a comment if this fall-through is intended.'));
        }
    }

    private function handleEmptyCatch(\PHPParser_Node_Stmt_Catch $node)
    {
        if (!$this->getSetting('non_commented_empty_catch_block')) {
            return;
        }

        if (count($node->stmts) > 0) {
            return;
        }

        if (-1 === $line = $node->getLine()) {
            return;
        }

        $code = explode("\n", $this->phpFile->getContent());

        $previousBlock = $node->getAttribute('parent')->stmts;
        foreach ($previousBlock as $lastChild);
        $previousLineEnd = count($previousBlock) > 0 ? $lastChild->getLine()
            : $previousBlock->getLine();

        // Not sure how readable that code is supposed to be, but it doesn't seem to look good.
        if ($previousLineEnd === $node->getLine()) {
            $this->phpFile->addComment($previousLineEnd, Comment::warning(
                'coding_style.unreadable_catch',
                'Consider moving this CATCH statement to a new line.'));

            return;
        }

        // Start scanning for a comment one line after the last statement of the previous block.
        $foundComment = $afterCatch = $inCatchBlock = $terminateOnNextNonWhitespace = $afterCondition = false;
        for ($i = $previousLineEnd, $c = count($code); $i<$c; $i++) {
            if (empty($code[$i])) {
                continue;
            }

            // Might break for some weird inlined HTML, but honestly who does that these days?
            $lineTokens = token_get_all('<?php '.$code[$i]);
            foreach ($lineTokens as $token) {

                if (!is_array($token)) {
                    if ($terminateOnNextNonWhitespace) {
                        break 2;
                    }

                    if ($afterCondition && '{' === $token) {
                        $inCatchBlock = true;
                        continue;
                    }

                    if ($inCatchBlock && '}' === $token) {
                        $terminateOnNextNonWhitespace = true;
                        continue;
                    }

                    if ($afterCatch && ')' === $token) {
                        $afterCondition = true;
                        continue;
                    }

                    // no whitespace and after the catch block means something like
                    // catch (\Exception $ex) 'foo';
                    if ($afterCondition && !$inCatchBlock) {
                        break;
                    }

                    continue;
                }

                if (\T_DOC_COMMENT === $token[0] || \T_COMMENT === $token[0]) {
                    $foundComment = true;
                    break 2;
                }

                if (\T_WHITESPACE === $token[0]) {
                    continue;
                }

                if ($terminateOnNextNonWhitespace) {
                    break 2;
                }

                if (\T_CATCH === $token[0]) {
                    $afterCatch = true;
                    continue;
                }
            }
        }

        if (!$foundComment) {
            $this->phpFile->addComment($node->getLine(), Comment::warning(
                'suspicious_code.empty_catch_block',
                'Consider adding a comment why this CATCH block is empty.'));
        }
    }
}