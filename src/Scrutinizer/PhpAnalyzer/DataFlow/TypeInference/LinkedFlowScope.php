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

use Scrutinizer\PhpAnalyzer\DataFlow\LatticeElementInterface;
use Scrutinizer\PhpAnalyzer\PhpParser\Scope\Scope;
use Scrutinizer\PhpAnalyzer\PhpParser\Scope\StaticSlotInterface;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\PhpType;

/**
 * LinkFlowScope.
 *
 * Although the properties are public in this class, they should not be accessed
 * from outside of the TypeInference/ sub-namespace.
 */
class LinkedFlowScope implements FlowScopeInterface
{
    const MAX_DEPTH = 300;

    // The closest flow scope cache.
    /** @var FlatFlowScopeCache */
    public $cache;

    // The parent flow scope.
    public $parent;

    // The distance between this flow scope and the closest flat flow scope.
    public $depth;

    // A FlatFlowScopeCache equivalent to this scope.
    public $flattened;

    // Flow scopes assume that all their ancestors are immutable.
    // So once a child scope is created, this flow scope may not be modified.
    public $frozen;

    // The last slot defined in this flow instruction, and the head of the
    // linked list of slots. We use this for example to compare scopes.
    /** @var LinkedFlowSlot */
    public $lastSlot;

    public static function createLatticeElement(Scope $scope)
    {
        return new LinkedFlowScope(FlatFlowScopeCache::createFromSyntacticScope($scope));
    }

    public static function createFromLinkedFlowScope(LinkedFlowScope $directParent)
    {
        return new self($directParent->cache, $directParent);
    }

    public function __construct(FlatFlowScopeCache $cache, LinkedFlowScope $directParent = null)
    {
        $this->cache = $cache;

        if (null === $directParent) {
            $this->lastSlot = null;
            $this->depth = 0;
            $this->parent = $this->cache->linkedEquivalent;
        } else {
            $this->lastSlot = $directParent->lastSlot;
            $this->depth = $directParent->depth + 1;
            $this->parent = $directParent;
        }
    }

    /**
     * Remove flow scopes that add nothing to the flow.
     *
     * This function is not compatible with ``findUniqueRefinedSlot`` because there
     * we assume that this scope is a direct descendant of the blind scope. For
     * optimized scopes, this is not necessarily true anymore though. So, just use
     * this if you are sure that you do not need to use ``findUniqueRefinedSlot``.
     *
     * @return LinkedFlowScope
     */
    public function optimize()
    {
        $current = $this;
        while (null !== $current->parent
                    && $current->lastSlot === $current->parent->lastSlot) {
            $current = $current->parent;
        }

        return $current;
    }

    /**
     * @return LinkedFlowScope
     */
    public function createChildFlowScope()
    {
        $this->frozen = true;

        if ($this->depth > self::MAX_DEPTH) {
            if (null === $this->flattened) {
                $this->flattened = FlatFlowScopeCache::createFromLinkedScope($this);
            }

            return new LinkedFlowScope($this->flattened);
        }

        return LinkedFlowScope::createFromLinkedFlowScope($this);
    }

    public function flowsFromBottom()
    {
        return $this->cache->functionScope->isBottom();
    }

    /**
     * @return Scope
     */
    public function getFunctionScope()
    {
        return $this->cache->functionScope;
    }

    /**
     * Gets all the symbols that have been defined before this point
     * in the current flow. Does not return slots that have not changed during
     * the flow.
     *
     * For example, consider the code:
     *
     * ```php
     *
     *     $x = 3;
     *     function() {
     *         $y = 5;
     *         $y = 6; // FLOW POINT
     *         $z = $y;
     *
     *         return $z;
     *     };
     * ```
     *
     * A FlowScope at FLOW POINT will return a slot for y, but not a slot for x or z.
     */
    public function allFlowSlots()
    {
        $slots = array();

        for ($slot = $this->lastSlot; $slot !== null; $slot = $slot->parent) {
            if (!isset($slots[$slot->getName()])) {
                $slots[$slot->getName()] = $slot;
            }
        }

        foreach ($this->cache->symbols as $name => $slot) {
            if (!isset($slots[$name])) {
                $slots[$name] = $slot;
            }
        }

        return $slots;
    }

    public function inferSlotType($name, PhpType $type)
    {
        assert(!$this->frozen);

        $this->lastSlot = new LinkedFlowSlot($name, $type, $this->lastSlot);
        $this->depth += 1;

        if ( ! in_array($name, $this->cache->dirtySymbols, true)) {
            $this->cache->dirtySymbols[] = $name;
        }
    }

    public function inferQualifiedSlot(\PHPParser_Node $node, $symbol, PhpType $bottomType, PhpType $inferredType)
    {
        $functionScope = $this->getFunctionScope();

        if ($functionScope->isLocal()) {
            if ( ! $functionScope->isDeclared($symbol) && ! $functionScope->isBottom()) {
                $functionScope->declareVar($symbol, $bottomType)->setNameNode($node);
            }

            $this->inferSlotType($symbol, $inferredType);
        }
    }

    public function getTypeOfThis()
    {
        return $this->cache->functionScope->getTypeOfThis();
    }

    public function getRootNode()
    {
        return $this->cache->functionScope->getRootNode();
    }

    public function getParentScope()
    {
        return $this->cache->functionScope->getParentScope();
    }

    /**
     * @param string $name
     *
     * @return SimpleSlot|null
     */
    public function getSlot($name)
    {
        if (in_array($name, $this->cache->dirtySymbols, true)) {
            for ($slot = $this->lastSlot; $slot !== null; $slot = $slot->parent) {
                if ($slot->getName() === $name) {
                    return $slot;
                }
            }
        }

        return $this->cache->getSlot($name);
    }

    public function hasSlot($name)
    {
        return null !== $this->getSlot($name);
    }

    /**
     * Iterate through all the linked flow scopes before this one.
     *
     * If there's one and only one slot defined between this scope and the blind scope, return it.
     *
     * @return StaticSlotInterface|null
     */
    public function findUniqueRefinedSlot(LinkedFlowScope $blindScope)
    {
        $result = null;

        $currentScope = $this;
        while ($currentScope !== $blindScope) {
            $currentSlot = $currentScope->lastSlot;
            while (null !== $currentSlot
                       && (null === $currentScope->parent
                           || $currentScope->parent->lastSlot !== $currentSlot)) {
                if (null === $result) {
                    $result = $currentSlot;
                } else if ($currentSlot->getName() !== $result->getName()) {
                    return null;
                }

                $currentSlot = $currentSlot->parent;
            }

            $currentScope = $currentScope->parent;
        }

        return $result;
    }

    /**
     * Completes the given scope with type information inferred from the local flow where necessary.
     */
    public function completeScope(Scope $scope)
    {
        foreach ($scope->getVars() as $name => $var) {
            if ($var->isTypeInferred()) {
                $type = $var->getType();
                if (null === $type) {
                    $flowType = $this->getSlot($name)->getType();
                    $var->setType($flowType);
                }
            }
        }
    }

    public function equals(LatticeElementInterface $that)
    {
        if (!$that instanceof LinkedFlowScope) {
            return false;
        }

        if ($this->optimize() === $that->optimize()) {
            return true;
        }

        // If two flow scopes are in the same function, then they could have two possible function scopes:
        //   1) the real function scope, or
        //   2) the BOTTOM scope.
        // If they have different function scopes, we theoretically should iterate through all the variable in each
        // scope and compare. However, 99.9% of the time, they're not equal. The other 0.1% of the time, we can just
        // assume that they are equal. Eventually, it just means that data flow analysis has to propagate the entry
        // lattice a bit further than it really needs to, but the end result is not affected.
        if ($this->getFunctionScope() !== $that->getFunctionScope()) {
            return false;
        }

        if ($this->cache === $that->cache) {
            // If the two flow scopes have the same cache, then we can check equality a lot faster: by just looking at
            // the "dirty" elements in the cache, and comparing them in both scopes.
            foreach ($this->cache->dirtySymbols as $name) {
                if ($this->diffSlots($this->getSlot($name), $that->getSlot($name))) {
                    return false;
                }
            }

            return true;
        }

        $myFlowSlots = $this->allFlowSlots();
        $otherFlowSlots = $that->allFlowSlots();

        foreach ($myFlowSlots as $name => $slot) {
            if ($this->diffSlots($slot, isset($otherFlowSlots[$name]) ? $otherFlowSlots[$name] : null)) {
                return false;
            }
            unset($otherFlowSlots[$name]);
        }

        foreach ($otherFlowSlots as $name => $slot) {
            if ($this->diffSlots($slot, isset($myFlowSlots[$name]) ? $myFlowSlots[$name] : null)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Determines whether two slots are considered meaningfully different.
     */
    public function diffSlots(StaticSlotInterface $slotA = null, StaticSlotInterface $slotB = null)
    {
        $aIsNull = null === $slotA || null === $slotA->getType();
        $bIsNull = null === $slotB || null === $slotB->getType();

        if ($aIsNull && $bIsNull) {
            return false;
        }

        if ($aIsNull ^ $bIsNull) {
            return true;
        }

        return $slotA->getType()->differsFrom($slotB->getType());
    }

    public function __toString()
    {
        $repr = 'LinkedFlowScope('.substr(sha1(spl_object_hash($this)), 0, 6).", \n";

        $getDirtySymbolType = function($name) {
            for ($slot = $this->lastSlot; $slot !== null; $slot = $slot->parent) {
                if ($slot->getName() === $name) {
                    return (string) $slot->getType();
                }
            }

            return null;
        };
        $getDirtySymbolType->bindTo($this);

        $dirtySymbols = array();
        foreach ($this->cache->dirtySymbols as $name) {
            $dirtySymbols[$name] = $getDirtySymbolType($name);
        }
        $repr .= "    dirtySlots = ".json_encode($dirtySymbols).",\n";

        $repr .= "    cachedSlots = ".json_encode(array_map(function($v) {
            return (string) $v->getType();
        }, $this->cache->symbols)).",\n";

        $repr .= "    functionSlots = ".json_encode(array_map(function($v) {
            return (string) $v->getType();
        }, $this->cache->functionScope->getVars()))."\n";
        $repr .= ")";

        return $repr;
    }
}