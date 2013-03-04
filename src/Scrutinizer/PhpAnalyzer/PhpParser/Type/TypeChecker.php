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
 * Performs type-related checks.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
final class TypeChecker
{
    const LEVEL_STRICT = 'strict';
    const LEVEL_LENIENT = 'lenient';

    private $typeRegistry;
    private $level;

    public function __construct(TypeRegistry $registry, $level = self::LEVEL_LENIENT)
    {
        $this->typeRegistry = $registry;
        $this->level = $level;
    }

    public function setLevel($level)
    {
        $this->level = $level;
    }

    /**
     * Returns whether the given type may be passed for the given expected type.
     *
     * @param PhpType $expectedType
     * @param PhpType $actualType
     *
     * @return boolean
     */
    public function mayBePassed(PhpType $expectedType, PhpType $actualType)
    {
        // This effectively disables all type checks for mock objects.
        // TODO: Remove this once we have support for parameterized types, and
        //       anonymous classes.
        if ((null !== $objType = $actualType->toMaybeObjectType())
                && $objType->getName() === 'PHPUnit_Framework_MockObject_MockObject') {
            return true;
        }
        if ($actualType instanceof ProxyObjectType
                && $actualType->getReferenceName() === 'PHPUnit_Framework_MockObject_MockObject') {
            return true;
        }

        // If the actual type is not yet resolved, then we need to let it go through
        // in favor of avoiding false positives. This indicates a non-existent class,
        // but that is handled in a different pass.
        if ($actualType->isNoResolvedType()) {
            return true;
        }

        if ($expectedType->isCallableType()) {
            return $actualType->canBeCalled();
        }

        // Allow an object that is implementing __toString to be passed where a
        // string is expected. This should work in most cases unless users perform
        // some sort of is_??? check, but then they probably also expect non strings.
        if ($expectedType->isStringType()
                && (null !== $objType = $actualType->toMaybeObjectType())
                && $objType->hasMethod('__toString')) {
            return true;
        }

        if ($actualType->isSubtypeOf($expectedType)) {
            return true;
        }

        // If we are in strict mode, it's already over here.
        if (self::LEVEL_LENIENT !== $this->level) {
            return false;
        }

        // If the actual type is an all-type, we will not make any fuzz in lenient mode. Simply, because there are a lot
        // of cases where "all" should really rather mean "unknown" type.
        if ($actualType->isAllType()) {
            return true;
        }

        switch (true) {
            case $expectedType->isArrayType():
                // If the generic array type is passed in places where a more specific
                // array type is required, we will let this go through in lenient mode.
                if ($actualType === $this->typeRegistry->getNativeType('array')) {
                    return true;
                }

                if ( ! $actualType->isArrayType()) {
                    return false;
                }

                return $this->mayBePassed($expectedType->getElementType(), $actualType->getElementType());

            case $expectedType->isDoubleType():
            case $expectedType->isStringType():
            case $expectedType->isIntegerType():
                $actualType = $actualType->restrictByNotNull();

                return $actualType->isSubTypeOf($this->typeRegistry->createUnionType(array('string', 'integer', 'double')));

            // For unions we let the check pass if the actual type may be passed for any
            // of the union's alternates.
            case $expectedType->isUnionType():
                assert($expectedType instanceof UnionType);
                foreach ($expectedType->getAlternates() as $alt) {
                    if ($this->mayBePassed($alt, $actualType)) {
                        return true;
                    }
                }

                return false;

            default:
                return false;
        }
    }
}