<?php

/** @Assertions(5) */
function foo($x) {
    /** @type unknown */ $x;
    if (true == is_object($x)) {
        /** @type object */ $x;
    }

    /** @type unknown */ $x;
    if (true === is_object($x)) {
        /** @type object */ $x;
    }

    /** @type unknown */ $x;
}