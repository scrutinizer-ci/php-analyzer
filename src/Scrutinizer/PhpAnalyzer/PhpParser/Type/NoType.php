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
 * None Type.
 *
 * This type is the BOTTOM type in the type hierarchy; it is a sub-type of all types.
 *
 * In PHP Doc this type is never explicitly specified, but we it is required for static analysis. For example,
 * when trying to find the greatest sub-type of two types.
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class NoType extends PhpType
{
    public function visit(VisitorInterface $v)
    {
        return $v->visitNoType();
    }

    public function isNoType()
    {
        return true;
    }

    public function isTraversable()
    {
        return true;
    }

    public function isNullable()
    {
        return true;
    }

    public function isSubTypeOf(PhpType $that)
    {
        return true;
    }

    public function matchesObjectContext()
    {
        return true;
    }

    public function getPossibleOutcomesComparedToBoolean()
    {
        return array();
    }

    public function getDisplayName()
    {
        return 'NoType';
    }
}