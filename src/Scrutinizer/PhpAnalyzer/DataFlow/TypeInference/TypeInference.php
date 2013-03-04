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

namespace Scrutinizer\PhpAnalyzer\DataFlow\TypeInference;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Scrutinizer\PhpAnalyzer\ControlFlow\ControlFlowGraph;
use Scrutinizer\PhpAnalyzer\ControlFlow\GraphEdge;
use Scrutinizer\PhpAnalyzer\DataFlow\BinaryJoinOp;
use Scrutinizer\PhpAnalyzer\DataFlow\BranchedForwardDataFlowAnalysis;
use Scrutinizer\PhpAnalyzer\DataFlow\LatticeElementInterface;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil;
use Scrutinizer\PhpAnalyzer\PhpParser\Scope\Scope;
use Scrutinizer\PhpAnalyzer\PhpParser\Scope\Variable;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\ArrayType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\NamedType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\PhpType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\ThisType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\UnionType;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\FunctionInterpreter\FunctionInterpreterInterface;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\ReverseInterpreter\ReverseInterpreterInterface;
use Scrutinizer\PhpAnalyzer\Model\ClassProperty;
use Scrutinizer\PhpAnalyzer\Model\Clazz;
use Scrutinizer\PhpAnalyzer\Model\ContainerMethodInterface;
use Scrutinizer\PhpAnalyzer\Model\GlobalFunction;
use JMS\PhpManipulator\PhpParser\BlockNode;

class TypeInference extends BranchedForwardDataFlowAnalysis
{
    /** @var LoggerInterface */
    private $logger;

    /** @var TypeRegistry */
    private $typeRegistry;

    /** @var ReverseInterpreterInterface */
    private $reverseInterpreter;

    /** @var FunctionInterpreterInterface */
    private $functionInterpreter;

    /** @var MethodInterpreter\MethodInterpreterInterface */
    private $methodInterpreter;

    /** @var \Scrutinizer\PhpAnalyzer\PhpParser\DocCommentParser */
    private $commentParser;

    /** @var Scope */
    private $syntacticScope;

    /** @var LinkedFlowScope */
    private $functionScope;

    /** @var LinkedFlowScope */
    private $bottomScope;

    /**
     * Returns whether the given node has a uniquely identifying name.
     *
     * @param \PHPParser_Node $node
     *
     * @return boolean
     */
    public static function hasQualifiedName(\PHPParser_Node $node)
    {
        return null !== self::getQualifiedName($node);
    }

    /**
     * Returns a name that uniquely identifies a slot in the flow scope.
     *
     * @param \PHPParser_Node $node
     *
     * @return string|null
     */
    public static function getQualifiedName(\PHPParser_Node $node)
    {
        if ($node instanceof \PHPParser_Node_Expr_Variable) {
            return is_string($node->name) ? $node->name : null;
        }

        if ($node instanceof \PHPParser_Node_Expr_StaticPropertyFetch) {
            if ( ! $node->class instanceof \PHPParser_Node_Name) {
                return null;
            }

            if ( ! is_string($node->name)) {
                return null;
            }

            $objType = $node->class->getAttribute('type');
            if (null === $objType || null === $resolvedObjType = $objType->toMaybeObjectType()) {
                if ($objType instanceof NamedType) {
                    return $objType->getReferenceName().'::$'.$node->name;
                }

                return null;
            }

            // If this property has been declared correctly, then we fetch the declaring class
            // to make type inference a bit more precise. Otherwise, we will simply fallback
            // to using the current class name, and assume that it will be implicitly declared.
            if ($resolvedObjType->isClass() && $resolvedObjType->hasProperty($node->name)) {
                return $resolvedObjType->getProperty($node->name)->getDeclaringClass().'::$'.$node->name;
            }

            return $resolvedObjType->getName().'::$'.$node->name;
        }

        if ($node instanceof \PHPParser_Node_Expr_PropertyFetch) {
            if ( ! $node->var instanceof \PHPParser_Node_Expr_Variable) {
                return null;
            }

            if ( ! is_string($node->name)) {
                return null;
            }

            if ( ! is_string($node->var->name)) {
                return null;
            }

            return $node->var->name.'->'.$node->name;
        }

        return null;
    }

    public static function createJoinOperation()
    {
        return new BinaryJoinOp(function(LinkedFlowScope $a, LinkedFlowScope $b) {
            $a->frozen = true;
            $b->frozen = true;

            if ($a->optimize() === $b->optimize()) {
                return $a->createChildFlowScope();
            }

            return new LinkedFlowScope(FlatFlowScopeCache::createFromLinkedScopes($a, $b));
        });
    }

    public function __construct(ControlFlowGraph $cfg, ReverseInterpreterInterface $reverseInterpreter, FunctionInterpreterInterface $functionInterpreter, MethodInterpreter\MethodInterpreterInterface $methodInterpreter, \Scrutinizer\PhpAnalyzer\PhpParser\DocCommentParser $commentParser, Scope $functionScope, TypeRegistry $registry, LoggerInterface $logger = null)
    {
        parent::__construct($cfg, self::createJoinOperation());

        $this->reverseInterpreter = $reverseInterpreter;
        $this->functionInterpreter = $functionInterpreter;
        $this->methodInterpreter = $methodInterpreter;
        $this->syntacticScope = $functionScope;
        $this->commentParser = $commentParser;
        $this->functionScope = LinkedFlowScope::createLatticeElement($functionScope);
        $this->bottomScope = LinkedFlowScope::createLatticeElement(
            Scope::createBottomScope($functionScope->getRootNode(), $functionScope->getTypeOfThis()));
        $this->typeRegistry = $registry;
        $this->logger = $logger ?: new NullLogger();
    }

    protected function createInitialEstimateLattice()
    {
        return $this->bottomScope;
    }

    protected function createEntryLattice()
    {
        return $this->functionScope;
    }

    /**
     * Calculates the out lattices for the different branches.
     *
     * Right now, we just treat ON_EX edges like UNCOND edges. If we want to be perfect, we would have to actually join
     * all the out lattices of this flow with the in lattice, and then make that the out lattice for the ON_EX edge.
     * However, that would add some extra computation for an edge case. So, the current behavior looks like a "good enough"
     * approximation.
     *
     * @param \PHPParser_Node $source
     * @param LatticeElementInterface $input
     *
     * @return array<LatticeElementInterface>
     */
    protected function branchedFlowThrough($source, LatticeElementInterface $input)
    {
        assert($source instanceof \PHPParser_Node);
        assert($input instanceof LinkedFlowScope);

        // If we have not walked a path from the entry node to this node, we cannot infer anything
        // about this scope. So, just skip it for now, we will come back later.
        if ($input === $this->bottomScope) {
            $output = $input;
        } else {
            $output = $input->createChildFlowScope();
            $output = $this->traverse($source, $output);
        }

        $condition = $conditionFlowScope = $conditionOutcomes = null;
        $result = array();
        foreach ($this->cfg->getOutEdges($source) as $branchEdge) {
            $branch = $branchEdge->getType();
            $newScope = $output;

            switch ($branch) {
                case GraphEdge::TYPE_ON_TRUE:
                    if ($source instanceof \PHPParser_Node_Stmt_Foreach) {
                        $informed = $this->traverse($source->expr, $output->createChildFlowScope());
                        $exprType = $this->getType($source->expr);

                        if (null !== $source->keyVar && $source->keyVar instanceof \PHPParser_Node_Expr_Variable) {
                            $keyType = null;
                            if ($exprType->isArrayType()) {
                                $keyType = $exprType->toMaybeArrayType()->getKeyType();
                            }

                            // If we do not have the precise key type available, we can always
                            // assume it to be either a string, or an integer.
                            $this->redeclareSimpleVar($informed, $source->keyVar, $keyType ?: $this->typeRegistry->getNativeType('generic_array_key'));
                        }

                        $valueType = $this->typeRegistry->getNativeType('unknown');
                        if ($exprType->isArrayType()) {
                            $valueType = $exprType->toMaybeArrayType()->getElementType();
                        } else if ($exprType->toMaybeObjectType()) {
                            $valueType = $exprType->toMaybeObjectType()->getTraversableElementType();
                        }
                        $source->valueVar->setAttribute('type', $valueType);
                        $this->redeclareSimpleVar($informed, $source->valueVar, $valueType);

                        $newScope = $informed;

                        break;
                    }

                    // FALL THROUGH

                case GraphEdge::TYPE_ON_FALSE:
                    if (null === $condition) {
                        $condition = NodeUtil::getConditionExpression($source);
                        if (null === $condition && $source instanceof \PHPParser_Node_Stmt_Case
                                // Do ignore DEFAULT statements.
                                && null !== $source->cond) {
                            $condition = $source;

                            // conditionFlowScope is cached from previous iterations of the loop
                            if (null === $conditionFlowScope) {
                                $conditionFlowScope = $this->traverse($source->cond, $output->createChildFlowScope());
                            }
                        }
                    }

                    if (null !== $condition) {
                        if ($condition instanceof \PHPParser_Node_Expr_BooleanAnd
                                || $condition instanceof \PHPParser_Node_Expr_LogicalAnd
                                || $condition instanceof \PHPParser_Node_Expr_BooleanOr
                                || $condition instanceof \PHPParser_Node_Expr_LogicalOr) {
                            // When handling the short-circuiting binary operators, the outcome scope on true can be
                            // different than the outcome scope on false.
                            //
                            // TODO:
                            // The "right" way to do this is to carry the known outcome all the way through the
                            // recursive traversal, so that we can construct a different flow scope based on the outcome.
                            // However, this would require a bunch of code and a bunch of extra computation for an edge
                            // case. This seems to be a "good enough" approximation.

                            // conditionOutcomes is cached from previous iterations of the loop.
                            if (null === $conditionOutcomes) {
                                $conditionOutcomes = ($condition instanceof \PHPParser_Node_Expr_BooleanAnd
                                                        || $condition instanceof \PHPParser_Node_Expr_LogicalAnd)
                                    ? $this->traverseAnd($condition, $output->createChildFlowScope())
                                        : $this->traverseOr($condition, $output->createChildFlowScope());
                            }

                            $newScope = $this->reverseInterpreter->getPreciserScopeKnowingConditionOutcome(
                                $condition, $conditionOutcomes->getOutcomeFlowScope(
                                     $condition, $branch === GraphEdge::TYPE_ON_TRUE),
                                $branch === GraphEdge::TYPE_ON_TRUE);
                        } else {
                            // conditionFlowScope is cached from previous iterations of the loop.
                            if (null === $conditionFlowScope) {
                                // In case of a FOR loop, $condition might be an array of expressions. PHP will execute
                                // all expressions, but only the last one is used to determine the expression outcome.
                                if (is_array($condition)) {
                                    $conditionFlowScope = $output->createChildFlowScope();
                                    foreach ($condition as $cond) {
                                        $conditionFlowScope = $this->traverse($cond, $conditionFlowScope);
                                    }
                                } else {
                                    $conditionFlowScope = $this->traverse($condition, $output->createChildFlowScope());
                                }
                            }

                            // See above comment regarding the handling of FOR loops.
                            $relevantCondition = $condition;
                            if (is_array($condition)) {
                                $relevantCondition = end($condition);
                            }

                            $newScope = $this->reverseInterpreter->getPreciserScopeKnowingConditionOutcome(
                                $relevantCondition, $conditionFlowScope, $branch === GraphEdge::TYPE_ON_TRUE);
                        }
                    }
                    break;
            }

            if (null === $newScope) {
                throw new \LogicException('$newScope must not be null for source '.get_class($source).' and branch '.GraphEdge::getLiteral($branch).'.');
            }

            $result[] = $newScope->optimize();
        }

        return $result;
    }

    private function traverse(\PHPParser_Node $node, LinkedFlowScope $scope)
    {
        if (null !== $docComment = $node->getDocComment()) {
            foreach ($this->commentParser->getTypesFromInlineComment($docComment) as $name => $type) {
                $scope->inferSlotType($name, $type);
            }
        }

        switch (true) {
            case $node instanceof \PHPParser_Node_Expr_Clone:
                $scope = $this->traverseClone($node, $scope);
                break;

            case $node instanceof \PHPParser_Node_Expr_Assign:
            case $node instanceof \PHPParser_Node_Expr_AssignRef:
                $scope = $this->traverseAssign($node, $scope);
                break;

            case $node instanceof \PHPParser_Node_Expr_AssignList:
                $scope = $this->traverseAssignList($node, $scope);
                break;

            case $node instanceof \PHPParser_Node_Expr_StaticPropertyFetch:
            case $node instanceof \PHPParser_Node_Expr_PropertyFetch:
                $scope = $this->traverseGetProp($node, $scope);
                break;

            case $node instanceof \PHPParser_Node_Expr_LogicalAnd:
            case $node instanceof \PHPParser_Node_Expr_BooleanAnd:
                $scope = $this->traverseAnd($node, $scope)->getJoinedFlowScope()
                            ->createChildFlowScope();
                break;

            case $node instanceof \PHPParser_Node_Expr_Array:
                $scope = $this->traverseArray($node, $scope);
                break;

            case $node instanceof \PHPParser_Node_Expr_LogicalOr:
            case $node instanceof \PHPParser_Node_Expr_BooleanOr:
                $scope = $this->traverseOr($node, $scope)->getJoinedFlowScope()
                            ->createChildFlowScope();
                break;

            case $node instanceof \PHPParser_Node_Expr_Ternary:
                $scope = $this->traverseTernary($node, $scope);
                break;

            case $node instanceof \PHPParser_Node_Expr_FuncCall:
            case $node instanceof \PHPParser_Node_Expr_MethodCall:
            case $node instanceof \PHPParser_Node_Expr_StaticCall:
                $scope = $this->traverseCall($node, $scope);
                break;

            case $node instanceof \PHPParser_Node_Expr_New:
                $scope = $this->traverseNew($node, $scope);
                break;

            case $node instanceof \PHPParser_Node_Expr_UnaryPlus:
            case $node instanceof \PHPParser_Node_Expr_UnaryMinus:
                $scope = $this->traverseUnaryPlusMinus($node, $scope);
                break;

            case $node instanceof \PHPParser_Node_Expr_BooleanNot:
                $scope = $this->traverse($node->expr, $scope);
                $node->setAttribute('type', $this->typeRegistry->getNativeType('boolean'));
                break;

            case $node instanceof \PHPParser_Node_Expr_Identical:
            case $node instanceof \PHPParser_Node_Expr_Equal:
            case $node instanceof \PHPParser_Node_Expr_Greater:
            case $node instanceof \PHPParser_Node_Expr_GreaterOrEqual:
            case $node instanceof \PHPParser_Node_Expr_NotEqual:
            case $node instanceof \PHPParser_Node_Expr_NotIdentical:
            case $node instanceof \PHPParser_Node_Expr_Smaller:
            case $node instanceof \PHPParser_Node_Expr_SmallerOrEqual:
            case $node instanceof \PHPParser_Node_Expr_LogicalXor:
            case $node instanceof \PHPParser_Node_Expr_Empty:
            case $node instanceof \PHPParser_Node_Expr_Cast_Bool:
            case $node instanceof \PHPParser_Node_Expr_Isset:
            case $node instanceof \PHPParser_Node_Expr_Instanceof:
                $scope = $this->traverseChildren($node, $scope);
                $node->setAttribute('type', $this->typeRegistry->getNativeType('boolean'));
                break;

            case $node instanceof \PHPParser_Node_Expr_Cast_Int:
            case $node instanceof \PHPParser_Node_Expr_Mod:
            case $node instanceof \PHPParser_Node_Expr_BitwiseXor:
            case $node instanceof \PHPParser_Node_Expr_BitwiseOr:
            case $node instanceof \PHPParser_Node_Expr_BitwiseNot:
            case $node instanceof \PHPParser_Node_Expr_BitwiseAnd:
            case $node instanceof \PHPParser_Node_Expr_ShiftLeft:
            case $node instanceof \PHPParser_Node_Expr_ShiftRight:
                $scope = $this->traverseChildren($node, $scope);
                $node->setAttribute('type', $this->typeRegistry->getNativeType('integer'));
                break;

            case $node instanceof \PHPParser_Node_Expr_Cast_Double:
                $scope = $this->traverseChildren($node, $scope);
                $node->setAttribute('type', $this->typeRegistry->getNativeType('double'));
                break;

            case $node instanceof \PHPParser_Node_Expr_AssignPlus:
                $scope = $this->traversePlus($node, $node->var, $node->expr, $scope);
                break;

            case $node instanceof \PHPParser_Node_Expr_Plus:
                $scope = $this->traversePlus($node, $node->left, $node->right, $scope);
                break;

            case $node instanceof \PHPParser_Node_Expr_AssignDiv:
            case $node instanceof \PHPParser_Node_Expr_AssignMinus:
            case $node instanceof \PHPParser_Node_Expr_AssignMul:
            case $node instanceof \PHPParser_Node_Expr_Div:
            case $node instanceof \PHPParser_Node_Expr_Minus:
            case $node instanceof \PHPParser_Node_Expr_Mul:
                $scope = $this->traverseChildren($node, $scope);

                if (isset($node->left, $node->right)) {
                    $left = $node->left;
                    $right = $node->right;
                } else if (isset($node->var, $node->expr)) {
                    $left = $node->var;
                    $right = $node->expr;
                } else {
                    throw new \LogicException('Previous statements were exhaustive.');
                }

                if ($this->getType($left)->isIntegerType() && $this->getType($right)->isIntegerType()) {
                    $type = $this->typeRegistry->getNativeType('integer');
                } else if ($this->getType($left)->isDoubleType() || $this->getType($right)->isDoubleType()) {
                    $type = $this->typeRegistry->getNativeType('double');
                } else {
                    $type = $this->typeRegistry->getNativeType('number');
                }

                $node->setAttribute('type', $type);
                $this->updateScopeForTypeChange($scope, $left, $this->getType($left), $type);

                break;

            case $node instanceof \PHPParser_Node_Expr_PostDec:
            case $node instanceof \PHPParser_Node_Expr_PostInc:
            case $node instanceof \PHPParser_Node_Expr_PreDec:
            case $node instanceof \PHPParser_Node_Expr_PreInc:
                $scope = $this->traverseChildren($node, $scope);

                if ($this->getType($node->var)->isIntegerType()) {
                    $node->setAttribute('type', $this->typeRegistry->getNativeType('integer'));
                } else if ($this->getType($node->var)->isDoubleType()) {
                    $node->setAttribute('type', $this->typeRegistry->getNativeType('double'));
                } else {
                    $node->setAttribute('type', $this->typeRegistry->getNativeType('number'));
                }

                break;

            case $node instanceof \PHPParser_Node_Expr_Cast_String:
            case $node instanceof \PHPParser_Node_Expr_Concat:
                $scope = $this->traverseChildren($node, $scope);
                $node->setAttribute('type', $this->typeRegistry->getNativeType('string'));
                break;

            case $node instanceof \PHPParser_Node_Stmt_Return:
                $scope = $this->traverseReturn($node, $scope);
                break;

            case $node instanceof \PHPParser_Node_Expr_Variable:
                $scope = $this->traverseVariable($node, $scope);
                break;

            case $node instanceof \PHPParser_Node_Stmt_Echo:
            case $node instanceof \PHPParser_Node_Expr_ArrayItem:
            case $node instanceof \PHPParser_Node_Stmt_Throw:
                $scope = $this->traverseChildren($node, $scope);
                break;

            case $node instanceof \PHPParser_Node_Arg:
                $scope = $this->traverseChildren($node, $scope);
                $node->setAttribute('type', $node->value->getAttribute('type'));
                break;

            case $node instanceof \PHPParser_Node_Stmt_Switch:
                $scope = $this->traverse($node->cond, $scope);
                break;

            case $node instanceof \PHPParser_Node_Stmt_Catch:
                $scope = $this->traverseCatch($node, $scope);
                break;

            case $node instanceof \PHPParser_Node_Expr_ArrayDimFetch:
                $scope = $this->traverseArrayDimFetch($node, $scope);
                break;
        }

        return $scope;
    }

    private function traversePlus(\PHPParser_Node_Expr $plusExpr, \PHPParser_Node_Expr $left, \PHPParser_Node_Expr $right, LinkedFlowScope $scope)
    {
        $scope = $this->traverseChildren($plusExpr, $scope);

        $leftType = $this->getType($left);
        $rightType = $this->getType($right);

        // If both sides are unknown, we leave the outcome at unknown type. The alternative would be to assume the union,
        // integer, double, and array types, which would most likely never be the case in the real world, and just result
        // in weird error messages without any real benefit.
        if ($leftType->isUnknownType() && $rightType->isUnknownType()) {
            $resultType = $this->typeRegistry->getNativeType('unknown');
        } else {
            $joinTypes = array();
            if ( ! $leftType->isUnknownType()) {
                $joinTypes[] = $leftType;
            }
            if ( ! $rightType->isUnknownType()) {
                $joinTypes[] = $rightType;
            }
            $joinedType = $this->typeRegistry->createUnionType($joinTypes);

            switch (true) {
                // If only integer is available, then that is the resulting type.
                // In rare circumstances, we might get a double even if both input types
                // are integers, namely if there is an overflow. However, this seems
                // like an appropriate approximation.
                case $joinedType->isIntegerType():
                    $resultType = $joinedType;
                    break;

                // If one, or both types are doubles, then the resulting type is a double
                // as whatever is not a double will be cast to one.
                case $joinedType->isSubtypeOf($this->typeRegistry->createUnionType(array('integer', 'double'))):
                    $resultType = $this->typeRegistry->getNativeType('double');
                    break;

                // If all types are scalars, we assume the result to be either an integer, or a double.
                // This is in line with Zend engine's behavior which tries to cast the scalar to a number.
                case $joinedType->isSubtypeOf($this->typeRegistry->createUnionType(array('scalar', 'null'))):
                    $resultType = $this->typeRegistry->getNativeType('number');
                    break;

                // If we reach this, it get's tricky as array types might be involved :/
                default:
                    // If we do have something else than an array, then we go
                    // assume the result type to be unknown. Everything else, would
                    // be a weird combination of types which should never happen
                    // in the real world and if we indeed run into this in real
                    // code making anything more sophisticated here would also not
                    // help us in any way.
                    if ( ! $joinedType->isSubtypeOf($this->typeRegistry->getNativeType('array'))) {
                        $resultType = $this->typeRegistry->getNativeType('unknown');
                    } else if ($joinedType->isArrayType()) {
                        $resultType = $joinedType;
                    } else if ($joinedType->isUnionType()) {
                        $keyTypes = array();
                        $elementTypes = array();

                        foreach ($joinedType->getAlternates() as $alt) {
                            if ($alt->isNoType()) {
                                continue;
                            }

                            if ( ! $alt->isArrayType()) {
                                throw new \LogicException(sprintf('All alternates must be arrays, but got "%s".', $alt));
                            }

                            $keyTypes[] = $alt->getKeyType();

                            $elementType = $alt->getElementType();
                            if ( ! $elementType->isUnknownType()) {
                                $elementTypes[] = $elementType;
                            }
                        }

                        $keyType = empty($keyTypes) ? $this->typeRegistry->getNativeType('generic_array_key')
                                        : $this->typeRegistry->createUnionType($keyTypes);
                        $elementType = empty($elementTypes) ? $this->typeRegistry->getNativeType('generic_array_value')
                                : $this->typeRegistry->createUnionType($elementTypes);


                        $resultType = $this->typeRegistry->getArrayType($elementType, $keyType);
                    } else {
                        throw new \LogicException(sprintf('The previous conditions were exhaustive, but got joined type of "%s".', $joinedType));
                    }
            }
        }

        $plusExpr->setAttribute('type', $resultType);
        $this->updateScopeForTypeChange($scope, $left, $leftType, $resultType);

        return $scope;
    }

    private function traverseClone(\PHPParser_Node_Expr_Clone $node, LinkedFlowScope $scope)
    {
        $scope = $this->traverse($node->expr, $scope);

        if ($type = $this->getType($node->expr)) {
            $type = $type->restrictByNotNull();

            if ($type instanceof NamedType || $type->isObjectType()) {
                $node->setAttribute('type', $type);
            } else if ($type->isUnionType()) {
                $node->setAttribute('type', $type);
            } else {
                $node->setAttribute('type', $this->typeRegistry->getNativeType('object'));
            }
        } else {
            $node->setAttribute('type', $this->typeRegistry->getNativeType('object'));
        }

        return $scope;
    }

    private function traverseCall(\PHPParser_Node $node, LinkedFlowScope $scope)
    {
        assert($node instanceof \PHPParser_Node_Expr_MethodCall
                   || $node instanceof \PHPParser_Node_Expr_FuncCall
                   || $node instanceof \PHPParser_Node_Expr_StaticCall);

        $scope = $this->traverseChildren($node, $scope);

        // Propagate type for constants
        if (NodeUtil::isConstantDefinition($node)) {
            $constantName = $node->args[0]->value->value;

            if ($constant = $this->typeRegistry->getConstant($constantName)) {
                $constant->setPhpType($this->getType($node->args[1]->value));
            }
        }

        // If the assert function is called inside a block node, we are just going to assume
        // that some sort of exception is thrown if the assert fails (and it will not silently
        // be ignored).
        // TODO: This needs to be extracted as a general concept where we can have different
        //       effects for functions/methods. For example, array_pop affects the known item
        //       types of arrays on which it is called.
        if ($node->getAttribute('parent') instanceof BlockNode
                && NodeUtil::isMaybeFunctionCall($node, 'assert')
                && isset($node->args[0])) {
            $scope = $this->reverseInterpreter->getPreciserScopeKnowingConditionOutcome($node->args[0]->value, $scope, true);
        }

        $returnType = null;
        if (null !== $function = $this->typeRegistry->getCalledFunctionByNode($node)) {
            $node->setAttribute('returns_by_ref', $function->isReturnByRef());

            $argValues = $argTypes = array();
            foreach ($node->args as $arg) {
                $argValues[] = $arg->value;
                $argTypes[] = $this->getType($arg);
            }

            if ($function instanceof ContainerMethodInterface) {
                $objType = $function->getContainer();

                $maybeReturnType = $function->getReturnType();
                if (null !== $restrictedType = $this->methodInterpreter->getPreciserMethodReturnTypeKnowingArguments($function, $argValues, $argTypes)) {
                    $maybeReturnType = $restrictedType;
                }

                $returnType = $this->updateThisReference(
                        $node instanceof \PHPParser_Node_Expr_MethodCall ? $node->var : $node->class,
                        $scope->getTypeOfThis(),
                        $objType,
                        $maybeReturnType);
            } else if ($function instanceof GlobalFunction) {
                if (null !== $restrictedType = $this->functionInterpreter->getPreciserFunctionReturnTypeKnowingArguments($function, $argValues, $argTypes)) {
                    $returnType = $restrictedType;
                } else if (null === $returnType) {
                    $returnType = $function->getReturnType();
                }
            } else {
                throw new \LogicException(sprintf('Unknown function "%s".', get_class($function)));
            }
        }
        $node->setAttribute('type', $returnType ?: $this->typeRegistry->getNativeType('unknown'));

        return $scope;
    }

    /**
     * Updates the context of returned ThisType types.
     *
     * ```php
     *     class A {
     *         public function returnsThis() {
     *             return $this; // this<A>
     *         }
     *     }
     *
     *     class B extends A {
     *         public function returnsSomething() {
     *             $rs = $this->returnsThis();
     *
     *             return $rs; // this<B>
     *         }
     *
     *         public function returnsSomethingElse()
     *         {
     *             $rs = parent::returnsThis();
     *
     *             return $rs; // this<B>
     *         }
     *     }
     *
     *     class C extends B { }
     *
     *     $c = new C();
     *     $c->returnsThis(); // object<C>
     *     $c->returnsSomething(); // object<C>
     * ```
     *
     * We have two basic cases:
     *
     *     1. The called node refers to the current scope ($this->, self::,
     *        parent::, static::, or SuperTypeName::).
     *     2. The node was called from the "outside" $foo->...().
     *
     * In the first case, we need to preserve the wrapping with ThisType with
     * an updated creation context. In the second case, we have to unwrap the
     * ThisType.
     *
     * @param \PHPParser_Node $calledNode
     * @param PhpType $thisType Type of the current context
     * @param PhpType $calledType Type of the $calledNode
     * @param PhpType $returnType
     *
     * @return PhpType
     */
    private function updateThisReference(\PHPParser_Node $calledNode, PhpType $thisType = null, PhpType $calledType, PhpType $returnType = null)
    {
        // Delays execution until necessary.
        $needsWrapping = function() use ($calledNode, $thisType, $calledType) {
            if (null === $thisType) {
                return false;
            }

            switch (true) {
                case $calledNode instanceof \PHPParser_Node_Expr_Variable:
                    return 'this' === $calledNode->name;

                case $calledNode instanceof \PHPParser_Node_Name:
                    if (in_array(strtolower(implode("\\", $calledNode->parts)), array('self', 'static', 'parent'), true)) {
                        return true;
                    }

                    // This handles the following case:
                    //
                    //       class A { public function foo() { return $this; } }
                    //       class B extends A { public function bar() { return A::foo(); } }
                    //       $x = (new B())->bar();
                    //
                    // $x is resolved to object<B>.
                    if (null !== $thisType && $thisType->isSubtypeOf($calledType)) {
                        return true;
                    }

                    return false;

                default:
                    return false;
            }
        };

        switch (true) {
            case $returnType instanceof ThisType:
                return $needsWrapping() ? $this->typeRegistry->getThisType($thisType) : $calledType;

            case $returnType instanceof UnionType:
                $types = array();
                foreach ($returnType->getAlternates() as $alt) {
                    if ($alt instanceof ThisType) {
                        $types[] = $needsWrapping() ? $this->typeRegistry->getThisType($thisType) : $calledType;

                        continue;
                    }

                    $types[] = $alt;
                }

                return $this->typeRegistry->createUnionType($types);

            default:
                return $returnType;
        }
    }

    /**
     * On the first run of the type inference engine, arrays always have
     * the generic array type at this point. This is assigned by the
     * {@see AbstractScopeBuilder::attachLiteralTypes}.
     *
     * We try to make this more specific by introspecting the values of
     * the items which are assigned.
     *
     * @param \PHPParser_Node_Expr_Array $node
     * @param LinkedFlowScope $scope
     *
     * @return LinkedFlowScope
     */
    private function traverseArray(\PHPParser_Node_Expr_Array $node, LinkedFlowScope $scope)
    {
        $keyTypes = $elementTypes = $itemTypes = array();

        $lastNumericKey = -1;
        $containsDynamicKey = false;
        foreach ($node->items as $item) {
            assert($item instanceof \PHPParser_Node_Expr_ArrayItem);

            $scope = $this->traverse($item, $scope);

            if (null === $item->key) {
                $keyTypes[] = 'integer';
            } else if (null !== $type = $item->key->getAttribute('type')) {
                $keyTypes[] = $type;
            } else {
                // If the key type is not available, then we will just assume both
                // possible types. It's not that bad after all.
                $keyTypes[] = 'string';
                $keyTypes[] = 'integer';
            }

            $itemName = null;
            $keyValue = NodeUtil::getValue($item->key);
            if (null === $item->key && ! $containsDynamicKey) {
                $itemName = ++$lastNumericKey;
            } else if ($keyValue->isDefined()) {
                // We cast everything to a string (even integers), and then process
                // both cases: 1) numeric key, and 2) anything else
                $keyValue = (string) $keyValue->get();

                if (ctype_digit($keyValue)) { // Case 1)
                    $itemName = (integer) $keyValue;

                    // PHP will always use the next highest number starting from the
                    // greatest numeric value that the array contains when no explicit
                    // key has been defined; so, we need to keep track of this.
                    if ($itemName > $lastNumericKey) {
                        $lastNumericKey = $itemName;
                    }
                } else { // Case 2)
                    $itemName = $keyValue;
                }
            } else {
                $containsDynamicKey = true;
            }

            if (null !== $type = $item->value->getAttribute('type')) {
                if (null !== $itemName) {
                    $itemTypes[$itemName] = $type;
                }

                $elementTypes[] = $type;
            }
        }

        $node->setAttribute('type', $this->typeRegistry->getArrayType(
            $elementTypes ? $this->typeRegistry->createUnionType($elementTypes) : null,
            $keyTypes ? $this->typeRegistry->createUnionType($keyTypes) : null,
            $itemTypes));

        return $scope;
    }

    private function traverseArrayDimFetch(\PHPParser_Node_Expr_ArrayDimFetch $node, LinkedFlowScope $scope)
    {
        $scope = $this->traverseChildren($node, $scope);

        $varType = $this->getType($node->var);
        if ($varType->isStringType()) {
            $node->setAttribute('type', $this->typeRegistry->getNativeType('string'));
        } else if ($varType->isArrayType()) {
            $itemType = NodeUtil::getValue($node->dim)
                    ->flatMap([$varType, 'getItemType'])
                    ->getOrCall([$varType, 'getElementType']);

            $node->setAttribute('type', $itemType);
        } else {
            $node->setAttribute('type', $this->typeRegistry->getNativeType('unknown'));
        }

        return $this->dereferencePointer($node->var, $scope);
    }

    private function traverseCatch(\PHPParser_Node_Stmt_Catch $node, LinkedFlowScope $scope)
    {
        $className = implode("\\", $node->type->parts);
        $type = $this->typeRegistry->getClassOrCreate($className);
        $scope->inferSlotType($node->var, $type);

        return $scope;
    }

    /**
     * Traverse a return value.
     */
    private function traverseReturn(\PHPParser_Node_Stmt_Return $node, LinkedFlowScope $scope)
    {
        $scope = $this->traverseChildren($node, $scope);

        if (null === $node->expr) {
            $node->setAttribute('type', $this->typeRegistry->getNativeType('null'));
        } else {
            $node->setAttribute('type', $this->getType($node->expr));
        }

        return $scope;
    }

    private function traverseVariable(\PHPParser_Node_Expr_Variable $node, LinkedFlowScope $scope)
    {
        if ('this' === $node->name) {
            if (null === $scope->getTypeOfThis()) {
                $node->setAttribute('type', $this->typeRegistry->getNativeType('none'));

                return $scope;
            }

            $node->setAttribute('type', $this->typeRegistry->getThisType($scope->getTypeOfThis()));

            return $scope;
        }

        if (is_string($node->name)) {
            $slot = $scope->getSlot($node->name);
            if ($slot && $type = $slot->getType()) {
                $node->setAttribute('type', $type);
            }
        } else {
            $scope = $this->traverseChildren($node, $scope);
        }

        return $scope;
    }

    private function traverseUnaryPlusMinus(\PHPParser_Node $node, LinkedFlowScope $scope)
    {
        $scope = $this->traverse($node->expr, $scope);

        $exprType = $node->expr->getAttribute('type');
        $type = null;
        if ($exprType) {
            if ($exprType->isIntegerType()) {
                $type = $this->typeRegistry->getNativeType('integer');
            } else if ($exprType->isDoubleType()) {
                $type = $this->typeRegistry->getNativeType('double');
            }
        }

        $node->setAttribute('type', $type ?: $this->typeRegistry->getNativeType('number'));

        return $scope;
    }

    private function traverseNew(\PHPParser_Node_Expr_New $node, LinkedFlowScope $scope)
    {
        $scope = $this->traverse($node->class, $scope);

        $type = null;
        if ($node->class instanceof \PHPParser_Node_Name) {
            $type = $node->class->getAttribute('type');
        }
        if (null === $type) {
            $type = $this->typeRegistry->getNativeType('object');
        }
        $node->setAttribute('type', $type);

        foreach ($node->args as $arg) {
            $scope = $this->traverse($arg, $scope);
        }

        return $scope;
    }

    private function traverseAnd(\PHPParser_Node $node, LinkedFlowScope $scope)
    {
        return $this->traverseShortCircuitingBinOp($node, $scope, true);
    }

    private function traverseTernary(\PHPParser_Node_Expr_Ternary $n, LinkedFlowScope $scope)
    {
        if (null === $n->if) { // $a ?: $b
            $condition = new \PHPParser_Node_Expr_NotIdentical(new \PHPParser_Node_Expr_ConstFetch(new \PHPParser_Node_Name(array('null'))), $n->cond);
            $trueNode = $n->cond;
            $falseNode = $n->else;
        } else { // $a ? $b : $c
            $condition = $n->cond;
            $trueNode = $n->if;
            $falseNode = $n->else;
        }

        // verify the condition
        $scope = $this->traverse($condition, $scope);

        // reverse abstract interpret the condition to produce two new scopes
        $trueScope = $this->reverseInterpreter->getPreciserScopeKnowingConditionOutcome($condition, $scope, true);
        $falseScope = $this->reverseInterpreter->getPreciserScopeKnowingConditionOutcome($condition, $scope, false);

        // traverse the true node with the trueScope
        $this->traverse($trueNode, $trueScope->createChildFlowScope());

        // traverse the false node with the falseScope
        $this->traverse($falseNode, $falseScope->createChildFlowScope());

        // meet true and false node's types and assign
        $trueType = $trueNode->getAttribute('type');
        $falseType = $falseNode->getAttribute('type');

        // For statements such as $a ?: $b, the true type ($a) can never be null.
        if (null === $n->if && $trueType) {
            $trueType = $trueType->restrictByNotNull();
        }

        if (null !== $trueType && null !== $falseType) {
            $n->setAttribute('type', $trueType->getLeastSupertype($falseType));
        } else {
            $n->setAttribute('type', null);
        }

        return $scope->createChildFlowScope();
    }

    private function traverseOr(\PHPParser_Node $n, LinkedFlowScope $scope) {
        return $this->traverseShortCircuitingBinOp($n, $scope, false);
    }

    private function traverseShortCircuitingBinOp(\PHPParser_Node $node, LinkedFlowScope $scope, $condition)
    {
        $left = $node->left;
        $right = $node->right;

        // Type the left node.
        $leftLiterals = $this->traverseWithinShortCircuitingBinOp($left, $scope->createChildFlowScope());
        $leftType = $left->getAttribute('type');

        // As these operations are short circuiting, we can reverse interpreter the left node as we know the
        // outcome to its evaluation if we are ever going to reach the right node.
        $rightScope = $this->reverseInterpreter->getPreciserScopeKnowingConditionOutcome($left,
                $leftLiterals->getOutcomeFlowScope($left, $condition), $condition);

        // Now, type the right node in the updated scope.
        $rightLiterals = $this->traverseWithinShortCircuitingBinOp($right, $rightScope->createChildFlowScope());
        $rightType = $right->getAttribute('type');

        if (null === $leftType || null === $rightType) {
            return new BooleanOutcomePair(
                        $this,
                        array(true, false),
                        array(true, false),
                        $leftLiterals->getJoinedFlowScope(),
                        $rightLiterals->getJoinedFlowScope());
        }

        // In PHP, binary operations are always of boolean type.
        $node->setAttribute('type', $this->typeRegistry->getNativeType('boolean'));

        if ($leftLiterals->toBooleanOutcomes === array(!$condition)) {
            // Use the restricted left type, since the right side never gets evaluated.
            return $leftLiterals;
        }

        // Use the join of the restricted left type knowing the outcome of the
        // ToBoolean predicate of the right type.
        return BooleanOutcomePair::fromPairs($this, $leftLiterals, $rightLiterals, $condition);
    }

    private function traverseWithinShortCircuitingBinOp(\PHPParser_Node $node, LinkedFlowScope $scope)
    {
        if ($node instanceof \PHPParser_Node_Expr_BooleanAnd) {
            return $this->traverseAnd($node, $scope);
        }

        if ($node instanceof \PHPParser_Node_Expr_BooleanOr) {
            return $this->traverseOr($node, $scope);
        }

        $scope = $this->traverse($node, $scope);

        if (null === $type = $node->getAttribute('type')) {
            return new BooleanOutcomePair($this, array(true, false), array(true, false), $scope, $scope);
        }

        return new BooleanOutcomePair($this, $type->getPossibleOutcomesComparedToBoolean(),
            $this->typeRegistry->getNativeType('boolean')->isSubTypeOf($type) ? array(true, false) : array(),
            $scope, $scope);
    }

    private function traverseGetProp(\PHPParser_Node $node, LinkedFlowScope $scope)
    {
        assert($node instanceof \PHPParser_Node_Expr_PropertyFetch
                   || $node instanceof \PHPParser_Node_Expr_StaticPropertyFetch);

        $objNode = $node instanceof \PHPParser_Node_Expr_PropertyFetch ? $node->var : $node->class;
        $property = $node->name;
        $scope = $this->traverseChildren($node, $scope);

        if (is_string($property)) {
            $node->setAttribute('type', $this->getPropertyType($objNode->getAttribute('type'), $property, $node, $scope));
        } else {
            $node->setAttribute('type', $this->typeRegistry->getNativeType(TypeRegistry::NATIVE_UNKNOWN));
        }

        return $this->dereferencePointer($objNode, $scope);
    }

    private function traverseAssign(\PHPParser_Node_Expr $node, LinkedFlowScope $scope)
    {
        assert($node instanceof \PHPParser_Node_Expr_Assign
                    || $node instanceof \PHPParser_Node_Expr_AssignRef);

        $scope = $this->traverseChildren($node, $scope);

        $leftType = $node->var->getAttribute('type');
        $rightType = $this->getType($node->expr);
        $node->setAttribute('type', $rightType);

        $castTypes = $this->commentParser->getTypesFromInlineComment($node->getDocComment());

        $this->updateScopeForTypeChange($scope, $node->var, $leftType, $rightType, $castTypes);

        return $scope;
    }

    private function traverseAssignList(\PHPParser_Node_Expr_AssignList $node, LinkedFlowScope $scope)
    {
        $scope = $this->traverse($node->expr, $scope);

        $exprType = $this->getType($node->expr);
        $isArray = $exprType->isArrayType();

        $checkedUnknown = $this->typeRegistry->getNativeType('unknown_checked');
        $castTypes = $this->commentParser->getTypesFromInlineComment($node->getDocComment());

        foreach ($node->vars as $i => $var) {
            if (null === $var) {
                continue;
            }

            $newType = $isArray ? $exprType->getItemType($i)->getOrElse($checkedUnknown) : $checkedUnknown;
            $this->updateScopeForTypeChange($scope, $var, null, $newType, $castTypes);
        }

        return $scope;
    }

    private function traverseChildren(\PHPParser_Node $node, LinkedFlowScope $scope)
    {
        foreach ($node as $subNode) {
            if (is_array($subNode)) {
                foreach ($subNode as $aSubNode) {
                    $scope = $this->traverse($aSubNode, $scope);
                }
            } else if ($subNode instanceof \PHPParser_Node) {
                $scope = $this->traverse($subNode, $scope);
            }
        }

        return $scope;
    }

    private function dereferencePointer(\PHPParser_Node $node, LinkedFlowScope $scope)
    {
        if ($this->hasQualifiedName($node)) {
            if ($type = $node->getAttribute('type')) {
                $narrowed = $type->restrictByNotNull();

                if ($type !== $narrowed) {
                    $scope = $this->narrowScope($scope, $node, $narrowed);
                }
            }
        }

        return $scope;
    }

    private function narrowScope(LinkedFlowScope $scope, \PHPParser_Node $node, PhpType $narrowed)
    {
        $scope = $scope->createChildFlowScope();
        if ($node instanceof \PHPParser_Node_Expr_PropertyFetch
                || $node instanceof \PHPParser_Node_Expr_StaticPropertyFetch) {
            $scope->inferQualifiedSlot($node, $this->getQualifiedName($node), $this->getType($node), $narrowed);
        } else {
            $this->redeclareSimpleVar($scope, $node, $narrowed);
        }

        return $scope;
    }

    private function redeclareSimpleVar(LinkedFlowScope $scope, \PHPParser_Node $nameNode, PhpType $varType = null)
    {
        if (!self::hasQualifiedName($nameNode)) {
            return;
        }

        if (null === $varType) {
            $varType = $this->typeRegistry->getNativeType('unknown');
        }

        $varName = self::getQualifiedName($nameNode);
        $scope->inferSlotType($varName, $varType);
    }

    private function updateScopeForTypeChange(LinkedFlowScope $scope, \PHPParser_Node $left, PhpType $leftType = null, PhpType $resultType = null, array $castTypes = array())
    {
        assert('null !== $resultType');

        switch (true) {
            case $left instanceof \PHPParser_Node_Expr_Variable
                    && is_string($left->name):
                $var = $this->syntacticScope->getVar($left->name);

                // If an explicit type has been annotated for the variable, we
                // use this instead of whatever the type inference engine has found.
                if (isset($castTypes[$left->name])) {
                    $resultType = $castTypes[$left->name];
                }

                $this->redeclareSimpleVar($scope, $left, $resultType);
                $left->setAttribute('type', $resultType);

                if (null !== $var && $var->isTypeInferred()) {
                    $oldType = $var->getType();
                    $newType = null === $oldType ? $resultType : $oldType->getLeastSupertype($resultType);
                    $var->setType($newType);
                }
                break;

            case $left instanceof \PHPParser_Node_Expr_ArrayDimFetch:
                if ($left->var instanceof \PHPParser_Node_Expr_Variable
                        && is_string($left->var->name)) {
                    $varType = $this->getType($left->var);

                    // If the variable type is not known yet, then it has not yet
                    // been initialized. In such a case, it must be an array.
                    if (null === $varType || $varType->isUnknownType()) {
                        $this->redeclareSimpleVar($scope, $left->var, $this->getRefinedArrayType($this->typeRegistry->getNativeType('array'), $resultType, $left));
                    } else if ($varType->isArrayType()) {
                        $this->redeclareSimpleVar($scope, $left->var, $this->getRefinedArrayType($varType, $resultType, $left));
                    }
                } else if (($left->var instanceof \PHPParser_Node_Expr_PropertyFetch
                                || $left->var instanceof \PHPParser_Node_Expr_StaticPropertyFetch
                            ) && null !== $qualifiedName = self::getQualifiedName($left->var)) {
                    $bottomType = $this->getType($left->var);

                    if (null === $bottomType || $bottomType->isUnknownType()) {
                        $refinedArrayType = $this->getRefinedArrayType(
                                $this->typeRegistry->getNativeType('array'), $resultType, $left);
                        $scope->inferQualifiedSlot($left->var, $qualifiedName,
                                $bottomType, $refinedArrayType);
                        $left->setAttribute('type', $refinedArrayType);
                        $this->ensurePropertyDefined($left->var, $refinedArrayType);
                    } else if ($bottomType->isArrayType()) {
                        $refinedArrayType = $this->getRefinedArrayType($bottomType, $resultType, $left);
                        $scope->inferQualifiedSlot($left->var, $qualifiedName, $bottomType, $refinedArrayType);

                        $left->setAttribute('type', $refinedArrayType);
                        $this->ensurePropertyDefined($left->var, $refinedArrayType);
                    }
                }

                break;

            case $left instanceof \PHPParser_Node_Expr_PropertyFetch:
            case $left instanceof \PHPParser_Node_Expr_StaticPropertyFetch:
                if (null === $qualifiedName = self::getQualifiedName($left)) {
                    break;
                }

                $bottomType = $leftType ?: $this->typeRegistry->getNativeType('unknown');
                $scope->inferQualifiedSlot($left, $qualifiedName, $bottomType, $resultType);

                $left->setAttribute('type', $resultType);
                $this->ensurePropertyDefined($left, $resultType);
                break;
        }
    }

    /**
     * Refines an array type with the information about the used key, and assigned element type.
     *
     * This also defines the type for the item key if available and non-dynamic.
     *
     * @param PhpType $varType
     * @param PhpType $resultType
     * @param \PHPParser_Node_Expr_ArrayDimFetch $node
     *
     * @return ArrayType
     */
    private function getRefinedArrayType(PhpType $varType, PhpType $resultType, \PHPParser_Node_Expr_ArrayDimFetch $node)
    {
        $usedKeyType = $this->getKeyTypeForDimFetch($node);

        $keyType = $varType->getKeyType();
        if ($keyType === $this->typeRegistry->getNativeType('generic_array_key')) {
            $refinedKeyType = $usedKeyType;
        } else {
            $refinedKeyType = $keyType->getLeastSupertype($usedKeyType);
        }

        $elementType = $varType->getElementType();
        if ($elementType === $this->typeRegistry->getNativeType('generic_array_value')) {
            $refinedElementType = $resultType;
        } else {
            $refinedElementType = $elementType->getLeastSupertype($resultType);
        }

        $refinedType = $this->typeRegistry->getArrayType($refinedElementType, $refinedKeyType);

        // Infer the type of the key if a concrete value is available.
        NodeUtil::getValue($node->dim)->map(function($keyName) use (&$refinedType, $resultType) {
            $refinedType = $refinedType->inferItemType($keyName, $resultType);
        });

        return $refinedType;
    }

    private function getKeyTypeForDimFetch(\PHPParser_Node_Expr_ArrayDimFetch $node)
    {
        if (null === $node->dim) {
            return $this->typeRegistry->getNativeType('integer');
        }

        if ($type = $node->dim->getAttribute('type')) {
            return $type;
        }

        return $this->typeRegistry->getNativeType('generic_array_key');
    }

    private function ensurePropertyDefined($left, $resultType)
    {
        if ($resultType->isUnknownType()) {
            return;
        }

        $property = $this->typeRegistry->getFetchedPropertyByNode($left);
        if (null !== $property) {
            assert($property instanceof ClassProperty);

            // Specifically ignore anything that is being set in a tearDown() method.
            // TODO: We should think about introducing a general concept for this which takes
            //       inter procedural control flow into account.
            $scopeRoot = $this->syntacticScope->getRootNode();
            if ( ! $scopeRoot instanceof BlockNode && ! $scopeRoot instanceof \PHPParser_Node_Expr_Closure) {
                $testCase = $this->typeRegistry->getClassOrCreate('PHPUnit_Framework_TestCase');
                $method = $this->typeRegistry->getFunctionByNode($scopeRoot);
                if ($property->getDeclaringClassType()->isSubtypeOf($testCase)
                        && (null !== $thisType = $this->syntacticScope->getTypeOfThis())
                        && $thisType->isSubtypeOf($testCase)
                        && null !== $method && strtolower($method->getName()) === 'teardown') {
                    return;
                }
            }

            if (null !== $type = $property->getPhpType()) {
                $newType = $this->typeRegistry->createUnionType(array($type, $resultType));
            } else {
                $newType = $resultType;
            }

            $property->setPhpType($newType);
            if ($astNode = $property->getAstNode()) {
                $astNode->setAttribute('type', $newType);
            }
        }
    }

    /**
     * This method gets the PhpType from the Node argument and verifies that it is
     * present. If not type is present, it will be logged, and unknown is returned.
     *
     * @return PhpType
     */
    private function getType(\PHPParser_Node $node)
    {
        $type = $node->getAttribute('type');

        if (null === $type) {
            // Theoretically, we should never enter this branch because all
            // interesting nodes should have received a type by the scope builder,
            // or the type inference engine. It is not worth throwing an exception,
            // but we should at least log it, and fix it over time.
            $this->logger->debug(sprintf('Encountered untyped node "%s"; assuming unknown type.', get_class($node)));

            return $this->typeRegistry->getNativeType('unknown');
        }

        return $type;
    }

    private function getPropertyType(PhpType $objType = null, $propName, \PHPParser_Node $n, LinkedFlowScope $scope)
    {
        // Check whether the scope contains inferred type information about the property,
        // or fallback to the wider property type if not available. Scopes could
        // contain information about properties if we have reverse interpreted
        // the property previously.
        $qualifiedName = self::getQualifiedName($n);
        $var = $scope->getSlot($qualifiedName);

        if (null !== $var && null !== $varType = $var->getType()) {
            if ($varType->equals($this->typeRegistry->getNativeType('unknown'))
                    && $var !== $this->syntacticScope->getSlot($qualifiedName)) {
                // If the type of this qualified name has been checked in this scope,
                // then use CHECKED_UNKNOWN_TYPE instead to indicate that.
                // TODO: The checked unknown type has not really proved useful in
                //       practice. Consider removing it entirely.
                return $this->typeRegistry->getNativeType('unknown_checked');
            }

            return $varType;
        }

        $propertyType = null;
        if (null !== $objType && (null !== $objType = $objType->toMaybeObjectType())
                && $objType instanceof Clazz && $objType->hasProperty($propName)) {
            $propertyType = $objType->getProperty($propName)->getPhpType();
        }

        return $propertyType ?: $this->typeRegistry->getNativeType('unknown');
    }
}