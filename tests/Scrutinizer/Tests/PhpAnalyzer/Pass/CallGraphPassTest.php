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

use Scrutinizer\PhpAnalyzer\Model\AbstractFunction;
use Scrutinizer\PhpAnalyzer\Model\CallGraph\CallSite;
use Scrutinizer\PhpAnalyzer\Model\Clazz;
use Scrutinizer\PhpAnalyzer\Model\GlobalFunction;
use Scrutinizer\PhpAnalyzer\Model\Method;
use Scrutinizer\Tests\PhpAnalyzer\Pass\BaseAnalyzingPassTest;

class CallGraphPassTest extends BaseAnalyzingPassTest
{
    public function testAnalyze()
    {
        $this->analyzeAst('CallGraph/mixed_calls.php');

        $fooFunc = $this->registry->getFunction('foo');
        $barFunc = $this->registry->getFunction('bar');
        $fooMethod = $this->registry->getClass('Foo')->getMethod('foo')->getMethod();
        $barMethod = $this->registry->getClass('Bar')->getMethod('bar')->getMethod();

        $this->assertInCallSites($fooFunc, array($barMethod), array($barFunc));
        $this->assertOutCallSites($fooFunc, array(), array());

        $this->assertInCallSites($barFunc, array(), array());
        $this->assertOutCallSites($barFunc, array($fooMethod), array($fooFunc));

        $this->assertInCallSites($fooMethod, array($barMethod), array($barFunc));
        $this->assertOutCallSites($fooMethod, array(), array());

        $this->assertInCallSites($barMethod, array(), array());
        $this->assertOutCallSites($barMethod, array($fooMethod), array($fooFunc));
    }

    public function testCachedCallSitesAreRemoved()
    {
        $cachedFooFunc = new GlobalFunction('foo');
        $cachedBarFunc = new GlobalFunction('bar');
        $cachedBarClass = new Clazz('Bar');
        $cachedBarClass->addMethod($cachedBarMethod = new Method('bar'));
        $cachedFooClass = new Clazz('Foo');
        $cachedFooClass->addMethod($cachedFooMethod = new Method('foo'));

        CallSite::create($cachedFooFunc, $cachedBarFunc);
        CallSite::create($cachedFooMethod, $cachedBarFunc);
        CallSite::create($cachedFooMethod, $cachedBarMethod);
        CallSite::create($cachedBarFunc, $cachedFooFunc);
        CallSite::create($cachedBarMethod, $cachedFooFunc);

        $this->provider->addFunction($cachedFooFunc);
        $this->provider->addFunction($cachedBarFunc);
        $this->provider->addClass($cachedBarClass);
        $this->provider->addClass($cachedFooClass);

        $this->analyzeAst('CallGraph/cache.php');

        $fooFunc = $this->registry->getFunction('foo');
        $fooMethod = $this->registry->getClass('Foo')->getMethod('foo')->getMethod();

        $this->assertInCallSites($fooFunc, array($cachedBarMethod), array($cachedBarFunc));
        $this->assertOutCallSites($fooFunc, array(), array());

        $this->assertInCallSites($fooMethod, array(), array());
        $this->assertOutCallSites($fooMethod, array($cachedBarMethod), array($cachedBarFunc));

        $this->assertInCallSites($cachedBarMethod, array($fooMethod), array());
        $this->assertOutCallSites($cachedBarMethod, array(), array($fooFunc));

        $this->assertInCallSites($cachedBarFunc, array($fooMethod), array());
        $this->assertOutCallSites($cachedBarFunc, array(), array($fooFunc));

        $this->assertInCallSites($cachedFooFunc, array($cachedBarMethod), array($cachedBarFunc));
        $this->assertOutCallSites($cachedFooFunc, array(), array($cachedBarFunc));

        $this->assertInCallSites($cachedFooMethod, array(), array());
        $this->assertOutCallSites($cachedFooMethod, array($cachedBarMethod), array($cachedBarFunc));
    }

    public function testClassDefinedMoreThanOnce()
    {
        $this->analyzeAst('CallGraph/class_defined_more_than_once.php');
    }

    public function testTargetMethodHiddenBecauseClassDefinedMoreThanOnce()
    {
        $this->analyzeAst('CallGraph/target_method_undefined.php');

        $fooFunction = $this->registry->getFunction('foo');
        $bar = $this->registry->getClass('Bar');
        $getBar = $bar->getMethod('getBar')->getMethod();
        $fooBar = $this->registry->getClass('FooBar');
        $construct = $fooBar->getMethod('__construct')->getMethod();

        $foo = $this->registry->getClass('Foo');
        $this->assertFalse($foo->hasMethod('getFoo'));

        $this->assertInCallSites($fooFunction, array(), array());
        $this->assertOutCallSites($fooFunction, array($getBar), array());

        $this->assertInCallSites($getBar, array($construct), array($fooFunction));
        $this->assertOutCallSites($getBar, array(), array());

        $this->assertInCallSites($construct, array(), array());
        $this->assertOutCallSites($construct, array($getBar), array());
    }

    protected function getPasses()
    {
        return \Scrutinizer\PhpAnalyzer\PassConfig::createForTypeScanning()->getPasses();
    }

    private function assertInCallSites(AbstractFunction $function, array $inMethods, array $inFunctions)
    {
        $this->assertSame(count($inMethods) + count($inFunctions), count($function->getInCallSites()));
        $this->assertSame(count($inMethods), count($function->getInMethodCallSites()));
        $this->assertSame(count($inFunctions), count($function->getInFunctionCallSites()));

        $i = 0;
        foreach ($function->getInMethodCallSites() as $site) {
            $this->assertTrue(isset($inMethods[$i]), 'Index '.$i.' not expected');
            $this->assertSame($function, $site->getTarget());
            $this->assertSame($inMethods[$i], $site->getSource());

            $i += 1;
        }

        $i = 0;
        foreach ($function->getInFunctionCallSites() as $site) {
            $this->assertTrue(isset($inFunctions[$i]));
            $this->assertSame($function, $site->getTarget());
            $this->assertSame($inFunctions[$i], $site->getSource());

            $i += 1;
        }
    }

    private function assertOutCallSites(AbstractFunction $function, array $outMethods, array $outFunctions)
    {
        $this->assertSame(count($outMethods) + count($outFunctions), count($function->getOutCallSites()));
        $this->assertSame(count($outMethods), count($function->getOutMethodCallSites()));
        $this->assertSame(count($outFunctions), count($function->getOutFunctionCallSites()));

        $i = 0;
        foreach ($function->getOutMethodCallSites() as $site) {
            $this->assertTrue(isset($outMethods[$i]));
            $this->assertSame($function, $site->getSource());
            $this->assertSame($outMethods[$i], $site->getTarget());

            $i += 1;
        }

        $i = 0;
        foreach ($function->getOutFunctionCallSites() as $site) {
            $this->assertTrue(isset($outFunctions[$i]));
            $this->assertSame($function, $site->getSource());
            $this->assertSame($outFunctions[$i], $site->getTarget());

            $i += 1;
        }
    }
}