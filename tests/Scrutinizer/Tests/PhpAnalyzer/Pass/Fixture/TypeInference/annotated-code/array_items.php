<?php

/** @Assertions(2) */
function testArrayMerge() {
    $options = array('foo' => 1);
    $options = array_merge($options, array(
        'foo' => 'bar',
        'enabled' => true,
    ));

    /** @type string */ $options['foo'];
    /** @type boolean */ $options['enabled'];
};

/** @Assertions(4) */
function testArrayMergeWhenNumericKeys() {
    $tupel = array_merge(array('a', 1), array(3.4, true));

    /** @type string */ $tupel[0];
    /** @type integer */ $tupel[1];
    /** @type double */ $tupel[2];
    /** @type boolean */ $tupel[3];
}

/** @Assertions(2) */
function testArrayReplace() {
    $options = array_replace(array('foo' => 1), array('foo' => 'bar', 'enabled' => true));
    /** @type string */ $options['foo'];
    /** @type boolean */ $options['enabled'];
};

/** @Assertions(4) */
function testArrayReplaceWhenNumericKeys() {
    $tupel = array_replace(array('a', 1), array(3.4, true));

    /** @type double */ $tupel[0];
    /** @type boolean */ $tupel[1];

    // The following two could be discussed. Theoretically, we could know that
    // we know all defined items, and then automatically assign the None, or
    // Unknown type when items for which we know for sure that they do not exist
    // are accessed.
    /** @type string|integer|double|boolean */ $tupel[2];
    /** @type string|integer|double|boolean */ $tupel[3];
}

/** @Assertions(5) */
function testArrayMergeRecursive() {
    $ar1 = array("color" => array("favorite" => "red"), 5);
    $ar2 = array(10.4545, "color" => array("favorite" => true, 43.432));
    $result = array_merge_recursive($ar1, $ar2);

    // Resulting Array
    // ['color' => ['favorite' => ['red', true], 43.432], 5, 10.4545]

    /** @type string */ $result['color']['favorite'][0];
    /** @type boolean */ $result['color']['favorite'][1];
    /** @type double */ $result['color'][0];
    /** @type integer */ $result[0];
    /** @type double */ $result[1];
};

/** @Assertions(4) */
function testArrayReplaceRecursive() {
    $base = array('citrus' => array("orange") , 'berries' => array("blackberry", 434), 'others' => true );
    $replacements = array('citrus' => 'pineapple', 'berries' => array('blueberry'), 'others' => array('litchis'));
    $replacements2 = array('citrus' => array('pineapple'), 'berries' => array('blueberry'), 'others' => 'litchis');

    $basket = array_replace_recursive($base, $replacements, $replacements2);

    /** @type string */ $basket['citrus'][0];
    /** @type string */ $basket['berries'][0];
    /** @type integer */ $basket['berries'][1];
    /** @type string */ $basket['others'];
}