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

use JMS\PhpManipulator\TokenStream\AbstractToken;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeTraversal;
use Scrutinizer\PhpAnalyzer\PhpParser\NodeUtil;
use JMS\PhpManipulator\TokenStream\LiteralToken;
use JMS\PhpManipulator\TokenStream\PhpToken;
use Scrutinizer\PhpAnalyzer\Model\ClassMethod;
use Scrutinizer\PhpAnalyzer\Model\Clazz;
use Scrutinizer\PhpAnalyzer\Model\Comment;

/**
 * Style Checks
 *
 * Performs various style related checks which are highly configurable. At the
 * moment mostly naming related checks are implemented (see the reference).
 *
 * @internal
 * Most of these checks are shamelessly taken from the Eclipse Checkstyle plugin.
 *
 * @see http://checkstyle.sourceforge.net/checks.html
 * @category checks
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class CheckstylePass extends SuccessiveTokenAndAstAnalyzer implements ConfigurablePassInterface
{
    use ConfigurableTrait;

    public function __construct()
    {
        parent::__construct();
        $this->stream->setIgnoreWhitespace(false);
    }

    protected function isEnabled()
    {
        return true === $this->getSetting('enabled');
    }

    public function visit(NodeTraversal $t, \PHPParser_Node $node, \PHPParser_Node $parent = null)
    {
        switch (true) {
            case $node instanceof \PHPParser_Node_Expr_Variable:
                if (NodeUtil::isSuperGlobal($node)) {
                    return;
                }

                if (is_string($node->name)) {
                    $this->checkNaming($node->getLine(), '$'.$node->name, $node->name, 'local_variable');
                }
                break;

            case $node instanceof \PHPParser_Node_Stmt_PropertyProperty:
                $this->checkNaming($node->getLine(), '$'.$node->name, $node->name, 'property_name');
                break;

            case $node instanceof \PHPParser_Node_Stmt_Function:
            case $node instanceof \PHPParser_Node_Stmt_ClassMethod:
                $function = $this->typeRegistry->getFunctionByNode($node);

                // If a class method is constrained by a contract of an interface, or an abstract
                // class, then there is no point in adding warnings for them.
                if ($function instanceof ClassMethod && $function->isConstrainedByContract()) {
                    break;
                }

                $this->checkNaming($node->getLine(), 'function '.$node->name.'()', $node->name, 'method_name');

                if (null !== $function && (null !== $returnType = $function->getReturnType()) && $returnType->isBooleanType()) {
                    $this->checkNaming($node->getLine(), 'function '.$node->name.'()', $node->name, 'isser_method_name');
                }

                $this->checkParameters($node->params);
                break;

            case $node instanceof \PHPParser_Node_Expr_Closure:
                $this->checkParameters($node->params);
                break;

            case NodeUtil::isMethodContainer($node):
                $class = $this->typeRegistry->getClassByNode($node)->toMaybeObjectType();

                if ($class) {
                    $this->checkNaming($node->getLine(), $class->getShortName(), $class->getShortName(), 'type_name');

                    if ($class->isInterface()) {
                        $this->checkNaming($node->getLine(), $class->getShortName(), $class->getShortName(), 'interface_name');
                    } elseif ($class instanceof Clazz) {
                        if ($class->isUtilityClass()) {
                            $this->checkNaming($node->getLine(), $class->getShortName(), $class->getShortName(), 'utility_class_name');
                        } elseif ($class->isAbstract()) {
                            $this->checkNaming($node->getLine(), $class->getShortName(), $class->getShortName(), 'abstract_class_name');
                        }

                        // No Else-If here as there might be an abstract exception.
                        // TODO: isSubtype needs to return a TernaryValue so that we
                        //       can decide here what to do in the unknown case.
//                        if ($class->isSubTypeOf($this->typeRegistry->getClassOrCreate('Exception'))) {
//                            $this->checkNaming($node->getLine(), $class->getShortName(), $class->getShortName(), 'exception_name');
//                        }
                    }

                    // The original check only allows final classes to have a private
                    // constructor. We also allow the constructor to be private if it
                    // is declared final, and the class is declared abstract. This is
                    // for example a common practice for utility classes.
                    $method = $class->getMethod('__construct');
                    if (null !== $method && $method->isPrivate() && ! $class->isFinal()
                            && ( ! $method->isFinal() || ! $class->isAbstract()))

                    if (($method = $class->getMethod('__construct'))
                            && $method->isPrivate() && !$class->isFinal()
                            && (!$method->isFinal() || !$class->isAbstract())) {

                        if ($method->isFinal()) {
                            if ($class->isUtilityClass()) {
                                $this->phpFile->addComment($node->getLine(), Comment::warning(
                                    'coding_style.non_abstract_util_class',
                                    'Since you have declared the constructor as final, and this seems like a utility class, maybe you should also declare the class as abstract.'));
                            } else {
                                if (null === $method->getAstNode()) {
                                    break;
                                }

                                $this->phpFile->addComment($method->getAstNode()->getLine(), Comment::warning(
                                    'coding_style.non_util_class_with_final_private_constructor',
                                    'Instead of declaring the constructor as final, maybe you should declare the entire class as final.'));
                            }
                        } elseif ($class->isAbstract()) {
                            if ($class->isUtilityClass()) {
                                $this->phpFile->addComment($method->getAstNode()->getLine(), Comment::warning(
                                    'coding_style.util_class_with_non_final_constructor',
                                    'Since you have declared this class abstract, and the constructor is private, maybe you should also declare the constructor as final.'));
                            } else {
                                $this->phpFile->addComment($method->getAstNode()->getLine(), Comment::warning(
                                    'coding_style.abstract_non_util_class_with_private_constructor',
                                    'Something seems to be off here. Are you sure you want to declare the constructor as private, and the class as abstract?'));
                            }
                        } else {
                            $this->phpFile->addComment($node->getLine(), Comment::warning(
                                'coding_style.non_final_class_with_private_constructor',
                                'Since you have declared the constructor as private, maybe you should also declare the class as final.'));
                        }
                    }
                }

                break;
        }
    }

    protected function analyzeStream()
    {
        while ($this->stream->moveNext()) {
            if ($this->stream->token->isLastTokenOnLine()) {
                if ($this->getSetting('no_trailing_whitespace')
                        && preg_match('/^(?: \t)+(?:\n|$)/', $this->stream->token->getContent())) {
                    $this->phpFile->addComment($this->stream->token->getLine(), Comment::warning(
                        'coding_style.trailing_whitespace',
                        'Could you remove the trailing whitespace on this line?'));
                }
            }

            if ($this->stream->token instanceof LiteralToken
                    || $this->stream->token instanceof \JMS\PhpManipulator\TokenStream\MarkerToken) {
                continue;
            } elseif ($this->stream->token instanceof PhpToken) {
                switch ($this->stream->token->getType()) {
                    case T_IF:
                        $this->stream->skipUntil('(');
                        $startToken = $this->stream->token;
                        $this->stream->skipCurrentBlock();
                        $endToken = $this->stream->token;
                        $this->stream->skipUntil('{');
                        $curlyToken = $this->stream->token;

                        // Deactivated for now. There is a problem that the configuration for
                        // left curly braces cannot be correctly displayed.
                        break;
                        $this->checkLeftCurlyPlacement($startToken, $endToken, $curlyToken, $this->getSetting('left_curly.if'));

                        break;
                }
            } else {
                throw new \RuntimeException('Unknown token.');
            }
        }
    }

    private function checkLeftCurlyPlacement(AbstractToken $startToken, AbstractToken $endToken, AbstractToken $curlyToken, $config)
    {
        if ('new line on wrap' === $config) {
            $config = $startToken->getLine() === $endToken->getLine()
                        ? 'same line' : 'new line';
        }

        switch ($config) {
            case 'same line':
                if ($endToken->getLine() !== $curlyToken->getLine()) {
                    $this->phpFile->addComment($curlyToken->getLine(), Comment::warning(
                        'coding_style.token_should_be_on_same_line',
                        '``%token%`` should probably be on the same line like ``%end_token%``.',
                        array('token' => $curlyToken->getContent(), 'end_token' => $endToken->getContent()))->varyIn(array()));
                }
                break;

            case 'new line':
                if ($endToken->getLine() + 1 !== $curlyToken->getLine()) {
                    $this->phpFile->addComment($curlyToken->getLine(), Comment::warning(
                        'coding_style.token_should_be_on_new_line',
                        '``%token`` should probably be on a new line after ``%end_token%``.',
                        array('token' => $curlyToken->getContent(), 'end_token' => $endToken->getContent()))->varyIn(array()));
                }

                break;

            default:
                throw new \InvalidArgumentException(sprintf('Unknown left curly setting "%s".', $config));
        }
    }

    private function checkParameters(array $params)
    {
        foreach ($params as $param) {
            assert($param instanceof \PHPParser_Node_Param);
            $this->checkNaming($param->getLine(), '$'.$param->name, $param->name, 'parameter_name');
        }
    }

    /**
     * @param integer $line
     * @param string  $type
     */
    private function checkNaming($line, $code, $name, $type)
    {
        if ( ! $this->getSetting('naming.enabled')) {
            return;
        }

        if (!preg_match('#'.str_replace('#', '\\#', $this->getSetting('naming.'.$type)).'#', $name)) {
            $this->phpFile->addComment($line, Comment::warning(
                'coding_style.naming',
                '``%code%`` does not seem to conform to the naming convention (``%regex%``).',
                array('code' => $code, 'regex' => $this->getSetting('naming.'.$type)))->varyIn(array('regex')));
        }
    }
}
