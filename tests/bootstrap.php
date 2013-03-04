<?php

use Doctrine\Common\Annotations\AnnotationRegistry;

$loader = require __DIR__.'/../vendor/autoload.php';
$loader->add('Scrutinizer', __DIR__);
AnnotationRegistry::registerLoader('class_exists');

assert_options(ASSERT_BAIL);