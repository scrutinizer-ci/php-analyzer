<?php

class Foo
{
    public static function createSelf()
    {
        return new self();
    }

    public static function createStatic()
    {
        return new static();
    }
}