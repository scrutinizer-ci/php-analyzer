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

namespace Scrutinizer\PhpAnalyzer\Model\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\TextType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\NoType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\AllType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\ArrayType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\BooleanType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\CallableType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\DoubleType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\FalseType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\IntegerType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\NamedType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\NoObjectType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\NullType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\ObjectType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\PhpType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\ResourceType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\StringType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\ThisType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\UnionType;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\UnknownType;

class PhpTypeType extends TextType
{
    const NAME = 'PhpType';

    /** @var TypeRegistry */
    private static $typeRegistry;

    public static function setTypeRegistry(TypeRegistry $registry)
    {
        self::$typeRegistry = $registry;
    }

    public static function getTypeRegistry()
    {
        if (null === self::$typeRegistry) {
            self::$typeRegistry = new TypeRegistry();
        }

        return self::$typeRegistry;
    }

    /**
     * Converts a value from its PHP representation to its database representation
     * of this type.
     *
     * @param mixed $value The value to convert.
     * @param AbstractPlatform $platform The currently used database platform.
     * @return mixed The database representation of the value.
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        if (null === $value) {
            return null;
        }

        if (null === self::$typeRegistry) {
            self::$typeRegistry = new TypeRegistry();
        }

        if ( ! $value instanceof PhpType) {
            throw ConversionException::conversionFailed($value, 'string');
        }

        return $this->convertToString($value);
    }

    public function convertToString(PhpType $value)
    {
        $typeName = $this->getStringRepr($value);
        $attrs = $value->getAttributes();

        if (empty($attrs)) {
            return $typeName;
        }

        return $typeName.$this->dumpJsonLike($attrs, true);
    }

    /**
     * Converts a value from its database representation to its PHP representation
     * of this type.
     *
     * @param mixed $value The value to convert.
     * @param AbstractPlatform $platform The currently used database platform.
     * @return mixed The PHP representation of the value.
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $value = (is_resource($value)) ? stream_get_contents($value) : $value;
        if (empty($value)) {
            return null;
        }

        if (null === self::$typeRegistry) {
            self::$typeRegistry = new TypeRegistry();
        }

        // We cannot lookup classes in the cache as this would result in another
        // database query during the currently executed query, and consequentially
        // in a FATAL error inside Doctrine's hydrators.
        return self::$typeRegistry->resolveType($value, TypeRegistry::LOOKUP_NO_CACHE);
    }

    // TODO: This should probably be moved to the types themselves.
    private function getStringRepr(PhpType $type)
    {
        switch (true) {
            case $type instanceof AllType:
                return TypeRegistry::NATIVE_ALL;

            // This handles the generic array type specially.
            case $type === self::$typeRegistry->getNativeType('array'):
                return 'array';

            case $type instanceof ArrayType:
                $itemTypes = $type->getItemTypes();
                if (empty($itemTypes)) {
                    return TypeRegistry::NATIVE_ARRAY.'<'.$this->getStringRepr($type->getKeyType()).','.$this->getStringRepr($type->getElementType()).'>';
                }

                return sprintf(
                        'array<%s,%s,%s>',
                        $this->getStringRepr($type->getKeyType()),
                        $this->getStringRepr($type->getElementType()),
                        $this->dumpJsonLike($itemTypes, true));

            case $type instanceof FalseType:
                return TypeRegistry::NATIVE_BOOLEAN_FALSE;

            case $type instanceof BooleanType:
                return TypeRegistry::NATIVE_BOOLEAN;

            case $type instanceof CallableType:
                return TypeRegistry::NATIVE_CALLABLE;

            case $type instanceof ResourceType:
                return TypeRegistry::NATIVE_RESOURCE;

            case $type instanceof DoubleType:
                return TypeRegistry::NATIVE_DOUBLE;

            case $type instanceof IntegerType:
                return TypeRegistry::NATIVE_INTEGER;

            case $type instanceof ThisType:
                return 'this<'.$type->getReferenceName().'>';

            case $type instanceof NamedType:
                // If this type has been resolved, we can get the representation
                // of the resolved type instead of using the reference name.
                if ( ! $type->isNoResolvedType()) {
                    return $this->getStringRepr($type->getReferencedType());
                }

                return 'object<'.$type->getReferenceName().'>';

            case $type instanceof NoObjectType:
                return TypeRegistry::NATIVE_OBJECT;

            case $type instanceof NoType:
                return TypeRegistry::NATIVE_NONE;

            case $type instanceof NullType:
                return TypeRegistry::NATIVE_NULL;

            case $type instanceof ObjectType:
                return 'object<'.$type->getName().'>';

            case $type instanceof StringType:
                return TypeRegistry::NATIVE_STRING;

            case $type instanceof UnionType:
                $alt = array();
                foreach ($type->getAlternates() as $t) {
                    $alt[] = $this->getStringRepr($t);
                }

                return implode('|', $alt);

            case $type instanceof UnknownType:
                return $type->isChecked() ? TypeRegistry::NATIVE_UNKNOWN_CHECKED : TypeRegistry::NATIVE_UNKNOWN;
        }

        throw new \InvalidArgumentException(sprintf('The SWITCH statement is exhaustive, but got "%s".', get_class($type)));
    }

    private function dumpJsonLike(array $data, $isRoot = false)
    {
        $isList = $isRoot ? false : (array_keys($data) === range(0, count($data) - 1));
        $jsonLike = $isList ? '[' : '{';

        $first = true;
        foreach ($data as $k => $v) {
            if ( ! $first) {
                $jsonLike .= ',';
            }
            $first = false;

            if ( ! $isList) {
                $jsonLike .= json_encode($k).':';
            }

            if ($v instanceof PhpType) {
                $jsonLike .= 'type('.$this->getStringRepr($v).')';
            } else if ($v === null || is_scalar($v)) {
                $jsonLike .= json_encode($v);
            } else if (is_array($v)) {
                $jsonLike .= $this->dumpJsonLike($v);
            } else {
                throw new \RuntimeException('Encountered unsupported data of type %s.', gettype($v));
            }
        }
        $jsonLike .= $isList ? ']' : '}';

        return $jsonLike;
    }

    public function getName()
    {
        return self::NAME;
    }
}