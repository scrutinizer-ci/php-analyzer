<?php

interface Base { }
interface SpecificA extends Base { }
interface SpecificB extends Base { }

function foo (Base $x) {
    if (!$x instanceof SpecificA && !$x instanceof SpecificB) {
        $x; // should be object<Base>

        return;
    }

    $x; // should be object<SpecificA>|object<SpecificB>
}