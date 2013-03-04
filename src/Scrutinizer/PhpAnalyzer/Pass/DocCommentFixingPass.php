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

use Scrutinizer\PhpAnalyzer\PhpParser\Type\PhpType;
use Scrutinizer\PhpAnalyzer\Model\AbstractFunction;
use Scrutinizer\PhpAnalyzer\Model\Clazz;
use Scrutinizer\PhpAnalyzer\Model\MethodContainer;

/**
 * Documentation Type Fixes
 *
 * This pass performs a couple of type related checks around the doc comments,
 * ``@param`` and ``@return``.
 *
 * 1. Correction of Existing Comments
 * ----------------------------------
 * If you already have added doc comments, it will check whether the comment
 * still matches the code, and suggest a fix if not. It will also detect typos
 * in parameter names, and suggest fixes for that.
 *
 * 2. Addition of New Comments
 * ---------------------------
 * In certain cases, it will also suggest to add a doc comment. At the moment,
 * it only suggest to add comments if the type cannot be inferred from the
 * immediate code. Then, it will checks the call-sites to see what types are
 * passed, and suggest this as types::
 *
 *     function foo(A $a, $b) { }
 *     foo(new A(), 'foo');
 *
 * In the example above, it would suggest to add ``@param string $b``, but it would
 * not suggest to add a doc comment for ``$a`` because that is already clear from the
 * type-hint on the parameter itself. The reasoning behind this is that it doesn't
 * want to "clutter" your code with redundant information.
 *
 * @category fixes
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class DocCommentFixingPass extends FixingAnalyzerPass implements ConfigurablePassInterface
{
    use ConfigurableTrait;

    private $commentParser;

    public function setAnalyzer(\Scrutinizer\PhpAnalyzer\Analyzer $analyzer)
    {
        parent::setAnalyzer($analyzer);
        $this->commentParser = new \Scrutinizer\PhpAnalyzer\PhpParser\DocCommentParser($this->registry);
    }

    protected function analyzeStream()
    {
        while ($this->stream->moveNext()) {
            if ($this->stream->token->matches(T_FUNCTION)) {
                // Check if there is a doc comment.
                $maybeDocCommentToken = $this->stream->token->findPreviousToken(T_DOC_COMMENT);
                if ($maybeDocCommentToken->isDefined() && $maybeDocCommentToken->get()->getValue() === $this->stream->node->getDocComment()) {
                    $this->checkDocComment($maybeDocCommentToken->get(), $this->stream->node);
                } else {
                    $this->addDocCommentIfValuable();
                }
            }
        }
    }

    protected function isEnabled()
    {
        return true === $this->getSetting('enabled');
    }

    private function addDocCommentIfValuable()
    {
        // No doc comment was found, let's see whether we need, and actually can
        // create something useful.
        list($function, $importedNamespaces, $class) = $this->getFunctionAndNamespaces($this->stream->node);
        if (null === $function) {
            return;
        }

        $paramAnnotations = array();
        foreach ($this->stream->node->params as $i => $param) {
            $functionParameter = $function->getParameter($i);
            if (null === $functionParameter) {
                if (null === $class) {
                    $this->analyzer->logger->error(sprintf('The function "%s" does not have a parameter with index %d.', $function->getName(), $i));
                } else {
                    $this->analyzer->logger->error(sprintf('The method %s::%s does not have a parameter with index %d.', $class->getName(), $function->getName(), $i));
                }

                continue;
            }

            $paramType = $functionParameter->getPhpType();

            // The ALL type is not ignored here as we have no doc comment, and all
            // parameters are assumed to be of that type if we have nothing more precise.
            if (null !== $paramType && ! $paramType->isUnknownType() && ! $paramType->isAllType()) {
                continue;
            }

            $inferredType = $this->inferTypeForParameter($function, $function->getParameter($i));
            if (null === $inferredType) {
                continue;
            }

            $function->getParameter($i)->setPhpType($inferredType);
            $paramAnnotations[$param->name] = $inferredType->getDocType($importedNamespaces);
        }

        $returnDocType = $this->tryGettingMoreSpecificType(null, $function->getReturnType(), $function, $importedNamespaces, $class, 'Return');
        if ($returnDocType) {
            $function->setReturnType($returnDocType);
        }

        $this->addDocComment($importedNamespaces, $paramAnnotations, $returnDocType);
    }

    private function addDocComment(array $importedNamespaces, array $paramAnnotations, PhpType $returnDocType = null)
    {
        if (! $paramAnnotations && ! $returnDocType) {
            return;
        }

        $startToken = $this->stream->token;
        $this->stream->token->findPreviousToken(function(\JMS\PhpManipulator\TokenStream\AbstractToken $token) use (&$startToken) {
            $maybePreviousToken = $token->getPreviousToken();
            if ($maybePreviousToken->isEmpty()) {
                return true;
            }
            $previousToken = $maybePreviousToken->get();

            if ( ! $previousToken->matches(T_PUBLIC) && ! $previousToken->matches(T_PRIVATE)
                && ! $previousToken->matches(T_PROTECTED) && ! $previousToken->matches(T_FINAL)
                && ! $previousToken->matches(T_STATIC) && ! $previousToken->matches(T_ABSTRACT)
                && ! $previousToken->matches(T_WHITESPACE)) {
                return true;
            }

            if ( ! $token->matches(T_WHITESPACE)) {
                $startToken = $token;
            }

            return false;
        });

        $indentation = '';
        $lineStartToken = $startToken;
        while ( ! $lineStartToken->isFirstTokenOnLine()) {
            $lineStartToken = $lineStartToken->getPreviousToken()->get();

            // If we go over non-whitespace while searching for the beginning of the
            // line, then the code looks weird. We just bail out in that case, and
            // do not add any comment.
            if ( ! $lineStartToken->matches(T_WHITESPACE)) {
                return;
            }

            $indentation .= $lineStartToken->getValue();
        }

        $comment = "/**\n";
        foreach ($paramAnnotations as $paramName => $type) {
            $comment .= $indentation." * @param $type \$$paramName\n";
        }

        if ($returnDocType) {
            if ($paramAnnotations) {
                $comment .= $indentation." *\n";
            }

            $comment .= $indentation." * @return ".$this->getReturnTypeAsString($returnDocType, $importedNamespaces)."\n";
        }

        $comment .= $indentation." */";

        $this->stream->insertTokensBefore($startToken, array(
            array(T_DOC_COMMENT, $comment),
            array(T_WHITESPACE, "\n".$indentation),
        ));
    }

    /**
     * @param boolean $allowNull
     */
    private function refineTypeForAnnotation(PhpType $type, $allowNull = false)
    {
        if ($type->isNullType()) {
            return $allowNull ? $type : null;
        }

        if ($type->isUnknownType() || $type->isNoType() || $type->isAllType()) {
            return null;
        }

        // Just adding "false" does not make much sense as it could simply be omitted,
        // so let's at least widen it a bit and assume boolean.
        if ($type->isFalse()) {
            return $this->registry->getNativeType('boolean');
        }

        return $type;
    }

    private function getFunctionAndNamespaces(\PHPParser_Node $node)
    {
        switch (true) {
            case $node instanceof \PHPParser_Node_Stmt_Function:
                $function = $this->registry->getFunctionByNode($node);
                $importedNamespaces = $function->getImportedNamespaces();

                return array($function, $importedNamespaces, null);

            case $node instanceof \PHPParser_Node_Stmt_ClassMethod:
                $class = $this->registry->getClassByNode($node->getAttribute('parent')->getAttribute('parent'));
                $importedNamespaces = $class->getImportedNamespaces();
                if (null === $classMethod = $class->getMethod($node->name)) {
                    return array(null, array(), null);
                }

                return array($classMethod->getMethod(), $importedNamespaces, $class);

            case $node instanceof \PHPParser_Node_Expr_Closure:
                return array(null, array(), null);

            default:
                throw new \LogicException('The previous cases were exhaustive.');
        }
    }

    private function checkDocComment(\JMS\PhpManipulator\TokenStream\PhpToken $token, \PHPParser_Node $node)
    {
        list($function, $importedNamespaces, $class) = $this->getFunctionAndNamespaces($node);
        if (null === $function) {
            return;
        }

        $comment = $token->getValue();

        $this->removeOrRenameNonExistentParams($node, $comment);

        // Check parameters.
        foreach ($node->params as $i => $param) {
            $docType = $this->commentParser->getTypeFromParamAnnotation($node, $param->name);
            $functionParameter = $function->getParameter($i);
            if (null === $functionParameter) {
                if (null === $class) {
                    $this->analyzer->logger->error(sprintf('The function "%s" does not have a parameter with index %d.', $function->getName(), $i));
                } else {
                    $this->analyzer->logger->error(sprintf('The method %s::%s does not have a parameter with index %d.', $class->getName(), $function->getName(), $i));
                }

                continue;
            }

            $actualType = $functionParameter->getPhpType();
            $actualDocType = $actualType->getDocType($importedNamespaces);

            if ($docType && $docType->getDocType($importedNamespaces) !== $actualDocType
                    && ! $actualType->isUnknownType() && ! $actualType->isNoType()) {
                $comment = preg_replace_callback('/(@param\s+)(?:([^\s]+)(\s+\$'.preg_quote($param->name, '/').')|('.preg_quote($param->name, '/').'\s+)([^\s\*][^\s]+))(\s|$)/im',
                    function($match) use ($actualDocType) {
                        // Form: @param type $paramName
                        if (!empty($match[2])) {
                            $match[2] = $actualDocType;

                            return $match[1].$actualDocType.$match[3].$match[6];
                        }

                        // Form: @param paramName type
                        return $match[1].$match[4].$actualDocType. $match[6];
                    }, $comment);
            } elseif ( ! $docType && ($actualType->isUnknownType() || $actualType->isAllType() || $actualType === $this->registry->getNativeType('array'))) {
                $inferredType = $this->inferTypeForParameter($function, $functionParameter);

                if ($inferredType) {
                    $functionParameter->setPhpType($inferredType);
                    $this->addParamAnnotation($comment, $param->name, $inferredType->getDocType($importedNamespaces));
                }
            }
        }

        $actualReturnType = $function->getReturnType();
        // If the method is defined by an interface, or an abstract method of a parent class,
        // we will assume that to be the actual return type for the purposes of this fixer.
        if ($class instanceof Clazz && null !== $defMethod = $class->getContractDefiningMethod($function->getName())) {
            $actualReturnType = $defMethod->getReturnType();
        }

        // Check return type.
        $moreSpecificReturnType = $this->tryGettingMoreSpecificType(
            $docType = $this->commentParser->getTypeFromReturnAnnotation($node),
            $actualReturnType,
            $function,
            $importedNamespaces,
            $class,
            'Return'
        );
        $this->addOrUpdateReturnType($comment, $moreSpecificReturnType, $function, $importedNamespaces, $class);

        $token->setValue($comment);
    }

    private function getReturnTypeAsString(PhpType $type, array $importedNamespaces)
    {
        if ($type->isNullType() || $type->isNoType()) {
            return 'void';
        }

        return $type->getDocType($importedNamespaces);
    }

    private function addOrUpdateReturnType(&$comment, PhpType $moreSpecificType = null, AbstractFunction $function, array $importedNamespaces, MethodContainer $class = null)
    {
        if (null === $moreSpecificType) {
            return;
        }

        $function->setReturnType($moreSpecificType);
        $typeStr = $this->getReturnTypeAsString($moreSpecificType, $importedNamespaces);

        if (preg_match('/@return\s+[^\s]+/', $comment)) {
            $comment = preg_replace('/(@return\s+)[^\s]+/', '\\1'.$typeStr, $comment);

            return;
        }

        $comment = preg_replace('#^(\s+)\*/#m', "\\1* @return ".$typeStr."\n\\0", $comment);
    }

    /**
     * @param string $type
     *
     * @return null|PhpType
     */
    private function tryGettingMoreSpecificType(PhpType $docType = null, PhpType $actualType,
                                                AbstractFunction $function,
                                                array $importedNamespaces,
                                                MethodContainer $container = null,
                                                $type)
    {
        if (! $docType) {
            if ( ! $actualType->isUnknownType() && ! $actualType->isAllType()) {
                return null;
            }

            return $this->{'infer'.$type.'TypeForFunction'}($function, $container);
        }

        // If the type defined by the comment is an object (and not the NoObjectType),
        // and a super type of the actual type, we keep it.
        if (null !== $docType->toMaybeObjectType() && $actualType->isSubtypeOf($docType)) {
            return $docType;
        }

        // If the type defined by the comment is a nullable object type (excluding the
        // NoObject type), we keep the comment that is currently in place.
        if ($this->isNullableObjectType($docType) && $actualType->isSubtypeOf($docType)) {
            return $docType;
        }

        if ($docType->getDocType($importedNamespaces) !== $actualType->getDocType($importedNamespaces)) {
            return $actualType;
        }

        if ($actualType === $this->registry->getNativeType('array')) {
            $inferredType = $this->{'infer'.$type.'TypeForFunction'}($function, $container);

            if ($inferredType && $inferredType->isArrayType()) {
                return $inferredType;
            }
        }

        return null;
    }

    private function isNullableObjectType(PhpType $type)
    {
        if ( ! $type->isUnionType()) {
            return false;
        }

        foreach ($type->getAlternates() as $alt) {
            if (null === $alt->toMaybeObjectType() && ! $alt->isNullType()) {
                return false;
            }
        }

        return true;
    }

    private function addParamAnnotation(&$comment, $paramName, $docType)
    {
        if (preg_match('#^(.*@param.*?)(\s+\*(?:\s+@|/).*?)$#s', $comment, $match)) {
            $lines = explode("\n", $match[1]);

            if (preg_match('#^[\s\*]+$#', end($lines))) {
                $match[2] = "\n".array_pop($lines).$match[2];
            }

            $indentation = '';
            if (preg_match('#^(\s+)#', end($lines), $wMatch)) {
                $indentation = $wMatch[1];
            }

            $comment = implode("\n", $lines)."\n".$indentation."* @param ".$docType." $".$paramName.$match[2];

            return;
        }

        // We do not take special care of formatting here as we will run a separate pass which
        // is solely dedicated to formatting PHPDoc comments.
        $comment = preg_replace('#^(\s+)\*/$#m', "\\1* @param ".$docType." \$$paramName\n\\0", $comment);
    }

    /**
     * Returns the inferred return type for the given function.
     *
     * If we cannot infer any type NO type is returned, this is also returned if we can only infer
     * ALL type, or UNKNOWN type.
     *
     * We use two characteristics for inferring a return type. First, we are looking at how the
     * return value of the function is used in the places from where it is called. Currently, we
     * are only looking at whether it is passed to other functions/methods and what their expected
     * parameter types are.
     *
     * Second, we also take a look at the exit points of the CFG of the function itself to see
     * whether there are some specific types which we can infer. For example, a function might return
     * NULL type, or ALL type in which case its return type would normally be set to ALL type.
     * However, for our analysis here, we just ignore the ALL type, and add the NULL type to the
     * list of allowed types.
     *
     * @param AbstractFunction $function
     *
     * @return PhpType
     */
    private function inferReturnTypeForFunction(AbstractFunction $function, MethodContainer $container = null)
    {
        $types = array();

        if ($container instanceof \Scrutinizer\PhpAnalyzer\Model\InterfaceC) {
            foreach ($container->getImplementingClasses() as $class) {
                if (null === $implementedFunction = $class->getMethod($function->getName())) {
                    continue;
                }

                $returnType = $implementedFunction->getReturnType();
                if ($returnType->isUnknownType() || $returnType->isAllType()) {
                    continue;
                }

                $types[] = $returnType;
            }
        }

        if (! $types) {
            foreach ($function->getInCallSites() as $site) {
                // We can only analyze call sites which are part of the code which is currently being
                // scanned. Otherwise, we would need to persist the possible types to the database.
                // This could be a future improvement though if we deem it necessary.
                if (null === $node = $site->getAstNode()) {
                    continue;
                }

                if (null === $parent = $node->getAttribute('parent')) {
                    continue;
                }

                switch (true) {
                    case $parent instanceof \PHPParser_Node_Arg:
                        if (null === $callNode = $parent->getAttribute('parent')) {
                            break;
                        }

                        $paramType = $this->getSpecificParamTypeForArg($callNode, $parent);
                        if ($paramType) {
                            $types[] = $paramType;
                        }

                        break;

                    case $parent instanceof \PHPParser_Node_Expr_Assign:
                    case $parent instanceof \PHPParser_Node_Expr_AssignRef:
                        foreach ($parent->var->getAttribute('maybe_using_vars', array()) as $varNode) {
                            if (null === $argNode = $varNode->getAttribute('parent')) {
                                continue;
                            }

                            if (! $argNode instanceof \PHPParser_Node_Arg) {
                                continue;
                            }

                            $callNode = $argNode->getAttribute('parent');
                            $paramType = $this->getSpecificParamTypeForArg($callNode, $argNode);
                            if (null !== $paramType) {
                                $types[] = $paramType;
                            }
                        }

                        break;
                }
            }
        }

        $cfa = new \Scrutinizer\PhpAnalyzer\ControlFlow\ControlFlowAnalysis();
        $cfa->process($function->getAstNode());
        $cfg = $cfa->getGraph();
        foreach ($cfg->getDirectedSuccNodes($cfg->getImplicitReturn()) as $exitGraphNode) {
            $exitNode = $exitGraphNode->getAstNode();

            if (! $exitNode instanceof \PHPParser_Node_Stmt_Return || null === $exitNode->expr) {
                $types[] = $this->registry->getNativeType('null');
                continue;
            }

            $returnType = $exitNode->expr->getAttribute('type');
            if ($returnType && ! $returnType->isUnknownType() && ! $returnType->isAllType()) {
                $types[] = $returnType;
            }
        }

        return $this->refineTypeForAnnotation($this->registry->createUnionType($types), true);
    }

    private function getSpecificParamTypeForArg(\PHPParser_Node $callNode, \PHPParser_Node $argNode)
    {
        if (null === $calledFunction = $this->registry->getCalledFunctionByNode($callNode)) {
            return null;
        }

        $argIndex = array_search($argNode, $callNode->args, true);
        if ( ! $calledFunction->hasParameter($argIndex)) {
            return null;
        }

        $paramType = $calledFunction->getParameter($argIndex)->getPhpType();
        if (null === $paramType || $paramType->isUnknownType() || $paramType->isAllType()) {
            return null;
        }

        return $paramType;
    }

    private function inferTypeForParameter(AbstractFunction $function, \Scrutinizer\PhpAnalyzer\Model\Parameter $param)
    {
        $index   = $param->getIndex();
        $types = array();

        foreach ($function->getInCallSites() as $site) {
            $args = $site->getArgs();

            if (!isset($args[$index])) {
                continue;
            }

            $argType = $args[$index]->getPhpType();
            if ($argType->isUnknownType() || $argType->isNoType()) {
                continue;
            }

            $types[] = $argType;
        }

        if ((null !== $astNode = $param->getAstNode())
                && null !== $astNode->default) {
            $defaultType = $astNode->default->getAttribute('type');

            // The default type might be null for example if a constant is assigned
            // as a default value, and we could not determine the value of that constant.
            if (null !== $defaultType) {
                $types[] = $defaultType;
            }
        }

        return $this->refineTypeForAnnotation($this->registry->createUnionType($types));
    }

    private function removeOrRenameNonExistentParams(\PHPParser_Node $node, &$comment)
    {
        $annotatedParams = array();
        if (preg_match_all('#@param(?: |\t)+[^\s]+(?: |\t)+\$([^\s]+)#', $comment, $matches)) {
            $annotatedParams = $matches[1];
        }

        $paramsWithoutAnnotation = array();
        $paramNames = array();
        foreach ($node->params as $param) {
            $paramNames[] = $param->name;
            if ( ! in_array($param->name, $annotatedParams)) {
                $paramsWithoutAnnotation[] = $param->name;
            }
        }

        $findSimilarName = function($name) use (&$paramsWithoutAnnotation) {
            $similarity = array();
            foreach ($paramsWithoutAnnotation as $paramName) {
                similar_text($name, $paramName, $similarity[$paramName]);
            }

            arsort($similarity);
            list($mostSimilarName, $percentage) = each($similarity);

            return $percentage >= 70 ? $mostSimilarName : null;
        };

        foreach ($annotatedParams as $name) {
            // The annotated parameter actually exists.
            if (in_array($name, $paramNames, true)) {
                continue;
            }

            // The annotated parameter does not exist, but we found a non-annotated parameter which
            // looks very similar to the annotated one (probably just a typo).
            if (null !== $newName = $findSimilarName($name)) {
                unset($paramsWithoutAnnotation[$newName]);
                $comment = preg_replace('#(@param(?: |\t)+[^\s]+(?: |\t)+\$)'.preg_quote($name, '#').'\b#', '\\1'.$newName, $comment);

                continue;
            }

            // We remove the annotation for this parameter as it does not exist (anymore).
            $comment = preg_replace_callback('#^(?: |\t)+\*(?: |\t)+@param(?: |\t)[^\s]+(?: |\t)+\$'.preg_quote($name, '#').'(?:[^@\*]|(?<! \* )@|\*(?!/))*#ms', function($match) {
                $lines = explode("\n", $match[0]);

                // We always append the indentation of the last line, and in addition of any line
                // before that which is empty.
                $rs = '';
                do {
                    if ('' !== $rs) {
                        $rs = "\n".$rs;
                    }

                    $rs = array_pop($lines).$rs;
                } while (preg_match('#^[\* \t]*$#', end($lines)) > 0);

                return $rs;
            }, $comment);
        }
    }
}
