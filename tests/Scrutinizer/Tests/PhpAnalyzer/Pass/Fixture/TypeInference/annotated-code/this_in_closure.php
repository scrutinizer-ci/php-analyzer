<?php

class Foo
{
    /** @Assertions(3) */
    public function bar()
    {
        function() {
            /** @type this<Foo> */ $this;

            function() {
                /** @type this<Foo> */ $this;

                function() {
                    /** @type this<Foo> */ $this;
                };
            };
        };
    }
}

/** @Assertions(1) */
function foo() {
    function() {
        /** @type none */ $this;
    };
}

function() {
    /** @type none */ $this;
};