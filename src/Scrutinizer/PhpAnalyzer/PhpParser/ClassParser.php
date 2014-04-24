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

namespace Scrutinizer\PhpAnalyzer\PhpParser;

use Psr\Log\LoggerInterface;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;
use Scrutinizer\PhpAnalyzer\Model\Clazz;
use Scrutinizer\PhpAnalyzer\Model\Constant;
use Scrutinizer\PhpAnalyzer\Model\InterfaceC;
use Scrutinizer\PhpAnalyzer\Model\Method;
use Scrutinizer\PhpAnalyzer\Model\MethodContainer;
use Scrutinizer\PhpAnalyzer\Model\Property;
use Scrutinizer\PhpAnalyzer\Model\TraitC;

class ClassParser
{
    private $paramParser;
    private $typeRegistry;
    private $commentParser;
    private $logger;
    private $importedNamespaces = array();

    public function __construct(TypeRegistry $registry, ParameterParser $paramParser, DocCommentParser $commentParser, LoggerInterface $logger)
    {
        $this->typeRegistry = $registry;
        $this->paramParser = $paramParser;
        $this->commentParser = $commentParser;
        $this->logger = $logger;
    }

    public function setImportedNamespaces(array $namespaces)
    {
        $this->importedNamespaces = $namespaces;
    }

    public function parse(\PHPParser_Node $node)
    {
        if ($node instanceof \PHPParser_Node_Stmt_Class) {
            $class = new Clazz(implode("\\", $node->namespacedName->parts));

            // convert PHPParser modifier to our modifier
            $modifier = 0;
            if (\PHPParser_Node_Stmt_Class::MODIFIER_FINAL
                    === ($node->type & \PHPParser_Node_Stmt_Class::MODIFIER_FINAL)) {
                $modifier |= Clazz::MODIFIER_FINAL;
            }
            if (\PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT
                    === ($node->type & \PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT)) {
                $modifier |= Clazz::MODIFIER_ABSTRACT;
            }
            $class->setModifier($modifier);

            if (null !== $node->extends) {
                $class->setSuperClass(implode("\\", $node->extends->parts));
            }

            foreach ($node->implements as $iface) {
                $class->addImplementedInterface(implode("\\", $iface->parts));
            }
        } else if ($node instanceof \PHPParser_Node_Stmt_Interface) {
            $class = new InterfaceC(implode("\\", $node->namespacedName->parts));

            foreach ($node->extends as $interface) {
                $class->addExtendedInterface(implode("\\", $interface->parts));
            }
        } else if ($node instanceof \PHPParser_Node_Stmt_Trait) {
            $class = new TraitC(implode("\\", $node->namespacedName->parts));
        } else {
            throw new \LogicException(sprintf('The other statements were exhaustive. The node "%s" is not supported.', get_class($node)));
        }

        $class->setImportedNamespaces($this->importedNamespaces);
        $class->setAstNode($node);

        // add methods, properties, and constants
        foreach ($node->stmts as $stmt) {
            if ($stmt instanceof \PHPParser_Node_Stmt_ClassMethod) {
                $this->scanMethod($stmt, $class);
            } else if ($stmt instanceof \PHPParser_Node_Stmt_Property) {
                $visibility = \PHPParser_Node_Stmt_Class::MODIFIER_PUBLIC
                                  === ($stmt->type & \PHPParser_Node_Stmt_Class::MODIFIER_PUBLIC)
                              ? Property::VISIBILITY_PUBLIC : (
                                    \PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED
                                        === ($stmt->type & \PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED)
                                    ? Property::VISIBILITY_PROTECTED : Property::VISIBILITY_PRIVATE);
                foreach ($stmt->props as $propNode) {
                    assert($propNode instanceof \PHPParser_Node_Stmt_PropertyProperty);

                    // This is a PHP error which we flag in a later pass. Here, we can just ignore the property definition.
                    if ($class->isInterface()) {
                        continue;
                    }

                    assert($class instanceof Clazz || $class instanceof TraitC);

                    $property = new Property($propNode->name);
                    $property->setAstNode($propNode);
                    $property->setVisibility($visibility);
                    $class->addProperty($property);
                }
            } else if ($stmt instanceof \PHPParser_Node_Stmt_ClassConst) {
                foreach ($stmt->consts as $constNode) {
                    assert($constNode instanceof \PHPParser_Node_Const);
                    $constant = new Constant($constNode->name);
                    $constant->setAstNode($constNode);

                    if (null !== $type = $constNode->value->getAttribute('type')) {
                        $constant->setPhpType($type);
                    }

                    $class->addConstant($constant);
                }
            }
        }

        // Add magic properties denoted by @property, @property-read, @property-write.
        if ($node instanceof \PHPParser_Node_Stmt_Class
                && preg_match_all('#@(?:property|property-read|property-write)\s+[^\s]+\s+\$?([^\s]+)#', $node->getDocComment(), $matches)) {
            for ($i=0,$c=count($matches[0]); $i<$c; $i++) {
                // If there is already an explicitly declared property of the same
                // name, it has precedence for us.
                if ($class->hasProperty($matches[1][$i])) {
                    $this->logger->warning(sprintf('The property "%s" is already defined in code; ignoring @property tag.', $matches[1][$i]));

                    continue;
                }

                $property = new Property($matches[1][$i]);
                $property->setAstNode($node);
                $class->addProperty($property);
            }
        }

        // Add magic methods denoted by @method.
        if ($node instanceof \PHPParser_Node_Stmt_Class
                && preg_match_all('#@method\s+([^@]+)#s', $node->getDocComment(), $matches)) {
            foreach ($matches[1] as $methodDef) {
                if (null !== $method = $this->parseCommentMethodDef($methodDef)) {
                    // An explicitly defined method has precedence.
                    if ($class->hasMethod($method->getName())) {
                        $this->logger->warning(sprintf('The method "%s" is already defined in code; ignoring @method tag.', $method->getName()));

                        continue;
                    }

                    $class->addMethod($method);
                }
            }
        }

        return $class;
    }

    /**
     * Parses the method definition (@method), and extract
     *
     * This should ideally all be extracted to a dedicated parser component. There is
     * no point in keeping this hidden away in here.
     *
     * @param string $def
     *
     * @return null|Method
     */
    private function parseCommentMethodDef($def)
    {
        // We use some half-assed regular expression, and hope that it will work out.
        // To bad that there is no well defined structure, and also no well written
        // parser :-(
        if ( ! preg_match('/
                # Syntax: [static] [return type] [name]([type] [parameter], [...]) [<description>]
                # Possibly a static comment
                (?:(static)\s+)?

                # Possible Type Name
                (?:([^\s]+)\s+)?

                # Method Name (must be followed by "()")
                ([^\s(]+)

                # Arguments
                \(([^)]*)\)

                /xs', $def, $match)) {
            $this->logger->warning(sprintf('Could not parse @method tag "%s".', $def));

            return null;
        }

        list(, $static, $typeName, $methodName, $arguments) = $match;

        $method = new Method($methodName);
        if ( ! empty($typeName) && null !== $returnType = $this->commentParser->tryGettingType($typeName)) {
            $method->setReturnType($returnType);
        }
        $arguments = trim($arguments);

        if( ! empty($static) ){
            $method->setModifier(Method::MODIFIER_STATIC);
        }

        if (empty($arguments)) {
            return $method;
        }

        foreach (explode(',', $arguments) as $i => $argument) {
            if ( ! preg_match('/(?:([^\s]+)\s+)?\$?([^\s,]+)/', $argument, $match)) {
                $this->logger->warning(sprintf('Could not parse argument "%s" of @method "%s"; skipping all further arguments.', $argument, $method->getName()));

                break;
            }

            $parameter = new \Scrutinizer\PhpAnalyzer\Model\MethodParameter($match[2], $i);
            if ( ! empty($match[1]) && null !== $paramType = $this->commentParser->tryGettingType($match[1])) {
                $parameter->setPhpType($paramType);
            }

            $method->addParameter($parameter);
        }

        return $method;
    }

    private function scanMethod(\PHPParser_Node_Stmt_ClassMethod $stmt, MethodContainer $class)
    {
        $method = new Method($stmt->name);
        $method->setAstNode($stmt);
        $method->setReturnByRef($stmt->byRef);

        // convert PHPParser modifier to our modifier
        $modifier = 0;
        if (\PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT
                === ($stmt->type & \PHPParser_Node_Stmt_Class::MODIFIER_ABSTRACT)) {
            $modifier |= Method::MODIFIER_ABSTRACT;
        }
        if (\PHPParser_Node_Stmt_Class::MODIFIER_FINAL
                === ($stmt->type & \PHPParser_Node_Stmt_Class::MODIFIER_FINAL)) {
            $modifier |= Method::MODIFIER_FINAL;
        }
        if (\PHPParser_Node_Stmt_Class::MODIFIER_STATIC
                === ($stmt->type & \PHPParser_Node_Stmt_Class::MODIFIER_STATIC)) {
            $modifier |= Method::MODIFIER_STATIC;
        }

        // All interface methods are automatically abstract without being declared as such.
        if ($class instanceof InterfaceC) {
            $modifier |= Method::MODIFIER_ABSTRACT;
        }

        $method->setModifier($modifier);

        $method->setVisibility(
            \PHPParser_Node_Stmt_Class::MODIFIER_PRIVATE
                === ($stmt->type & \PHPParser_Node_Stmt_Class::MODIFIER_PRIVATE)
            ? Method::VISIBILITY_PRIVATE : (
                \PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED
                    === ($stmt->type & \PHPParser_Node_Stmt_Class::MODIFIER_PROTECTED)
                    ? Method::VISIBILITY_PROTECTED : Method::VISIBILITY_PUBLIC
            )
        );

        foreach ($this->paramParser->parse($stmt->params, 'Scrutinizer\PhpAnalyzer\Model\MethodParameter') as $param) {
            $method->addParameter($param);
        }

        foreach ($stmt->params as $i => $param) {
            if (null !== $docType = $this->commentParser->getTypeFromParamAnnotation($param, $param->name, true)) {
                $method->setParamDocType($i, $docType);
            }
        }

        if (null !== $returnDocType = $this->commentParser->getTypeFromReturnAnnotation($stmt, true)) {
            $method->setReturnDocType($returnDocType);
        }

        $class->addMethod($method);
    }
}