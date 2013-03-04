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

class TypeParser extends \JMS\Parser\AbstractParser
{
    private static $typeNameTypes = array(TypeLexer::T_TYPENAME, TypeLexer::T_FALSE, TypeLexer::T_NULL);

    /** @var TypeRegistry */
    private $registry;
    private $lookupMode = TypeRegistry::LOOKUP_BOTH;

    public function __construct(TypeRegistry $registry)
    {
        parent::__construct(new TypeLexer());

        $this->registry = $registry;
    }

    public function parseType($typeInput, $lookupMode)
    {
        $this->lookupMode = $lookupMode;

        return $this->parse($typeInput);
    }

    /**
     * TypeConstruct :== Type ("|" Type)*
     */
    protected function parseInternal()
    {
        $types = array();

        while (true) {
            $types[] = $this->Type();

            if ($this->lexer->isNext(TypeLexer::T_ALTERNATE)) {
                $this->lexer->moveNext();

                continue;
            }

            break;
        }

        return $this->registry->createUnionType($types);
    }

    private function createType($name, array $parameters, array $attributes)
    {
        switch ($name) {
            case 'array':
                switch (count($parameters)) {
                    case 0:
                        return $this->registry->getNativeType('array')->addAttributes($attributes);

                    case 1:
                        return $this->registry->getArrayType($parameters[0], $this->registry->getNativeType('integer'))->addAttributes($attributes);

                    case 2:
                        return $this->registry->getArrayType($parameters[1], $parameters[0])->addAttributes($attributes);

                    case 3:
                        return $this->registry->getArrayType($parameters[1], $parameters[0], $parameters[2])->addAttributes($attributes);

                    default:
                        throw new \RuntimeException(sprintf('Arrays cannot have more than 3 parameters.'));
                }

            case 'object':
                switch (count($parameters)) {
                    case 0:
                        return $this->registry->getNativeType('object')->addAttributes($attributes);

                    case 1:
                        return $this->registry->getClassOrCreate($parameters[0], $this->lookupMode)->addAttributes($attributes);

                    default:
                        throw new \RuntimeException('"object" types expect zero, or one parameter.');
                }

            case 'this':
                if (1 !== count($parameters)) {
                    throw new \RuntimeException('"this" types expect exactly one parameter.');
                }

                $class = $this->registry->getClassOrCreate($parameters[0], $this->lookupMode);

                return (new ThisType($this->registry, $class))->addAttributes($attributes);

            default:
                if ( ! empty($parameters)) {
                    throw new \RuntimeException(sprintf('The type "%s" cannot have any parameters.', $name));
                }

                if (null === $nativeType = $this->registry->getNativeType($name)) {
                    throw new \RuntimeException(sprintf('There is no native type named "%s".', $name));
                }

                return $nativeType->addAttributes($attributes);
        }
    }

    /**
     * Type :== TypeName [ Parameters ] [ Attributes ]
     * Attributes :== JsonObject
     * TypeName :== T_TYPENAME | T_FALSE | T_NULL
     */
    private function Type()
    {
        $typeName = $this->matchAny(self::$typeNameTypes);

        $parameters = array();
        if ($this->lexer->isNext(TypeLexer::T_OPEN_ANGLE_BRACKET)) {
            $parameters = $this->Parameters($typeName);
        }

        $attributes = array();
        if ($this->lexer->isNext(TypeLexer::T_OPEN_BRACE)) {
            $attributes = $this->JsonObject();
        }

        return $this->createType($typeName, $parameters, $attributes);
    }

    /**
     * Parameters :== "<" ParameterValue ("," ParameterValue)* ">"
     *
     * @param string $typeName
     */
    private function Parameters($typeName)
    {
        $parameters = array();

        $this->match(TypeLexer::T_OPEN_ANGLE_BRACKET);
        while (true) {
            $parameters[] = $this->ParameterValue($typeName);

            if ($this->lexer->isNext(TypeLexer::T_COMMA)) {
                $this->lexer->moveNext();

                continue;
            }

            break;
        }
        $this->match(TypeLexer::T_CLOSE_ANGLE_BRACKET);

        return $parameters;
    }

    /**
     * ParameterValue :== ( TypeConstruct | JsonObject )
     *
     * @param string $typeName
     */
    private function ParameterValue($typeName)
    {
        if ($this->lexer->isNext(TypeLexer::T_OPEN_BRACE)) {
            return $this->JsonObject();
        }
        if ($this->lexer->isNextAny(self::$typeNameTypes)) {
            // Unfortunately, the grammar was not really well defined in the first
            // implementation. Now, we need to make this quick fix for object types :(
            // Basically, we have no way to distinguish a class name from a type name
            // without knowing the context in which it is used. For namespaced classes,
            // classes with underscores, or upper case characters, we could, but that
            // would not include all valid PHP names. Thus, making the parser context
            // aware here, seems like the better solution.
            if ('object' === $typeName || 'this' === $typeName) {
                return $this->matchAny(self::$typeNameTypes);
            }

            return $this->parseInternal();
        }

        // This will throw an exception.
        $this->matchAny(array_merge(self::$typeNameTypes, array(TypeLexer::T_OPEN_BRACE)));
    }

    /**
     * JsonObject :== "{" [ JsonKeyValuePairs ] "}"
     * JsonKeyValuePairs :== JsonKey ":" JsonValue ("," JsonKey ":" JsonValue)*
     * JsonKey :== T_STRING | T_INTEGER
     */
    private function JsonObject()
    {
        $this->match(TypeLexer::T_OPEN_BRACE);

        if ($this->lexer->isNext(TypeLexer::T_CLOSE_BRACE)) {
            $this->lexer->moveNext();

            return array();
        }

        $values = array();
        while (true) {
            $key = $this->matchAny(array(TypeLexer::T_STRING, TypeLexer::T_INTEGER));
            $this->match(TypeLexer::T_COLON);
            $values[$key] = $this->JsonValue();

            if ($this->lexer->isNext(TypeLexer::T_COMMA)) {
                $this->lexer->moveNext();

                continue;
            }

            break;
        }
        $this->match(TypeLexer::T_CLOSE_BRACE);

        return $values;
    }

    /**
     * JsonValue :== JsonObject | JsonArray | JsonPrimitive | JsonType
     * JsonType :== T_TYPE "(" Type ")"
     * JsonPrimitive :== T_STRING | T_INTEGER | T_TRUE | T_FALSE | T_NULL
     */
    private function JsonValue()
    {
        if ($this->lexer->isNext(TypeLexer::T_OPEN_BRACE)) {
            return $this->JsonObject();
        }

        if ($this->lexer->isNext(TypeLexer::T_OPEN_BRACKET)) {
            return $this->JsonArray();
        }

        if ($this->lexer->isNext(TypeLexer::T_TRUE)) {
            $this->lexer->moveNext();

            return true;
        }

        if ($this->lexer->isNext(TypeLexer::T_FALSE)) {
            $this->lexer->moveNext();

            return false;
        }

        if ($this->lexer->isNext(TypeLexer::T_NULL)) {
            $this->lexer->moveNext();

            return null;
        }

        if ($this->lexer->isNext(TypeLexer::T_STRING)) {
            return $this->match(TypeLexer::T_STRING);
        }

        if ($this->lexer->isNext(TypeLexer::T_TYPE)) {
            $this->lexer->moveNext();
            $this->match(TypeLexer::T_OPEN_PARENTHESIS);
            $type = $this->parseInternal();
            $this->match(TypeLexer::T_CLOSE_PARENTHESIS);

            return $type;
        }

        // Theoretically, we could just match against an integer here, but in case of
        // an error matching against all the types from above produces a better error
        // message, and the overhead is minimal, so no reason to not do it.
        return $this->matchAny(array(TypeLexer::T_INTEGER, TypeLexer::T_OPEN_BRACE, TypeLexer::T_OPEN_BRACKET,
            TypeLexer::T_TRUE, TypeLexer::T_FALSE, TypeLexer::T_NULL, TypeLexer::T_STRING, TypeLexer::T_TYPENAME));
    }

    /**
     * JsonArray :== "[" [ JsonValue ("," JsonValue)* ] "]"
     */
    private function JsonArray()
    {
        $this->match(TypeLexer::T_OPEN_BRACKET);

        if ($this->lexer->isNext(TypeLexer::T_CLOSE_BRACKET)) {
            $this->lexer->moveNext();

            return array();
        }

        $array = array();
        while (true) {
            $array[] = $this->JsonValue();

            if ($this->lexer->isNext(TypeLexer::T_COMMA)) {
                $this->lexer->moveNext();

                continue;
            }

            break;
        }
        $this->match(TypeLexer::T_CLOSE_BRACKET);

        return $array;
    }
}