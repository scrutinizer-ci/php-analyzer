<?php

class Foo {
    function foo() { }
}

class Bar {
    function bar() {
        $foo = new Foo();
        $foo->foo();

        foo();
    }
}

function foo() { }
function bar() {
    $foo = new Foo();
    $foo->foo();

    foo();
}