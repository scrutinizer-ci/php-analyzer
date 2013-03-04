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

namespace Foo;

interface A {
    const A = 0;
    function a();
}
interface B extends A {
    const B = 0;
    function b();
}
interface C {
    const C = 0;
    function c();
}
interface D {
    const D = 1;
    function d();
}

abstract class Bar implements D {
    const BAR = 0;
    private $bar = 0;
    abstract function bar();
}
abstract class Foo extends Bar implements B, C {
    const FOO = 0;
    private $foo = 0;
    abstract function foo();
}