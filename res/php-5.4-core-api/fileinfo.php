<?php

// Start of fileinfo v.1.0.5

/** @jms-builtin */
class finfo  {

	/**
	 * @param $options [optional]
	 * @param $arg [optional]
	 */
	public function finfo ($options, $arg) {}

	/**
	 * @param $options
	 */
	public function set_flags ($options) {}

	/**
	 * @param $filename
	 * @param $options [optional]
	 * @param $context [optional]
	 */
	public function file ($filename, $options, $context) {}

	/**
	 * @param $string
	 * @param $options [optional]
	 * @param $context [optional]
	 */
	public function buffer ($string, $options, $context) {}

}

/**
 * (PHP &gt;= 5.3.0, PECL fileinfo &gt;= 0.1.0)<br/>
 * Create a new fileinfo resource
 * @link http://php.net/manual/en/function.finfo-open.php
 * @param $options [optional]
 * @param $arg [optional]
 * @return mixed (Procedural style only)
 * Returns a magic database resource on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function finfo_open ($options, $arg) {}

/**
 * (PHP &gt;= 5.3.0, PECL fileinfo &gt;= 0.1.0)<br/>
 * Close fileinfo resource
 * @link http://php.net/manual/en/function.finfo-close.php
 * @param $finfo
 * @return mixed <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function finfo_close ($finfo) {}

/**
 * (PHP &gt;= 5.3.0, PECL fileinfo &gt;= 0.1.0)<br/>
 * Set libmagic configuration options
 * @link http://php.net/manual/en/function.finfo-set-flags.php
 * @param $finfo
 * @param $options
 * @return mixed <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function finfo_set_flags ($finfo, $options) {}

/**
 * (PHP &gt;= 5.3.0, PECL fileinfo &gt;= 0.1.0)<br/>
 * Return information about a file
 * @link http://php.net/manual/en/function.finfo-file.php
 * @param $finfo
 * @param $filename
 * @param $options [optional]
 * @param $context [optional]
 * @return mixed a textual description of the contents of the
 * <i>filename</i> argument, or <b>FALSE</b> if an error occurred.
 * @jms-builtin
 */
function finfo_file ($finfo, $filename, $options, $context) {}

/**
 * (PHP 5 &gt;= 5.3.0, PECL fileinfo &gt;= 0.1.0)<br/>
 * Return information about a string buffer
 * @link http://php.net/manual/en/function.finfo-buffer.php
 * @param $finfo
 * @param $string
 * @param $options [optional]
 * @param $context [optional]
 * @return mixed a textual description of the <i>string</i>
 * argument, or <b>FALSE</b> if an error occurred.
 * @jms-builtin
 */
function finfo_buffer ($finfo, $string, $options, $context) {}

/**
 * (PHP 4 &gt;= 4.3.0, PHP 5)<br/>
 * Detect MIME Content-type for a file (deprecated)
 * @link http://php.net/manual/en/function.mime-content-type.php
 * @param string $filename <p>
 * Path to the tested file.
 * </p>
 * @return string the content type in MIME format, like
 * text/plain or application/octet-stream.
 * @jms-builtin
 */
function mime_content_type ($filename) {}


/**
 * No special handling.
 * @link http://php.net/manual/en/fileinfo.constants.php
 */
define ('FILEINFO_NONE', 0);

/**
 * Follow symlinks.
 * @link http://php.net/manual/en/fileinfo.constants.php
 */
define ('FILEINFO_SYMLINK', 2);

/**
 * Return the mime type and mime encoding as defined by RFC 2045.
 * @link http://php.net/manual/en/fileinfo.constants.php
 */
define ('FILEINFO_MIME', 1040);

/**
 * Return the mime type.
 * Available since PHP 5.3.0.
 * @link http://php.net/manual/en/fileinfo.constants.php
 */
define ('FILEINFO_MIME_TYPE', 16);

/**
 * Return the mime encoding of the file.
 * Available since PHP 5.3.0.
 * @link http://php.net/manual/en/fileinfo.constants.php
 */
define ('FILEINFO_MIME_ENCODING', 1024);

/**
 * Look at the contents of blocks or character special devices.
 * @link http://php.net/manual/en/fileinfo.constants.php
 */
define ('FILEINFO_DEVICES', 8);

/**
 * Return all matches, not just the first.
 * @link http://php.net/manual/en/fileinfo.constants.php
 */
define ('FILEINFO_CONTINUE', 32);

/**
 * If possible preserve the original access time.
 * @link http://php.net/manual/en/fileinfo.constants.php
 */
define ('FILEINFO_PRESERVE_ATIME', 128);

/**
 * Don't translate unprintable characters to a \ooo octal
 * representation.
 * @link http://php.net/manual/en/fileinfo.constants.php
 */
define ('FILEINFO_RAW', 256);

// End of fileinfo v.1.0.5
?>
