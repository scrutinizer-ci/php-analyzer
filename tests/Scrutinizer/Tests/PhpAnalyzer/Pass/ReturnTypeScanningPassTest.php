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

namespace Scrutinizer\Tests\PhpAnalyzer\Pass;

use Scrutinizer\PhpAnalyzer\Pass\ReturnTypeScanningPass;
use Scrutinizer\PhpAnalyzer\Pass\TypeInferencePass;
use Scrutinizer\PhpAnalyzer\Pass\TypeScanningPass;
use Scrutinizer\PhpAnalyzer\Pass\RepeatedPass;

class ReturnTypeScanningPassTest extends BaseAnalyzingPassTest
{
    public function testSimpleReturnType()
    {
        $this->analyzeAst('ReturnType/simple_return_type.php');

        $function = $this->registry->getFunction('foo');
        $this->assertEquals('string', (string) $function->getReturnType());
    }

    public function testImplicitReturn()
    {
        $this->analyzeAst('ReturnType/implicit_return.php');

        $function = $this->registry->getFunction('foo');
        $this->assertEquals('null', (string) $function->getReturnType());
    }

    public function testCompositeReturn()
    {
        $this->analyzeAst('ReturnType/composite_return_type.php');

        $method = $this->registry->getClass('Foo')->getMethod('foo');
        $this->assertEquals('string|false|null', (string) $method->getReturnType());
    }

    public function testUnknownReturnType()
    {
        $this->analyzeAst('ReturnType/unknown_return_type.php');

        $function = $this->registry->getFunction('foo');
        $this->assertEquals('', (string) $function->getReturnType());
        $this->assertNull($function->getReturnType());
    }

    public function testReturnTypeOfInterfaceMethod()
    {
        $this->analyzeAst('ReturnType/interfaces.php');

        $method = $this->registry->getClass('Test')->getMethod('getFoo');
        $this->assertEquals('', (string) $method->getReturnType());
        $this->assertNull($method->getReturnType());
    }

    public function testReturnTypeOfBuiltinFunction()
    {
        $this->analyzeAst('ReturnType/builtin_function.php');

        $function = $this->registry->getFunction('ignored_return_type');
        $this->assertEquals('', (string) $function->getReturnType());
        $this->assertNull($function->getReturnType());
    }

    public function testReturnTypeOfBuiltinMethod()
    {
        $this->analyzeAst('ReturnType/builtin_method.php');

        $method = $this->registry->getClass('Foo')->getMethod('foo');
        $this->assertEquals('', (string) $method->getReturnType());
        $this->assertNull($method->getReturnType());
    }

    protected function getPasses()
    {
        return array(
            new TypeScanningPass(),
            new RepeatedPass(array(
                new TypeInferencePass(),
                new ReturnTypeScanningPass(),
            )),
        );
    }
}