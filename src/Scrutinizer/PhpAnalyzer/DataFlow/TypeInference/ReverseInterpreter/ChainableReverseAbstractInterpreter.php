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

use Scrutinizer\PhpAnalyzer\PhpParser\Type\PhpType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\LinkedFlowScope;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\TypeInference;

abstract class ChainableReverseAbstractInterpreter implements ReverseInterpreterInterface
{
    /** @var ChainableReverseAbstractInterpreter */
    private $firstLink;

    /** @var ChainableReverseAbstractInterpreter */
    private $nextLink;

    protected $typeRegistry;

    public function __construct(TypeRegistry $registry)
    {
        $this->typeRegistry = $registry;
        $this->firstLink = $this;
    }

    /**
     * Appends an interpreter, returning the updated last link.
     *
     * @param ChainableReverseAbstractInterpreter $lastLink
     * @return ChainableReverseAbstractInterpreter The updated last link
     */
    public function append(ChainableReverseAbstractInterpreter $lastLink)
    {
        assert(null === $lastLink->nextLink);

        $this->nextLink = $lastLink;
        $lastLink->firstLink = $this;

        return $lastLink;
    }

    public function getFirst()
    {
        return $this->firstLink;
    }

    /**
     * @param \PHPParser_Node $condition
     * @param LinkedFlowScope $blindScope
     * @param boolean $outcome
     *
     * @return LinkedFlowScope
     */
    protected function firstPreciserScopeKnowingConditionOutcome(\PHPParser_Node $condition,
                            LinkedFlowScope $blindScope, $outcome)
    {
        return $this->firstLink->getPreciserScopeKnowingConditionOutcome($condition, $blindScope, $outcome);
    }

    /**
     * Delegates the calculation of the preciser scope to the next link.
     * If there is no next link, returns the blind scope.
     *
     * @return LinkedFlowScope
     */
    protected function nextPreciserScopeKnowingConditionOutcome(\PHPParser_Node $condition,
                            LinkedFlowScope $blindScope, $outcome)
    {
        return $this->nextLink !== null ? $this->nextLink->getPreciserScopeKnowingConditionOutcome(
            $condition, $blindScope, $output) : $blindScope;
    }

    /**
     * Returns the type of the given node in the flow scope.
     *
     * If the node cannot be matched to a slot, or simply is not capable of being
     * refined, then this method returns null.
     *
     * @return PhpType|null
     */
    public function getTypeIfRefinable(\PHPParser_Node $node, LinkedFlowScope $scope)
    {
        switch (true) {
            case $node instanceof \PHPParser_Node_Expr_ArrayDimFetch:
                $varType = $this->getTypeIfRefinable($node->var, $scope);
                if ($varType && $varType->isArrayType()) {
                    $dim = \Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil::getValue($node->dim);
                    if ($dim->isEmpty()) {
                        return null;
                    }

                    return $dim->flatMap([$varType, 'getItemType'])
                               ->getOrCall([$varType, 'getElementType']);
                }

                return null;

            // Handle the common pattern of assigning the result of an expression
            // inside of a condition, e.g. ``if (null !== $obj = $this->findObj())``.
            case $node instanceof \PHPParser_Node_Expr_Assign:
            case $node instanceof \PHPParser_Node_Expr_AssignRef:
                if (TypeInference::hasQualifiedName($node->var)
                        && (null !== $qualifiedName = TypeInference::getQualifiedName($node->var))
                        && null !== $nameVar = $scope->getSlot($node->var->name)) {
                    if (null !== $nameType = $nameVar->getType()) {
                        return $nameType;
                    }

                    return $node->var->getAttribute('type');
                }

                return null;

            case $node instanceof \PHPParser_Node_Expr_Variable:
                if (is_string($node->name)) {
                    $nameVar = $scope->getSlot($node->name);

                    if (null !== $nameVar) {
                        if (null !== $nameVar->getType()) {
                            return $nameVar->getType();
                        }

                        return $node->getAttribute('type');
                    }
                }

                return null;

            case $node instanceof \PHPParser_Node_Expr_StaticPropertyFetch:
            case $node instanceof \PHPParser_Node_Expr_PropertyFetch:
                if (null === $qualifiedName = TypeInference::getQualifiedName($node)) {
                    return null;
                }

                $propVar = $scope->getSlot($qualifiedName);
                if (null !== $propVar && null !== $propVar->getType()) {
                    return $propVar->getType();
                }

                if (null !== $type = $node->getAttribute('type')) {
                    return $type;
                }

                return null;
        }

        return null;
    }

    /**
     * Returns a version of $type that is restricted by some knowledge
     * about the result of the gettype function.
     *
     * @param PhpType $type
     * @param string $value
     * @param boolean $resultEqualsValue
     */
    protected function getRestrictedByGettypeResult(PhpType $type = null, $value, $resultEqualsValue)
    {
        if (null === $type) {
            if ($resultEqualsValue) {
                $result = $this->getNativeTypeForGettypeResult($value);

                return $result ?: $this->typeRegistry->getNativeType('unknown');
            }

            return null;
        }

        return $type->visit(new RestrictByGettypeResultVisitor($this->typeRegistry, $value, $resultEqualsValue));
    }

    protected function getRestrictedByGetClassResult(PhpType $type = null, $value, $resultEqualsValue)
    {
        if (null === $type) {
            if ($resultEqualsValue) {
                return $this->typeRegistry->getClassOrCreate($value);
            }

            return null;
        }

        return $type->visit(new RestrictByGetClassResultVisitor($this->typeRegistry, $value, $resultEqualsValue));
    }

    private function getNativeTypeForGettypeResult($resultName)
    {
        return self::getNativeTypeForGettypeResultHelper($this->typeRegistry, $resultName);
    }

    public static function getNativeTypeForGettypeResultHelper(TypeRegistry $registry, $resultName)
    {
        switch ($resultName) {
            case 'integer':
            case 'double':
            case 'array':
            case 'object':
            case 'boolean':
            case 'string':
                return $registry->getNativeType($resultName);

            case 'NULL':
                return $registry->getNativeType('null');

            default:
                return null;
        }
    }

    /**
     * Declares a refined type in {@code scope} for the name represented by
     * {@code node}. It must be possible to refine the type of the given node in
     * the given scope, as determined by {@link #getTypeIfRefinable}.
     */
    protected function declareNameInScope(LinkedFlowScope $scope, \PHPParser_Node $node, PhpType $type) {
        switch (true) {
            case $node instanceof \PHPParser_Node_Expr_Variable:
                if (is_string($node->name)) {
                    $scope->inferSlotType($node->name, $type);
                }
                break;

            case $node instanceof \PHPParser_Node_Expr_StaticPropertyFetch:
            case $node instanceof \PHPParser_Node_Expr_PropertyFetch:
                if (null !== $qualifiedName = TypeInference::getQualifiedName($node)) {
                    $origType = $node->getAttribute('type') ?: $this->typeRegistry->getNativeType('unknown');
                    $scope->inferQualifiedSlot($node, $qualifiedName, $origType, $type);
                }
                break;

            // Something like: if (is_array($functions = getFunctionNames())) { }
            case $node instanceof \PHPParser_Node_Expr_Assign:
            case $node instanceof \PHPParser_Node_Expr_AssignRef:
                $this->declareNameInScope($scope, $node->var, $type);
                break;

            case $node instanceof \PHPParser_Node_Expr_ArrayDimFetch:
                $dim = \Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil::getValue($node->dim);
                if ($dim->isDefined()
                        && (null !== $slotType = $node->var->getAttribute('type'))
                        && $slotType->isArrayType()) {
                    $newSlotType = $slotType->inferItemType($dim->get(), $type);
                    $this->declareNameInScope($scope, $node->var, $newSlotType);
                }

                break;
        }
    }
}