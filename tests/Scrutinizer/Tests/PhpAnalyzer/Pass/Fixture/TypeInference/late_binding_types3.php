<?php

class A
{
    public function returnsThis()
    {
        if ($foo) {
            return null;
        }

        return $this;
    }
}

class B extends A
{
    public function returnsThis()
    {
        if (null === $rs = parent::returnsThis()) {
            $rs = new B();
        }

        return $rs;
    }
}

$b = new B();
$b->returnsThis(); // object<B>