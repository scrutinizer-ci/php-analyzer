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

use PhpOption\Option;

class UnionTypeBuilder
{
    private $registry;
    private $alternates = array();
    private $isAllType = false;
    private $isNativeUnknownType = false;
    private $areAllUnknownsChecked = true;

    public function __construct(TypeRegistry $registry)
    {
        $this->registry = $registry;
    }

    public function getAlternates()
    {
        return $this->alternates;
    }

    public function addAlternate(PhpType $alternate)
    {
        // build() returns the bottom type by default, so we can
        // just bail out early here.
        if ($alternate->isNoType()) {
            return $this;
        }

        $this->isAllType = $this->isAllType || $alternate->isAllType();

        $isAlternateUnknown = $alternate instanceof UnknownType; // instanceof is desired here
        $this->isNativeUnknownType = $this->isNativeUnknownType || $isAlternateUnknown;

        if ($isAlternateUnknown) {
            $this->areAllUnknownsChecked = $this->areAllUnknownsChecked && $alternate->isChecked();
        }

        if (!$this->isAllType && !$this->isNativeUnknownType) {
            if ($alternate->isUnionType()) {
                $union = $alternate->toMaybeUnionType();
                foreach ($union->getAlternates() as $unionAlt) {
                    $this->addAlternate($unionAlt);
                }
            } else {
                // Look through the alternates we've got so far,
                // and check if any of them are duplicates of
                // one another.
                foreach ($this->alternates as $index => $current) {
                    // The Unknown type is special in that we cannot use our
                    // subtype based check, but need to check for equality to
                    // avoid duplicates, and not remove all other alternates.
                    if ($alternate->isUnknownType()) {
                        if ($alternate->equals($current)) {
                            return $this;
                        }

                        continue;
                    }

                    // Check if we already have a more general type in the union.
                    // Then, we do not add this alternate.
                    if ($alternate->isSubTypeOf($current)) {
                        return $this;
                    }

                    // Check if we have a subtype of the passed alternate. Then,
                    // we remove that alternate in favor of the newly passed one.
                    if ($current->isSubTypeOf($alternate)) {
                        unset($this->alternates[$index]);
                    }
                }

                $this->alternates[] = $alternate;
            }
        }

        return $this;
    }

    /**
     * Creates a union.
     *
     * If no type is given, NoType will be returned.
     *
     * @return PhpType
     */
    public function build()
    {
        return $this->reduceAlternatesWithoutUnion()
                    ->getOrElse(new UnionType($this->registry, $this->alternates));
    }

    /**
     * Attempts to reduce alternates into a non-union type.
     *
     * If not possible, it will return null. If there are no alternates at all,
     * a NoType will be returned.
     *
     * @return Option<PhpType>
     */
    private function reduceAlternatesWithoutUnion()
    {
        if ($this->isAllType) {
            return new \PhpOption\Some($this->registry->getNativeType(TypeRegistry::NATIVE_ALL));
        } else if ($this->isNativeUnknownType) {
            if ($this->areAllUnknownsChecked) {
                return new \PhpOption\Some($this->registry->getNativeType(TypeRegistry::NATIVE_UNKNOWN_CHECKED));
            }

            return new \PhpOption\Some($this->registry->getNativeType(TypeRegistry::NATIVE_UNKNOWN));
        } else {
            $size = count($this->alternates);

            if ($size > 1) {
                return \PhpOption\None::create();
            } else if (1 === $size) {
                return new \PhpOption\Some(reset($this->alternates));
            } else {
                return new \PhpOption\Some($this->registry->getNativeType(TypeRegistry::NATIVE_NONE));
            }
        }
    }
}