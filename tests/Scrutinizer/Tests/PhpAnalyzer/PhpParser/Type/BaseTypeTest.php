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

namespace Scrutinizer\Tests\PhpAnalyzer\PhpParser\Type;

use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;

abstract class BaseTypeTest extends \PHPUnit_Framework_TestCase
{
    protected $provider;
    protected $registry;

    protected function setUp()
    {
        $this->provider = $this->getMock('Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeProviderInterface');
        $this->registry = new TypeRegistry($this->provider);
    }

    protected function assertTypeEquals($thisType, $thatType)
    {
        $thisType = $this->resolveType($thisType);
        $thatType = $this->resolveType($thatType);

        $this->assertTrue($thisType->equals($thatType), 'Expected type "'.$thisType.'", but got type "'.$thatType.'".');
    }

    protected function createUnionType($types)
    {
        if (!is_array($types)) {
            $types = func_get_args();
        }

        return $this->registry->createUnionType($types);
    }

    protected function getNative($type)
    {
        return $this->registry->getNativeType($type);
    }

    protected function createNullableType($type)
    {
        $type = $this->resolveType($type);

        return $this->registry->createNullableType($type);
    }

    protected function resolveType($type)
    {
        return $this->registry->resolveType($type);
    }

    protected function getType($type)
    {
        return $this->registry->getNativeType($type);
    }
}