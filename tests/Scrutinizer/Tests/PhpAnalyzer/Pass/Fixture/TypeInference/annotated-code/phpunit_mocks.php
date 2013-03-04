<?php

interface PHPUnit_Framework_MockObject_MockBuilder
{
    /**
     * @return mixed
     */
    function getMock();
}

interface PHPUnit_Framework_TestCase
{
    /**
     * @return mixed
     */
    function getMock();

    /**
     * @return PHPUnit_Framework_MockObject_MockBuilder
     */
    function getMockBuilder();
}

abstract class MyTest implements PHPUnit_Framework_TestCase
{
    /** @Assertions(3) */
    public function testFoo()
    {
        /** @type unknown */
        $x = $this->getMock();

        /** @type object<PHPUnit_Framework_MockObject_MockBuilder> */
        $x = $this->getMockBuilder();

        /** @type unknown */
        $x = $x->getMock();
    }
}