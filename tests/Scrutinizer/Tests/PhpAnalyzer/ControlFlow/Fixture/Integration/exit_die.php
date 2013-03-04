<?php

switch (true) {
    case 'a':
        exit();
        
    case 'b':
        die();
        
    case 'c':
        echo 'foo';
        break;
}