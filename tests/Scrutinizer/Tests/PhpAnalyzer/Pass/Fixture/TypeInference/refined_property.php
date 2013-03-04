<?php

class Foo
{
    private $bar;

    public function doSomething()
    {
        $this->bar = null;
        if ($foo) {
            $this->bar = new Bar();
        }

        while(true) {
            $this->bar;
            if (null !== $this->bar) {
                // ``$this->bar`` should be of type object<Bar> inside this IF.
                $this->bar;
            }
            $this->bar;
        }
    }
}