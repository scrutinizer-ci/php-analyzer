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

class UnionType extends PhpType
{
    private $alternates;

    public function __construct(TypeRegistry $registry, array $alternates)
    {
        parent::__construct($registry);

        $this->alternates = $alternates;
    }

    public function getDocType(array $importedNamespaces = array())
    {
        $alt = array();
        foreach ($this->getAlternates() as $alternate) {
            $alt[] = $alternate->getDocType($importedNamespaces);
        }

        return implode('|', $alt);
    }

    public function visit(VisitorInterface $v)
    {
        return $v->visitUnionType($this);
    }

    public function getDisplayName()
    {
        $alt = array();
        foreach ($this->alternates as $t) {
            $alt[] = $t->getDisplayName();
        }

        return implode('|', $alt);
    }

    public function __toString()
    {
        return implode('|', array_map('strval', $this->alternates));
    }

    public function getAlternates()
    {
        return $this->alternates;
    }

    public function restrictByNotNull()
    {
        $builder = new UnionTypeBuilder($this->registry);
        foreach ($this->alternates as $t) {
            $builder->addAlternate($t->restrictByNotNull());
        }

        return $builder->build();
    }

    public function isTraversable()
    {
        return $this->forAll(function($alt) { return $alt->isTraversable(); });
    }

    public function matchesObjectContext()
    {
        foreach ($this->alternates as $alternate) {
            if (false === $alternate->matchesObjectContext()) {
                return false;
            }
        }

        return true;
    }

    public function canAssignTo(PhpType $that)
    {
        $canAssign = true;
        foreach ($this->alternates as $alternate) {
            if ($alternate->isUnknownType()) {
                return true;
            }

            $canAssign &= $alternate->canAssignTo($that);
        }

        return (boolean) $canAssign;
    }

    public function canBeCalled()
    {
        foreach ($this->alternates as $t) {
            if (!$t->canBeCalled()) {
                return false;
            }
        }

        return true;
    }

    public function testForEquality(PhpType $that)
    {
        $result = null;
        foreach ($this->alternates as $t) {
            $test = $t->testForEquality($that);
            if (null === $result) {
                $result = $test;
            } else if ($result !== $test) {
                return TernaryValue::get('unknown');
            }
        }

        return $result;
    }

    /**
     * This predicate determines whether objects of this type can have the
     * {@code null} value, and therefore can appear in contexts where
     * {@code null} is expected.
     *
     * @return {@code true} for everything but {@code Number} and
     *         {@code boolean} types.
     */
    public function isNullable()
    {
        foreach ($this->alternates as $t) {
            if ($t->isNullable()) {
                return true;
            }
        }

        return false;
    }

    public function getLeastSupertype(PhpType $that)
    {
        if (!$that->isUnknownType() && !$that->isUnknownType()) {
            foreach ($this->alternates as $alternate) {
                if (!$alternate->isUnknownType() && $that->isSubTypeOf($alternate)) {
                    return $this;
                }
            }
        }

        return $this->equals($that) ? $this : $this->registry->createUnionType(array($this, $that));
    }

    public function meet(PhpType $that)
    {
        $builder = new UnionTypeBuilder($this->registry);
        foreach ($this->alternates as $alternate) {
            if ($alternate->isSubTypeOf($that)) {
                $builder->addAlternate($alternate);
            }
        }

        if ($that->isUnionType()) {
            foreach ($that->toMaybeUnionType()->getAlternates() as $otherAlternate) {
                if ($otherAlternate->isSubTypeOf($this)) {
                    $builder->addAlternate($otherAlternate);
                }
            }
        } else if ($that->isSubTypeOf($this)) {
            $builder->addAlternate($that);
        }

        return $builder->build();
    }

    /**
     * Two union types are equal if they have the same number of alternates
     * and all alternates are equal.
     */
    public function equals(PhpType $that)
    {
        if ($that->isUnionType()) {
            $that = $that->toMaybeUnionType();
            if (count($this->alternates) !== count($that->getAlternates())) {
                return false;
            }

            foreach ($that->getAlternates() as $alternate) {
                if (!$this->contains($alternate)) {
                    return false;
                }
            }

            return true;
        }

        return false;
    }

    public function toMaybeUnionType()
    {
        return $this;
    }

    /**
     * Returns whether this union contains a given type.
     *
     * In contrast to ``isSubtypeOf``, this method does an equality check as such
     * subtypes are not checked here.
     *
     * @param PhpType $type The alternate which might be in this union.
     *
     * @return boolean
     */
    public function contains(PhpType $type) {
        foreach ($this->alternates as $alternate) {
            if ($alternate->equals($type)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns a more restricted union by removing all subtypes of the passed type.
     *
     * @param PhpType $type the supertype of the types to be removed from this union
     *
     * @return PhpType
     */
    public function getRestrictedUnion(PhpType $type)
    {
        $restricted = new UnionTypeBuilder($this->registry);
        foreach ($this->alternates as $t) {
            if ($t->isUnknownType() || !$t->isSubTypeOf($type)) {
                $restricted->addAlternate($t);
            }
        }

        return $restricted->build();
    }

    public function isSubTypeOf(PhpType $that)
    {
        if ($that->isUnknownType()) {
            return true;
        }

        if ($that->isAllType()) {
            return true;
        }

        foreach ($this->alternates as $element) {
            if (!$element->isSubTypeOf($that)) {
                return false;
            }
        }

        return true;
    }

    public function getRestrictedTypeGivenToBooleanOutcome($outcome)
    {
        // gather elements after restriction
        $restricted = new UnionTypeBuilder($this->registry);
        foreach ($this->alternates as $element) {
            $restricted->addAlternate($element->getRestrictedTypeGivenToBooleanOutcome($outcome));
        }

        return $restricted->build();
    }

    public function getPossibleOutcomesComparedToBoolean()
    {
        $outcomes = array();
        foreach ($this->alternates as $element) {
            $outcomes = array_merge($outcomes, $element->getPossibleOutcomesComparedToBoolean());
        }

        return array_unique($outcomes);
    }

    public function getTypesUnderEquality(PhpType $that)
    {
        return $this->getTypesForComparison($that, __FUNCTION__);
    }

    public function getTypesUnderInequality(PhpType $that)
    {
        return $this->getTypesForComparison($that, __FUNCTION__);
    }

    public function getTypesUnderShallowEquality(PhpType $that)
    {
        return $this->getTypesForComparison($that, __FUNCTION__);
    }

    public function getTypesUnderShallowInequality(PhpType $that)
    {
        return $this->getTypesForComparison($that, __FUNCTION__);
    }

    public function collapseUnion()
    {
        $currentValue = null;
        $currentCommonSuper = null;
        foreach ($this->alternates as $a) {
            if ($a->isUnknownType()) {
                return $this->registry->getNativeType(TypeRegistry::NATIVE_UNKNOWN);
            }
        }

        return $currentCommonSuper;
    }

    /**
     * Verifies that a predicate holds for all alternates.
     *
     * @param callable $predicate
     * @return boolean
     */
    private function forAll($predicate)
    {
        foreach ($this->alternates as $alt) {
            if (false === call_user_func($predicate, $alt)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Returns the restricted types given the comparison method.
     *
     * @param PhpType $that
     * @param string $comparisonMethod
     *
     * @return PhpType[]
     */
    private function getTypesForComparison(PhpType $that, $comparisonMethod)
    {
        $thisRestricted = new UnionTypeBuilder($this->registry);
        $thatRestricted = new UnionTypeBuilder($this->registry);

        foreach ($this->alternates as $element) {
            $p = $element->$comparisonMethod($that);

            if (null !== $p[0]) {
                $thisRestricted->addAlternate($p[0]);
            }
            if (null !== $p[1]) {
                $thatRestricted->addAlternate($p[1]);
            }
        }

        return array($thisRestricted->build(), $thatRestricted->build());
    }
}