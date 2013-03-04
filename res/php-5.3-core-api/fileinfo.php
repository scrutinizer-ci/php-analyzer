<?php

// Start of fileinfo v.1.0.5-dev

/** @jms-builtin */
class finfo  {

	/**
	 * @param options[optional]
	 * @param arg[optional]
	 */
	public function finfo ($options, $arg) {}

	/**
	 * @param options
	 */
	public function set_flags ($options) {}

	/**
	 * @param filename
	 * @param options[optional]
	 * @param context[optional]
	 */
	public function file ($filename, $options, $context) {}

	/**
	 * @param string
	 * @param options[optional]
	 * @param context[optional]
	 */
	public function buffer ($string, $options, $context) {}

}

/**
 * Create a new fileinfo resource
 * @link http://www.php.net/manual/en/function.finfo-open.php
 * @param options int[optional] <p>
 * One or disjunction of more Fileinfo
 * constants.
 * </p>
 * @param magic_file string[optional] <p>
 * Name of a magic database file, usually something like
 * /path/to/magic.mime. If not specified,
 * the MAGIC environment variable is used. If this variable
 * is not set either, /usr/share/misc/magic is used by default.
 * A .mime and/or .mgc suffix is added if
 * needed.
 * </p>
 * <p>
 * Passing NULL or an empty string will be equivalent to the default value.
 * </p>
 *
 * @jms-builtin
 */
function finfo_open ($options = null, $magic_file = null) {}

/**
 * Close fileinfo resource
 * @link http://www.php.net/manual/en/function.finfo-close.php
 * @param finfo resource <p>
 * Fileinfo resource returned by finfo_open.
 * </p>
 * @return bool Returns true on success or false on failure.
 *
 * @jms-builtin
 */
function finfo_close ($finfo) {}

/**
 * Set libmagic configuration options
 * @link http://www.php.net/manual/en/function.finfo-set-flags.php
 * @param options int <p>
 * One or disjunction of more Fileinfo
 * constants.
 * </p>
 * @return bool Returns true on success or false on failure.
 *
 * @jms-builtin
 */
function finfo_set_flags ($options) {}

/**
 * Return information about a file
 * @link http://www.php.net/manual/en/function.finfo-file.php
 * @param file_name string <p>
 * Name of a file to be checked.
 * </p>
 * @param options int[optional] <p>
 * One or disjunction of more Fileinfo
 * constants.
 * </p>
 * @param context resource[optional] <p>
 * For a description of contexts, refer to .
 * </p>
 * @return string a textual description of the contents of the
 * filename argument, or false if an error occurred.
 *
 * @jms-builtin
 */
function finfo_file ($file_name, $options = null, $context = null) {}

/**
 * Return information about a string buffer
 * @link http://www.php.net/manual/en/function.finfo-buffer.php
 * @param string string <p>
 * Content of a file to be checked.
 * </p>
 * @param options int[optional] <p>
 * One or disjunction of more Fileinfo
 * constants.
 * </p>
 * @param context resource[optional] <p>
 * </p>
 * @return string a textual description of the string
 * argument, or false if an error occurred.
 *
 * @jms-builtin
 */
function finfo_buffer ($string, $options = null, $context = null) {}

/**
 * Detect MIME Content-type for a file (deprecated)
 * @link http://www.php.net/manual/en/function.mime-content-type.php
 * @param filename string <p>
 * Path to the tested file.
 * </p>
 * @return string the content type in MIME format, like 
 * text/plain or application/octet-stream.
 *
 * @jms-builtin
 */
function mime_content_type ($filename) {}


/**
 * No special handling.
 * @link http://www.php.net/manual/en/fileinfo.constants.php
 */
define ('FILEINFO_NONE', 0);

/**
 * Follow symlinks.
 * @link http://www.php.net/manual/en/fileinfo.constants.php
 */
define ('FILEINFO_SYMLINK', 2);

/**
 * Return the mime type and mime encoding as defined by RFC 2045.
 * @link http://www.php.net/manual/en/fileinfo.constants.php
 */
define ('FILEINFO_MIME', 1040);

/**
 * Return the mime type.
 * Available since PHP 5.3.0.
 * @link http://www.php.net/manual/en/fileinfo.constants.php
 */
define ('FILEINFO_MIME_TYPE', 16);

/**
 * Return the mime encoding of the file.
 * Available since PHP 5.3.0.
 * @link http://www.php.net/manual/en/fileinfo.constants.php
 */
define ('FILEINFO_MIME_ENCODING', 1024);

/**
 * Look at the contents of blocks or character special devices.
 * @link http://www.php.net/manual/en/fileinfo.constants.php
 */
define ('FILEINFO_DEVICES', 8);

/**
 * Return all matches, not just the first.
 * @link http://www.php.net/manual/en/fileinfo.constants.php
 */
define ('FILEINFO_CONTINUE', 32);

/**
 * If possible preserve the original access time.
 * @link http://www.php.net/manual/en/fileinfo.constants.php
 */
define ('FILEINFO_PRESERVE_ATIME', 128);

/**
 * Don't translate unprintable characters to a \ooo octal
 * representation.
 * @link http://www.php.net/manual/en/fileinfo.constants.php
 */
define ('FILEINFO_RAW', 256);

// End of fileinfo v.1.0.5-dev
?>
