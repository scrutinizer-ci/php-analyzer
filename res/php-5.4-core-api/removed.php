<?php

/**
 * (PHP 4 &gt;= 4.0.5, PHP 5)<br/>
 * Convert character encoding as output buffer handler
 * @link http://php.net/manual/en/function.ob-iconv-handler.php
 * @param string $contents
 * @param int $status
 * @return string See <b>ob_start</b> for information about this handler
 * return values.
 * @jms-builtin
 */
function ob_iconv_handler ($contents, $status) {}

/**
 * (PHP 5)<br/>
 * ob_start callback function to repair the buffer
 * @link http://php.net/manual/en/function.ob-tidyhandler.php
 * @param string $input <p>
 * The buffer.
 * </p>
 * @param int $mode [optional] <p>
 * The buffer mode.
 * </p>
 * @return string the modified buffer.
 * @jms-builtin
 */
function ob_tidyhandler ($input, $mode = null) {}

/**
 * (PHP 4, PHP 5 &lt; 5.4.0)<br/>
 * Register one or more global variables with the current session
 * @link http://php.net/manual/en/function.session-register.php
 * @param mixed $name <p>
 * A string holding the name of a variable or an array consisting of
 * variable names or other arrays.
 * </p>
 * @param mixed $_ [optional]
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function session_register ($name, $_ = null) {}

/**
 * (PHP 4, PHP 5 &lt; 5.4.0)<br/>
 * Unregister a global variable from the current session
 * @link http://php.net/manual/en/function.session-unregister.php
 * @param string $name <p>
 * The variable name.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function session_unregister ($name) {}

/**
 * (PHP 4, PHP 5 &lt; 5.4.0)<br/>
 * Find out whether a global variable is registered in a session
 * @link http://php.net/manual/en/function.session-is-registered.php
 * @param string $name <p>
 * The variable name.
 * </p>
 * @return bool <b>session_is_registered</b> returns <b>TRUE</b> if there is a
 * global variable with the name <i>name</i> registered in
 * the current session, <b>FALSE</b> otherwise.
 * @jms-builtin
 */
function session_is_registered ($name) {}

/**
 * (PHP 4 &gt;= 4.0.5, PHP 5)<br/>
 * Change the root directory
 * @link http://php.net/manual/en/function.chroot.php
 * @param string $directory <p>
 * The path to change the root directory to.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function chroot ($directory) {}
?>
