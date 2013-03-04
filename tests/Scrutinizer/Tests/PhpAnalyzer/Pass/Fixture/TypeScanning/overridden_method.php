<?php

class A
{
    public function a()
    {
        if ($foO) {
            return null;
        }

        return $this;
    }
}

class B extends A
{
    public function a()
    {
        if (null === $rs = parent::a()) {
            $rs = new B();
        }

        return $rs;
    }
}