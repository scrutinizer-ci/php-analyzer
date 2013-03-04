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
 * All Type.
 *
 * This is the TOP type in the type hierarchy. All other types are sub-types of this type.
 *
 * In PHP Doc, "mixed" is often used to describe this type. We cannot generally make this translation though as it
 * sometimes more meant to say that the type is UNKNOWN instead.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
final class AllType extends PhpType
{
    public function visit(VisitorInterface $visitor)
    {
        return $visitor->visitAllType();
    }

    public function matchesObjectContext()
    {
        return true;
    }

    public function canBeCalled()
    {
        return false;
    }

    public function testForEquality(PhpType $that)
    {
        return TernaryValue::get('unknown');
    }

    public function getDocType(array $importedNamespaces = array())
    {
        return 'mixed';
    }

    public function getDisplayName()
    {
        return '*';
    }

    public function hasDisplayName()
    {
        return true;
    }

    public function getPossibleOutcomesComparedToBoolean()
    {
        return array(true, false);
    }

    public function isAllType()
    {
        return true;
    }
}