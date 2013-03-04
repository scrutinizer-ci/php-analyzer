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

class StringTypeTest extends BaseTypeTest
{
    private $string;

    public function testSheq1()
    {
        $types = $this->string->getTypesUnderShallowEquality($this->createNullableType('string'));
        $this->assertTypeEquals('string', $types[0]);
        $this->assertTypeEquals('string', $types[1]);
    }

    public function testSheq2()
    {
        $types = $this->string->getTypesUnderShallowEquality($this->getType('string'));
        $this->assertTypeEquals('string', $types[0]);
        $this->assertTypeEquals('string', $types[1]);
    }

    protected function setUp()
    {
        parent::setUp();

        $this->string = $this->getType('string');
    }
}