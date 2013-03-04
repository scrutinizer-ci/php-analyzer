<?php

// This function is called by a cached function/method which are not part
// of the current analysis.
function foo() { }

class Foo {
    // This method calls a cached function/method which are not part of
    // the current analysis.
    function foo() {
        $bar = new Bar();
        $bar->bar();

        bar();
    }
}