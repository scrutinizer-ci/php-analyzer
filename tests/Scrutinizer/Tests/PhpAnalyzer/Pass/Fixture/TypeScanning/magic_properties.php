<?php


/**
 * @property string $foo
 * @property string $bar
 */
class Foo
{
    // The real property has precedence over the annotation
    private $bar;
}