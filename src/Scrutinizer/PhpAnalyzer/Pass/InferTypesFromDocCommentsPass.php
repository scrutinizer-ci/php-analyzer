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

use Scrutinizer\PhpAnalyzer\Pass\AnalysisPassInterface;
use Scrutinizer\PhpAnalyzer\Analyzer;
use Scrutinizer\PhpAnalyzer\AnalyzerAwareInterface;
use Scrutinizer\PhpAnalyzer\Pass\CallbackAnalysisPassInterface;
use Scrutinizer\PhpAnalyzer\PhpParser\DocCommentParser;
use Scrutinizer\PhpAnalyzer\Model\AbstractFunction;
use Scrutinizer\PhpAnalyzer\Model\Clazz;
use Scrutinizer\PhpAnalyzer\Model\Constant;
use Scrutinizer\PhpAnalyzer\Model\GlobalConstant;
use Scrutinizer\PhpAnalyzer\Model\GlobalFunction;
use Scrutinizer\PhpAnalyzer\Model\InterfaceC;
use Scrutinizer\PhpAnalyzer\Model\Method;
use Scrutinizer\PhpAnalyzer\Model\Parameter;
use Scrutinizer\PhpAnalyzer\Model\Property;
use Scrutinizer\PhpAnalyzer\Model\TraitC;
use Scrutinizer\PhpAnalyzer\Model\File;

/**
 * Parses Doc comments and applies type information to nodes.
 *
 * TODO: There is a lot of code duplication in this pass which should be
 *       cleaned up.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class InferTypesFromDocCommentsPass implements AnalysisPassInterface, AnalyzerAwareInterface, CallbackAnalysisPassInterface
{
    private $registry;
    private $parser;

    public function setAnalyzer(Analyzer $analyzer)
    {
        $this->registry = $analyzer->getTypeRegistry();
        $this->parser = new DocCommentParser($this->registry);
    }

    public function analyze(File $file) { }
    public function afterAnalysis() { }

    public function beforeAnalysis()
    {
        foreach ($this->registry->getClasses() as $class) {
            if (null === $class->getAstNode()) {
                continue;
            }

            if ($class instanceof Clazz) {
                $this->inferTypesForClass($class);
            } else if ($class instanceof InterfaceC) {
                $this->inferTypesForInterface($class);
            } else if ($class instanceof TraitC) {
                $this->inferTypesForTrait($class);
            } else {
                throw new \LogicException('The previous ifs are exhaustive.');
            }
        }

        $this->parser->setCurrentClassName(null);
        foreach ($this->registry->getFunctions() as $function) {
            if (null === $function->getAstNode()) {
                continue;
            }

            $this->inferTypesForMethodOrFunction($function);
        }

        $this->parser->setImportedNamespaces(array('' => ''));
        foreach ($this->registry->getConstants() as $constant) {
            if (null === $constant->getAstNode()) {
                continue;
            }

            $this->inferTypesForConstant($constant);
        }
    }

    private function inferTypesForClass(Clazz $class)
    {
        $this->parser->setCurrentClassName($class->getName());
        $this->parser->setImportedNamespaces($class->getImportedNamespaces());
        foreach ($class->getConstants() as $constant) {
            $this->inferTypesForClassConstant($constant->getConstant());
        }

        foreach ($class->getMethods() as $method) {
            $this->inferTypesForMethodOrFunction($method->getMethod(), $class);
        }

        foreach ($class->getProperties() as $property) {
            $this->inferTypesForProperty($property->getProperty());
        }
    }

    private function inferTypesForInterface(InterfaceC $interface)
    {
        $this->parser->setCurrentClassName($interface->getName());
        $this->parser->setImportedNamespaces($interface->getImportedNamespaces());
        foreach ($interface->getConstants() as $constant) {
            $this->inferTypesForClassConstant($constant->getConstant());
        }

        foreach ($interface->getMethods() as $method) {
            // We do not pass the container here as it does not make sense to
            // check for @inheritdoc on interfaces (methods cannot be overridden).
            $this->inferTypesForMethodOrFunction($method->getMethod());
        }
    }

    private function inferTypesForTrait(TraitC $trait)
    {
        $this->parser->setCurrentClassName($trait->getName()); // TODO: This is not correct as a trait is not a type.
        $this->parser->setImportedNamespaces($trait->getImportedNamespaces());

        // TODO: Can a trait have constants?

        foreach ($trait->getMethods() as $method) {
            // We do not pass the container here as it does not make sense to
            // check for @inheritdoc on traits (there is nothing to inherit from).
            $this->inferTypesForMethodOrFunction($method->getMethod());
        }

        foreach ($trait->getProperties() as $property) {
            $this->inferTypesForProperty($property->getProperty());
        }
    }

    private function inferTypesForParameter(Parameter $param, AbstractFunction $function, Clazz $clazz = null)
    {
        if (($type = $param->getPhpType()) && !$type->isUnknownType() && !$type->isNullType()
                && !$type->isFalse() && !$type->isAllType()) {
            return;
        }

        $allowsNull = $type && $type->isNullType();

        if (null !== $node = $param->getAstNode()) {
            $type = $this->parser->getTypeFromParamAnnotation($node, $param->getName());

            // Check whether we can use the type of a overridden method.
            // We only do this if we find a parent method that has been defined
            // on an interface (I), or if we find an abstract method (II).
            if (null === $type && $function instanceof Method && null !== $clazz) {
                foreach ($this->findOverriddenMethodsForDocInheritance($function->getName(), $clazz) as $parentMethod) {
                    if (null !== $docType = $parentMethod->getParamDocType($param->getIndex())) {
                        $type = $this->parser->getType($docType);
                        break;
                    }
                }
            }
        }

        if (null === $type) {
            $type = $this->registry->getNativeType('unknown');
        } else if ($allowsNull) {
            $type = $this->registry->createNullableType($type);
        }

        $param->setPhpType($type);
        if ($node) {
            $node->setAttribute('type', $type);
        }
    }

    private function inferTypesForConstant(GlobalConstant $constant)
    {
        if (($type = $constant->getPhpType()) && !$type->isUnknownType()) {
            return;
        }

        if (null !== $node = $constant->getAstNode()) {
            $type = $this->parser->getTypeFromVarAnnotation($node);
        }

        if (null === $type) {
            $type = $this->registry->getNativeType('unknown');
        }

        $constant->setPhpType($type);
        if ($node) {
            $node->setAttribute('type', $type);
        }
    }

    private function inferTypesForProperty(Property $property)
    {
        if (($type = $property->getPhpType()) && !$type->isUnknownType()) {
            return;
        }

        if (null !== $node = $property->getAstNode()) {
            if ($property instanceof \PHPParser_Node_Stmt_Class) {
                $type = $this->parser->getTypeFromMagicPropertyAnnotation($node, $property->getName());
            } else {
                $type = $this->parser->getTypeFromVarAnnotation($node);
            }
        }

        if (null === $type) {
            $type = $this->registry->getNativeType('unknown');
        }

        $property->setPhpType($type);
        if ($node) {
            $node->setAttribute('type', $type);
        }
    }

    private function findOverriddenMethodsForDocInheritance($methodName, Clazz $clazz)
    {
        $methods = array();

        foreach ($clazz->getImplementedInterfaces() as $interfaceName) {
            if (null !== $interface = $this->registry->getClass($interfaceName)) {
                if ($interface->hasMethod($methodName)) {
                    $methods[] = $interface->getMethod($methodName);
                }
            }
        }

        $superClass = $clazz;
        while (null !== $superClass = $superClass->getSuperClassType()) {
            if ($superClass->hasMethod($methodName)
                    && ($parentMethod = $superClass->getMethod($methodName))
                    && $parentMethod->isAbstract()) {
                $methods[] = $parentMethod;
            }
        }

        return $methods;
    }

    private function inferTypesForMethodOrFunction(AbstractFunction $function, Clazz $clazz = null)
    {
        if ($function instanceof GlobalFunction) {
            $this->parser->setImportedNamespaces($function->getImportedNamespaces());
        }

        foreach ($function->getParameters() as $param) {
            $this->inferTypesForParameter($param, $function, $clazz);
        }

        if (($type = $function->getReturnType()) && !$type->isUnknownType()) {
            return;
        }

        if (null !== $node = $function->getAstNode()) {
            $type = $this->parser->getTypeFromReturnAnnotation($node);

            if (null === $type && $function instanceof Method && null !== $clazz) {
                foreach ($this->findOverriddenMethodsForDocInheritance($function->getName(), $clazz) as $parentMethod) {
                    if (null !== $docType = $parentMethod->getReturnDocType()) {
                        $type = $this->parser->getType($docType);
                        break;
                    }
                }
            }
        }

        if (null === $type) {
            $type = $this->registry->getNativeType('unknown');
        }

        $function->setReturnType($type);
        if ($node) {
            $node->setAttribute('type', $type);
        }
    }

    private function inferTypesForClassConstant(Constant $constant)
    {
        if (($type = $constant->getPhpType()) && !$type->isUnknownType()) {
            return;
        }

        if (null !== $node = $constant->getAstNode()) {
            $type = $this->parser->getTypeFromVarAnnotation($node);
        }

        if (null === $type) {
            $type = $this->registry->getNativeType('unknown');
        }

        $constant->setPhpType($type);
        if ($node) {
            $node->setAttribute('type', $type);
        }
    }
}