<?php

function foo() { }

class Foo {
}

class Foo {
    public function getFoo() {
        foo();
    }
}

class Bar {
    public function getFoo() {
        foo();
    }
}

class Bar {
}
