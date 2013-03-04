<?php

class SimpleXmlElement
{
    /** @Assertions(0) */
    public function asXml() { }
}

/** @Assertions(2) */
function test() {
    $x = new SimpleXmlElement();
    $a = $x->asXml();
    $b = $x->asXml(__FILE__);

    /** @type string|false */ $a;
    /** @type boolean */ $b;
}