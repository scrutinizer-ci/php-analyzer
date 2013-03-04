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

use JMS\PhpManipulator\PhpParser\BlockNode;
use PhpOption\None;
use PhpOption\Some;
use PhpOption\Option;

abstract class NodeUtil
{
    // http://www.php.net/manual/de/language.variables.superglobals.php
    public static $superGlobalNames = array('GLOBALS', '_SERVER', '_GET', '_POST', '_FILES', '_COOKIE', '_SESSION', '_REQUEST', '_ENV');

    /**
     * Returns the value of the node if it is static, and the node has a value.
     *
     * @param \PHPParser_Node $node
     *
     * @return Option<string>
     */
    public static function getValue(\PHPParser_Node $node = null)
    {
        if ($node instanceof \PHPParser_Node_Scalar_String) {
            return new Some($node->value);
        }
        if ($node instanceof \PHPParser_Node_Scalar_LNumber) {
            return new Some($node->value);
        }
        if ($node instanceof \PHPParser_Node_Scalar_DNumber) {
            return new Some($node->value);
        }
        if ($node instanceof \PHPParser_Node_Expr_UnaryMinus) {
            return self::getValue($node->expr)->map(function($v) { return $v * -1; });
        }
        if ($node instanceof \PHPParser_Node_Expr_UnaryPlus) {
            return self::getValue($node->expr);
        }
        if ($node instanceof \PHPParser_Node_Expr_ConstFetch) {
            switch (strtolower(implode("\\", $node->name->parts))) {
                case 'false':
                    return new Some(false);

                case 'true':
                    return new Some(true);

                case 'null':
                    return new Some(null);
            }
        }

        return None::create();
    }

    public static function isSuperGlobal(\PHPParser_Node $node)
    {
        if (!$node instanceof \PHPParser_Node_Expr_Variable
                || !is_string($node->name)) {
            return false;
        }

        return in_array($node->name, self::$superGlobalNames, true);
    }

    public static function isGet(\PHPParser_Node $node)
    {
        return $node instanceof \PHPParser_Node_Expr_PropertyFetch
                    || $node instanceof \PHPParser_Node_Expr_ArrayDimFetch
                    || $node instanceof \PHPParser_Node_Expr_StaticPropertyFetch;
    }

    public static function isCallToMethod(\PHPParser_Node $node, $name)
    {
        if ( ! $node instanceof \PHPParser_Node_Expr_MethodCall) {
            return false;
        }

        if ( ! is_string($node->name)) {
            return false;
        }

        return strtolower($name) === strtolower($node->name);
    }

    /**
     * Returns whether the given node can be associated with a method or function.
     *
     * We also include NEW nodes here which are possibly linked to a constructor if it exists.
     *
     * The check we are performing is a maybe-check in that we cannot guarantee that the method or
     * function actually exists, but only that it could exist.
     *
     * @param \PHPParser_Node $node
     * @return boolean
     */
    public static function isCallLike(\PHPParser_Node $node)
    {
        return $node instanceof \PHPParser_Node_Expr_FuncCall
                    || $node instanceof \PHPParser_Node_Expr_MethodCall
                    || $node instanceof \PHPParser_Node_Expr_StaticCall
                    || $node instanceof \PHPParser_Node_Expr_New;
    }

    public static function isName(\PHPParser_Node $node)
    {
        return $node instanceof \PHPParser_Node_Expr_Variable
                    && is_string($node->name);
    }

    public static function isScopeCreator(\PHPParser_Node $node = null)
    {
        if (null === $node) {
            return false;
        }

        return $node instanceof \PHPParser_Node_Stmt_ClassMethod
                    || $node instanceof \PHPParser_Node_Stmt_Function
                    || $node instanceof \PHPParser_Node_Expr_Closure;
    }

    public static function dump(\PHPParser_Node $node, $level = 0)
    {
        $output = '';
        $writeln = function($line) use ($level, &$output) {
            $output .= str_repeat(' ', $level * 4).$line."\n";
        };

        $writeln(json_encode(get_class($node)).':');

        if (count($attrs = $node->getAttributes()) > 0) {
            self::makeDisplayable($attrs);
            $writeln('    attributes: '.json_encode($attrs));
        }

        $writeln('    children:');
        foreach ($node as $name => $subNode) {
            if ($subNode instanceof \PHPParser_Node) {
                $writeln('        '.json_encode($name).':');
                $output .= self::dump($subNode, $level + 3);
            } else if (is_array($subNode)) {
                $writeln('        '.json_encode($name).': ');
                foreach ($subNode as $k => $aSubNode) {
                    if ($aSubNode instanceof \PHPParser_Node) {
                        $writeln('            '.json_encode($k).':');
                        $output .= self::dump($aSubNode, $level + 4);
                    } else {
                        $writeln('            '.json_encode($k).': '.var_export($aSubNode, true));
                    }
                }
            } else {
                $writeln('        '.json_encode($name).': '.var_export($subNode, true));
            }
        }

        return $output;
    }

    private static function makeDisplayable(array &$attrs)
    {
        unset($attrs['parent'], $attrs['next'], $attrs['previous']);

        foreach ($attrs as $key => &$attr) {
            if (null === $attr || is_scalar($attr)) {
                continue;
            } else if (is_array($attr)) {
                self::makeDisplayable($attr);
            } else if ($attr instanceof Type\PhpType) {
                $attr = (string) $attr;
            } else if (is_object($attr)) {
                $attr = get_class($attr);
            } else {
                $attr = gettype($attr);
            }
        }
    }

    public static function isBoolean(\PHPParser_Node $node = null)
    {
        if (!$node instanceof \PHPParser_Node_Expr_ConstFetch) {
            return false;
        }

        $name = strtolower(implode("\\", $node->name->parts));

        return 'true' === $name || 'false' === $name;
    }

    public static function getBooleanValue(\PHPParser_Node $node)
    {
        if (!$node instanceof \PHPParser_Node_Expr_ConstFetch) {
            throw new \InvalidArgumentException('$node is not a boolean, but '.get_class($node));
        }

        $name = strtolower(implode("\\", $node->name->parts));

        switch ($name) {
            case 'true':
                return true;

            case 'false':
                return false;

            default:
                throw new \InvalidArgumentException('$node is not a boolean, but constant '.$name);
        }
    }

    public static function isAssignmentOp(\PHPParser_Node $node = null)
    {
        if (null === $node) {
            return false;
        }

        return $node instanceof \PHPParser_Node_Expr_Assign
                    || $node instanceof \PHPParser_Node_Expr_AssignBitwiseAnd
                    || $node instanceof \PHPParser_Node_Expr_AssignBitwiseOr
                    || $node instanceof \PHPParser_Node_Expr_AssignBitwiseXor
                    || $node instanceof \PHPParser_Node_Expr_AssignConcat
                    || $node instanceof \PHPParser_Node_Expr_AssignDiv
                    || $node instanceof \PHPParser_Node_Expr_AssignList
                    || $node instanceof \PHPParser_Node_Expr_AssignMinus
                    || $node instanceof \PHPParser_Node_Expr_AssignMod
                    || $node instanceof \PHPParser_Node_Expr_AssignMul
                    || $node instanceof \PHPParser_Node_Expr_AssignPlus
                    || $node instanceof \PHPParser_Node_Expr_AssignRef
                    || $node instanceof \PHPParser_Node_Expr_AssignShiftLeft
                    || $node instanceof \PHPParser_Node_Expr_AssignShiftRight;
    }

    public static function isConstantDefinition(\PHPParser_Node $node)
    {
        if (!$node instanceof \PHPParser_Node_Expr_FuncCall) {
            return false;
        }

        if (!$node->name instanceof \PHPParser_Node_Name) {
            return false;
        }

        $name = implode("\\", $node->name->parts);
        if (strtolower($name) !== 'define') {
            return false;
        }

        return 2 === count($node->args);
    }

    public static function isGetTypeFunctionCall(\PHPParser_Node $node)
    {
        return $node instanceof \PHPParser_Node_Expr_FuncCall
                    && $node->name instanceof \PHPParser_Node_Name
                    && 'gettype' === strtolower(implode("\\", $node->name->parts));
    }

    public static function getFunctionName(\PHPParser_Node $node)
    {
        if ( ! $node instanceof \PHPParser_Node_Expr_FuncCall) {
            return null;
        }

        // This might be a dynamic function call.
        if ( ! $node->name instanceof \PHPParser_Node_Name) {
            return null;
        }

        return strtolower(implode("\\", $node->name->parts));
    }

    public static function isMaybeFunctionCall(\PHPParser_Node $node, $name)
    {
        return $node instanceof \PHPParser_Node_Expr_FuncCall
                   && $node->name instanceof \PHPParser_Node_Name
                   && strtolower(end($node->name->parts)) === strtolower($name);
    }

    public static function isTypeFunctionCall(\PHPParser_Node $node)
    {
        static $functionNames = array('is_array', 'is_bool', 'is_callable',
            'is_double', 'is_float', 'is_real', 'is_int', 'is_integer', 'is_long',
            'is_null', 'is_numeric', 'is_object', 'is_resource', 'is_scalar', 'is_string');

        if (!$node instanceof \PHPParser_Node_Expr_FuncCall) {
            return false;
        }

        if (!$node->name instanceof \PHPParser_Node_Name) {
            return false;
        }

        return in_array(end($node->name->parts), $functionNames, true);
    }

    /**
     * Returns the binary operator for the given node.
     *
     * @param \PHPParser_Node $node
     *
     * @return string
     *
     * @throws \InvalidArgumentException if node is not a binary operator
     */
    public static function getBinOp(\PHPParser_Node $node)
    {
        switch (true) {
            case $node instanceof \PHPParser_Node_Expr_BooleanAnd:
                return '&&';

            case $node instanceof \PHPParser_Node_Expr_BooleanOr:
                return '||';

            case $node instanceof \PHPParser_Node_Expr_LogicalAnd:
                return 'and';

            case $node instanceof \PHPParser_Node_Expr_LogicalOr:
                return 'or';

            case $node instanceof \PHPParser_Node_Expr_LogicalXor;
                return 'xor';

            default:
                throw new \InvalidArgumentException(sprintf('Unknown binary operator: %s', get_class($node)));
        }
    }

    public static function getBitOp(\PHPParser_Node $node)
    {
        switch (true) {
            case $node instanceof \PHPParser_Node_Expr_BitwiseAnd:
                return '&';

            case $node instanceof \PHPParser_Node_Expr_BitwiseOr:
                return '|';

            case $node instanceof \PHPParser_Node_Expr_BitwiseXor:
                return '^';

            case $node instanceof \PHPParser_Node_Expr_BitwiseNot:
                return '~';

            default:
                throw new \InvalidArgumentException(sprintf('Unknown bit operator: %s', get_class($node)));
        }
    }

    public static function getEqualOp(\PHPParser_Node $node)
    {
        switch (true) {
            case $node instanceof \PHPParser_Node_Expr_Equal:
                return '==';

            case $node instanceof \PHPParser_Node_Expr_NotEqual:
                return '!=';

            case $node instanceof \PHPParser_Node_Expr_Identical:
                return '===';

            case $node instanceof \PHPParser_Node_Expr_NotIdentical:
                return '!==';

            default:
                throw new \InvalidArgumentException(sprintf('Unknown equality operator: %s', get_class($node)));
        }
    }

    public static function isEqualityExpression(\PHPParser_Node $node)
    {
        switch (true) {
            case $node instanceof \PHPParser_Node_Expr_Identical:
            case $node instanceof \PHPParser_Node_Expr_NotIdentical:
            case $node instanceof \PHPParser_Node_Expr_Equal:
            case $node instanceof \PHPParser_Node_Expr_NotEqual:
                return true;

            default:
                return false;
        }
    }

    /**
     * Gets the condition of an ON_TRUE / ON_FALSE CFG edge.
     * @param \PHPParser_Node $n a node with an outgoing conditional CFG edge
     * @return \PHPParser_Node the condition node or null if the condition is not obviously a node
     */
    public static function getConditionExpression(\PHPParser_Node $node)
    {
        switch (true) {
            case $node instanceof \PHPParser_Node_Stmt_If:
            case $node instanceof \PHPParser_Node_Stmt_ElseIf:
            case $node instanceof \PHPParser_Node_Stmt_While:
            case $node instanceof \PHPParser_Node_Stmt_Do:
            case $node instanceof \PHPParser_Node_Stmt_Switch:
                return $node->cond;

            case $node instanceof \PHPParser_Node_Stmt_For:
                return empty($node->cond) ? null : end($node->cond); // PHP only takes the last condition into account

            case $node instanceof \PHPParser_Node_Stmt_Catch: // TODO: Re-think how catch should be handled (relevant for TypeInference).
            case $node instanceof \PHPParser_Node_Stmt_Foreach:
            case $node instanceof \PHPParser_Node_Stmt_Case:
                return null;
        }

        throw new \InvalidArgumentException('The node "'.get_class($node).'" does not have a condition.');
    }

    public static function getStringRepr(\PHPParser_Node $node)
    {
        static $prettyPrinter;
        if (null === $prettyPrinter) {
            $prettyPrinter = new \PHPParser_PrettyPrinter_Zend();
        }

        if ($node instanceof \PHPParser_Node_Stmt_If
                || $node instanceof \PHPParser_Node_Stmt_ElseIf) {
            $label = 'if (';
            $label .= $prettyPrinter->prettyPrintExpr($node->cond);
            $label .= ')';

            return $label;
        }

        if ($node instanceof \PHPParser_Node_Stmt_Label) {
            return 'Label '.$node->name;
        }

        if ($node instanceof \PHPParser_Node_Stmt_Echo) {
            return 'echo '.$prettyPrinter->prettyPrint($node->exprs);
        }

        if ($node instanceof \PHPParser_Node_Scalar_String) {
            return 'string('.strlen($node->value).') '.var_export($node->value, true);
        }

        if ($node instanceof \PHPParser_Node_Expr_Variable) {
            if (is_string($node->name)) {
                return '$'.$node->name;
            }

            return 'Variable';
        }

        if ($node instanceof BlockNode) {
            $str = 'Block';

            if ($parent = $node->getAttribute('parent')) {
                $str .= ' of '.self::getStringRepr($parent);
            } else {
                $str .= ' (global)';
            }

            return $str;
        }

        if ($node instanceof \PHPParser_Node_Expr_Assign) {
            return 'Assign (L'.$node->getLine().')';
        }

        if ($node instanceof \PHPParser_Node_Stmt_Catch) {
            return 'catch '.implode("\\", $node->type->parts);
        }

        return get_class($node);
    }

    public static function isMethodContainer(\PHPParser_Node $node)
    {
        return $node instanceof \PHPParser_Node_Stmt_Class
                    || $node instanceof \PHPParser_Node_Stmt_Interface
                    || $node instanceof \PHPParser_Node_Stmt_Trait;
    }

    public static function getContainer(\PHPParser_Node $node)
    {
        $parent = $node->getAttribute('parent');
        while ( ! self::isMethodContainer($parent) && null !== $parent = $parent->getAttribute('parent'));

        return Option::fromValue($parent);
    }

    /**
     * Returns whether the given node can be passed by reference.
     *
     * @param \PHPParser_Node $node
     * @return boolean
     *
     * @see http://php.net/manual/en/language.references.pass.php
     */
    public static function canBePassedByRef(\PHPParser_Node $node)
    {
        if ($node instanceof \PHPParser_Node_Expr_Variable
                || $node instanceof \PHPParser_Node_Expr_PropertyFetch
                || $node instanceof \PHPParser_Node_Expr_StaticPropertyFetch
                || $node instanceof \PHPParser_Node_Expr_ArrayDimFetch
                || $node instanceof \PHPParser_Node_Expr_New) {
            return true;
        }

        // Functions which return references may also be used for parameters
        // that request a reference.
        if ($node instanceof \PHPParser_Node_Expr_FuncCall
                || $node instanceof \PHPParser_Node_Expr_StaticCall
                || $node instanceof \PHPParser_Node_Expr_MethodCall) {
            return $node->getAttribute('returns_by_ref', false);
        }

        return false;
    }

    /**
     * Returns the parent node of the given class, or null if not found.
     *
     * @param \PHPParser_Node $node
     * @param string $parentClass
     *
     * @return \PHPParser_Node|null
     */
    public static function findParent(\PHPParser_Node $node, $parentClass)
    {
        $parent = $node->getAttribute('parent');
        while (null !== $parent) {
            if ($parent instanceof $parentClass) {
                return $parent;
            }

            $parent = $parent->getAttribute('parent');
        }

        return null;
    }

    private final function __construct() { }
}