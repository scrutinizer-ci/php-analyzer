<?php

function f($a) {
    $a;
    $hasA = is_array($a);

    if ($a instanceof A || $hasA) {
        $a; // array|McvPage
    }
}