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

namespace Scrutinizer\Tests\PhpAnalyzer\PhpParser;

use Scrutinizer\PhpAnalyzer\Util\TestUtils;

class StubsTest extends \PHPUnit_Framework_TestCase
{
    private $em;

    public function testDateIntervalHasProperties()
    {
        foreach (array('y', 'm', 'd', 'h', 'i', 's', 'invert', 'days') as $name) {
            $this->assertClassHasProperty('DateInterval', $name);
        }
    }

    private function assertClassHasProperty($className, $propertyName)
    {
        $class = $this->em->createQuery("SELECT c FROM Scrutinizer\PhpAnalyzer\Model\MethodContainer c WHERE c.name = :name")
                    ->setParameter('name', $className)
                    ->getOneOrNullResult();
        $this->assertNotNull($class, sprintf('The class "%s" did not exist.', $className));
        $this->assertTrue($class->hasProperty($propertyName), sprintf('The class "%s" does not have the property "%s".', $className, $propertyName));
    }

    protected function setUp()
    {
        $this->em = TestUtils::createTestEntityManager();
    }
}