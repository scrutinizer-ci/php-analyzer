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

class StringType extends PhpType
{
    public function visit(VisitorInterface $v)
    {
        return $v->visitStringType();
    }

    public function isStringType()
    {
        return true;
    }

    public function testForEquality(PhpType $that)
    {
        $rs = parent::testForEquality($that);

        if (null !== $rs) {
            return $rs;
        }

        if ($that->isUnknownType()
                || $that->isResourceType()
                || $that->isSubTypeOf($this->registry->getNativeType('object_number_string_boolean'))) {
            return TernaryValue::get('unknown');
        }

        return TernaryValue::get('false');
    }

    public function isSubTypeOf(PhpType $type)
    {
        if (parent::isSubTypeOf($type)) {
            return true;
        }

        if ($type->isCallableType()) {
            return true;
        }

        return false;
    }

    public function getDisplayName()
    {
        return 'string';
    }

    public function getPossibleOutcomesComparedToBoolean()
    {
        return array(true, false);
    }
}