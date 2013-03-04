<?php

foreach (new GenericObject() as $x) {
    echo $x; // $x should be "?".
}