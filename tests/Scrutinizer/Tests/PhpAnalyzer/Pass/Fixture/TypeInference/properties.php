<?php

class Foo { }

class Bar
{
    private $foo;

    private function bar()
    {
        $this->foo;
    }

    public function __construct()
    {
        $this->foo = new Foo;
    }
}