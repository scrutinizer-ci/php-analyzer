<?php

class PHPUnit_Framework_TestCase { }
class Logger { }

class MyTest extends PHPUnit_Framework_TestCase
{
    private $logger;
    private $nullableLogger;

    /** @Assertions(0) */
    protected function setUp()
    {
        $this->logger = new Logger();

        $this->nullableLogger = new Logger();
        if ($foo) {
            $this->nullableLogger = null;
        }
    }

    /** @Assertions(0) */
    protected function tearDown()
    {
        $this->logger = null;
    }

    /** @Assertions(2) */
    public function testFoo()
    {
        /** @type object<Logger> */ $x = $this->logger;
        /** @type object<Logger>|null */ $x = $this->nullableLogger;
    }
}