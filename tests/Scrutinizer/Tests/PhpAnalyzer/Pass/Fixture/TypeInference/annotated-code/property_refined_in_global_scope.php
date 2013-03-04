<?php

class A
{
    public $a;
}

$a = new A();
$a->a = 'foo';

/** @type string */
$x = $a->a;
