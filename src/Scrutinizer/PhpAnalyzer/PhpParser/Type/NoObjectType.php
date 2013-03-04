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

class NoObjectType extends ObjectType
{
    public function visit(VisitorInterface $v)
    {
        return $v->visitNoObjectType();
    }

    public function isTraversable()
    {
        return true;
    }

    public function isSubTypeOf(PhpType $that)
    {
        if (parent::isSubTypeOf($that)) {
            return true;
        }

        if ($that->isCallableType()) {
            return true;
        }

        return false;
    }

    public function testForEquality(PhpType $that)
    {
        $rs = parent::testForEquality($that);
        if (null !== $rs) {
            return $rs;
        }

        // Objects are comparable to objects only (by comparing their property
        // values). The no object type represents a generic object.
        if ($that->isObjectType() || $that->isNoObjectType()) {
            return TernaryValue::get('unknown');
        }

        // If the other value is no object, then PHP will raise a warning in
        // most cases, and if not the comparison is false.
        return TernaryValue::get('false');
    }

    public function isNoObjectType()
    {
        return true;
    }

    public function equals(PhpType $type)
    {
        return $type instanceof NoObjectType;
    }

    public function getPropertyType($name)
    {
        return $this->typeRegistry->getNativeType(TypeRegistry::NATIVE_NONE);
    }

    public function hasProperty($name)
    {
        // has all properties since it is any object
        return true;
    }

    public function hasMethod($name)
    {
        return true;
    }

    public function getDisplayName()
    {
        return 'object';
    }
}