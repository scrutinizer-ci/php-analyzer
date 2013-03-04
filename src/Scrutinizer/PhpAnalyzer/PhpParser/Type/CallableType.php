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

class CallableType extends PhpType
{
    public function visit(VisitorInterface $v)
    {
        return $v->visitCallableType($this);
    }

    public function getDisplayName()
    {
        return 'callable';
    }

    public function isCallableType()
    {
        return true;
    }

    public function canBeCalled()
    {
        return true;
    }

    public function testForEquality(PhpType $that)
    {
        if (null !== $rs = parent::testForEquality($that)) {
            return $rs;
        }

        if ($that->isIntegerType() || $that->isDoubleType() || $that->isNullType() || $that->isResourceType()
                || $that->isNullType() || $that->isBooleanType()) {
            return TernaryValue::get('false');
        }

        return TernaryValue::get('unknown');
    }
}