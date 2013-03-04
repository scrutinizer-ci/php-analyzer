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

use Scrutinizer\PhpAnalyzer\Model\MethodContainer;

/**
 * Base Type.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
abstract class PhpType implements \JsonSerializable
{
    protected $registry;

    /**
     * @var array<string,*>
     */
    private $attributes;

    public static function getShortestClassNameReference($fqcn, array $importedNamespaces = array())
    {
        $origFqcn = $fqcn;
        $parts = explode("\\", $fqcn);
        $namespace = 1 === count($parts) ? '' : implode("\\", array_slice($parts, 0, -1));
        $shortName = end($parts);

        // Check if the types are in the same namespace.
        if (isset($importedNamespaces['']) && $namespace === $importedNamespaces['']) {
            return $shortName;
        }

        // Go through the namespaces, and check for the longest imported alias.
        $aliasSuffix = '';
        while (false === $alias = array_search($fqcn, $importedNamespaces, true)) {
            if (false === $pos = strrpos($fqcn, '\\')) {
                break;
            }

            $aliasSuffix = substr($fqcn, $pos).$aliasSuffix;
            $fqcn = substr($fqcn, 0, $pos);
        }

        // The absolute alias.
        if (false === $alias) {
            if ( ! isset($importedNamespaces['']) || '' === $importedNamespaces['']) {
                return $origFqcn;
            }

            return '\\'.$origFqcn;
        } else if ('' === $alias) {
            $aliasSuffix = substr($aliasSuffix, 1);
        }

        return $alias.$aliasSuffix;
    }

    public function __construct(TypeRegistry $registry, array $attributes = array())
    {
        $this->registry = $registry;
        $this->attributes = $attributes;
    }

    public function jsonSerialize()
    {
        return $this->getDisplayName();
    }

    public function addAttribute($key, $value)
    {
        if (array_key_exists($key, $this->attributes) && $this->attributes[$key] === $value) {
            return $this;
        }

        $newType = clone $this;
        $newType->attributes[$key] = $value;

        return $newType;
    }

    public function addAttributes(array $kvMap)
    {
        if (empty($kvMap)) {
            return $this;
        }

        foreach ($kvMap as $k => $v) {
            if (array_key_exists($k, $this->attributes) && $this->attributes[$k] === $v) {
                continue;
            }

            $newType = clone $this;
            $newType->attributes = array_merge($newType->attributes, $kvMap);

            return $newType;
        }

        return $this;
    }

    public function removeAttribute($key)
    {
        if ( ! array_key_exists($key, $this->attributes)) {
            return $this;
        }

        $newType = clone $this;
        unset($newType[$key]);

        return $newType;
    }

    /**
     * Removes the given attributes from this type.
     *
     * @param string[] $kList
     *
     * @return $this
     */
    public function removeAttributes($kList)
    {
        if (empty($kList)) {
            return $this;
        }

        foreach ($kList as $k) {
            if ( ! array_key_exists($k, $this->attributes)) {
                continue;
            }

            $newType = clone $this;
            $newType->attributes = array_diff($newType->attributes, $kList);

            return $newType;
        }

        return $this;
    }

    public function getAttributes()
    {
        return $this->attributes;
    }

    public function getAttribute($key)
    {
        if ( ! array_key_exists($key, $this->attributes)) {
            throw new \InvalidArgumentException(sprintf('The attribute "%s" does not exist.', $key));
        }

        return $this->attributes[$key];
    }

    public function getAttributeOrElse($key, $default)
    {
        if ( ! array_key_exists($key, $this->attributes)) {
            return $default;
        }

        return $this->attributes[$key];
    }

    public function hasAttribute($key)
    {
        return array_key_exists($key, $this->attributes);
    }

    public function getNativeType($name)
    {
        return $this->registry->getNativeType($name);
    }

    public function isAllType()
    {
        return false;
    }

    public function isArrayType()
    {
        return false;
    }

    public function isBooleanType()
    {
        return false;
    }

    public function isDoubleType()
    {
        return false;
    }

    public function isIntegerType()
    {
        return false;
    }

    public function isCallableType()
    {
        return false;
    }

    public function isNoType()
    {
        return false;
    }

    public function isNullType()
    {
        return false;
    }

    public function isNoResolvedType()
    {
        return false;
    }

    public function isStringType()
    {
        return false;
    }

    public function isObjectType()
    {
        return $this->toMaybeObjectType() !== null;
    }

    public function isNoObjectType()
    {
        return false;
    }

    public function isUnionType()
    {
        return $this->toMaybeUnionType() !== null;
    }

    public function isUnknownType()
    {
        return false;
    }

    public function isFalse()
    {
        return false;
    }

    public function isResourceType()
    {
        return false;
    }

    public function isInterface()
    {
        return false;
    }

    public function isTrait()
    {
        return false;
    }

    public function isClass()
    {
        return $this->toMaybeObjectType()
                    && !$this->isInterface()
                    && !$this->isTrait();
    }

    public function getDisplayName()
    {
        return null;
    }

    public function hasDisplayName()
    {
        $name = $this->getDisplayName();

        return !empty($name);
    }

    public function matchesObjectContext()
    {
        return false;
    }

    /**
     * Returns whether the given type can be called.
     *
     * This differs from checking whether a type is a subtype of the Callable type insofar as that the check performed
     * here is a bit stronger, f.e. the Object type can verify that a ``__invoke`` method actually exists.
     *
     * @return boolean
     */
    public function canBeCalled()
    {
        return $this->isSubTypeOf($this->registry->getNativeType('callable'));
    }

    public function canAssignTo(PhpType $type)
    {
        return $this->isSubTypeOf($type);
    }

    /**
     * Checks whether this type is a sub-type of the passed type.
     *
     * @param PhpType $that
     *
     * @return boolean
     */
    public function isSubTypeOf(PhpType $that)
    {
        if ($that->isUnknownType()) {
            return true;
        }

        if ($this->equals($that)) {
            return true;
        }

        if ($that->isAllType()) {
            return true;
        }

        if ($that->isUnionType()) {
            $union = $that->toMaybeUnionType();
            foreach ($union->getAlternates() as $element) {
                if ($this->isSubTypeOf($element)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function collapseUnion()
    {
        return $this;
    }

    /**
     * @return UnionType|null
     */
    public function toMaybeUnionType()
    {
        return null;
    }

    /**
     * @return MethodContainer|null
     */
    public function toMaybeObjectType()
    {
        return null;
    }

    /**
     * @return ArrayType|null
     */
    public function toMaybeArrayType()
    {
        return null;
    }

    public function getLeastSuperType(PhpType $that)
    {
        if ($that->isUnionType()) {
            return $that->toMaybeUnionType()->getLeastSuperType($this);
        }

        return $this->equals($that) ? $this : $this->registry->createUnionType(array($this, $that));
    }

    /**
     * Returns the greatest subtype of this, and the passed type.
     *
     * The greatest subtype is also known as the infimum, or meet, or greatest lower bound.
     *
     * Examples:
     *
     *     Integer ^ All        = Integer
     *     Integer ^ Object     = None
     *     Object<Foo> ^ Object = Object<Foo>
     *
     * @param PhpType $that
     * @return PhpType
     */
    public function getGreatestSubtype(PhpType $that)
    {
        if ($this->equals($that)) {
            return $this;
        }

        if ($this->isUnknownType() || $that->isUnknownType()) {
            return $this->registry->getNativeType('unknown');
        }

        if ($this->isSubtypeOf($that)) {
            return $this;
        }

        if ($that->isSubtypeOf($this)) {
            return $that;
        }

        if ($this->isUnionType()) {
            return $this->meet($that);
        }

        if ($that->isUnionType()) {
            return $that->meet($this);
        }

        return $this->registry->getNativeType(TypeRegistry::NATIVE_NONE);
    }

    /**
     * Emulates a loose comparison between two types, and returns the result.
     *
     * The result can be true, false, or unknown:
     *
     *     - true: loose comparison of these types is always true
     *     - false: loose comparison of these types is always false
     *     - unknown: outcome depends on the actual values of these types
     *
     * @see http://php.net/manual/en/types.comparisons.php (table with loose comparison ==)
     *
     * @param PhpType $thisType
     * @param PhpType $thatType
     */
    public function testForEquality(PhpType $that)
    {
        if ($that->isAllType() || $that->isUnknownType() || $that->isNoResolvedType()
                || $this->isAllType() || $this->isUnknownType() || $this->isNoResolvedType()) {
            return TernaryValue::get('unknown');
        }

        if ($this->isNoType() || $that->isNoType()) {
            if ($this->isNoType() && $that->isNoType()) {
                return TernaryValue::get('true');
            }

            return TernaryValue::get('unknown');
        }

        $isThisNumeric = $this->isIntegerType() || $this->isDoubleType();
        $isThatNumeric = $that->isIntegerType() || $that->isDoubleType();

        if ((($isThisNumeric || $this->isStringType()) && $that->isArrayType())
                || (($isThatNumeric || $that->isStringType()) && $this->isArrayType())) {
            return TernaryValue::get('false');
        }

        if ($this->isObjectType() ^ $that->isObjectType()) {
            return TernaryValue::get('false');
        }

        if ($that->isUnionType()) {
            return $that->testForEquality($this);
        }

        if ($this->isArrayType() && $that->isArrayType()) {
            // TODO: Maybe make this more sophisticated by looking at the key,
            //       and element types.
            return TernaryValue::get('unknown');
        }

        // If this is reached, then this base type does not have enough information to
        // make an informed decision, but the method should be overridden by a subtype
        // as this method eventually is never allowed to return null.
        return null;
    }

    public function canTestForEqualityWith(PhpType $that)
    {
        return $this->testForEquality($that) === TernaryValue::get('unknown');
    }

    public function canTestForShallowEqualityWith(PhpType $that)
    {
        return $this->isSubTypeOf($that) || $that->isSubTypeOf($this);
    }

    public function isNullable()
    {
        return $this->isSubTypeOf($this->registry->getNativeType('null'));
    }

    public function isTraversable()
    {
        return false;
    }

    /**
     * Returns the doc type string.
     *
     * @param array $importedNamespaces a map of alias to imported namespace
     *
     * @return string
     */
    public function getDocType(array $importedNamespaces = array())
    {
        return $this->getDisplayName();
    }

    /**
     * Computes the restricted type knowing the outcome of a boolean comparison.
     *
     * @param boolean $outcome
     *
     * @return PhpType the restricted type, or the None Type if the underlying type could
     *         not have yielded this ToBoolean value
     *
     * TODO: This should probably be moved to the SemanticReverseAbstractInterpreter.
     */
    public function getRestrictedTypeGivenToBooleanOutcome($outcome)
    {
        if (in_array($outcome, $this->getPossibleOutcomesComparedToBoolean(), true)) {
            return $this;
        }

        return $this->registry->getNativeType(TypeRegistry::NATIVE_NONE);
    }

    /**
     * The possible outcomes when this type is used in a boolean comparison.
     *
     * @return boolean[]
     */
    public function getPossibleOutcomesComparedToBoolean()
    {
        return array();
    }

    /**
     * Computes the testricted types given a successful equality comparison.
     *
     * @return PhpType[] The first element is the restricted this type, the second element is the restricted type of the
     *                   passed type.
     */
    public function getTypesUnderEquality(PhpType $that)
    {
        // unions types
        if ($that->isUnionType()) {
            $p = $that->toMaybeUnionType()->getTypesUnderEquality($this);

            return array($p[1], $p[0]);
        }

        $rs = $this->testForEquality($that);
        if (null === $rs) {
            throw new \RuntimeException(sprintf('The testForEquality method should have been overridden by either the '.$this.' type, or '.$that.' type.'));
        }

        // other types
        switch ($rs->toBoolean(true)) {
            case false:
                return array(null, null);

            case true:
                return array($this, $that);

            default:
                throw new \RuntimeException('testForEquality() returned an invalid value.');
        }
    }

    /**
     * Computes the restricted types given a non-successful equality comparison.
     *
     * @return PhpType[] The first element is the restricted this type, the second element is the restricted type of the
     *                   passed type.
     */
    public function getTypesUnderInequality(PhpType $that)
    {
        // unions types
        if ($that->isUnionType()) {
            $p = $that->toMaybeUnionType()->getTypesUnderInequality($this);

            return array($p[1], $p[0]);
        }

        $rs = $this->testForEquality($that);
        if (null === $rs) {
            throw new \RuntimeException(sprintf('The testForEquality method should have been overridden by either the '.$this.' type, or '.$that.' type.'));
        }

        switch ($rs->toBoolean(false)) {
            case true:
                return array(null, null);

            case false:
                return array($this, $that);

            default:
                throw new \RuntimeException('testForEquality() returned an invalid value.');
        }
    }

    /**
     * Computes the restricted types given a successful shallow equality comparison.
     *
     * @return PhpType[] The first element is the restricted this type, the second element is the restricted type of the
     *                   passed type.
     */
    public function getTypesUnderShallowEquality(PhpType $that)
    {
        $commonType = $this->getGreatestSubType($that);

        return array($commonType, $commonType);
    }

    /**
     * Computes the restricted types given a non-successful shallow equality comparison.
     *
     * @return PhpType[] The first element is the restricted this type, the second element is the restricted type of the
     *                   passed type.
     */
    public function getTypesUnderShallowInequality(PhpType $that)
    {
        // union types
        if ($that->isUnionType()) {
            $p = $that->toMaybeUnionType()->getTypesUnderShallowInequality($this);

            return array($p[1], $p[0]);
        }

        // Other types.
        // There are only two types whose shallow inequality is deterministically
        // true -- null and false. We can just enumerate them. Should we ever add
        // a true type, this needs to be added here as well.
        if ($this->isNullType() && $that->isNullType()) {
            return array(null, null);
        }

        if ($this->isFalse() && $that->isFalse()) {
            return array(null, null);
        }

        return array($this, $that);
    }

    /**
     * Checks if two types are equivalent.
     *
     * @param PhpType $type
     * @return boolean
     */
    public function equals(PhpType $type)
    {
        return $type instanceof static;
    }

    /**
     * Returns whether the passed type is meaningfully different from this type.
     *
     * In contrast to ``equals()``, this method treats unknown types specially.
     *
     * If no unknown type is involved, then the result of this method is the opposite
     * boolean result of ``equals()``.
     *
     * If unknown types are involved, we regard the types as different if only one
     * of the involved types is an unknown type.
     *
     * TODO: Since the difference between known unknowns (aka checked unknowns),
     *       and unknown unknowns has been removed. This method should be superfluous.
     *
     * @see http://www.youtube.com/watch?v=QaxqUDd4fiw&feature=related
     */
    public function differsFrom(PhpType $that)
    {
        if ( ! $this->isUnknownType() && ! $that->isUnknownType()) {
            return ! $this->equals($that);
        }

        return (boolean) ($this->isUnknownType() ^ $that->isUnknownType());
    }

    public function restrictByNotNull()
    {
        return $this;
    }

    public function __toString()
    {
        return $this->getDisplayName();
    }

    abstract public function visit(VisitorInterface $visitor);
}