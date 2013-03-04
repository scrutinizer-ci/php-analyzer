<?php

foreach ($previousValue as $previousItem) {
    foreach ($value as $key => $item) {
        if ($item === $previousItem) {
            // Item found, don't add
            unset($itemsToAdd[$key]);

            // Next $previousItem
            continue 2;
        }
    }

    // Item not found, remove
    $objectOrArray->$removeMethod($previousItem);
}
