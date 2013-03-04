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

namespace Scrutinizer\Tests\PhpAnalyzer\PhpParser\Scope;

use Scrutinizer\PhpAnalyzer\PhpParser\Scope\Scope;
use Scrutinizer\PhpAnalyzer\PhpParser\Type\TypeRegistry;

class ScopeTest extends \PHPUnit_Framework_TestCase
{
    private $registry;

    public function testDeclareVar()
    {
        $scope = new Scope($this->getMock('PHPParser_Node'));

        $this->assertNull($scope->getVar('x'));

        $scope->declareVar('x', $this->registry->getNativeType('integer'));
        $this->assertNotNull($var = $scope->getVar('x'));
        $this->assertSame($this->registry->getNativeType('integer'), $var->getType());
        $this->assertTrue($var->isTypeInferred());
    }

    protected function setUp()
    {
        $this->registry = new TypeRegistry();
    }
}