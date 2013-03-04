<?php

class A
{
    private $x;

    public function __construct(X $x = null)
    {
        $this->x = $x;
    }

    public function sth()
    {
        $this->x;
        while (true) {
            if (null !== $this->x) {
                $this->x;
            }
        }
    }
}