<?php

class Foo { }

class Baz
{
    private $foo;

    private function baz()
    {
        $this->foo;
    }

    public function __construct(Foo $foo)
    {
        $this->foo = $foo;
    }
}