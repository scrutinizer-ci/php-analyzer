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

class UnknownType extends PhpType
{
    private $checked;

    /**
     * @param TypeRegistry $registry
     * @param boolean $checked
     */
    public function __construct(TypeRegistry $registry, $checked = false)
    {
        parent::__construct($registry);

        $this->checked = (boolean) $checked;
    }

    public function visit(VisitorInterface $v)
    {
        return $v->visitUnknownType();
    }

    public function isChecked()
    {
        return $this->checked;
    }

    public function isUnknownType()
    {
        return true;
    }

    public function canBeCalled()
    {
        return true;
    }

    public function matchesObjectContext()
    {
        return true;
    }

    public function isTraversable()
    {
        return true;
    }

    public function testForEquality(PhpType $that)
    {
        return TernaryValue::get('unknown');
    }

    public function isNullable()
    {
        return true;
    }

    public function isSubTypeOf(PhpType $that)
    {
        return true;
    }

    public function getDisplayName()
    {
        return '?';
    }

    public function hasDisplayName()
    {
        return true;
    }

    public function equals(PhpType $that)
    {
        if (parent::equals($that)) {
            return true;
        }

        if ($that->isUnknownType()) {
            return true;
        }

        return false;
    }

    public function getPossibleOutcomesComparedToBoolean()
    {
        return array(true, false);
    }
}