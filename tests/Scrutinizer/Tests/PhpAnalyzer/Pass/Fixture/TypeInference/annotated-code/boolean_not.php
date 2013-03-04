<?php

/** @Assertions(0) */
function stringOrFalse($x) {
    if ($x) {
        return false;
    }

    return 'foo';
}

/** @Assertions(5) */
function test($a) {
    $x = stringOrFalse($a);

    /** @type string|false */ $x;

    if ($x) {
        /** @type string */ $x;
    } else {
        /** @type false|string */ $x;
    }

    if ($x = stringOrFalse($a)) {
        /** @type string */ $x;
    }

    if ( ! $x = stringOrFalse($a)) {
        /** @type string|false */ $x;
    }
}
