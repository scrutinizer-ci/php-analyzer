<?php

for ($i=0; $i<10; $i++) {
    for ($j=0; $j<20; $j++) {
        if ($i < 0) {
            continue 2;
        }

        if ($j > 5) {
            break 2;
        }

        goto foo;
    }

    foo:
}