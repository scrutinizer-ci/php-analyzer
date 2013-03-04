<?php

class A
{
    public $a;
}

/** @Assertions(1) */
function() {
    $a = new A();
    $a->a = 'foo';
    $x = $a->a;

    /** @type string */ $x;
};