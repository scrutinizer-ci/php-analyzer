<?php

function foo($a) {
    $b = preg_replace('foo', 'bar', $a);

    return $b;
}

$a = array('foo');
$b = preg_replace('foo', 'bar', $a);
$c = preg_replace('foo', 'bar', 'foo');
