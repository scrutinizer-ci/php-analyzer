<?php

$x = null;
if ($foo) {
    $x = new Bar();
}
$x;

while (true) {
    if (null !== $x) {
        // ``$x`` should be of type object<Bar> inside this IF.
        $x;
    }
    $x;
}