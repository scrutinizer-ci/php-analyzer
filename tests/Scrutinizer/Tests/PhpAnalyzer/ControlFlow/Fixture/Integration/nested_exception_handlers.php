<?php

try {
    try {
        throw new \InvalidArgumentException();
    } catch (RuntimeException $ex) { }
} catch (\LogicException $ex) { }