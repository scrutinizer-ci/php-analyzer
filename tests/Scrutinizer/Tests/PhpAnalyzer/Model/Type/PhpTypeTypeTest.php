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

namespace Scrutinizer\Tests\PhpAnalyzer\Entity\Type;

use Doctrine\DBAL\Types\Type;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\NamedType;
use Scrutinizer\PhpAnalyzer\Model\Clazz;
use Scrutinizer\PhpAnalyzer\Model\Type\PhpTypeType;
use Scrutinizer\Tests\PhpAnalyzer\PhpParser\Type\BaseTypeTest;

class PhpTypeTypeTest extends BaseTypeTest
{
    private $type;
    private $platform;

    /**
     * @dataProvider getToDatabaseValueTests
     */
    public function testConvertToDatabaseValue($type, $expectedValue)
    {
        $this->assertDatabaseValue($type, $expectedValue);
    }

    public function getToDatabaseValueTests()
    {
        $tests = array();
        $tests[] = array('boolean', 'boolean');
        $tests[] = array('all', 'all');
        $tests[] = array('array', 'array');
        $tests[] = array('array<string>', 'array<integer,string>');
        $tests[] = array('array<string,string,{"foo":type(string)}>', 'array<string,string,{"foo":type(string)}>');
        $tests[] = array('string{"not_empty": false}', 'string{"not_empty":false}');
        $tests[] = array('callable', 'callable');
        $tests[] = array('double', 'double');
        $tests[] = array('integer', 'integer');
        $tests[] = array('object', 'object');
        $tests[] = array('none', 'none');
        $tests[] = array('null', 'null');
        $tests[] = array('string', 'string');
        $tests[] = array('unknown', 'unknown');
        $tests[] = array('unknown_checked', 'unknown_checked');
        $tests[] = array('string|integer', 'string|integer');
        $tests[] = array('string|array', 'string|array');
        $tests[] = array('string|array<integer|string,all>', 'string|array<integer|string,all>');

        return $tests;
    }

    /**
     * @dataProvider getToPHPValueTests
     */
    public function testConvertToPHPValue($databaseValue, $expectedPhpType)
    {
        $this->assertPhpType($databaseValue, $expectedPhpType);
    }

    public function getToPHPValueTests()
    {
        $tests = array();
        $tests[] = array('boolean', 'boolean');
        $tests[] = array('string', 'string');
        $tests[] = array('unknown', 'unknown');
        $tests[] = array('string|integer', 'string|integer');
        $tests[] = array('all', 'all');
        $tests[] = array('integer', 'integer');
        $tests[] = array('double', 'double');
        $tests[] = array('object', 'object');
        $tests[] = array('none', 'none');
        $tests[] = array('null', 'null');
        $tests[] = array('array<string|integer,unknown>', 'array');

        return $tests;
    }

    public function testArrayConversion()
    {
        $expected = $this->registry->getArrayType($this->getType('object'), $this->getType('string'));
        $this->assertPhpType('array<string,object>', $expected);
    }

    public function testArrayConversion2()
    {
        $expected = $this->registry->getArrayType($this->resolveType('object|integer'), $this->resolveType('integer|string'));
        $this->assertPhpType('array<integer|string,object|integer>', $expected);
    }

    public function testNull()
    {
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platform));
        $this->assertNull($this->type->convertToPHPValue(null, $this->platform));
        $this->assertNull($this->type->convertToPHPValue('', $this->platform));
    }

    public function testNamedType()
    {
        $this->assertDatabaseValue(new NamedType($this->registry, 'Foo'), 'object<Foo>');
    }

    public function testNamedTypeResolved()
    {
        $this->assertDatabaseValue(NamedType::createResolved($this->registry, new Clazz('Foo')), 'object<Foo>');
    }

    public static function setUpBeforeClass()
    {
        if (false === Type::hasType(PhpTypeType::NAME)) {
            Type::addType(PhpTypeType::NAME, 'Scrutinizer\PhpAnalyzer\Model\Type\PhpTypeType');
        }
    }

    protected function setUp()
    {
        parent::setup();

        $this->type = Type::getType(PhpTypeType::NAME);
        $this->type->setTypeRegistry($this->registry);

        $this->platform = $this->getMockForAbstractClass('Doctrine\DBAL\Platforms\AbstractPlatform');
    }

    private function assertDatabaseValue($type, $databaseValue)
    {
        $type = $this->resolveType($type);
        $this->assertSame($databaseValue, $this->type->convertToDatabaseValue($type, $this->platform));
    }

    private function assertPhpType($databaseValue, $type)
    {
        $type = $this->resolveType($type);
        $this->assertTrue($type->equals($this->type->convertToPhpValue($databaseValue, $this->platform)));
    }
}