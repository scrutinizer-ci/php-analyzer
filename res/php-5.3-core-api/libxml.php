<?php

// Start of libxml v.

/** @jms-builtin */
class LibXMLError  {
    /** @var string */
    public $message;

    /** @var int */
    public $line;

    /** @var int */
    public $level;

    /** @var string */
    public $file;

    /** @var int */
    public $column;

    /** @var int */
    public $code;
}

/**
 * Set the streams context for the next libxml document load or write
 * @link http://www.php.net/manual/en/function.libxml-set-streams-context.php
 * @param streams_context resource <p>
 * The stream context resource (created with
 * stream_context_create)
 * </p>
 * @return void
 *
 * @jms-builtin
 */
function libxml_set_streams_context ($streams_context) {}

/**
 * Disable libxml errors and allow user to fetch error information as needed
 * @link http://www.php.net/manual/en/function.libxml-use-internal-errors.php
 * @param use_errors bool[optional] <p>
 * Whether to enable user error handling.
 * </p>
 * @return bool This function returns the previous value of
 * use_errors.
 *
 * @jms-builtin
 */
function libxml_use_internal_errors ($use_errors = null) {}

/**
 * Retrieve last error from libxml
 * @link http://www.php.net/manual/en/function.libxml-get-last-error.php
 * @return LibXMLError a LibXMLError object if there is any error in the
 * buffer, false otherwise.
 *
 * @jms-builtin
 */
function libxml_get_last_error () {}

/**
 * Clear libxml error buffer
 * @link http://www.php.net/manual/en/function.libxml-clear-errors.php
 * @return void
 *
 * @jms-builtin
 */
function libxml_clear_errors () {}

/**
 * Retrieve array of errors
 * @link http://www.php.net/manual/en/function.libxml-get-errors.php
 * @return array an array with LibXMLError objects if there are any
 * errors in the buffer, or an empty array otherwise.
 *
 * @jms-builtin
 */
function libxml_get_errors () {}

/**
 * Disable the ability to load external entities
 * @link http://www.php.net/manual/en/function.libxml-disable-entity-loader.php
 * @param disable bool[optional] <p>
 * Disable (true) or enable (false) libxml extensions (such as
 * ,
 * and ) to load external entities.
 * </p>
 * @return ReturnType the previous value.
 *
 * @jms-builtin
 */
function libxml_disable_entity_loader ($disable = null) {}


/**
 * libxml version like 20605 or 20617
 * @link http://www.php.net/manual/en/libxml.constants.php
 */
define ('LIBXML_VERSION', 20632);

/**
 * libxml version like 2.6.5 or 2.6.17
 * @link http://www.php.net/manual/en/libxml.constants.php
 */
define ('LIBXML_DOTTED_VERSION', "2.6.32");
define ('LIBXML_LOADED_VERSION', 20632);

/**
 * Substitute entities
 * @link http://www.php.net/manual/en/libxml.constants.php
 */
define ('LIBXML_NOENT', 2);

/**
 * Load the external subset
 * @link http://www.php.net/manual/en/libxml.constants.php
 */
define ('LIBXML_DTDLOAD', 4);

/**
 * Default DTD attributes
 * @link http://www.php.net/manual/en/libxml.constants.php
 */
define ('LIBXML_DTDATTR', 8);

/**
 * Validate with the DTD
 * @link http://www.php.net/manual/en/libxml.constants.php
 */
define ('LIBXML_DTDVALID', 16);

/**
 * Suppress error reports
 * @link http://www.php.net/manual/en/libxml.constants.php
 */
define ('LIBXML_NOERROR', 32);

/**
 * Suppress warning reports
 * @link http://www.php.net/manual/en/libxml.constants.php
 */
define ('LIBXML_NOWARNING', 64);

/**
 * Remove blank nodes
 * @link http://www.php.net/manual/en/libxml.constants.php
 */
define ('LIBXML_NOBLANKS', 256);

/**
 * Implement XInclude substitution
 * @link http://www.php.net/manual/en/libxml.constants.php
 */
define ('LIBXML_XINCLUDE', 1024);

/**
 * Remove redundant namespaces declarations
 * @link http://www.php.net/manual/en/libxml.constants.php
 */
define ('LIBXML_NSCLEAN', 8192);

/**
 * Merge CDATA as text nodes
 * @link http://www.php.net/manual/en/libxml.constants.php
 */
define ('LIBXML_NOCDATA', 16384);

/**
 * Disable network access when loading documents
 * @link http://www.php.net/manual/en/libxml.constants.php
 */
define ('LIBXML_NONET', 2048);

/**
 * Activate small nodes allocation optimization. This may speed up your
 * application without needing to change the code.
 * <p>
 * Only available in Libxml &gt;= 2.6.21
 * </p>
 * @link http://www.php.net/manual/en/libxml.constants.php
 */
define ('LIBXML_COMPACT', 65536);

/**
 * Drop the XML declaration when saving a document
 * <p>
 * Only available in Libxml &gt;= 2.6.21
 * </p>
 * @link http://www.php.net/manual/en/libxml.constants.php
 */
define ('LIBXML_NOXMLDECL', 2);

/**
 * Expand empty tags (e.g. &lt;br/&gt; to
 * &lt;br&gt;&lt;/br&gt;)
 * <p>
 * This option is currently just available in the
 * and
 * functions.
 * </p>
 * @link http://www.php.net/manual/en/libxml.constants.php
 */
define ('LIBXML_NOEMPTYTAG', 4);

/**
 * No errors
 * @link http://www.php.net/manual/en/libxml.constants.php
 */
define ('LIBXML_ERR_NONE', 0);

/**
 * A simple warning
 * @link http://www.php.net/manual/en/libxml.constants.php
 */
define ('LIBXML_ERR_WARNING', 1);

/**
 * A recoverable error
 * @link http://www.php.net/manual/en/libxml.constants.php
 */
define ('LIBXML_ERR_ERROR', 2);

/**
 * A fatal error
 * @link http://www.php.net/manual/en/libxml.constants.php
 */
define ('LIBXML_ERR_FATAL', 3);

// End of libxml v.
?>
