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

class DocCommentParser
{
    /** @var LoggerInterface */
    private $logger;

    /** @var TypeRegistry */
    private $typeRegistry;

    private $importedNamespaces = array();
    private $currentClassName;
    private $tokens;
    private $pos;
    private $token;
    private $nextToken;
    private $currentComment;

    public function __construct(TypeRegistry $registry)
    {
        $this->typeRegistry = $registry;
    }

    public function setImportedNamespaces(array $namespaces)
    {
        $this->importedNamespaces = $namespaces;
    }

    public function setLogger(LoggerInterface $logger = null)
    {
        $this->logger = $logger;
    }

    /**
     * @param string|null $name
     */
    public function setCurrentClassName($name)
    {
        $this->currentClassName = $name;
    }

    public function getTypeFromMagicPropertyAnnotation(\PHPParser_Node $node, $propertyName)
    {
        if (!preg_match('/@(?:property|property-read|property-write)\s+([^\s]+)\s+\$?'.preg_quote($propertyName, '/').'(?:\s+|$)#m', $node->getDocComment(), $match)) {
            return null;
        }

        $this->currentComment = $node->getDocComment();

        return $this->tryGettingType($match[1]);
    }

    /**
     * Extracts types from an inline "@var" comment.
     *
     * @param string $comment
     *
     * @return array<string,PhpType>
     */
    public function getTypesFromInlineComment($comment)
    {
        $types = array();

        // Bail out fast, if there is no @var in the comment.
        if (false === strpos($comment, '@var')) {
            return $types;
        }

        // @var $name type
        if (preg_match_all('#@var\s+\$([^\s]+)\s+([^\s]+)#', $comment, $matches)) {
            foreach ($matches[1] as $i => $varName) {
                if (null !== $type = $this->tryGettingType($matches[2][$i])) {
                    $types[$varName] = $type;
                }
            }
        }

        // @var type $name
        $matches = array();
        if (preg_match_all('#@var\s+([^\s]+)\s+\$([^\s]+)#', $comment, $matches)) {
            foreach ($matches[2] as $i => $varName) {
                if (null !== $type = $this->tryGettingType($matches[1][$i])) {
                    $types[$varName] = $type;
                }
            }
        }

        return $types;
    }

    public function getTypeFromVarAnnotation(\PHPParser_Node $node)
    {
        if (false === stripos($comment = $node->getDocComment(), '@var')) {
            return null;
        }

        if (!preg_match('/@var\s+([^\s]+)/i', $comment, $match)) {
            return null;
        }

        $this->currentComment = $comment;

        return $this->tryGettingType($match[1]);
    }

    public function tryGettingType($typeName)
    {
        try {
            return $this->getType($typeName);
        } catch (\RuntimeException $ex) {
            if (null !== $this->logger) {
                $this->logger->warning(sprintf('Error while resolving doc type "%s": %s', $typeName, $ex->getMessage()));
            }

            return null;
        }
    }

    public function getTypeFromParamAnnotation(\PHPParser_Node $node, $paramName, $asString = false)
    {
        if ($node instanceof \PHPParser_Node_Param) {
            $node = $node->getAttribute('parent');
        }

        if (false === stripos($comment = $node->getDocComment(), '@param')) {
            return null;
        }

        $this->currentComment = $comment;

        $regex = '/@param\s+(?:([^\s]+)\s+\$'.preg_quote($paramName, '/').'|'.preg_quote($paramName, '/').'\s+([^\s\*][^\s]+))(?:\s|$)/im';
        if (!preg_match($regex, $comment, $match)) {
            return null;
        }

        $this->currentComment = $comment;

        $type = isset($match[2]) ? $match[2] : $match[1];
        if (null === $resolvedType = $this->tryGettingType($type)) {
            return null;
        }

        if ($asString) {
            if ($this->isThisReference(strtolower($type))) {
                return $type;
            }

            // We do return the doc comment with their fully qualified names.
            return $resolvedType->getDocType(array('' => true));
        }

        return $resolvedType;
    }

    public function getTypeFromReturnAnnotation(\PHPParser_Node $node, $asString = false)
    {
        if (false === stripos($comment = $node->getDocComment(), '@return')) {
            return null;
        }

        if (!preg_match('/@return\s+([^\s]+)/i', $comment, $match)) {
            return null;
        }

        $this->currentComment = $comment;

        if (null === $resolvedType = $this->tryGettingType($match[1])) {
            return null;
        }

        if ($asString) {
            if ($this->isThisReference(strtolower($match[1]))) {
                return $match[1];
            }

            // We do return fully qualified names.
            return $resolvedType->getDocType();
        }

        return $resolvedType;
    }

    private function moveNext()
    {
        $this->pos += 1;
        $this->token = isset($this->tokens[$this->pos]) ? $this->tokens[$this->pos] : null;
        $this->nextToken = isset($this->tokens[$this->pos+1]) ? $this->tokens[$this->pos+1] : null;

        return null !== $this->token;
    }

    private function isNextToken($expected)
    {
        if (null === $this->nextToken) {
            return false;
        }

        return $this->nextToken[0] === $expected;
    }

    private function match($token)
    {
        if ( ! $this->isNextToken($token)) {
            throw new \RuntimeException(sprintf('Expected "%s" at position %d, but found "%s".',
                $token, $this->pos + 1, null === $this->nextToken ? 'end of type' : $this->nextToken[0]));
        }

        $this->moveNext();
    }

    public function getType($name)
    {
        $this->tokens = preg_split('#(\(|\)|\||,|\[|\]|<|>|;)#', $name, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY | PREG_SPLIT_OFFSET_CAPTURE);
        $this->pos = -1;
        $this->moveNext();

        $type = $this->parse();

        if (null !== $this->nextToken) {
            throw new \RuntimeException(sprintf('Expected "|" or "end of type", but got "%s" at position %d.', $this->nextToken[0], $this->nextToken[1]));
        }

        return $type;
    }

    private function parse()
    {
        $types = array();
        do {
            $types[] = $this->parseTypeName();
        } while ($this->isNextToken('|') && $this->moveNext() && $this->moveNext());

        return $this->typeRegistry->createUnionType($types);
    }

    private function parseGroup()
    {
        $groupTypes = array();

        $this->moveNext();
        $groupTypes[] = $this->parseTypeName();

        while ($this->isNextToken('|')) {
            $this->match('|');
            $this->moveNext();
            $groupTypes[] = $this->parse();
        }
        $this->match(')');

        return $this->typeRegistry->createUnionType($groupTypes);
    }

    private function parseTypeName()
    {
        $type = null;
        if ('\\' === $this->token[0][0]) {
            $type = $this->typeRegistry->getClassOrCreate(substr($this->token[0], 1));
        } else {
            // First, we check whether the name is a class that we have already scanned
            $parts = explode("\\", $this->token[0]);
            if (isset($this->importedNamespaces[$parts[0]])) {
                $className = $this->importedNamespaces[$parts[0]];
                if (count($parts) > 1) {
                    $className .= '\\'.implode("\\", array_slice($parts, 1));
                }

                $type = $this->typeRegistry->getClassOrCreate($className);
            } else {
                switch (strtolower($this->token[0])) {
                    case '(':
                        $type = $this->parseGroup();
                        break;

                    case 'false':
                        $type = $this->typeRegistry->getNativeType('false');
                        break;

                    case 'bool':
                    case 'boolean':
                        $type = $this->typeRegistry->getNativeType('boolean');
                        break;

                    case 'int':
                    case 'integer':
                        $type = $this->typeRegistry->getNativeType('integer');
                        break;

                    case 'float':
                    case 'double':
                        $type = $this->typeRegistry->getNativeType('double');
                        break;

                    case 'scalar':
                        $type = $this->typeRegistry->getNativeType('scalar');
                        break;

                    case 'string':
                        $type = $this->typeRegistry->getNativeType('string');
                        break;

                    case 'array':
                        if ($this->isNextToken('<')) {
                            $this->match('<');
                            $this->moveNext();

                            $firstType = $this->parse();

                            if ($this->isNextToken(',')) {
                                $this->match(',');
                                $this->moveNext();
                                $arrayType = $this->typeRegistry->getArrayType($this->parse(), $firstType);
                                $this->match('>');

                                return $arrayType;
                            }

                            $this->match('>');

                            return $this->typeRegistry->getArrayType($firstType, $this->typeRegistry->getNativeType('integer'));
                        }

                        $type = $this->typeRegistry->getNativeType('array');
                        break;

                    case 'resource':
                        $type = $this->typeRegistry->getNativeType('resource');
                        break;

                    case '*':
                    case 'mixed':
                        $type = $this->typeRegistry->getNativeType('all');
                        break;

                    case 'number':
                        $type = $this->typeRegistry->getNativeType('number');
                        break;

                    case 'object':
                        $type = $this->typeRegistry->getNativeType('object');
                        break;

                    case 'void':
                    case 'null':
                        $type = $this->typeRegistry->getNativeType('null');
                        break;

                    case 'callable':
                    case 'callback':
                        $type = $this->typeRegistry->getNativeType('callable');
                        break;

                    default:
                        if ($this->isThisReference($this->token[0])) {
                            if (null === $this->currentClassName) {
                                throw new \RuntimeException(sprintf('"%s" is only available from within classes.', $this->token[0]));
                            }

                            $type = $this->typeRegistry->getClassOrCreate($this->currentClassName);
                        } else if (preg_match('/^[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*(?:\\\\[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)*$/', $this->token[0])) {
                            $className = !empty($this->importedNamespaces['']) ? $this->importedNamespaces[''].'\\' : '';
                            $className .= $this->token[0];

                            $type = $this->typeRegistry->getClassOrCreate($className);
                        } else {
                            throw new \RuntimeException(sprintf('Unknown type name "%s" at position %d.', $this->token[0], $this->token[1]));
                        }
                }
            }
        }

        if ( ! $type) {
            throw new \RuntimeException(sprintf('Internal error for token "%s" at position %d.', $this->token[0], $this->token[1]));
        }

        if ($this->isNextToken('[')) {
            $this->moveNext();

            // In the php stubs, there often is an [optional] appended to types.
            // We just ignore this suffix.
            if ($this->isNextToken('optional')) {
                $this->moveNext();
                $this->match(']');
            } else {
                $this->match(']');

                return $this->typeRegistry->getArrayType($type, $this->typeRegistry->getNativeType('integer'));
            }
        }

        return $type;
    }

    private function isThisReference($typeName)
    {
        return 'self' === $typeName || '$this' === $typeName;
    }
}