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

namespace Scrutinizer\PhpAnalyzer\DataFlow\TypeInference;

use Scrutinizer\PhpAnalyzer\PhpParser\Scope\Scope;

/**
 * A cached flow scope, i.e. a "flat" scope.
 *
 * This class allows flow scopes to perform fast lookups which is especially useful if we have a long,
 * and deep flow scope chain as we do not need to traverse through all parent scopes.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class FlatFlowScopeCache
{
    /**
     * Represents either the scope for the method/function, or the global
     * scope.
     *
     * @var Scope
     */
    public $functionScope;

    /**
     * The linked flow scope that this cache represents.
     *
     * @var LinkedFlowScope
     */
    public $linkedEquivalent;

    /**
     * Maps symbol names (properties prefixed with $, variables without prefix)
     * to PhpTypes.
     *
     * @var array<string,PhpType>
     */
    public $symbols = array();

    /**
     * Dirty Symbols are not looked up in the LinkedFlowScope as we assume
     * that they have been redefined in another LinkedFlowScope. Saves us
     * some time.
     *
     * @var array<string>
     */
    public $dirtySymbols = array();

    /**
     * Helper Constructor.
     *
     * This creates the bottom cache from the function scope that we started
     * with. Note that "function" scope also applies to methods and closures,
     * it is the same thing eventually.
     *
     * Keep in mind that Scope significantly depends on the ScopeCreator that
     * was used. We need the TypedScopeCreator, otherwise the analysis will not
     * yield desirable results.
     *
     * @param \Scrutinizer\PhpAnalyzer\PhpParser\Scope\Scope $scope
     *
     * @return FlatFlowScopeCache
     */
    public static function createFromSyntacticScope(Scope $scope)
    {
        $cache = new self();
        $cache->functionScope = $scope;

        // Symbols are left empty intentionally.

        return $cache;
    }

    /**
     * Helper Constructor.
     *
     * Called to cache in the middle of a long scope chain. This allows us to "condense" all the scopes between
     * a given scope and the top scope into a single scope which makes lookups a lot faster as we don't need to
     * traverse up the chain.
     *
     * @param LinkedFlowScope $scope
     * @return FlatFlowScopeCache
     */
    public static function createFromLinkedScope(LinkedFlowScope $directParent)
    {
        $cache = new self();
        $cache->functionScope = $directParent->cache->functionScope;
        $cache->symbols = $directParent->allFlowSlots();
        $cache->linkedEquivalent = $directParent;

        return $cache;
    }

    /**
     * Helper Constructor.
     *
     * This is called when we join two flow scope chains. For example, when we combine the outcomes of the ON_TRUE
     * and the ON_FALSE branch of an IF clause.
     *
     * @param \Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\LinkedFlowScope $joinedScopeA
     * @param \Scrutinizer\PhpAnalyzer\DataFlow\TypeInference\LinkedFlowScope $joinedScopeB
     *
     * @return FlatFlowScopeCache
     */
    public static function createFromLinkedScopes(LinkedFlowScope $joinedScopeA, LinkedFlowScope $joinedScopeB)
    {
        $cache = new self();

        // Always prefer the "real" scope to the faked-out bottom scope.
        $cache->functionScope = $joinedScopeA->flowsFromBottom() ?
            $joinedScopeB->getFunctionScope() : $joinedScopeA->getFunctionScope();

        $slotsA = $cache->symbols = $joinedScopeA->allFlowSlots();
        $slotsB = $joinedScopeB->allFlowSlots();

        // There are 5 different join cases:
        // 1) The type is declared in joinedScopeA, not in joinedScopeB,
        //    and not in functionScope. Just use the one in A.
        // 2) The type is declared in joinedScopeB, not in joinedScopeA,
        //    and not in functionScope. Just use the one in B.
        // 3) The type is declared in functionScope and joinedScopeA, but
        //    not in joinedScopeB. Join the two types.
        // 4) The type is declared in functionScope and joinedScopeB, but
        //    not in joinedScopeA. Join the two types.
        // 5) The type is declared in joinedScopeA and joinedScopeB. Join
        //    the two types.
        $symbolNames = array_unique(array_merge(array_keys($slotsA), array_keys($slotsB)));
        foreach ($symbolNames as $name) {
            $slotA = isset($slotsA[$name]) ? $slotsA[$name] : null;
            $slotB = isset($slotsB[$name]) ? $slotsB[$name] : null;

            $joinedType = null;
            if (null === $slotB || $slotB->getType() === null) {
                $fnSlot = $joinedScopeB->getFunctionScope()->getVar($name);
                $fnSlotType = null === $fnSlot ? null : $fnSlot->getType();

                if (null === $fnSlotType) {
                    // Case #1 -- The symbol was already inserted from A slots.
                } else {
                    // Case #3
                    $joinedType = $slotA->getType()->getLeastSuperType($fnSlotType);
                }
            } else if (null === $slotA || $slotA->getType() === null) {
                $fnSlot = $joinedScopeA->getFunctionScope()->getVar($name);
                $fnSlotType = null === $fnSlot ? null : $fnSlot->getType();

                if (null === $fnSlotType) {
                    // Case #2
                    $cache->symbols[$name] = $slotB;
                } else {
                    // Case #4
                    $joinedType = $slotB->getType()->getLeastSuperType($fnSlotType);
                }
            } else {
                // Case #5
                $joinedType = $slotA->getType()->getLeastSuperType($slotB->getType());
            }

            if (null !== $joinedType) {
                $cache->symbols[$name] = new SimpleSlot($name, $joinedType, true);
            }
        }

        return $cache;
    }

    private final function __construct()
    {
    }

    public function getSlot($name)
    {
        if (isset($this->symbols[$name])) {
            return $this->symbols[$name];
        }

        return $this->functionScope->getVar($name);
    }
}