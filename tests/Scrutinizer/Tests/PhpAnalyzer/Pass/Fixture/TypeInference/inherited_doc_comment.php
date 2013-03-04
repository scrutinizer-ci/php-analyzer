<?php

interface A {

    /**
     * @param string $a
     * @param integer $b
     *
     * @return boolean
     */
    function a($a, $b);
}

interface B extends A {

}

abstract class C implements B {
    /**
     * @param string $a
     * @return string
     */
    abstract function c($a);

    /**
     * @return boolean
     */
    public function d() { return true; }
}

class D extends C
{
    public function a($b, $a) { // we switched $a, $b with respect to the interface.
        return $foo; // return type, and parameter types should be inferred.
    }

    public function c($c)
    {
        return $asdf; // Return type, and parameter type should be inferred.
    }

    public function d()
    {
        return $asdf; // Return type should **not** be inferred.
    }
}