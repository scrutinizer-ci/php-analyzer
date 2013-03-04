<?php

/**
 * This function creates an infinite loop within the TypeInference analysis.
 */
function testInfiniteLoop($listener) {
    if (is_string($listener)) {
    } else {
    }

    while (true) {
        $listener;
    }
}