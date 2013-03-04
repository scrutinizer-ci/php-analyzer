<?php

class Foo {
}

class Foo {
    public function getFoo() { }
}

class Bar {
    public function getBar() { }
}

class Bar {
}

function foo() {
    $foo = new Foo();
    $foo->getFoo();

    $bar = new Bar();
    $bar->getBar();
}

class FooBar {
    public function __construct(Foo $foo, Bar $bar)
    {
        $foo->getFoo();
        $bar->getBar();
    }
}
