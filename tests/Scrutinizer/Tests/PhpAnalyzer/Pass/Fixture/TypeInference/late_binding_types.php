<?php

class A
{
    public static function returnsStatic()
    {
        return new static();
    }

    public static function returnsSelf()
    {
        return new self();
    }

    public function returnsThis()
    {
        return $this;
    }
}

class B extends A
{
    public function returnsParent()
    {
        return parent::returnsThis();
    }
}

$x = B::returnsStatic(); // $x should be B.
$x = B::returnsSelf(); // $x should be A.

$b = new B();
$x = $b->returnsThis(); // $x should be B.
$x = $b->returnsParent(); // $x should be B.