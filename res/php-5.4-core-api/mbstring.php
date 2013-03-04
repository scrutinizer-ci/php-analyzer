<?php

// Start of mbstring v.

/**
 * (PHP 4 &gt;= 4.3.0, PHP 5)<br/>
 * Perform case folding on a string
 * @link http://php.net/manual/en/function.mb-convert-case.php
 * @param string $str <p>
 * The string being converted.
 * </p>
 * @param int $mode [optional] <p>
 * The mode of the conversion. It can be one of
 * <b>MB_CASE_UPPER</b>,
 * <b>MB_CASE_LOWER</b>, or
 * <b>MB_CASE_TITLE</b>.
 * </p>
 * @param string $encoding [optional] The <i>encoding</i>
 * parameter is the character encoding. If it is omitted, the internal character
 * encoding value will be used.</p>
 * @return string A case folded version of <i>string</i> converted in the
 * way specified by <i>mode</i>.
 * @jms-builtin
 */
function mb_convert_case ($str, $mode = 'MB_CASE_UPPER', $encoding = 'mb_internal_encoding()') {}

/**
 * (PHP 4 &gt;= 4.3.0, PHP 5)<br/>
 * Make a string uppercase
 * @link http://php.net/manual/en/function.mb-strtoupper.php
 * @param string $str <p>
 * The string being uppercased.
 * </p>
 * @param string $encoding [optional] The <i>encoding</i>
 * parameter is the character encoding. If it is omitted, the internal character
 * encoding value will be used.</p>
 * @return string <i>str</i> with all alphabetic characters converted to uppercase.
 * @jms-builtin
 */
function mb_strtoupper ($str, $encoding = 'mb_internal_encoding()') {}

/**
 * (PHP 4 &gt;= 4.3.0, PHP 5)<br/>
 * Make a string lowercase
 * @link http://php.net/manual/en/function.mb-strtolower.php
 * @param string $str <p>
 * The string being lowercased.
 * </p>
 * @param string $encoding [optional] The <i>encoding</i>
 * parameter is the character encoding. If it is omitted, the internal character
 * encoding value will be used.</p>
 * @return string <i>str</i> with all alphabetic characters converted to lowercase.
 * @jms-builtin
 */
function mb_strtolower ($str, $encoding = 'mb_internal_encoding()') {}

/**
 * (PHP 4 &gt;= 4.0.6, PHP 5)<br/>
 * Set/Get current language
 * @link http://php.net/manual/en/function.mb-language.php
 * @param string $language [optional] <p>
 * Used for encoding
 * e-mail messages. Valid languages are "Japanese",
 * "ja","English","en" and "uni"
 * (UTF-8). <b>mb_send_mail</b> uses this setting to
 * encode e-mail.
 * </p>
 * <p>
 * Language and its setting is ISO-2022-JP/Base64 for
 * Japanese, UTF-8/Base64 for uni, ISO-8859-1/quoted printable for
 * English.
 * </p>
 * @return mixed If <i>language</i> is set and
 * <i>language</i> is valid, it returns
 * <b>TRUE</b>. Otherwise, it returns <b>FALSE</b>.
 * When <i>language</i> is omitted, it returns the language
 * name as a string. If no language is set previously, it then returns
 * <b>FALSE</b>.
 * @jms-builtin
 */
function mb_language ($language = null) {}

/**
 * (PHP 4 &gt;= 4.0.6, PHP 5)<br/>
 * Set/Get internal character encoding
 * @link http://php.net/manual/en/function.mb-internal-encoding.php
 * @param string $encoding [optional] <p>
 * <i>encoding</i> is the character encoding name
 * used for the HTTP input character encoding conversion, HTTP output
 * character encoding conversion, and the default character encoding
 * for string functions defined by the mbstring module.
 * </p>
 * @return mixed If <i>encoding</i> is set, then
 * Returns <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * If <i>encoding</i> is omitted, then
 * the current character encoding name is returned.
 * @jms-builtin
 */
function mb_internal_encoding ($encoding = 'mb_internal_encoding()') {}

/**
 * (PHP 4 &gt;= 4.0.6, PHP 5)<br/>
 * Detect HTTP input character encoding
 * @link http://php.net/manual/en/function.mb-http-input.php
 * @param string $type [optional] <p>
 * Input string specifies the input type.
 * "G" for GET, "P" for POST, "C" for COOKIE, "S" for string, "L" for list, and
 * "I" for the whole list (will return array).
 * If type is omitted, it returns the last input type processed.
 * </p>
 * @return mixed The character encoding name, as per the <i>type</i>.
 * If <b>mb_http_input</b> does not process specified
 * HTTP input, it returns <b>FALSE</b>.
 * @jms-builtin
 */
function mb_http_input ($type = "") {}

/**
 * (PHP 4 &gt;= 4.0.6, PHP 5)<br/>
 * Set/Get HTTP output character encoding
 * @link http://php.net/manual/en/function.mb-http-output.php
 * @param string $encoding [optional] <p>
 * If <i>encoding</i> is set,
 * <b>mb_http_output</b> sets the HTTP output character
 * encoding to <i>encoding</i>.
 * </p>
 * <p>
 * If <i>encoding</i> is omitted,
 * <b>mb_http_output</b> returns the current HTTP output
 * character encoding.
 * </p>
 * @return mixed If <i>encoding</i> is omitted,
 * <b>mb_http_output</b> returns the current HTTP output
 * character encoding. Otherwise,
 * Returns <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function mb_http_output ($encoding = null) {}

/**
 * (PHP 4 &gt;= 4.0.6, PHP 5)<br/>
 * Set/Get character encoding detection order
 * @link http://php.net/manual/en/function.mb-detect-order.php
 * @param mixed $encoding_list [optional] <p>
 * <i>encoding_list</i> is an array or
 * comma separated list of character encoding. See supported encodings.
 * </p>
 * <p>
 * If <i>encoding_list</i> is omitted, it returns
 * the current character encoding detection order as array.
 * </p>
 * <p>
 * This setting affects <b>mb_detect_encoding</b> and
 * <b>mb_send_mail</b>.
 * </p>
 * <p>
 * mbstring currently implements the following
 * encoding detection filters. If there is an invalid byte sequence
 * for the following encodings, encoding detection will fail.
 * </p>
 * UTF-8, UTF-7,
 * ASCII,
 * EUC-JP,SJIS,
 * eucJP-win, SJIS-win,
 * JIS, ISO-2022-JP
 * <p>
 * For ISO-8859-*, mbstring
 * always detects as ISO-8859-*.
 * </p>
 * <p>
 * For UTF-16, UTF-32,
 * UCS2 and UCS4, encoding
 * detection will fail always.
 * </p>
 * <p>
 * Useless detect order example
 * <pre>
 * ; Always detect as ISO-8859-1
 * detect_order = ISO-8859-1, UTF-8
 * ; Always detect as UTF-8, since ASCII/UTF-7 values are
 * ; valid for UTF-8
 * detect_order = UTF-8, ASCII, UTF-7
 * </pre>
 * </p>
 * @return mixed <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function mb_detect_order ($encoding_list = null) {}

/**
 * (PHP 4 &gt;= 4.0.6, PHP 5)<br/>
 * Set/Get substitution character
 * @link http://php.net/manual/en/function.mb-substitute-character.php
 * @param mixed $substrchar [optional] <p>
 * Specify the Unicode value as an integer,
 * or as one of the following strings:
 * "none" : no output
 * @return mixed If <i>substchar</i> is set, it returns <b>TRUE</b> for success,
 * otherwise returns <b>FALSE</b>.
 * If <i>substchar</i> is not set, it returns the Unicode value,
 * or "none" or "long".
 * @jms-builtin
 */
function mb_substitute_character ($substrchar = null) {}

/**
 * (PHP 4 &gt;= 4.0.6, PHP 5)<br/>
 * Parse GET/POST/COOKIE data and set global variable
 * @link http://php.net/manual/en/function.mb-parse-str.php
 * @param string $encoded_string <p>
 * The URL encoded data.
 * </p>
 * @param array $result [optional] <p>
 * An array containing decoded and character encoded converted values.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function mb_parse_str ($encoded_string, array &$result = null) {}

/**
 * (PHP 4 &gt;= 4.0.6, PHP 5)<br/>
 * Callback function converts character encoding in output buffer
 * @link http://php.net/manual/en/function.mb-output-handler.php
 * @param string $contents <p>
 * The contents of the output buffer.
 * </p>
 * @param int $status <p>
 * The status of the output buffer.
 * </p>
 * @return string The converted string.
 * @jms-builtin
 */
function mb_output_handler ($contents, $status) {}

/**
 * (PHP 4 &gt;= 4.0.6, PHP 5)<br/>
 * Get MIME charset string
 * @link http://php.net/manual/en/function.mb-preferred-mime-name.php
 * @param string $encoding <p>
 * The encoding being checked.
 * </p>
 * @return string The MIME charset string for character encoding
 * <i>encoding</i>.
 * @jms-builtin
 */
function mb_preferred_mime_name ($encoding) {}

/**
 * (PHP 4 &gt;= 4.0.6, PHP 5)<br/>
 * Get string length
 * @link http://php.net/manual/en/function.mb-strlen.php
 * @param string $str <p>
 * The string being checked for length.
 * </p>
 * @param string $encoding [optional] The <i>encoding</i>
 * parameter is the character encoding. If it is omitted, the internal character
 * encoding value will be used.</p>
 * @return int the number of characters in
 * string <i>str</i> having character encoding
 * <i>encoding</i>. A multi-byte character is
 * counted as 1.
 * @jms-builtin
 */
function mb_strlen ($str, $encoding = null) {}

/**
 * (PHP 4 &gt;= 4.0.6, PHP 5)<br/>
 * Find position of first occurrence of string in a string
 * @link http://php.net/manual/en/function.mb-strpos.php
 * @param string $haystack <p>
 * The string being checked.
 * </p>
 * @param string $needle <p>
 * The string to find in <i>haystack</i>. In contrast
 * with <b>strpos</b>, numeric values are not applied
 * as the ordinal value of a character.
 * </p>
 * @param int $offset [optional] <p>
 * The search offset. If it is not specified, 0 is used.
 * </p>
 * @param string $encoding [optional] The <i>encoding</i>
 * parameter is the character encoding. If it is omitted, the internal character
 * encoding value will be used.</p>
 * @return int the numeric position of
 * the first occurrence of <i>needle</i> in the
 * <i>haystack</i> string. If
 * <i>needle</i> is not found, it returns <b>FALSE</b>.
 * @jms-builtin
 */
function mb_strpos ($haystack, $needle, $offset = 0, $encoding = null) {}

/**
 * (PHP 4 &gt;= 4.0.6, PHP 5)<br/>
 * Find position of last occurrence of a string in a string
 * @link http://php.net/manual/en/function.mb-strrpos.php
 * @param string $haystack <p>
 * The string being checked, for the last occurrence
 * of <i>needle</i>
 * </p>
 * @param string $needle <p>
 * The string to find in <i>haystack</i>.
 * </p>
 * @param int $offset [optional] May be specified to begin searching an arbitrary number of characters into
 * the string. Negative values will stop searching at an arbitrary point
 * prior to the end of the string.
 * @param string $encoding [optional] The <i>encoding</i>
 * parameter is the character encoding. If it is omitted, the internal character
 * encoding value will be used.</p>
 * @return int the numeric position of
 * the last occurrence of <i>needle</i> in the
 * <i>haystack</i> string. If
 * <i>needle</i> is not found, it returns <b>FALSE</b>.
 * @jms-builtin
 */
function mb_strrpos ($haystack, $needle, $offset = 0, $encoding = null) {}

/**
 * (PHP 5 &gt;= 5.2.0)<br/>
 * Finds position of first occurrence of a string within another, case insensitive
 * @link http://php.net/manual/en/function.mb-stripos.php
 * @param string $haystack <p>
 * The string from which to get the position of the first occurrence
 * of <i>needle</i>
 * </p>
 * @param string $needle <p>
 * The string to find in <i>haystack</i>
 * </p>
 * @param int $offset [optional] <p>
 * The position in <i>haystack</i>
 * to start searching
 * </p>
 * @param string $encoding [optional] <p>
 * Character encoding name to use.
 * If it is omitted, internal character encoding is used.
 * </p>
 * @return int Return the numeric position of the first occurrence of
 * <i>needle</i> in the <i>haystack</i>
 * string, or <b>FALSE</b> if <i>needle</i> is not found.
 * @jms-builtin
 */
function mb_stripos ($haystack, $needle, $offset = null, $encoding = null) {}

/**
 * (PHP 5 &gt;= 5.2.0)<br/>
 * Finds position of last occurrence of a string within another, case insensitive
 * @link http://php.net/manual/en/function.mb-strripos.php
 * @param string $haystack <p>
 * The string from which to get the position of the last occurrence
 * of <i>needle</i>
 * </p>
 * @param string $needle <p>
 * The string to find in <i>haystack</i>
 * </p>
 * @param int $offset [optional] <p>
 * The position in <i>haystack</i>
 * to start searching
 * </p>
 * @param string $encoding [optional] <p>
 * Character encoding name to use.
 * If it is omitted, internal character encoding is used.
 * </p>
 * @return int Return the numeric position of
 * the last occurrence of <i>needle</i> in the
 * <i>haystack</i> string, or <b>FALSE</b>
 * if <i>needle</i> is not found.
 * @jms-builtin
 */
function mb_strripos ($haystack, $needle, $offset = 0, $encoding = null) {}

/**
 * (PHP 5 &gt;= 5.2.0)<br/>
 * Finds first occurrence of a string within another
 * @link http://php.net/manual/en/function.mb-strstr.php
 * @param string $haystack <p>
 * The string from which to get the first occurrence
 * of <i>needle</i>
 * </p>
 * @param string $needle <p>
 * The string to find in <i>haystack</i>
 * </p>
 * @param bool $before_needle [optional] <p>
 * Determines which portion of <i>haystack</i>
 * this function returns.
 * If set to <b>TRUE</b>, it returns all of <i>haystack</i>
 * from the beginning to the first occurrence of <i>needle</i> (excluding needle).
 * If set to <b>FALSE</b>, it returns all of <i>haystack</i>
 * from the first occurrence of <i>needle</i> to the end (including needle).
 * </p>
 * @param string $encoding [optional] <p>
 * Character encoding name to use.
 * If it is omitted, internal character encoding is used.
 * </p>
 * @return string the portion of <i>haystack</i>,
 * or <b>FALSE</b> if <i>needle</i> is not found.
 * @jms-builtin
 */
function mb_strstr ($haystack, $needle, $before_needle = false, $encoding = null) {}

/**
 * (PHP 5 &gt;= 5.2.0)<br/>
 * Finds the last occurrence of a character in a string within another
 * @link http://php.net/manual/en/function.mb-strrchr.php
 * @param string $haystack <p>
 * The string from which to get the last occurrence
 * of <i>needle</i>
 * </p>
 * @param string $needle <p>
 * The string to find in <i>haystack</i>
 * </p>
 * @param bool $part [optional] <p>
 * Determines which portion of <i>haystack</i>
 * this function returns.
 * If set to <b>TRUE</b>, it returns all of <i>haystack</i>
 * from the beginning to the last occurrence of <i>needle</i>.
 * If set to <b>FALSE</b>, it returns all of <i>haystack</i>
 * from the last occurrence of <i>needle</i> to the end,
 * </p>
 * @param string $encoding [optional] <p>
 * Character encoding name to use.
 * If it is omitted, internal character encoding is used.
 * </p>
 * @return string the portion of <i>haystack</i>.
 * or <b>FALSE</b> if <i>needle</i> is not found.
 * @jms-builtin
 */
function mb_strrchr ($haystack, $needle, $part = false, $encoding = null) {}

/**
 * (PHP 5 &gt;= 5.2.0)<br/>
 * Finds first occurrence of a string within another, case insensitive
 * @link http://php.net/manual/en/function.mb-stristr.php
 * @param string $haystack <p>
 * The string from which to get the first occurrence
 * of <i>needle</i>
 * </p>
 * @param string $needle <p>
 * The string to find in <i>haystack</i>
 * </p>
 * @param bool $before_needle [optional] <p>
 * Determines which portion of <i>haystack</i>
 * this function returns.
 * If set to <b>TRUE</b>, it returns all of <i>haystack</i>
 * from the beginning to the first occurrence of <i>needle</i> (including needle).
 * If set to <b>FALSE</b>, it returns all of <i>haystack</i>
 * from the first occurrence of <i>needle</i> to the end (excluding needle).
 * </p>
 * @param string $encoding [optional] <p>
 * Character encoding name to use.
 * If it is omitted, internal character encoding is used.
 * </p>
 * @return string the portion of <i>haystack</i>,
 * or <b>FALSE</b> if <i>needle</i> is not found.
 * @jms-builtin
 */
function mb_stristr ($haystack, $needle, $before_needle = false, $encoding = null) {}

/**
 * (PHP 5 &gt;= 5.2.0)<br/>
 * Finds the last occurrence of a character in a string within another, case insensitive
 * @link http://php.net/manual/en/function.mb-strrichr.php
 * @param string $haystack <p>
 * The string from which to get the last occurrence
 * of <i>needle</i>
 * </p>
 * @param string $needle <p>
 * The string to find in <i>haystack</i>
 * </p>
 * @param bool $part [optional] <p>
 * Determines which portion of <i>haystack</i>
 * this function returns.
 * If set to <b>TRUE</b>, it returns all of <i>haystack</i>
 * from the beginning to the last occurrence of <i>needle</i>.
 * If set to <b>FALSE</b>, it returns all of <i>haystack</i>
 * from the last occurrence of <i>needle</i> to the end,
 * </p>
 * @param string $encoding [optional] <p>
 * Character encoding name to use.
 * If it is omitted, internal character encoding is used.
 * </p>
 * @return string the portion of <i>haystack</i>.
 * or <b>FALSE</b> if <i>needle</i> is not found.
 * @jms-builtin
 */
function mb_strrichr ($haystack, $needle, $part = false, $encoding = null) {}

/**
 * (PHP 4 &gt;= 4.3.0, PHP 5)<br/>
 * Count the number of substring occurrences
 * @link http://php.net/manual/en/function.mb-substr-count.php
 * @param string $haystack <p>
 * The string being checked.
 * </p>
 * @param string $needle <p>
 * The string being found.
 * </p>
 * @param string $encoding [optional] The <i>encoding</i>
 * parameter is the character encoding. If it is omitted, the internal character
 * encoding value will be used.</p>
 * @return int The number of times the
 * <i>needle</i> substring occurs in the
 * <i>haystack</i> string.
 * @jms-builtin
 */
function mb_substr_count ($haystack, $needle, $encoding = null) {}

/**
 * (PHP 4 &gt;= 4.0.6, PHP 5)<br/>
 * Get part of string
 * @link http://php.net/manual/en/function.mb-substr.php
 * @param string $str <p>
 * The string to extract the substring from.
 * </p>
 * @param int $start <p>
 * Position of first character to use from <i>str</i>.
 * </p>
 * @param int $length [optional] <p>
 * Maximum number of characters to use from <i>str</i>.
 * </p>
 * @param string $encoding [optional] The <i>encoding</i>
 * parameter is the character encoding. If it is omitted, the internal character
 * encoding value will be used.</p>
 * @return string <b>mb_substr</b> returns the portion of
 * <i>str</i> specified by the
 * <i>start</i> and
 * <i>length</i> parameters.
 * @jms-builtin
 */
function mb_substr ($str, $start, $length = null, $encoding = null) {}

/**
 * (PHP 4 &gt;= 4.0.6, PHP 5)<br/>
 * Get part of string
 * @link http://php.net/manual/en/function.mb-strcut.php
 * @param string $str <p>
 * The string being cut.
 * </p>
 * @param int $start <p>
 * Starting position in bytes.
 * </p>
 * @param int $length [optional] <p>
 * Length in bytes.
 * </p>
 * @param string $encoding [optional] The <i>encoding</i>
 * parameter is the character encoding. If it is omitted, the internal character
 * encoding value will be used.</p>
 * @return string <b>mb_strcut</b> returns the portion of
 * <i>str</i> specified by the
 * <i>start</i> and
 * <i>length</i> parameters.
 * @jms-builtin
 */
function mb_strcut ($str, $start, $length = null, $encoding = null) {}

/**
 * (PHP 4 &gt;= 4.0.6, PHP 5)<br/>
 * Return width of string
 * @link http://php.net/manual/en/function.mb-strwidth.php
 * @param string $str <p>
 * The string being decoded.
 * </p>
 * @param string $encoding [optional] The <i>encoding</i>
 * parameter is the character encoding. If it is omitted, the internal character
 * encoding value will be used.</p>
 * @return int The width of string <i>str</i>.
 * @jms-builtin
 */
function mb_strwidth ($str, $encoding = null) {}

/**
 * (PHP 4 &gt;= 4.0.6, PHP 5)<br/>
 * Get truncated string with specified width
 * @link http://php.net/manual/en/function.mb-strimwidth.php
 * @param string $str <p>
 * The string being decoded.
 * </p>
 * @param int $start <p>
 * The start position offset. Number of
 * characters from the beginning of string. (First character is 0)
 * </p>
 * @param int $width <p>
 * The width of the desired trim.
 * </p>
 * @param string $trimmarker [optional] <p>
 * A string that is added to the end of string
 * when string is truncated.
 * </p>
 * @param string $encoding [optional] The <i>encoding</i>
 * parameter is the character encoding. If it is omitted, the internal character
 * encoding value will be used.</p>
 * @return string The truncated string. If <i>trimmarker</i> is set,
 * <i>trimmarker</i> is appended to the return value.
 * @jms-builtin
 */
function mb_strimwidth ($str, $start, $width, $trimmarker = null, $encoding = null) {}

/**
 * (PHP 4 &gt;= 4.0.6, PHP 5)<br/>
 * Convert character encoding
 * @link http://php.net/manual/en/function.mb-convert-encoding.php
 * @param string $str <p>
 * The string being encoded.
 * </p>
 * @param string $to_encoding <p>
 * The type of encoding that <i>str</i> is being converted to.
 * </p>
 * @param mixed $from_encoding [optional] <p>
 * Is specified by character code names before conversion. It is either
 * an array, or a comma separated enumerated list.
 * If <i>from_encoding</i> is not specified, the internal
 * encoding will be used.
 * </p>
 * <p>
 * See supported
 * encodings.
 * </p>
 * @return string The encoded string.
 * @jms-builtin
 */
function mb_convert_encoding ($str, $to_encoding, $from_encoding = null) {}

/**
 * (PHP 4 &gt;= 4.0.6, PHP 5)<br/>
 * Detect character encoding
 * @link http://php.net/manual/en/function.mb-detect-encoding.php
 * @param string $str <p>
 * The string being detected.
 * </p>
 * @param mixed $encoding_list [optional] <p>
 * <i>encoding_list</i> is list of character
 * encoding. Encoding order may be specified by array or comma
 * separated list string.
 * </p>
 * <p>
 * If <i>encoding_list</i> is omitted,
 * detect_order is used.
 * </p>
 * @param bool $strict [optional] <p>
 * <i>strict</i> specifies whether to use
 * the strict encoding detection or not.
 * Default is <b>FALSE</b>.
 * </p>
 * @return string The detected character encoding or <b>FALSE</b> if the encoding cannot be
 * detected from the given string.
 * @jms-builtin
 */
function mb_detect_encoding ($str, $encoding_list = 'mb_detect_order()', $strict = false) {}

/**
 * (PHP 5)<br/>
 * Returns an array of all supported encodings
 * @link http://php.net/manual/en/function.mb-list-encodings.php
 * @return array a numerically indexed array.
 * @jms-builtin
 */
function mb_list_encodings () {}

/**
 * (PHP 5 &gt;= 5.3.0)<br/>
 * Get aliases of a known encoding type
 * @link http://php.net/manual/en/function.mb-encoding-aliases.php
 * @param string $encoding <p>
 * The encoding type being checked, for aliases.
 * </p>
 * @return array a numerically indexed array of encoding aliases on success,
 * or <b>FALSE</b> on failure
 * @jms-builtin
 */
function mb_encoding_aliases ($encoding) {}

/**
 * (PHP 4 &gt;= 4.0.6, PHP 5)<br/>
 * Convert "kana" one from another ("zen-kaku", "han-kaku" and more)
 * @link http://php.net/manual/en/function.mb-convert-kana.php
 * @param string $str <p>
 * The string being converted.
 * </p>
 * @param string $option [optional] <p>
 * The conversion option.
 * </p>
 * <p>
 * Specify with a combination of following options.
 * <table>
 * Applicable Conversion Options
 * <tr valign="top">
 * <td>Option</td>
 * <td>Meaning</td>
 * </tr>
 * <tr valign="top">
 * <td>r</td>
 * <td>
 * Convert "zen-kaku" alphabets to "han-kaku"
 * </td>
 * </tr>
 * <tr valign="top">
 * <td>R</td>
 * <td>
 * Convert "han-kaku" alphabets to "zen-kaku"
 * </td>
 * </tr>
 * <tr valign="top">
 * <td>n</td>
 * <td>
 * Convert "zen-kaku" numbers to "han-kaku"
 * </td>
 * </tr>
 * <tr valign="top">
 * <td>N</td>
 * <td>
 * Convert "han-kaku" numbers to "zen-kaku"
 * </td>
 * </tr>
 * <tr valign="top">
 * <td>a</td>
 * <td>
 * Convert "zen-kaku" alphabets and numbers to "han-kaku"
 * </td>
 * </tr>
 * <tr valign="top">
 * <td>A</td>
 * <td>
 * Convert "han-kaku" alphabets and numbers to "zen-kaku"
 * (Characters included in "a", "A" options are
 * U+0021 - U+007E excluding U+0022, U+0027, U+005C, U+007E)
 * </td>
 * </tr>
 * <tr valign="top">
 * <td>s</td>
 * <td>
 * Convert "zen-kaku" space to "han-kaku" (U+3000 -> U+0020)
 * </td>
 * </tr>
 * <tr valign="top">
 * <td>S</td>
 * <td>
 * Convert "han-kaku" space to "zen-kaku" (U+0020 -> U+3000)
 * </td>
 * </tr>
 * <tr valign="top">
 * <td>k</td>
 * <td>
 * Convert "zen-kaku kata-kana" to "han-kaku kata-kana"
 * </td>
 * </tr>
 * <tr valign="top">
 * <td>K</td>
 * <td>
 * Convert "han-kaku kata-kana" to "zen-kaku kata-kana"
 * </td>
 * </tr>
 * <tr valign="top">
 * <td>h</td>
 * <td>
 * Convert "zen-kaku hira-gana" to "han-kaku kata-kana"
 * </td>
 * </tr>
 * <tr valign="top">
 * <td>H</td>
 * <td>
 * Convert "han-kaku kata-kana" to "zen-kaku hira-gana"
 * </td>
 * </tr>
 * <tr valign="top">
 * <td>c</td>
 * <td>
 * Convert "zen-kaku kata-kana" to "zen-kaku hira-gana"
 * </td>
 * </tr>
 * <tr valign="top">
 * <td>C</td>
 * <td>
 * Convert "zen-kaku hira-gana" to "zen-kaku kata-kana"
 * </td>
 * </tr>
 * <tr valign="top">
 * <td>V</td>
 * <td>
 * Collapse voiced sound notation and convert them into a character. Use with "K","H"
 * </td>
 * </tr>
 * </table>
 * </p>
 * @param string $encoding [optional] The <i>encoding</i>
 * parameter is the character encoding. If it is omitted, the internal character
 * encoding value will be used.</p>
 * @return string The converted string.
 * @jms-builtin
 */
function mb_convert_kana ($str, $option = "KV", $encoding = null) {}

/**
 * (PHP 4 &gt;= 4.0.6, PHP 5)<br/>
 * Encode string for MIME header
 * @link http://php.net/manual/en/function.mb-encode-mimeheader.php
 * @param string $str <p>
 * The string being encoded.
 * </p>
 * @param string $charset [optional] <p>
 * <i>charset</i> specifies the name of the character set
 * in which <i>str</i> is represented in. The default value
 * is determined by the current NLS setting (mbstring.language).
 * <b>mb_internal_encoding</b> should be set to same encoding.
 * </p>
 * @param string $transfer_encoding [optional] <p>
 * <i>transfer_encoding</i> specifies the scheme of MIME
 * encoding. It should be either "B" (Base64) or
 * "Q" (Quoted-Printable). Falls back to
 * "B" if not given.
 * </p>
 * @param string $linefeed [optional] <p>
 * <i>linefeed</i> specifies the EOL (end-of-line) marker
 * with which <b>mb_encode_mimeheader</b> performs
 * line-folding (a RFC term,
 * the act of breaking a line longer than a certain length into multiple
 * lines. The length is currently hard-coded to 74 characters).
 * Falls back to "\r\n" (CRLF) if not given.
 * </p>
 * @param int $indent [optional] <p>
 * Indentation of the first line (number of characters in the header
 * before <i>str</i>).
 * </p>
 * @return string A converted version of the string represented in ASCII.
 * @jms-builtin
 */
function mb_encode_mimeheader ($str, $charset = null, $transfer_encoding = null, $linefeed = '&quot;\r\n&quot;', $indent = 0) {}

/**
 * (PHP 4 &gt;= 4.0.6, PHP 5)<br/>
 * Decode string in MIME header field
 * @link http://php.net/manual/en/function.mb-decode-mimeheader.php
 * @param string $str <p>
 * The string being decoded.
 * </p>
 * @return string The decoded string in internal character encoding.
 * @jms-builtin
 */
function mb_decode_mimeheader ($str) {}

/**
 * (PHP 4 &gt;= 4.0.6, PHP 5)<br/>
 * Convert character code in variable(s)
 * @link http://php.net/manual/en/function.mb-convert-variables.php
 * @param string $to_encoding <p>
 * The encoding that the string is being converted to.
 * </p>
 * @param mixed $from_encoding <p>
 * <i>from_encoding</i> is specified as an array
 * or comma separated string, it tries to detect encoding from
 * <i>from-coding</i>. When <i>from_encoding</i>
 * is omitted, detect_order is used.
 * </p>
 * @param mixed $vars <p>
 * <i>vars</i> is the reference to the
 * variable being converted. String, Array and Object are accepted.
 * <b>mb_convert_variables</b> assumes all parameters
 * have the same encoding.
 * </p>
 * @param mixed $_ [optional] <p>
 * Additional <i>vars</i>.
 * </p>
 * @return string The character encoding before conversion for success,
 * or <b>FALSE</b> for failure.
 * @jms-builtin
 */
function mb_convert_variables ($to_encoding, $from_encoding, &$vars, &$_ = null) {}

/**
 * (PHP 4 &gt;= 4.0.6, PHP 5)<br/>
 * Encode character to HTML numeric string reference
 * @link http://php.net/manual/en/function.mb-encode-numericentity.php
 * @param string $str <p>
 * The string being encoded.
 * </p>
 * @param array $convmap <p>
 * <i>convmap</i> is array specifies code area to
 * convert.
 * </p>
 * @param string $encoding The <i>encoding</i>
 * parameter is the character encoding. If it is omitted, the internal character
 * encoding value will be used.</p>
 * @return string The converted string.
 * @jms-builtin
 */
function mb_encode_numericentity ($str, array $convmap, $encoding) {}

/**
 * (PHP 4 &gt;= 4.0.6, PHP 5)<br/>
 * Decode HTML numeric string reference to character
 * @link http://php.net/manual/en/function.mb-decode-numericentity.php
 * @param string $str <p>
 * The string being decoded.
 * </p>
 * @param array $convmap <p>
 * <i>convmap</i> is an array that specifies
 * the code area to convert.
 * </p>
 * @param string $encoding The <i>encoding</i>
 * parameter is the character encoding. If it is omitted, the internal character
 * encoding value will be used.</p>
 * @return string The converted string.
 * @jms-builtin
 */
function mb_decode_numericentity ($str, array $convmap, $encoding) {}

/**
 * (PHP 4 &gt;= 4.0.6, PHP 5)<br/>
 * Send encoded mail
 * @link http://php.net/manual/en/function.mb-send-mail.php
 * @param string $to <p>
 * The mail addresses being sent to. Multiple
 * recipients may be specified by putting a comma between each
 * address in <i>to</i>.
 * This parameter is not automatically encoded.
 * </p>
 * @param string $subject <p>
 * The subject of the mail.
 * </p>
 * @param string $message <p>
 * The message of the mail.
 * </p>
 * @param string $additional_headers [optional] <p>
 * <i>additional_headers</i> is inserted at
 * the end of the header. This is typically used to add extra
 * headers. Multiple extra headers are separated with a
 * newline ("\n").
 * </p>
 * @param string $additional_parameter [optional] <p>
 * <i>additional_parameter</i> is a MTA command line
 * parameter. It is useful when setting the correct Return-Path
 * header when using sendmail.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function mb_send_mail ($to, $subject, $message, $additional_headers = null, $additional_parameter = null) {}

/**
 * (PHP 4 &gt;= 4.2.0, PHP 5)<br/>
 * Get internal settings of mbstring
 * @link http://php.net/manual/en/function.mb-get-info.php
 * @param string $type [optional] <p>
 * If <i>type</i> isn't specified or is specified to
 * "all", an array having the elements "internal_encoding",
 * "http_output", "http_input", "func_overload", "mail_charset",
 * "mail_header_encoding", "mail_body_encoding" will be returned.
 * </p>
 * <p>
 * If <i>type</i> is specified as "http_output",
 * "http_input", "internal_encoding", "func_overload",
 * the specified setting parameter will be returned.
 * </p>
 * @return mixed An array of type information if <i>type</i>
 * is not specified, otherwise a specific <i>type</i>.
 * @jms-builtin
 */
function mb_get_info ($type = "all") {}

/**
 * (PHP 4 &gt;= 4.4.3, PHP 5 &gt;= 5.1.3)<br/>
 * Check if the string is valid for the specified encoding
 * @link http://php.net/manual/en/function.mb-check-encoding.php
 * @param string $var [optional] <p>
 * The byte stream to check. If it is omitted, this function checks
 * all the input from the beginning of the request.
 * </p>
 * @param string $encoding [optional] <p>
 * The expected encoding.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function mb_check_encoding ($var = null, $encoding = 'mb_internal_encoding()') {}

/**
 * (PHP 4 &gt;= 4.2.0, PHP 5)<br/>
 * Returns current encoding for multibyte regex as string
 * @link http://php.net/manual/en/function.mb-regex-encoding.php
 * @param string $encoding [optional] The <i>encoding</i>
 * parameter is the character encoding. If it is omitted, the internal character
 * encoding value will be used.</p>
 * @return mixed
 * @jms-builtin
 */
function mb_regex_encoding ($encoding = null) {}

/**
 * (PHP 4 &gt;= 4.3.0, PHP 5)<br/>
 * Set/Get the default options for mbregex functions
 * @link http://php.net/manual/en/function.mb-regex-set-options.php
 * @param string $options [optional] <p>
 * The options to set. This is a a string where each
 * character is an option. To set a mode, the mode
 * character must be the last one set, however there
 * can only be set one mode but multiple options.
 * </p>
 * <table>
 * Regex options
 * <tr valign="top">
 * <td>Option</td>
 * <td>Meaning</td>
 * </tr>
 * <tr valign="top">
 * <td>i</td>
 * <td>Ambiguity match on</td>
 * </tr>
 * <tr valign="top">
 * <td>x</td>
 * <td>Enables extended pattern form</td>
 * </tr>
 * <tr valign="top">
 * <td>m</td>
 * <td>'.' matches with newlines</td>
 * </tr>
 * <tr valign="top">
 * <td>s</td>
 * <td>'^' -> '\A', '$' -> '\Z'</td>
 * </tr>
 * <tr valign="top">
 * <td>p</td>
 * <td>Same as both the m and s options</td>
 * </tr>
 * <tr valign="top">
 * <td>l</td>
 * <td>Finds longest matches</td>
 * </tr>
 * <tr valign="top">
 * <td>n</td>
 * <td>Ignores empty matches</td>
 * </tr>
 * <tr valign="top">
 * <td>e</td>
 * <td><b>eval</b> resulting code</td>
 * </tr>
 * </table>
 * <table>
 * Regex syntax modes
 * <tr valign="top">
 * <td>Mode</td>
 * <td>Meaning</td>
 * </tr>
 * <tr valign="top">
 * <td>j</td>
 * <td>Java (Sun java.util.regex)</td>
 * </tr>
 * <tr valign="top">
 * <td>u</td>
 * <td>GNU regex</td>
 * </tr>
 * <tr valign="top">
 * <td>g</td>
 * <td>grep</td>
 * </tr>
 * <tr valign="top">
 * <td>c</td>
 * <td>Emacs</td>
 * </tr>
 * <tr valign="top">
 * <td>r</td>
 * <td>Ruby</td>
 * </tr>
 * <tr valign="top">
 * <td>z</td>
 * <td>Perl</td>
 * </tr>
 * <tr valign="top">
 * <td>b</td>
 * <td>POSIX Basic regex</td>
 * </tr>
 * <tr valign="top">
 * <td>d</td>
 * <td>POSIX Extended regex</td>
 * </tr>
 * </table>
 * @return string The previous options. If <i>options</i> is omitted,
 * it returns the string that describes the current options.
 * @jms-builtin
 */
function mb_regex_set_options ($options = "msr") {}

/**
 * (PHP 4 &gt;= 4.2.0, PHP 5)<br/>
 * Regular expression match with multibyte support
 * @link http://php.net/manual/en/function.mb-ereg.php
 * @param string $pattern <p>
 * The search pattern.
 * </p>
 * @param string $string <p>
 * The search string.
 * </p>
 * @param array $regs [optional] <p>
 * Contains a substring of the matched string.
 * </p>
 * @return int
 * @jms-builtin
 */
function mb_ereg ($pattern, $string, array $regs = null) {}

/**
 * (PHP 4 &gt;= 4.2.0, PHP 5)<br/>
 * Regular expression match ignoring case with multibyte support
 * @link http://php.net/manual/en/function.mb-eregi.php
 * @param string $pattern <p>
 * The regular expression pattern.
 * </p>
 * @param string $string <p>
 * The string being searched.
 * </p>
 * @param array $regs [optional] <p>
 * Contains a substring of the matched string.
 * </p>
 * @return int
 * @jms-builtin
 */
function mb_eregi ($pattern, $string, array $regs = null) {}

/**
 * (PHP 4 &gt;= 4.2.0, PHP 5)<br/>
 * Replace regular expression with multibyte support
 * @link http://php.net/manual/en/function.mb-ereg-replace.php
 * @param string $pattern <p>
 * The regular expression pattern.
 * </p>
 * <p>
 * Multibyte characters may be used in <i>pattern</i>.
 * </p>
 * @param string $replacement <p>
 * The replacement text.
 * </p>
 * @param string $string <p>
 * The string being checked.
 * </p>
 * @param string $option [optional] Matching condition can be set by <i>option</i>
 * parameter. If i is specified for this
 * parameter, the case will be ignored. If x is
 * specified, white space will be ignored. If m
 * is specified, match will be executed in multiline mode and line
 * break will be included in '.'. If p is
 * specified, match will be executed in POSIX mode, line break
 * will be considered as normal character. If e
 * is specified, <i>replacement</i> string will be
 * evaluated as PHP expression.
 * @return string The resultant string on success, or <b>FALSE</b> on error.
 * @jms-builtin
 */
function mb_ereg_replace ($pattern, $replacement, $string, $option = "msr") {}

/**
 * (PHP 4 &gt;= 4.2.0, PHP 5)<br/>
 * Replace regular expression with multibyte support ignoring case
 * @link http://php.net/manual/en/function.mb-eregi-replace.php
 * @param string $pattern <p>
 * The regular expression pattern. Multibyte characters may be used. The case will be ignored.
 * </p>
 * @param string $replace <p>
 * The replacement text.
 * </p>
 * @param string $string <p>
 * The searched string.
 * </p>
 * @param string $option [optional] <i>option</i> has the same meaning as in
 * <b>mb_ereg_replace</b>.
 * @return string The resultant string or <b>FALSE</b> on error.
 * @jms-builtin
 */
function mb_eregi_replace ($pattern, $replace, $string, $option = "msri") {}

/**
 * (No version information available, might only be in SVN)<br/>
 * Perform a regular expresssion seach and replace with multibyte support using a callback
 * @link http://php.net/manual/en/function.mb-ereg-replace-callback.php
 * @param string $pattern <p>
 * The regular expression pattern.
 * </p>
 * <p>
 * Multibyte characters may be used in <i>pattern</i>.
 * </p>
 * @param callable $callback <p>
 * A callback that will be called and passed an array of matched elements
 * in the <i>subject</i> string. The callback should
 * return the replacement string.
 * </p>
 * <p>
 * You'll often need the <i>callback</i> function
 * for a <b>mb_ereg_replace_callback</b> in just one place.
 * In this case you can use an
 * anonymous function (since
 * PHP 5.3.0) or <b>create_function</b> to
 * declare an anonymous function as callback within the call to
 * <b>mb_ereg_replace_callback</b>. By doing it this way
 * you have all information for the call in one place and do not
 * clutter the function namespace with a callback function's name
 * not used anywhere else.
 * </p>
 * @param string $string <p>
 * The string being checked.
 * </p>
 * @param string $option [optional] <p>
 * Matching condition can be set by <i>option</i>
 * parameter. If i is specified for this
 * parameter, the case will be ignored. If x is
 * specified, white space will be ignored. If m
 * is specified, match will be executed in multiline mode and line
 * break will be included in '.'. If p is
 * specified, match will be executed in POSIX mode, line break
 * will be considered as normal character. Note that e
 * cannot be used for <b>mb_ereg_replace_callback</b>.
 * </p>
 * @return string The resultant string on success, or <b>FALSE</b> on error.
 * @jms-builtin
 */
function mb_ereg_replace_callback ($pattern, callable $callback, $string, $option = "msr") {}

/**
 * (PHP 4 &gt;= 4.2.0, PHP 5)<br/>
 * Split multibyte string using regular expression
 * @link http://php.net/manual/en/function.mb-split.php
 * @param string $pattern <p>
 * The regular expression pattern.
 * </p>
 * @param string $string <p>
 * The string being split.
 * </p>
 * @param int $limit [optional] If optional parameter <i>limit</i> is specified,
 * it will be split in <i>limit</i> elements as
 * maximum.
 * @return array The result as an array.
 * @jms-builtin
 */
function mb_split ($pattern, $string, $limit = -1) {}

/**
 * (PHP 4 &gt;= 4.2.0, PHP 5)<br/>
 * Regular expression match for multibyte string
 * @link http://php.net/manual/en/function.mb-ereg-match.php
 * @param string $pattern <p>
 * The regular expression pattern.
 * </p>
 * @param string $string <p>
 * The string being evaluated.
 * </p>
 * @param string $option [optional] <p>
 * </p>
 * @return bool
 * @jms-builtin
 */
function mb_ereg_match ($pattern, $string, $option = "msr") {}

/**
 * (PHP 4 &gt;= 4.2.0, PHP 5)<br/>
 * Multibyte regular expression match for predefined multibyte string
 * @link http://php.net/manual/en/function.mb-ereg-search.php
 * @param string $pattern [optional] <p>
 * The search pattern.
 * </p>
 * @param string $option [optional] <p>
 * The search option.
 * </p>
 * @return bool
 * @jms-builtin
 */
function mb_ereg_search ($pattern = null, $option = "ms") {}

/**
 * (PHP 4 &gt;= 4.2.0, PHP 5)<br/>
 * Returns position and length of a matched part of the multibyte regular expression for a predefined multibyte string
 * @link http://php.net/manual/en/function.mb-ereg-search-pos.php
 * @param string $pattern [optional] <p>
 * The search pattern.
 * </p>
 * @param string $option [optional] <p>
 * The search option.
 * </p>
 * @return array
 * @jms-builtin
 */
function mb_ereg_search_pos ($pattern = null, $option = "ms") {}

/**
 * (PHP 4 &gt;= 4.2.0, PHP 5)<br/>
 * Returns the matched part of a multibyte regular expression
 * @link http://php.net/manual/en/function.mb-ereg-search-regs.php
 * @param string $pattern [optional] <p>
 * The search pattern.
 * </p>
 * @param string $option [optional] <p>
 * The search option.
 * </p>
 * @return array
 * @jms-builtin
 */
function mb_ereg_search_regs ($pattern = null, $option = "ms") {}

/**
 * (PHP 4 &gt;= 4.2.0, PHP 5)<br/>
 * Setup string and regular expression for a multibyte regular expression match
 * @link http://php.net/manual/en/function.mb-ereg-search-init.php
 * @param string $string <p>
 * The search string.
 * </p>
 * @param string $pattern [optional] <p>
 * The search pattern.
 * </p>
 * @param string $option [optional] <p>
 * The search option.
 * </p>
 * @return bool
 * @jms-builtin
 */
function mb_ereg_search_init ($string, $pattern = null, $option = "msr") {}

/**
 * (PHP 4 &gt;= 4.2.0, PHP 5)<br/>
 * Retrieve the result from the last multibyte regular expression match
 * @link http://php.net/manual/en/function.mb-ereg-search-getregs.php
 * @return array
 * @jms-builtin
 */
function mb_ereg_search_getregs () {}

/**
 * (PHP 4 &gt;= 4.2.0, PHP 5)<br/>
 * Returns start point for next regular expression match
 * @link http://php.net/manual/en/function.mb-ereg-search-getpos.php
 * @return int
 * @jms-builtin
 */
function mb_ereg_search_getpos () {}

/**
 * (PHP 4 &gt;= 4.2.0, PHP 5)<br/>
 * Set start point of next regular expression match
 * @link http://php.net/manual/en/function.mb-ereg-search-setpos.php
 * @param int $position <p>
 * The position to set.
 * </p>
 * @return bool
 * @jms-builtin
 */
function mb_ereg_search_setpos ($position) {}

/**
 * @param $encoding [optional]
 * @jms-builtin
 */
function mbregex_encoding ($encoding) {}

/**
 * @param $pattern
 * @param $string
 * @param $registers [optional]
 * @jms-builtin
 */
function mbereg ($pattern, $string, &$registers) {}

/**
 * @param $pattern
 * @param $string
 * @param $registers [optional]
 * @jms-builtin
 */
function mberegi ($pattern, $string, &$registers) {}

/**
 * @param $pattern
 * @param $replacement
 * @param $string
 * @param $option [optional]
 * @jms-builtin
 */
function mbereg_replace ($pattern, $replacement, $string, $option) {}

/**
 * @param $pattern
 * @param $replacement
 * @param $string
 * @jms-builtin
 */
function mberegi_replace ($pattern, $replacement, $string) {}

/**
 * @param $pattern
 * @param $string
 * @param $limit [optional]
 * @jms-builtin
 */
function mbsplit ($pattern, $string, $limit) {}

/**
 * @param $pattern
 * @param $string
 * @param $option [optional]
 * @jms-builtin
 */
function mbereg_match ($pattern, $string, $option) {}

/**
 * @param $pattern [optional]
 * @param $option [optional]
 * @jms-builtin
 */
function mbereg_search ($pattern, $option) {}

/**
 * @param $pattern [optional]
 * @param $option [optional]
 * @jms-builtin
 */
function mbereg_search_pos ($pattern, $option) {}

/**
 * @param $pattern [optional]
 * @param $option [optional]
 * @jms-builtin
 */
function mbereg_search_regs ($pattern, $option) {}

/**
 * @param $string
 * @param $pattern [optional]
 * @param $option [optional]
 * @jms-builtin
 */
function mbereg_search_init ($string, $pattern, $option) {} 

/** @jms-builtin */
function mbereg_search_getregs () {} 

/** @jms-builtin */
function mbereg_search_getpos () {}

/**
 * @param $position
 * @jms-builtin
 */
function mbereg_search_setpos ($position) {}

define ('MB_OVERLOAD_MAIL', 1);
define ('MB_OVERLOAD_STRING', 2);
define ('MB_OVERLOAD_REGEX', 4);
define ('MB_CASE_UPPER', 0);
define ('MB_CASE_LOWER', 1);
define ('MB_CASE_TITLE', 2);

// End of mbstring v.
?>
