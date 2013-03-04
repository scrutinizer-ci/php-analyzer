<?php

switch (true) {
    case 'foo':
        return 'bar';

    case 'baz';
    case 'bar':
        echo 'foo';
        break;

    default:
        echo 'boo';
}