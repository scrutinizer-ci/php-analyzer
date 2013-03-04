<?php

for ($i = 0; $i < $this->length; ++$i) {
    if (is_object($objectOrArray)) {
        $value = $this->readProperty($objectOrArray, $i);
        // arrays need to be treated separately (due to PHP bug?)
        // http://bugs.php.net/bug.php?id=52133
    } elseif (is_array($objectOrArray)) {
        $property = $this->elements[$i];
        if (!array_key_exists($property, $objectOrArray)) {
            $objectOrArray[$property] = $i + 1 < $this->length ? array() : null;
        }
        $value =& $objectOrArray[$property];
    } else {
        throw new UnexpectedTypeException($objectOrArray, 'object or array');
    }

    $objectOrArray =& $value;
}

return $value;