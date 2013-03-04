<?php

// Start of json v.1.2.1

/**
 * Returns the JSON representation of a value
 * @link http://www.php.net/manual/en/function.json-encode.php
 * @param value mixed <p>
 * The value being encoded. Can be any type except
 * a resource.
 * </p>
 * <p>
 * This function only works with UTF-8 encoded data.
 * </p>
 * @param options int[optional] <p>
 * Bitmask consisting of JSON_HEX_QUOT,
 * JSON_HEX_TAG,
 * JSON_HEX_AMP,
 * JSON_HEX_APOS,
 * JSON_FORCE_OBJECT.
 * </p>
 * @return string a JSON encoded string on success.
 *
 * @jms-builtin
 */
function json_encode ($value, $options = null) {}

/**
 * Decodes a JSON string
 * @link http://www.php.net/manual/en/function.json-decode.php
 * @param json string <p>
 * The json string being decoded.
 * </p>
 * @param assoc bool[optional] <p>
 * When true, returned objects will be converted into
 * associative arrays.
 * </p>
 * @param depth int[optional] <p>
 * User specified recursion depth.
 * </p>
 * @return mixed the value encoded in json in appropriate
 * PHP type. Values true, false and
 * null (case-insensitive) are returned as true, false
 * and &null; respectively. &null; is returned if the
 * json cannot be decoded or if the encoded
 * data is deeper than the recursion limit.
 *
 * @jms-builtin
 */
function json_decode ($json, $assoc = null, $depth = null) {}

// End of json v.1.2.1
?>
