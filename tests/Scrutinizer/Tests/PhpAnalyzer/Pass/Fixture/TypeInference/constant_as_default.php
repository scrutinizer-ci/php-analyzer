<?php

// Currently, we only support class constants declared in the same class.
// Global constants (define, or const) are not supported.
class Foo
{
    const FOO = false;

    public function test($foo = self::FOO)
    {
        echo $foo;
    }
}
