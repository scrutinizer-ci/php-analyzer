<?php

class A
{
    public function a() { }
}

class B
{
    private static $a;

    public function __construct()
    {
        if (null === self::$a) {
            if (!$foo) {
                self::$a = false;
            } else {
                self::$a = new A();
            }
        }
    }

    public function foo()
    {
        if (false !== self::$a) {
            self::$a->a(); // self::$a should be "object<A>".
        }
    }
}