<?php

/** @Assertions(0) */
function returnsTupel() {
    return array('foo', 4, true);
}

/** @Assertions(7) */
function test() {
    list($a, $b, $c) = returnsTupel();
    list(,$d,) = returnsTupel();
    list($e,,$f) = returnsTupel();

    /** @type array<integer,string|integer|boolean,{0:type(string),1:type(integer),2:type(boolean)}> */
    returnsTupel();

    /** @type string */ $a;
    /** @type integer */ $b;
    /** @type boolean */ $c;
    /** @type integer */ $d;
    /** @type string */ $e;
    /** @type boolean */ $f;
}