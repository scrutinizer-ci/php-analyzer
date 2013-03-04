<?php

class Session
{
    public function getFlashes($all)
    {
        $return = array();
        if ($all) {
            foreach ($all as $name => $array) {
                if (is_numeric(key($array))) {
                    $return[$name] = reset($array);
                } else {
                    $return[$name] = $array;
                }
            }
        }

        return $return;
    }
}