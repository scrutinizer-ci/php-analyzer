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

namespace Scrutinizer\PhpAnalyzer\PhpParser\Type;

/**
 * Type Lexer.
 * 
 * The grammar has become unnecessary complex over time, if we ever should find
 * time, we can try to come up with a new way to define types with more elaborate
 * metadata. However, that would cause a major BC break, and updates in many places.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class TypeLexer extends \JMS\Parser\AbstractLexer
{
    const T_UNKNOWN = 0;
    const T_STRING = 1;
    const T_TYPENAME = 2;
    const T_TRUE = 3;
    const T_FALSE = 4;
    const T_NULL = 5;
    const T_INTEGER = 6;
    const T_TYPE = 7;

    const T_OPEN_BRACKET = 100; // [
    const T_CLOSE_BRACKET = 101; // ]
    const T_OPEN_BRACE = 102; // {
    const T_CLOSE_BRACE = 103; // }
    const T_COMMA = 104;
    const T_ALTERNATE = 105; // |
    const T_COLON = 106;
    const T_OPEN_ANGLE_BRACKET = 107; // <
    const T_CLOSE_ANGLE_BRACKET = 108; // >
    const T_OPEN_PARENTHESIS = 109; // (
    const T_CLOSE_PARENTHESIS = 110; // )

    protected function getRegex()
    {
        return '/
            # JSON strings (\ is not allowed as last character in a string)
            ("(?:[^"]|(?<=\\\\)")*")

            # Integers
            | ([0-9]+)

            # Class Names & Type Names (unfortunately we have no way to distinguish them)
            | ((?:[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\\\\)*[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)

            # Ignore Whitespace
            | \s+

            # Terminals like |[]{},:
            | (.)
            /x';
    }

    protected function determineTypeAndValue($value)
    {
        if ('[' === $value) {
            return array(self::T_OPEN_BRACKET, $value);
        }
        if (']' === $value) {
            return array(self::T_CLOSE_BRACKET, $value);
        }
        if ('{' === $value) {
            return array(self::T_OPEN_BRACE, $value);
        }
        if ('}' === $value) {
            return array(self::T_CLOSE_BRACE, $value);
        }
        if (',' === $value) {
            return array(self::T_COMMA, $value);
        }
        if (':' === $value) {
            return array(self::T_COLON, $value);
        }
        if ('|' === $value) {
            return array(self::T_ALTERNATE, $value);
        }
        if ('<' === $value) {
            return array(self::T_OPEN_ANGLE_BRACKET, $value);
        }
        if ('>' === $value) {
            return array(self::T_CLOSE_ANGLE_BRACKET, $value);
        }
        if ('(' === $value) {
            return array(self::T_OPEN_PARENTHESIS, '(');
        }
        if (')' === $value) {
            return array(self::T_CLOSE_PARENTHESIS, ')');
        }

        if ('"' === $value[0]) {
            return array(self::T_STRING, substr($value, 1, -1));
        }

        if ('false' === $value) {
            return array(self::T_FALSE, $value);
        }
        if ('true' === $value) {
            return array(self::T_TRUE, $value);
        }
        if ('null' === $value) {
            return array(self::T_NULL, $value);
        }
        if ('type' === $value) {
            return array(self::T_TYPE, 'type');
        }

        if (is_numeric($value)) {
            return array(self::T_INTEGER, (integer) $value);
        }

        if (preg_match('/^(?:[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\\\\)*[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*$/', $value)) {
            return array(self::T_TYPENAME, $value);
        }

        return array(self::T_UNKNOWN, $value);
    }
}