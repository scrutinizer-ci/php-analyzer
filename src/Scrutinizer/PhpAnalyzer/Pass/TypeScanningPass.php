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
use Scrutinizer\PhpAnalyzer\Pass\CallbackAnalysisPassInterface;
use Scrutinizer\PhpAnalyzer\PhpParser\ClassParser;
use Scrutinizer\PhpAnalyzer\PhpParser\DocCommentParser;
use Scrutinizer\PhpAnalyzer\PhpParser\FunctionParser;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil;
use Scrutinizer\PhpAnalyzer\PhpParser\ParameterParser;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;
use Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\TypedScopeCreator;
use Scrutinizer\PhpAnalyzer\Model\Clazz;
use Scrutinizer\PhpAnalyzer\Model\ConstantContainerInterface;
use Scrutinizer\PhpAnalyzer\Model\GlobalConstant;
use Scrutinizer\PhpAnalyzer\Model\InterfaceC;
use Scrutinizer\PhpAnalyzer\Model\MethodContainer;
use Scrutinizer\PhpAnalyzer\Model\TraitC;

/**
 * Scans the code for new type definitions, and then populates the
 * TypeRegistry with them.
 *
 * After all files have been scanned, the found types are normalized if
 * possible to speed up lookups.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class TypeScanningPass extends AstAnalyzerPass implements CallbackAnalysisPassInterface
{
    /** @var DocCommentParser */
    private $commentParser;

    /** @var ClassParser */
    private $classParser;

    /** @var FunctionParser */
    private $functionParser;
    private $visitedTypes;
    private $classFiles;
    private $importedNamespaces = array();

    public function setAnalyzer(Analyzer $analyzer)
    {
        parent::setAnalyzer($analyzer);

        $paramParser = new ParameterParser($this->typeRegistry);

        $this->commentParser = new DocCommentParser($this->typeRegistry);
        $this->commentParser->setLogger($analyzer->logger);

        $this->classParser = new ClassParser($this->typeRegistry, $paramParser, $this->commentParser, $analyzer->logger);
        $this->classFiles = new \SplObjectStorage();

        $this->functionParser = new FunctionParser($this->typeRegistry, $paramParser);
    }

    public function enterScope(NodeTraversal $traversal)
    {
        // This has the side-effect of attaching type information to parameters.
        // TODO: This is more a hack atm, we should look into refactoring this.
        //       Maybe a simple rename to buildScope(), or getOrBuildScope.
        $traversal->getScope();
    }

    public function shouldTraverse(NodeTraversal $t, \PHPParser_Node $node, \PHPParser_Node $parent = null)
    {
        if ($node instanceof \PHPParser_Node_Stmt_Namespace) {
            $this->importedNamespaces = array('' => $node->name ? implode("\\", $node->name->parts) : '');
        } else if ($node instanceof \PHPParser_Node_Stmt_UseUse) {
            $this->importedNamespaces[$node->alias] = implode("\\", $node->name->parts);
        }

        return true;
    }

    public function visit(NodeTraversal $t, \PHPParser_Node $node, \PHPParser_Node $parent = null)
    {
        $this->enterNode($node);

        if ($node instanceof \PHPParser_Node_Stmt_Namespace) {
            $this->importedNamespaces = array();
        }
    }

    private function enterNode(\PHPParser_Node $node)
    {
        if (NodeUtil::isMethodContainer($node)) {
            $this->commentParser->setCurrentClassName(implode("\\", $node->namespacedName->parts));
            $this->commentParser->setImportedNamespaces($this->importedNamespaces);
            $this->classParser->setImportedNamespaces($this->importedNamespaces);
            $class = $this->classParser->parse($node);
            $this->classFiles[$class] = $this->phpFile;

            if ($this->typeRegistry->hasClass($class->getName(), TypeRegistry::LOOKUP_NO_CACHE)) {
                $this->analyzer->logger->warning(sprintf('The class "%s" has been defined more than once (maybe as a fixture for code generation). Ignoring the second definition.', $class->getName()));

                return;
            }

            $this->typeRegistry->registerClass($class);
        } else if ($node instanceof \PHPParser_Node_Stmt_Function) {
            $this->functionParser->setImportedNamespaces($this->importedNamespaces);
            $function = $this->functionParser->parse($node);

            if ($this->typeRegistry->hasFunction($functionName = $function->getName(), false)) {
                $this->analyzer->logger->warning(sprintf('The function "%s" has been defined more than once (probably conditionally). Ignoring the second definition.', $functionName));

                return;
            }

            $this->typeRegistry->registerFunction($function);
        } else if (NodeUtil::isConstantDefinition($node)) {
            assert($node instanceof \PHPParser_Node_Expr_FuncCall);

            if (!$node->args[0]->value instanceof \PHPParser_Node_Scalar_String) {
                return;
            }

            $constant = new GlobalConstant($node->args[0]->value->value);
            $constant->setAstNode($node);
            $constant->setPhpType($this->typeRegistry->getNativeType('unknown'));

            if (null !== $type = $node->args[1]->value->getAttribute('type')) {
                $constant->setPhpType($type);
            }

            if ($this->typeRegistry->hasConstant($constant->getName(), false)) {
                $this->analyzer->logger->warning(sprintf('The constant "%s" was defined more than once. Ignoring all but first definition.', $constant->getName()));

                return;
            }

            $this->typeRegistry->registerConstant($constant);
        }
    }

    public function afterAnalysis()
    {
        // Enable the cache again now that we have scanned all types.
        $this->typeRegistry->setCacheDisabled(false);

        $this->visitedTypes = new \SplObjectStorage();
        foreach ($this->typeRegistry->getClasses() as $class) {
            $this->normalizeType($class);
        }

        $this->typeRegistry->resolveNamedTypes();
    }

    public function beforeAnalysis()
    {
        // We disable the cache during type scanning in order to not load any
        // stale classes from the cache which might have been modified, but which
        // we just have not scanned yet.
        $this->typeRegistry->setCacheDisabled(true);

        $this->analyzer->logger->info('Scanning Code For Types');
    }

    protected function getScopeCreator()
    {
        return new TypedScopeCreator($this->typeRegistry);
    }

    private function normalizeType(MethodContainer $class)
    {
        // If the class is coming from the cache, it is already normalized.
        if ($class->isNormalized()) {
            return true;
        }

        // If this class was visited as part of a parent/interface, return here.
        if (isset($this->visitedTypes[$class])) {
            return $this->visitedTypes[$class];
        }

        $normalized = true;
        if ($class instanceof Clazz) {
            if ($superClass = $class->getSuperClass()) {
                // Check if the type registry contains this class
                if ($superClassType = $this->typeRegistry->getClass($superClass)) {
                    if (!$this->normalizeType($superClassType)) {
                        $normalized = false;
                    }

                    $this->mergeTypes($class, $superClassType);
                } else {
                    $normalized = false;
                }
            }

            foreach ($class->getImplementedInterfaces() as $interfaceName) {
                if (null === $interfaceType = $this->typeRegistry->getClass($interfaceName)) {
                    $normalized = false;
                    continue;
                }

                if (!$this->normalizeType($interfaceType)) {
                    $normalized = false;
                }

                $this->mergeTypes($class, $interfaceType);
            }
        } else if ($class instanceof InterfaceC) {
            foreach ($class->getExtendedInterfaces() as $interfaceName) {
                if (null === $interfaceType = $this->typeRegistry->getClass($interfaceName)) {
                    $normalized = false;
                    continue;
                }

                if (!$this->normalizeType($interfaceType)) {
                    $normalized = false;
                }

                $this->mergeTypes($class, $interfaceType);
            }
        } else if ($class instanceof TraitC) {
            // TODO: Implement support for Traits
        } else {
            throw new \LogicException('Previous ifs were exhaustive.');
        }

        $class->setNormalized($normalized);
        $this->visitedTypes[$class] = $normalized;

        return $normalized;
    }

    private function mergeTypes(MethodContainer $a, MethodContainer $b)
    {
        switch (true) {
            case $a instanceof Clazz && $b instanceof Clazz:
                $this->mergeClasses($a, $b);
                break;

            case $a instanceof Clazz && $b instanceof InterfaceC:
                $this->mergeInterfaceIntoClass($a, $b);
                break;

            case $a instanceof InterfaceC && $b instanceof InterfaceC:
                $this->mergeInterfaces($a, $b);
                break;

            default:
                $this->analyzer->logger->warning(sprintf('Cannot merge %s into %s; probably invalid code.', $b, $a));
        }
    }

    private function mergeInterfaces(InterfaceC $a, InterfaceC $b)
    {
        $this->copyMethods($a, $b);
        $this->copyConstants($a, $b);

        $a->setExtendedInterfaces(array_unique(array_merge(
            $a->getExtendedInterfaces(),
            $b->getExtendedInterfaces())));
    }

    private function mergeClasses(Clazz $a, Clazz $b)
    {
        $this->copyMethods($a, $b);
        $this->copyConstants($a, $b);

        foreach ($b->getProperties() as $classProperty) {
            if ($a->hasProperty($classProperty->getName())) {
                // TODO: Check if $a has a type, and if not copy whatever
                //       type from $b if available
                continue;
            }

            $property = $classProperty->getProperty();

            // We do copy all properties, even private ones. This is done to allow
            // analyzing passes to check access restrictions without traversing
            // the entire inheritance chain.

            $a->addProperty($property, $classProperty->getDeclaringClass());
        }

        $a->setSuperClasses(array_merge(
            array($b->getName()),
            $b->getSuperClasses()
        ));

        $a->setImplementedInterfaces(array_unique(array_merge(
            $a->getImplementedInterfaces(),
            $b->getImplementedInterfaces()
        )));
    }

    private function mergeInterfaceIntoClass(Clazz $a, InterfaceC $b)
    {
        $this->copyConstants($a, $b);
        $this->copyMethods($a, $b);

        $a->setImplementedInterfaces(array_unique(array_merge(
             $a->getImplementedInterfaces(),
             $b->getExtendedInterfaces())));
    }

    private function copyConstants(ConstantContainerInterface $a, ConstantContainerInterface $b)
    {
        foreach ($b->getConstants() as $interfaceConstant) {
            if ($a->hasConstant($interfaceConstant->getName())) {
                continue;
            }

            $constant = $interfaceConstant->getConstant();
            $a->addConstant($constant, $interfaceConstant->getDeclaringClass());
        }
    }

    private function copyMethods(MethodContainer $a, MethodContainer $b)
    {
        // merge in methods from $b into $a
        foreach ($b->getMethods() as $containerMethod) {
            // method already exists on $a
            if ($a->hasMethod($containerMethod->getName())) {
                // TODO: Check types for the method in $a, and if not complete
                //       copy over all available types from $b
                continue;
            }

            $method = $containerMethod->getMethod();

            // We do copy over all methods, even private ones. See above for properties.
            $a->addMethod($method, $containerMethod->getDeclaringClass());
        }
    }
}