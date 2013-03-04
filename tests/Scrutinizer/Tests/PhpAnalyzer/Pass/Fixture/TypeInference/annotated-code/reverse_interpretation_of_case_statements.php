<?php

interface I {}
class A implements I { }
class B implements I { }

/** @Assertions(6) */
function a(I $x) {
    switch (true) {
        case /** @type object<I> */ $x instanceof A:
            /** @type object<A> */ $x;
            break;

        case /** @type object<I> */ $x instanceof B:
            /** @type object<B> */ $x;
            break;

        default:
            /** @type object<I> */ $x;
    }

    /** @type object<I> */ $x;
}