<?php

$x = array('foo');  // $x should be "array<string>"
while ($y = array_pop($x)) {
    $y; // should be "string" here
}