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

namespace Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\ReverseInterpreter;

use Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\PhpType;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\LinkedFlowScope;

/**
 * Our default reverse abstract interpreter.
 *
 * This reverse abstract interpreter uses the semantics of the PHP language as a
 * means to reverse interpret computations. For example, we are going to infer
 * the type of variables knowing the outcome to certain conditions.
 *
 * For this interpreter to work, the input nodes must already be typed. Without
 * type information, this is of limited use.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class SemanticReverseAbstractInterpreter extends ChainableReverseAbstractInterpreter
{
    public function getPreciserScopeKnowingConditionOutcome(\PHPParser_Node $node, LinkedFlowScope $blindScope, $outcome)
    {
        // Here, we are checking some special type related functions, such as
        // gettype(), the is_??? flavors, or get_class().
        if ($node instanceof \PHPParser_Node_Expr_Equal
                || $node instanceof \PHPParser_Node_Expr_NotEqual
                || $node instanceof \PHPParser_Node_Expr_Identical
                || $node instanceof \PHPParser_Node_Expr_NotIdentical
                || $node instanceof \PHPParser_Node_Stmt_Case) {
            if (null !== $rs = $this->checkForTypeFunctionCall($node, $blindScope, $outcome)) {
                return $rs;
            }
        }

        switch (true) {
            case $node instanceof \PHPParser_Node_Expr_BooleanAnd:
            case $node instanceof \PHPParser_Node_Expr_LogicalAnd:
                if ($outcome) {
                    return $this->caseAndOrNotShortCircuiting($node->left, $node->right, $blindScope, true);
                }

                return $this->caseAndOrMaybeShortCircuiting($node->left, $node->right, $blindScope, true);

            case $node instanceof \PHPParser_Node_Expr_BooleanOr:
            case $node instanceof \PHPParser_Node_Expr_LogicalOr:
                if (!$outcome) {
                    return $this->caseAndOrNotShortCircuiting($node->left, $node->right, $blindScope, false);
                }

                return $this->caseAndOrMaybeShortCircuiting($node->left, $node->right, $blindScope, false);

            case $node instanceof \PHPParser_Node_Expr_Equal:
                if ($outcome) {
                    return $this->caseEquality($node->left, $node->right, $blindScope, 'Equality');
                }

                return $this->caseEquality($node->left, $node->right, $blindScope, 'Inequality');

            case $node instanceof \PHPParser_Node_Expr_NotEqual:
                if ($outcome) {
                    return $this->caseEquality($node->left, $node->right, $blindScope, 'Inequality');
                }

                return $this->caseEquality($node->left, $node->right, $blindScope, 'Equality');

            case $node instanceof \PHPParser_Node_Expr_Identical:
                if ($outcome) {
                    return $this->caseEquality($node->left, $node->right, $blindScope, 'ShallowEquality');
                }

                return $this->caseEquality($node->left, $node->right, $blindScope, 'ShallowInequality');

            case $node instanceof \PHPParser_Node_Expr_NotIdentical:
                if ($outcome) {
                    return $this->caseEquality($node->left, $node->right, $blindScope, 'ShallowInequality');
                }

                return $this->caseEquality($node->left, $node->right, $blindScope, 'ShallowEquality');

            case $node instanceof \PHPParser_Node_Expr_Variable:
            case $node instanceof \PHPParser_Node_Expr_PropertyFetch:
            case $node instanceof \PHPParser_Node_Expr_StaticPropertyFetch:
                return $this->caseVariableOrGetProp($node, $blindScope, $outcome);

            case $node instanceof \PHPParser_Node_Expr_Assign:
                return $this->firstPreciserScopeKnowingConditionOutcome($node->var,
                    $this->firstPreciserScopeKnowingConditionOutcome($node->expr, $blindScope, $outcome), $outcome);

            case $node instanceof \PHPParser_Node_Expr_BooleanNot:
                return $this->firstPreciserScopeKnowingConditionOutcome(
                    $node->expr, $blindScope, !$outcome);

            case $node instanceof \PHPParser_Node_Expr_SmallerOrEqual:
            case $node instanceof \PHPParser_Node_Expr_Smaller:
            case $node instanceof \PHPParser_Node_Expr_GreaterOrEqual:
            case $node instanceof \PHPParser_Node_Expr_Greater:
                return $this->caseComparison($node, $blindScope, $outcome);

            case $node instanceof \PHPParser_Node_Expr_Instanceof:
                return $this->caseInstanceOf($node->expr, $node->class, $blindScope, $outcome);

            case $node instanceof \PHPParser_Node_Expr_FuncCall:
                if (NodeUtil::isTypeFunctionCall($node) && isset($node->args[0])
                        && null !== $informed = $this->caseTypeFunctionCall($node->name, $node->args[0], $blindScope, $outcome)) {
                    return $informed;
                }
                break;

            case $node instanceof \PHPParser_Node_Stmt_Case:
                $left = $node->getAttribute('parent')->cond;
                $right = $node->cond;

                if ($outcome) {
                    // In PHP, a case does a loose comparison (==)
                    return $this->caseEquality($left, $right, $blindScope, 'Equality');
                }

                return $this->caseEquality($left, $right, $blindScope, 'Inequality');

//             case $node instanceof \PHPParser_Node_Expr_Empty:
//                 // TODO
//                 return $this->caseEmpty();

//             case $node instanceof \PHPParser_Node_Expr_Isset:
//                 // TODO
//                 return $this->caseIsset();
        }

        return $this->nextPreciserScopeKnowingConditionOutcome($node, $blindScope, $outcome);
    }

    private function caseTypeFunctionCall(\PHPParser_Node $name, \PHPParser_Node_Arg $arg, LinkedFlowScope $blindScope, $outcome)
    {
        if (!$name instanceof \PHPParser_Node_Name) {
            return null;
        }

        $type = $this->getTypeIfRefinable($arg->value, $blindScope);

        $restrictedType = null;
        $functionName = implode("\\", $name->parts);

        switch ($functionName) {
            case 'is_array':
                $restrictedType = $this->restrictByValueType($type, $outcome, 'array');
                break;

            case 'is_bool':
                $restrictedType = $this->restrictByValueType($type, $outcome, 'boolean');
                break;

            case 'is_callable':
                $restrictedType = $this->restrictByCallable($type, $outcome);
                break;

            case 'is_double':
            case 'is_float':
            case 'is_real':
                $restrictedType = $this->restrictByValueType($type, $outcome, 'double');
                break;

            case 'is_int':
            case 'is_integer':
            case 'is_long':
                $restrictedType = $this->restrictByValueType($type, $outcome, 'integer');
                break;

            case 'is_null':
                $restrictedType = $this->restrictByValueType($type, $outcome, 'null');
                break;

            case 'is_numeric':
                $restrictedType = $this->restrictByNumericType($type, $outcome);
                break;

            case 'is_object':
                $restrictedType = $this->restrictByObjectType($type, $outcome);
                break;

//             case 'is_resource':
//                 $restrictedType = $this->restrictByTypeForResource($type, $outcome);
//                 break;

            case 'is_scalar':
                $restrictedType = $this->restrictByScalarType($type, $outcome);
                break;

            case 'is_string':
                $restrictedType = $this->restrictByValueType($type, $outcome, 'string');
                break;

            default:
                // let the next abstract interpreter handle it
                return null;
        }

        if (null !== $restrictedType) {
            $informed = $blindScope->createChildFlowScope();
            $this->declareNameInScope($informed, $arg->value, $restrictedType);

            return $informed;
        }

        return $blindScope;
    }

    private function restrictByScalarType(PhpType $type = null, $outcome)
    {
        if ($outcome) {
            return $this->typeRegistry->getNativeType('scalar');
        }

        if (null === $type) {
            return null;
        }

        if ($type->isIntegerType() || $type->isDoubleType() || $type->isStringType()
                || $type->isBooleanType()) {
            return $this->typeRegistry->getNativeType('none');
        }

        if ($type->isUnionType()) {
            return $type->getRestrictedUnion($this->typeRegistry->getNativeType('scalar'));
        }

        if ($type->isAllType()) {
            return $this->typeRegistry->createUnionType(array('object', 'array', 'null'));
        }

        return $type;
    }

    private function restrictByNumericType(PhpType $type = null, $outcome)
    {
        if ($outcome) {
            return $this->typeRegistry->getNativeType('numeric');
        }

        if (null === $type) {
            return null;
        }

        // String is excluded here since not all strings are necessarily numeric.
        // So, even if the condition evaluates to false, we could technically
        // still have a string inside the if (just that it is not numeric).
        if ($type->isIntegerType() || $type->isDoubleType()) {
            return $this->typeRegistry->getNativeType('none');
        }

        if ($type->isUnionType()) {
            // Again, we do not restrict by string as explained above.
            return $type->getRestrictedUnion($this->typeRegistry->createUnionType(array('integer', 'double')));
        }

        if ($type->isAllType()) {
            return $this->typeRegistry->createUnionType(array('object', 'string', 'null', 'array', 'boolean'));
        }

        return $type;
    }

    private function restrictByObjectType(PhpType $type = null, $outcome)
    {
        if ($outcome) {
            if (null === $type || $type->isUnknownType()) {
                return $this->typeRegistry->getNativeType('object');
            }

            return $type->getGreatestSubtype($this->typeRegistry->getNativeType('object'));
        }

        if (null === $type || $type->isAllType()) {
            return $this->typeRegistry->createAllTypeExcept(array('object'));
        }

        if ($type->isObjectType() || $type->isNoObjectType()) {
            return $this->typeRegistry->getNativeType('none');
        }

        if ($type->isUnionType()) {
            return $type->getRestrictedUnion($this->typeRegistry->getNativeType('object'));
        }

        return $type;
    }

    private function restrictByValueType(PhpType $type = null, $outcome, $typeName)
    {
        // If the type function evaluates to true, we can always say what
        // the type is afterwards even if we do not know the input.
        if ($outcome) {
            return $this->typeRegistry->getNativeType($typeName);
        }

        if (null === $type) {
            return null;
        }

        // If we know the outcome is false, and only the given type is passed,
        // e.g. is_array() receives only arrays, then this condition always
        // evaluates to false.
        if ($type->{'is'.$typeName.'Type'}()) {
            return $this->typeRegistry->getNativeType('none');
        }

        // If a union type is passed, the value can be whatever the union type
        // represents minus the checked type.
        if ($type->isUnionType()) {
            return $type->getRestrictedUnion($this->typeRegistry->getNativeType($typeName));
        }

        // If an all type is passed, the value can be all value types minus the
        // checked value type.
        if ($type->isAllType()) {
            $allTypes = array('object', 'integer', 'double', 'string', 'null', 'array', 'boolean');
            unset($allTypes[array_search($typeName, $allTypes, true)]);

            return $this->typeRegistry->createUnionType($allTypes);
        }

        // If nothing from above is applicable, just return the type. It is an
        // indication that the condition is always false, for example
        // is_array() receives always non-array values.
        return $type;
    }

    private function restrictByCallable(PhpType $type = null, $outcome)
    {
        if (null === $type) {
            return $outcome ? $this->typeRegistry->getNativeType('callable') : null;
        }

        if ($outcome) {
            if ($type->isUnknownType() || $type->isAllType()) {
                return $this->typeRegistry->getNativeType('callable');
            }

            if ($type->isUnionType()) {
                $types = array();
                foreach ($type->getAlternates() as $altType) {
                    if ($altType->canBeCalled()) {
                        $types[] = $altType;
                    }
                }

                return $this->typeRegistry->createUnionType($types);
            }

            return $type->canBeCalled() ? $type : $this->typeRegistry->getNativeType('none');
        }

        if ($type->isCallableType()) {
            return $this->typeRegistry->getNativeType('none');
        }

        if ($type->isUnionType()) {
            $types = array();
            foreach ($type->getAlternates() as $altType) {
                if ($altType->isCallableType()) {
                    continue;
                }

                $types[] = $altType;
            }

            return $this->typeRegistry->createUnionType($types);
        }

        return $type;
    }

    private function caseVariableOrGetProp(\PHPParser_Node $node, LinkedFlowScope $blindScope, $outcome)
    {
        // First, we check if there is a defining expression for this node (only in case of a variable).
        // In addition, we also narrow down the type of the variable itself.
        if (null !== $defExpr = $node->getAttribute('defining_expr')) {
            $blindScope = $this->firstPreciserScopeKnowingConditionOutcome($defExpr, $blindScope, $outcome);
        }

        if (null !== $type = $this->getTypeIfRefinable($node, $blindScope)) {
            $restrictedType = $type->getRestrictedTypeGivenToBooleanOutcome($outcome);
            $informed = $blindScope->createChildFlowScope();
            $this->declareNameInScope($informed, $node, $restrictedType);

            return $informed;
        }

        return $blindScope;
    }

    private function caseInstanceOf(\PHPParser_Node $left, \PHPParser_Node $right, LinkedFlowScope $blindScope, $outcome)
    {
        // If the left side of the instanceof expression is not a variable, or a
        // property, just return here as we cannot infer anything.
        if (null === $leftType = $this->getTypeIfRefinable($left, $blindScope)) {
            return $blindScope;
        }

        // If the class is variable, we can just ensure that if the condition
        // evaluates to true, the true scope contains an object, but not of
        // which class that object is.
        if (!$right instanceof \PHPParser_Node_Name_FullyQualified) {
            if ($outcome) {
                $restrictedType = $leftType->getGreatestSubtype($this->typeRegistry->getNativeType('object'));
                $informed = $blindScope->createChildFlowScope();
                $this->declareNameInScope($informed, $left, $restrictedType);

                return $informed;
            }

            // For the false scope, we cannot infer anything.
            return $blindScope;
        }

        // If we reach this, we have a concrete class on the right side.
        $rightType = $right->getAttribute('type');

        if ($outcome) {
            $restrictedType = $leftType->isUnknownType() ? $rightType : $leftType->getGreatestSubtype($rightType);
            $informed = $blindScope->createChildFlowScope();
            $this->declareNameInScope($informed, $left, $restrictedType);

            return $informed;
        }

        $restrictedType = null;
        if ( ! $leftType->isUnknownType() && $leftType->isSubtypeOf($rightType)) {
            $restrictedType = $this->typeRegistry->getNativeType('none');
        } else if ($leftType->isUnionType()) {
            $restrictedType = $leftType->getRestrictedUnion($rightType);
        }

        if (null === $restrictedType) {
            return $blindScope;
        }

        $informed = $blindScope->createChildFlowScope();
        $this->declareNameInScope($informed, $left, $restrictedType);

        return $informed;
    }

    /**
     * There are a few special cases in PHP, which we need to handle differently.
     *
     * The default behavior is to fallback to caseEquality when outcome is true.
     *
     * @param \PHPParser_Node $comparison
     * @param LinkedFlowScope $blindScope
     * @param boolean $outcome
     * @throws \LogicException
     */
    private function caseComparison(\PHPParser_Node $comparison, LinkedFlowScope $blindScope, $outcome)
    {
        // Use the default behavior first.
        if ($outcome) {
            $newScope = $this->caseEquality($comparison->left, $comparison->right, $blindScope, 'Inequality');
        } else {
            $newScope = $blindScope;
        }

        // Now, check the exception cases where the default fails.
        list($leftType, $leftIsRefinable) = $this->getTypeStatus($comparison->left, $blindScope);
        list($rightType, $rightIsRefinable) = $this->getTypeStatus($comparison->right, $blindScope);
        if (null === $leftType || null === $rightType) {
            return $blindScope;
        }

        $leftTypeRefined = $rightTypeRefined = null;

        // The onFalse branch of a SmallerOrEqual comparison can never contain a null type on left side.
        // Example: if ($a <= $foo) { } else { /* $a can never be null here. */ }
        if (!$outcome && $comparison instanceof \PHPParser_Node_Expr_SmallerOrEqual && $leftType->isNullable()) {
            $leftTypeRefined = $leftType->restrictByNotNull();
        }
        // The onFalse branch of a GreaterOrEqual comparison can never contain a null type on right side.
        // Example: if ($foo >= $a) { } else { /* $a can never be null here. */ }
        if (!$outcome && $comparison instanceof \PHPParser_Node_Expr_GreaterOrEqual && $rightType->isNullable()) {
            $rightTypeRefined = $rightType->restrictByNotNull();
        }

        // The onTrue branch of a Greater comparison can never contain a null type on left side.
        // Example: if ($a > $foo) { /* $a can never be null here. */ } else { }
        if ($outcome && $comparison instanceof \PHPParser_Node_Expr_Greater && $leftType->isNullable()) {
            $leftTypeRefined = $leftType->restrictByNotNull();
        }
        // The onTrue branch of a Smaller comparison can never contain a null type on right side.
        // Example: if ($foo < $a) { /* $a can never be null here. */ } else { }
        if ($outcome && $comparison instanceof \PHPParser_Node_Expr_Smaller && $rightType->isNullable()) {
            $rightTypeRefined = $rightType->restrictByNotNull();
        }

        if (($leftIsRefinable && $leftTypeRefined)
                || ($rightIsRefinable && $rightTypeRefined)) {
            // Do not create a new flowscope if the default behavior has already created one.
            $newScope = $newScope === $blindScope ? $blindScope->createChildFlowScope() : $newScope;

            if ($leftIsRefinable && $leftTypeRefined) {
                $this->declareNameInScope($newScope, $comparison->left, $leftTypeRefined);
            }
            if ($rightIsRefinable && $rightTypeRefined) {
                $this->declareNameInScope($newScope, $comparison->right, $rightTypeRefined);
            }
        }

        return $newScope;
    }

    private function caseEquality(\PHPParser_Node $left, \PHPParser_Node $right,
                                    LinkedFlowScope $blindScope, $mergeMethod)
    {
        list($leftType, $leftIsRefineable) = $this->getTypeStatus($left, $blindScope);
        list($rightType, $rightIsRefineable) = $this->getTypeStatus($right, $blindScope);
        if (null === $leftType || null === $rightType) {
            return $blindScope;
        }

        // Merge Types.
        $merged = $leftType->{'getTypesUnder'.$mergeMethod}($rightType);

        // Check whether one side can be refined, and if so create a new scope.
        if (null !== $merged) {
            $refineLeft = $leftIsRefineable && null !== $merged[0];
            $refineRight = $rightIsRefineable && null !== $merged[1];

            if ($refineLeft || $refineRight) {
                $informed = $blindScope->createChildFlowScope();

                if ($refineLeft) {
                    $this->declareNameInScope($informed, $left, $merged[0]);
                }

                if ($refineRight) {
                    $this->declareNameInScope($informed, $right, $merged[1]);
                }

                return $informed;
            }
        }

        return $blindScope;
    }

    private function getTypeStatus(\PHPParser_Node $node, LinkedFlowScope $scope)
    {
        if (null !== $type = $this->getTypeIfRefinable($node, $scope)) {
            return array($type, true);
        }

        return array($node->getAttribute('type'), false);
    }

    private function checkForTypeFunctionCall(\PHPParser_Node $node, LinkedFlowScope $blindScope, $outcome)
    {
        if ($node instanceof \PHPParser_Node_Stmt_Case) {
            $left = $node->getAttribute('parent')->cond;
            $right = $node->cond;
        } else {
            $left = $node->left;
            $right = $node->right;
        }

        // Handle gettype() function calls.
        $gettypeNode = $stringNode = null;
        if (NodeUtil::isGetTypeFunctionCall($left)
                && $right instanceof \PHPParser_Node_Scalar_String) {
            $gettypeNode = $left;
            $stringNode = $right;
        } else if (NodeUtil::isGetTypeFunctionCall($right)
                        && $left instanceof \PHPParser_Node_Scalar_String) {
            $gettypeNode = $right;
            $stringNode = $left;
        }

        if (null !== $gettypeNode && null !== $stringNode
                && isset($gettypeNode->args[0])) {
            $operandNode = $gettypeNode->args[0]->value;
            $operandType = $this->getTypeIfRefinable($operandNode, $blindScope);

            if (null !== $operandType) {
                $resultEqualsValue = $node instanceof \PHPParser_Node_Expr_Equal
                                        || $node instanceof \PHPParser_Node_Expr_Identical
                                        || $node instanceof \PHPParser_Node_Stmt_Case;
                if (!$outcome) {
                    $resultEqualsValue = !$resultEqualsValue;
                }

                return $this->caseGettypeFunctionCall($operandNode, $operandType, $stringNode->value, $resultEqualsValue, $blindScope);
            }
        }

        // Handle get_class() function calls.
        $getClassNode = $stringNode = null;
        if ('get_class' === NodeUtil::getFunctionName($left)
                && $right instanceof \PHPParser_Node_Scalar_String) {
            $getClassNode = $left;
            $stringNode = $right;
        } else if ('get_class' === NodeUtil::getFunctionName($right)
                && $left instanceof \PHPParser_Node_Scalar_String) {
            $getClassNode = $right;
            $stringNode = $left;
        }

        if (null !== $getClassNode && null !== $stringNode
                && isset($getClassNode->args[0])) {
            $operandNode = $getClassNode->args[0]->value;
            $operandType = $this->getTypeIfRefinable($operandNode, $blindScope);

            if (null !== $operandType) {
                $resultEqualsValue = $node instanceof \PHPParser_Node_Expr_Equal
                                        || $node instanceof \PHPParser_Node_Expr_Identical
                                        || $node instanceof \PHPParser_Node_Stmt_Case;

                if ( ! $outcome) {
                    $resultEqualsValue = !$resultEqualsValue;
                }

                return $this->caseGetClassFunctionCall($operandNode, $operandType, $stringNode->value, $resultEqualsValue, $blindScope);
            }
        }

        // Handle is_??? and assert function calls as well as instanceof checks.
        $typeFunctionNode = $booleanNode = null;
        if ((NodeUtil::isTypeFunctionCall($left) || NodeUtil::isMaybeFunctionCall($left, 'assert')
                || $left instanceof \PHPParser_Node_Expr_Instanceof)
                && NodeUtil::isBoolean($right)) {
            $typeFunctionNode = $left;
            $booleanNode = $right;
        } else if ((NodeUtil::isTypeFunctionCall($right) || NodeUtil::isMaybeFunctionCall($right, 'assert')
                        || $right instanceof \PHPParser_Node_Expr_Instanceof)
                        && NodeUtil::isBoolean($left)) {
            $typeFunctionNode = $right;
            $booleanNode = $left;
        }

        if (null !== $booleanNode && null !== $typeFunctionNode) {
            $expectedOutcome = NodeUtil::getBooleanValue($booleanNode) ? $outcome : !$outcome;

            if ($typeFunctionNode instanceof \PHPParser_Node_Expr_Instanceof) {
                return $this->firstPreciserScopeKnowingConditionOutcome($typeFunctionNode, $blindScope, $expectedOutcome);
            }

            if (isset($typeFunctionNode->args[0])) {
                if (NodeUtil::isMaybeFunctionCall($typeFunctionNode, 'assert')) {
                    return $this->firstPreciserScopeKnowingConditionOutcome($typeFunctionNode->args[0]->value, $blindScope, $expectedOutcome);
                }

                return $this->caseTypeFunctionCall(
                    $typeFunctionNode->name,
                    $typeFunctionNode->args[0],
                    $blindScope,
                    $expectedOutcome);
            }
        }

        return null;
    }

    private function caseAndOrNotShortCircuiting(\PHPParser_Node $left, \PHPParser_Node $right, LinkedFlowScope $scope, $condition)
    {
        // left type
        if (null !== $leftType = $this->getTypeIfRefinable($left, $scope)) {
            $leftIsRefinable = true;

            if (NodeUtil::isName($left) && null !== $defExpr = $left->getAttribute('defining_expr')) {
                $scope = $this->firstPreciserScopeKnowingConditionOutcome($defExpr, $scope, $condition);
            }
        } else {
            $leftIsRefinable = false;
            $leftType = $left->getAttribute('type');
            $scope = $this->firstPreciserScopeKnowingConditionOutcome($left, $scope, $condition);
        }

        // restricting left type
        if (null !== $leftType) {
            $leftType = $leftType->getRestrictedTypeGivenToBooleanOutcome($condition);
        }

        if (null === $leftType) {
            return $this->firstPreciserScopeKnowingConditionOutcome($right, $scope, $condition);
        }

        // right type
        if (null !== $rightType = $this->getTypeIfRefinable($right, $scope)) {
            $rightIsRefinable = true;

            if (NodeUtil::isName($right) && null !== $defExpr = $right->getAttribute('defining_expr')) {
                $scope = $this->firstPreciserScopeKnowingConditionOutcome($defExpr, $scope, $condition);
            }
        } else {
            $rightIsRefinable = false;
            $rightType = $right->getAttribute('type');
            $scope = $this->firstPreciserScopeKnowingConditionOutcome($right, $scope, $condition);
        }

        if ($condition) {
            if (null !== $rightType) {
                $rightType = $rightType->getRestrictedTypeGivenToBooleanOutcome($condition);
            }

            // creating new scope
            if ((null !== $leftType && $leftIsRefinable)
                    || (null !== $rightType && $rightIsRefinable)) {
                $informed = $scope->createChildFlowScope();

                if ($leftIsRefinable && null !== $leftType) {
                    $this->declareNameInScope($informed, $left, $leftType);
                }
                if ($rightIsRefinable && null !== $rightType) {
                    $this->declareNameInScope($informed, $right, $rightType);
                }

                return $informed;
            }
        }

        return $scope;
    }

    /**
     * Handles the case where a AND/OR condition may be short circuiting.
     *
     * @param \PHPParser_Node $left
     * @param \PHPParser_Node $right
     * @param \Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\LinkedFlowScope $blindScope
     * @param boolean $condition
     *
     * @return \Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\LinkedFlowScope
     */
    private function caseAndOrMaybeShortCircuiting(\PHPParser_Node $left,
        \PHPParser_Node $right, LinkedFlowScope $blindScope, $condition)
    {
        $leftScope = $this->narrowMaybeConditionScope($left, $blindScope, ! $condition);
        if (null === $leftVar = $leftScope->findUniqueRefinedSlot($blindScope)) {
            return $blindScope;
        }

        $rightScope = $this->narrowMaybeConditionScope($left, $blindScope, $condition);
        $rightScope = $this->narrowMaybeConditionScope($right, $rightScope, ! $condition);

        $rightVar = $rightScope->findUniqueRefinedSlot($blindScope);
        if (null === $rightVar || $leftVar->getName() !== $rightVar->getName()) {
            return $blindScope;
        }

        $type = $leftVar->getType()->getLeastSupertype($rightVar->getType());
        $informed = $blindScope->createChildFlowScope();
        $informed->inferSlotType($leftVar->getName(), $type);

        return $informed;
    }

    private function narrowMaybeConditionScope(\PHPParser_Node $expr, LinkedFlowScope $blindScope, $condition)
    {
        // In contrast to a non-short-circuiting condition scope, for may-be short-circuiting scopes,
        // we make the simplification to not narrow down the expression, and the defining expression
        // by always using the defining expression in case the variable is of boolean type and never
        // if it has a different type.
        if (NodeUtil::isName($expr) && (null !== $defExpr = $expr->getAttribute('defining_expr'))
                && (null !== $exprType = $expr->getAttribute('type'))
                && $exprType->isBooleanType()) {
            return $this->firstPreciserScopeKnowingConditionOutcome($defExpr, $blindScope, $condition);
        }

        return $this->firstPreciserScopeKnowingConditionOutcome($expr, $blindScope, $condition);
    }

    private function caseGetClassFunctionCall(\PHPParser_Node $node, PhpType $type, $value, $resultEqualsValue, LinkedFlowScope $blindScope)
    {
        $restrictedType = $this->getRestrictedByGetClassResult($type, $value, $resultEqualsValue);
        if (null === $restrictedType) {
            return $blindScope;
        }

        $informed = $blindScope->createChildFlowScope();
        $this->declareNameInScope($informed, $node, $restrictedType);

        return $informed;
    }

    private function caseGettypeFunctionCall(\PHPParser_Node $node, PhpType $type, $value, $resultEqualsValue, LinkedFlowScope $blindScope)
    {
        $restrictedType = $this->getRestrictedByGettypeResult($type, $value, $resultEqualsValue);

        // cannot narrow the scope
        if (null === $restrictedType) {
            return $blindScope;
        }

        $informed = $blindScope->createChildFlowScope();
        $this->declareNameInScope($informed, $node, $restrictedType);

        return $informed;
    }
}