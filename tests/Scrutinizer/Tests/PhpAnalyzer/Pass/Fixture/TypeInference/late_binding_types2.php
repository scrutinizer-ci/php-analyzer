<?php

class A
{
    public function returnsThis()
    {
        return $this;
    }
}

class B extends A
{
    public function returnsThis()
    {
        return parent::returnsThis();
    }
}

class C extends B
{
    public function returnsThis()
    {
        return A::returnsThis();
    }
}

$c = new C();
$c->returnsThis(); // object<C>