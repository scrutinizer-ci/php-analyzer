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

use Scrutinizer\PhpAnalyzer\PhpParser\Type\TernaryValue;

/**
 * Special type representing a boolean false.
 *
 * We treat this specially since PHP's internal functions often return something like ``SomeObject|false``. Having a
 * special type for false allows the type inference engine to handle situations such as the following more effectively:
 *
 *     ```
 *     $a = array('string');
 *     while ($elem = array_pop($a)) {
 *         // $elem is a "string" here, and not a "boolean|string".
 *     }
 *     ```
 *
 * @author Johannes M. Schmitt <johannes@scrutinizer-ci.com>
 */
class FalseType extends BooleanType
{
    public function testForEquality(PhpType $that)
    {
        if ($that->isFalse()) {
            return TernaryValue::get('true');
        }

        return parent::testForEquality($that);
    }

    public function getPossibleOutcomesComparedToBoolean()
    {
        return array(false);
    }

    public function isSubTypeOf(PhpType $that)
    {
        if ($that->isBooleanType()) {
            return true;
        }

        return parent::isSubTypeOf($that);
    }

    public function isFalse()
    {
        return true;
    }

    public function getDisplayName()
    {
        return 'false';
    }

    public function equals(PhpType $type)
    {
        return $type instanceof self;
    }
}