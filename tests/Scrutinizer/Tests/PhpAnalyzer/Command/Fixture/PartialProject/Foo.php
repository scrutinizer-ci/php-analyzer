<?php

namespace Scrutinizer\Tests\PhpAnalyzer\Command\Fixture\PartialProject;

/**
 * This class uses a lot of unknown named types.
 */
class Foo
{
    public function __construct(X $x, Y $y, Z $z) { }
    public function addBar(Bar $bar) { }
    public function addBaz(Baz $baz) { }
}