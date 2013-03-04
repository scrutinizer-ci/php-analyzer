<?php

class ClassWithPropertyInitializer
{
    private $foo = array();
    private $bar;

    public function inferReturnType()
    {
        if ('foo' === $bar) {
            return 'foo';
        } else if ('bar' === $bar) {
            return 0;
        }

        return false;
    }

    public function typeHintedParameter(\Foo $foo, array $bar)
    {
        return 'foo' === $foo || $bar === 'moo';
    }
}