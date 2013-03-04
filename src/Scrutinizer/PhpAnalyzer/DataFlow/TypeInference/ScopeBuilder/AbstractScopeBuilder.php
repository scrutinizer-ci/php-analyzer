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

namespace Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\ScopeBuilder;

use Scrutinizer\PhpAnalyzer\PhpParser\DocCommentParser;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil;
use Scrutinizer\PhpAnalyzer\PhpParser\Scope\Scope;
use Scrutinizer\PhpAnalyzer\PhpParser\Traversal\CallbackInterface;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\ArrayType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\PhpType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\UnionTypeBuilder;

abstract class AbstractScopeBuilder implements CallbackInterface
{
    /** @var Scope */
    protected $scope;

    protected $typeRegistry;
    protected $commentParser;

    protected $importedClasses = array();

    public function __construct(Scope $scope, TypeRegistry $registry)
    {
        $this->scope = $scope;
        $this->typeRegistry = $registry;
        $this->commentParser = new DocCommentParser($registry);
    }

    public function shouldTraverse(NodeTraversal $traversal, \PHPParser_Node $node, \PHPParser_Node $parent = null)
    {
        if (NodeUtil::isScopeCreator($node) && null !== $parent) {
            if ($node instanceof \PHPParser_Node_Expr_Closure) {
                $node->setAttribute('type', $this->typeRegistry->getClassOrCreate('Closure'));
            }

            return false;
        }

        return true;
    }

    public function visit(NodeTraversal $t, \PHPParser_Node $node, \PHPParser_Node $parent = null)
    {
        $className = null;
        $classParent = $node;
        while ($classParent = $classParent->getAttribute('parent')) {
            if ($classParent instanceof \PHPParser_Node_Stmt_Class) {
                $className = implode("\\", $classParent->namespacedName->parts);
                break;
            }
        }

        $this->commentParser->setCurrentClassName($className);
        $this->commentParser->setImportedNamespaces($this->importedClasses);
        $this->attachLiteralTypes($node, $parent);

        switch (true) {
            case $node instanceof \PHPParser_Node_Stmt_PropertyProperty:
                $this->checkPropertyAssignment($node);
                break;

            case $node instanceof \PHPParser_Node_Param:
                $this->checkParameterAssignment($node);
                break;

            case $node instanceof \PHPParser_Node_Stmt_Catch:
                $exType = $this->typeRegistry->getClassOrCreate(implode("\\", $node->type->parts));

                if ($this->scope->isDeclared($node->var)) {
                    $var = $this->scope->getVar($node->var);
                    if ($varType = $var->getType()) {
                        $exType = $this->typeRegistry->createUnionType(array($varType, $exType));
                    }
                    $var->setType($exType);
                    $var->setTypeInferred(false);
                } else {
                    $var = $this->scope->declareVar($node->var, $exType, false);
                    $var->setNameNode($node);
                }

                break;

            case $node instanceof \PHPParser_Node_Stmt_StaticVar:
                if (!$this->scope->isDeclared($node->name)) {
                    $type = $node->default ? $node->default->getAttribute('type') : null;
                    $this->scope->declareVar($node->name, $type, null !== $type);
                }
                break;

            case $node instanceof \PHPParser_Node_Expr_Assign:
                if ($node->var instanceof \PHPParser_Node_Expr_Variable
                        && is_string($node->var->name)
                        && !$this->scope->isDeclared($node->var->name)) {
                    $this->scope->declareVar($node->var->name);
                }
                break;
        }
    }

    private function checkParameterAssignment(\PHPParser_Node_Param $node)
    {
        $builder = new UnionTypeBuilder($this->typeRegistry);
        if (null !== $node->default) {
            $this->attachLiteralTypes($node->default);
            $type = $node->default->getAttribute('type');

            if ($type) {
                $builder->addAlternate($type);
            }
        }

        if ('array' === $node->type) {
            $type = $this->typeRegistry->getNativeType('array');

            // If the annotated type also contains the array type, we use the annotated
            // type as it is potentially more specific, e.g. "array<string>" or "array|null".
            $annotatedType = $this->commentParser->getTypeFromParamAnnotation($node->getAttribute('parent'), $node->name);
            if (null !== $containedArrayType = $this->getContainedArrayType($annotatedType)) {
                $type = $containedArrayType;
            }

            $builder->addAlternate($type);
        } else if ($node->type instanceof \PHPParser_Node_Name) {
            $builder->addAlternate($this->typeRegistry->getClassOrCreate(implode("\\", $node->type->parts)));
        }
        $type = $builder->build();

        if ($type->isNoType()) {
            $type = $this->commentParser->getTypeFromParamAnnotation($node->getAttribute('parent'), $node->name);
        } else if ($type->isNullType()) {
            $commentType = $this->commentParser->getTypeFromParamAnnotation($node->getAttribute('parent'), $node->name);
            if ($commentType) {
                $type = $this->typeRegistry->createNullableType($commentType);
            } else {
                $type = null;
            }
        } else if ($type->isBooleanType()) {
            $commentType = $this->commentParser->getTypeFromParamAnnotation($node->getAttribute('parent'), $node->name);
            if ($commentType) {
                $type = $this->typeRegistry->createUnionType(array($type, $commentType));
            }
        }

        if (null === $type) {
            $type = $this->typeRegistry->getNativeType('unknown');
        }
        $node->setAttribute('type', $type);

        // This could be the case if the same name is used twice as parameter.
        if (false === $this->scope->isDeclared($node->name)) {
            $var = $this->scope->declareVar($node->name, $type, null === $type);
            $var->setNameNode($node);
            $var->setReference($node->byRef);
        } else {
            $var = $this->scope->getVar($node->name);
            if ($varType = $var->getType()) {
                $var->setType($this->typeRegistry->createUnionType(array($varType, $type)));
            } else if (null !== $type) {
                $var->setType($type);
                $var->setTypeInferred(true);
            }
            $var->setReference($node->byRef);
        }
    }

    /**
     * Returns the array type contained in the given type, or null if no array
     * type is available.
     *
     * @param PhpType|null $type
     *
     * @return ArrayType|null
     */
    private function getContainedArrayType(PhpType $type = null)
    {
        if (null === $type) {
            return null;
        }

        if ($type->isArrayType()) {
            return $type;
        }

        if ( ! $type->isUnionType()) {
            return null;
        }

        foreach ($type->getAlternates() as $alt) {
            if ($alt->isArrayType()) {
                return $alt;
            }
        }

        return null;
    }

    /**
     * Checks properties for types.
     *
     * The following precedence is used:
     *
     *     1. Checks rhs for type
     *     2. Checks for a "@var" annotation
     */
    private function checkPropertyAssignment(\PHPParser_Node_Stmt_PropertyProperty $node)
    {
        if (null !== $node->default && null !== $type = $node->default->getAttribute('type')) {
            // If we receive a generic array, let's see if there is a @var annotation
            // that is more specific about the type.
            if ($type instanceof ArrayType
                    && null === $type->getElementType()) {
                $newType = $this->commentParser->getTypeFromVarAnnotation($node);

                if ($newType instanceof ArrayType && null !== $type->getElementType()) {
                    $type = $newType;
                }
            }

            $node->setAttribute('type', $type);
        } else if (null !== $type = $this->commentParser->getTypeFromVarAnnotation($node)) {
            $node->setAttribute('type', $type);
        }
    }

    private function attachLiteralTypes(\PHPParser_Node $node, \PHPParser_Node $parent = null)
    {
        switch (true) {
            case $node instanceof \PHPParser_Node_Name_FullyQualified:
                $node->setAttribute('type', $this->typeRegistry->getClassOrCreate(implode("\\", $node->parts)));
                break;

            case $node instanceof \PHPParser_Node_Name:
                if ($parent instanceof \PHPParser_Node_Expr_New
                        || $parent instanceof \PHPParser_Node_Expr_StaticPropertyFetch
                        || $parent instanceof \PHPParser_Node_Expr_StaticCall
                        || $parent instanceof \PHPParser_Node_Expr_Instanceof
                        || $parent instanceof \PHPParser_Node_Stmt_Catch) {
                    $name = implode("\\", $node->parts);
                    $lowerName = strtolower($name);

                    if ('static' === $name) {
                        $node->setAttribute('type', $this->typeRegistry->getThisType($this->scope->getTypeOfThis()));
                    } else if ('self' === $name) {
                        $node->setAttribute('type', $this->scope->getTypeOfThis());
                    } else if ('parent' === $name) {
                        $thisType = $this->scope->getTypeOfThis()->toMaybeObjectType();
                        if (null === $thisType || ! $thisType->isClass() || null === $thisType->getSuperClassType()) {
                            $node->setAttribute('type', $this->typeRegistry->getNativeType('unknown'));
                        } else {
                            $node->setAttribute('type', $thisType->getSuperClassType());
                        }
                    } else {
                        $node->setAttribute('type', $this->typeRegistry->getClassOrCreate($name));
                    }
                }
                break;

            case $node instanceof \PHPParser_Node_Expr_Array:
            case $node instanceof \PHPParser_Node_Expr_Cast_Array:
                // We only do attach the generic array type on the first build.
                // For subsequent builds, other passes likely have already made
                // the array type more specific.
                if (null === $node->getAttribute('type')) {
                    $node->setAttribute('type', $this->typeRegistry->getNativeType('array'));
                }
                break;

            case $node instanceof \PHPParser_Node_Expr_UnaryMinus:
            case $node instanceof \PHPParser_Node_Expr_UnaryPlus:
            case $node instanceof \PHPParser_Node_Scalar_LNumber:
            case $node instanceof \PHPParser_Node_Scalar_LineConst:
                $node->setAttribute('type', $this->typeRegistry->getNativeType('integer'));
                break;

            case $node instanceof \PHPParser_Node_Scalar_DNumber:
                $node->setAttribute('type', $this->typeRegistry->getNativeType('double'));
                break;

            case $node instanceof \PHPParser_Node_Scalar_String:
            case $node instanceof \PHPParser_Node_Scalar_FileConst:
            case $node instanceof \PHPParser_Node_Scalar_DirConst:
                $node->setAttribute('type', $this->typeRegistry->getNativeType('string'));
                break;

            case $node instanceof \PHPParser_Node_Expr_ClassConstFetch:
                if ($node->class instanceof \PHPParser_Node_Name
                        && in_array($node->class->parts[0], array('self', 'static'))
                        && (null !== $thisType = $this->scope->getTypeOfThis())
                        && (null !== $objType = $thisType->toMaybeObjectType())
                        && ($objType->isClass() || $objType->isInterface())
                        && $objType->hasConstant($node->name)) {
                    $node->setAttribute('type', $objType->getConstant($node->name)->getPhpType());
                }

                break;

            case $node instanceof \PHPParser_Node_Expr_ConstFetch:
                $nameParts = $node->name->parts;

                if (1 === count($nameParts)) {
                    $name = strtolower($nameParts[0]);

                    if ('true' === $name)  {
                        $node->setAttribute('type', $this->typeRegistry->getNativeType('boolean'));
                    } else if ('false' === $name) {
                        $node->setAttribute('type', $this->typeRegistry->getNativeType('false'));
                    } else if ('null' === $name) {
                        $node->setAttribute('type', $this->typeRegistry->getNativeType('null'));
                    }
                }

                break;
        }
    }
}