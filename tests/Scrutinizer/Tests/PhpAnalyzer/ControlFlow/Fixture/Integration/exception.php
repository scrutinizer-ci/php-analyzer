<?php

function test() {
    try {
        throw new \RuntimeException();
    } catch (\RuntimeException $ex) {
    } catch (\LogicException $ex) {
    }

    echo 'foo';
}