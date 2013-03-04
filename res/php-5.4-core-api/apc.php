<?php

// Start of apc v.3.1.9

/**
 * (PECL apc &gt;= 2.0.0)<br/>
 * Retrieves cached information from APC's data store
 * @link http://php.net/manual/en/function.apc-cache-info.php
 * @param string $cache_type [optional] <p>
 * If <i>cache_type</i> is "user",
 * information about the user cache will be returned.
 * </p>
 * <p>
 * If <i>cache_type</i> is "filehits",
 * information about which files have been served from the bytecode cache
 * for the current request will be returned. This feature must be enabled at
 * compile time using --enable-filehits.
 * </p>
 * <p>
 * If an invalid or no <i>cache_type</i> is specified, information about
 * the system cache (cached files) will be returned.
 * </p>
 * @param bool $limited [optional] <p>
 * If <i>limited</i> is <b>TRUE</b>, the
 * return value will exclude the individual list of cache entries. This
 * is useful when trying to optimize calls for statistics gathering.
 * </p>
 * @return array Array of cached data (and meta-data) or <b>FALSE</b> on failure
 * @jms-builtin
 */
function apc_cache_info ($cache_type = null, $limited = false) {}

/**
 * (PECL apc &gt;= 2.0.0)<br/>
 * Clears the APC cache
 * @link http://php.net/manual/en/function.apc-clear-cache.php
 * @param string $cache_type [optional] <p>
 * If <i>cache_type</i> is "user", the
 * user cache will be cleared; otherwise, the system cache (cached files)
 * will be cleared.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function apc_clear_cache ($cache_type = null) {}

/**
 * (PECL apc &gt;= 2.0.0)<br/>
 * Retrieves APC's Shared Memory Allocation information
 * @link http://php.net/manual/en/function.apc-sma-info.php
 * @param bool $limited [optional] <p>
 * When set to <b>FALSE</b> (default) <b>apc_sma_info</b> will
 * return a detailed information about each segment.
 * </p>
 * @return array Array of Shared Memory Allocation data; <b>FALSE</b> on failure.
 * @jms-builtin
 */
function apc_sma_info ($limited = false) {}

/**
 * (PECL apc &gt;= 3.0.0)<br/>
 * Cache a variable in the data store
 * @link http://php.net/manual/en/function.apc-store.php
 * @param string $key <p>
 * Store the variable using this name. <i>key</i>s are
 * cache-unique, so storing a second value with the same
 * <i>key</i> will overwrite the original value.
 * </p>
 * @param mixed $var <p>
 * The variable to store
 * </p>
 * @param int $ttl [optional] <p>
 * Time To Live; store <i>var</i> in the cache for
 * <i>ttl</i> seconds. After the
 * <i>ttl</i> has passed, the stored variable will be
 * expunged from the cache (on the next request). If no <i>ttl</i>
 * is supplied (or if the <i>ttl</i> is
 * 0), the value will persist until it is removed from
 * the cache manually, or otherwise fails to exist in the cache (clear,
 * restart, etc.).
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * Second syntax returns array with error keys.
 * @jms-builtin
 */
function apc_store ($key, $var, $ttl = 0) {}

/**
 * (PECL apc &gt;= 3.0.0)<br/>
 * Fetch a stored variable from the cache
 * @link http://php.net/manual/en/function.apc-fetch.php
 * @param mixed $key <p>
 * The <i>key</i> used to store the value (with
 * <b>apc_store</b>). If an array is passed then each
 * element is fetched and returned.
 * </p>
 * @param bool $success [optional] <p>
 * Set to <b>TRUE</b> in success and <b>FALSE</b> in failure.
 * </p>
 * @return mixed The stored variable or array of variables on success; <b>FALSE</b> on failure
 * @jms-builtin
 */
function apc_fetch ($key, &$success = null) {}

/**
 * (PECL apc &gt;= 3.0.0)<br/>
 * Removes a stored variable from the cache
 * @link http://php.net/manual/en/function.apc-delete.php
 * @param string $key <p>
 * The <i>key</i> used to store the value (with
 * <b>apc_store</b>).
 * </p>
 * @return mixed <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function apc_delete ($key) {}

/**
 * (PECL apc &gt;= 3.1.1)<br/>
 * Deletes files from the opcode cache
 * @link http://php.net/manual/en/function.apc-delete-file.php
 * @param mixed $keys <p>
 * The files to be deleted. Accepts a string,
 * array of strings, or an <b>APCIterator</b>
 * object.
 * </p>
 * @return mixed <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * Or if <i>keys</i> is an array, then
 * an empty array is returned on success, or an array of failed files
 * is returned.
 * @jms-builtin
 */
function apc_delete_file ($keys) {}

/**
 * (PECL apc &gt;= 3.0.0)<br/>
 * Defines a set of constants for retrieval and mass-definition
 * @link http://php.net/manual/en/function.apc-define-constants.php
 * @param string $key <p>
 * The <i>key</i> serves as the name of the constant set
 * being stored. This <i>key</i> is used to retrieve the
 * stored constants in <b>apc_load_constants</b>.
 * </p>
 * @param array $constants <p>
 * An associative array of constant_name => value
 * pairs. The constant_name must follow the normal
 * constant naming rules.
 * value must evaluate to a scalar value.
 * </p>
 * @param bool $case_sensitive [optional] <p>
 * The default behaviour for constants is to be declared case-sensitive;
 * i.e. CONSTANT and Constant
 * represent different values. If this parameter evaluates to <b>FALSE</b> the
 * constants will be declared as case-insensitive symbols.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function apc_define_constants ($key, array $constants, $case_sensitive = true) {}

/**
 * (PECL apc &gt;= 3.0.0)<br/>
 * Loads a set of constants from the cache
 * @link http://php.net/manual/en/function.apc-load-constants.php
 * @param string $key <p>
 * The name of the constant set (that was stored with
 * <b>apc_define_constants</b>) to be retrieved.
 * </p>
 * @param bool $case_sensitive [optional] <p>
 * The default behaviour for constants is to be declared case-sensitive;
 * i.e. CONSTANT and Constant
 * represent different values. If this parameter evaluates to <b>FALSE</b> the
 * constants will be declared as case-insensitive symbols.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function apc_load_constants ($key, $case_sensitive = true) {}

/**
 * (PECL apc &gt;= 3.0.13)<br/>
 * Stores a file in the bytecode cache, bypassing all filters.
 * @link http://php.net/manual/en/function.apc-compile-file.php
 * @param string $filename <p>
 * Full or relative path to a PHP file that will be compiled and stored in
 * the bytecode cache.
 * </p>
 * @param bool $atomic [optional]
 * @return mixed <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function apc_compile_file ($filename, $atomic = true) {}

/**
 * (PECL apc &gt;= 3.0.13)<br/>
 * Cache a new variable in the data store
 * @link http://php.net/manual/en/function.apc-add.php
 * @param string $key <p>
 * Store the variable using this name. <i>key</i>s are
 * cache-unique, so attempting to use <b>apc_add</b> to
 * store data with a key that already exists will not overwrite the
 * existing data, and will instead return <b>FALSE</b>. (This is the only
 * difference between <b>apc_add</b> and
 * <b>apc_store</b>.)
 * </p>
 * @param mixed $var [optional] <p>
 * The variable to store
 * </p>
 * @param int $ttl [optional] <p>
 * Time To Live; store <i>var</i> in the cache for
 * <i>ttl</i> seconds. After the
 * <i>ttl</i> has passed, the stored variable will be
 * expunged from the cache (on the next request). If no <i>ttl</i>
 * is supplied (or if the <i>ttl</i> is
 * 0), the value will persist until it is removed from
 * the cache manually, or otherwise fails to exist in the cache (clear,
 * restart, etc.).
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * Second syntax returns array with error keys.
 * @jms-builtin
 */
function apc_add ($key, $var = null, $ttl = 0) {}

/**
 * (PECL apc &gt;= 3.1.1)<br/>
 * Increase a stored number
 * @link http://php.net/manual/en/function.apc-inc.php
 * @param string $key <p>
 * The key of the value being increased.
 * </p>
 * @param int $step [optional] <p>
 * The step, or value to increase.
 * </p>
 * @param bool $success [optional] <p>
 * Optionally pass the success or fail boolean value to
 * this referenced variable.
 * </p>
 * @return int the current value of <i>key</i>'s value on success,
 * or <b>FALSE</b> on failure
 * @jms-builtin
 */
function apc_inc ($key, $step = 1, &$success = null) {}

/**
 * (PECL apc &gt;= 3.1.1)<br/>
 * Decrease a stored number
 * @link http://php.net/manual/en/function.apc-dec.php
 * @param string $key <p>
 * The key of the value being decreased.
 * </p>
 * @param int $step [optional] <p>
 * The step, or value to decrease.
 * </p>
 * @param bool $success [optional] <p>
 * Optionally pass the success or fail boolean value to
 * this referenced variable.
 * </p>
 * @return int the current value of <i>key</i>'s value on success,
 * or <b>FALSE</b> on failure
 * @jms-builtin
 */
function apc_dec ($key, $step = 1, &$success = null) {}

/**
 * (PECL apc &gt;= 3.1.1)<br/>
 * Updates an old value with a new value
 * @link http://php.net/manual/en/function.apc-cas.php
 * @param string $key <p>
 * The key of the value being updated.
 * </p>
 * @param int $old <p>
 * The old value (the value currently stored).
 * </p>
 * @param int $new <p>
 * The new value to update to.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function apc_cas ($key, $old, $new) {}

/**
 * (PECL apc &gt;= 3.1.4)<br/>
 * Get a binary dump of the given files and user variables
 * @link http://php.net/manual/en/function.apc-bin-dump.php
 * @param array $files [optional] <p>
 * The files. Passing in <b>NULL</b> signals a dump of every entry, while
 * passing in <b>array</b> will dump nothing.
 * </p>
 * @param array $user_vars [optional] <p>
 * The user vars. Passing in <b>NULL</b> signals a dump of every entry, while
 * passing in <b>array</b> will dump nothing.
 * </p>
 * @return string a binary dump of the given files and user variables from the APC cache,
 * <b>FALSE</b> if APC is not enabled, or <b>NULL</b> if an unknown error is encountered.
 * @jms-builtin
 */
function apc_bin_dump (array $files = null, array $user_vars = null) {}

/**
 * (PECL apc &gt;= 3.1.4)<br/>
 * Load a binary dump into the APC file/user cache
 * @link http://php.net/manual/en/function.apc-bin-load.php
 * @param string $data <p>
 * The binary dump being loaded, likely from
 * <b>apc_bin_dump</b>.
 * </p>
 * @param int $flags [optional] <p>
 * Either <b>APC_BIN_VERIFY_CRC32</b>, <b>APC_BIN_VERIFY_MD5</b>,
 * or both.
 * </p>
 * @return bool <b>TRUE</b> if the binary dump <i>data</i> was loaded
 * with success, otherwise <b>FALSE</b> is returned. <b>FALSE</b> is returned if APC
 * is not enabled, or if the <i>data</i> is not a valid APC
 * binary dump (e.g., unexpected size).
 * @jms-builtin
 */
function apc_bin_load ($data, $flags = 0) {}

/**
 * (PECL apc &gt;= 3.1.4)<br/>
 * Output a binary dump of cached files and user variables to a file
 * @link http://php.net/manual/en/function.apc-bin-dumpfile.php
 * @param array $files <p>
 * The file names being dumped.
 * </p>
 * @param array $user_vars <p>
 * The user variables being dumped.
 * </p>
 * @param string $filename <p>
 * The filename where the dump is being saved.
 * </p>
 * @param int $flags [optional] <p>
 * Flags passed to the <i>filename</i> stream. See the
 * <b>file_put_contents</b> documentation for details.
 * </p>
 * @param resource $context [optional] <p>
 * The context passed to the <i>filename</i> stream. See the
 * <b>file_put_contents</b> documentation for details.
 * </p>
 * @return int The number of bytes written to the file, otherwise
 * <b>FALSE</b> if APC is not enabled, <i>filename</i> is an invalid file name,
 * <i>filename</i> can't be opened, the file dump can't be completed
 * (e.g., the hard drive is out of disk space), or an unknown error was encountered.
 * @jms-builtin
 */
function apc_bin_dumpfile (array $files, array $user_vars, $filename, $flags = 0, $context = null) {}

/**
 * (PECL apc &gt;= 3.1.4)<br/>
 * Load a binary dump from a file into the APC file/user cache
 * @link http://php.net/manual/en/function.apc-bin-loadfile.php
 * @param string $filename <p>
 * The file name containing the dump, likely from
 * <b>apc_bin_dumpfile</b>.
 * </p>
 * @param resource $context [optional] <p>
 * The files context.
 * </p>
 * @param int $flags [optional] <p>
 * Either <b>APC_BIN_VERIFY_CRC32</b>, <b>APC_BIN_VERIFY_MD5</b>,
 * or both.
 * </p>
 * @return bool <b>TRUE</b> on success, otherwise <b>FALSE</b> Reasons it may return <b>FALSE</b> include
 * APC is not enabled, <i>filename</i> is an invalid file name or empty,
 * <i>filename</i> can't be opened, the file dump can't be completed, or
 * if the <i>data</i> is not a valid APC binary dump (e.g., unexpected
 * size).
 * @jms-builtin
 */
function apc_bin_loadfile ($filename, $context = null, $flags = null) {}

/**
 * (PECL apc &gt;= 3.1.4)<br/>
 * Checks if APC key exists
 * @link http://php.net/manual/en/function.apc-exists.php
 * @param mixed $keys <p>
 * A string, or an array of strings, that
 * contain keys.
 * </p>
 * @return mixed <b>TRUE</b> if the key exists, otherwise <b>FALSE</b> Or if an
 * array was passed to <i>keys</i>, then
 * an array is returned that contains all existing keys, or an empty
 * array if none exist.
 * @jms-builtin
 */
function apc_exists ($keys) {}

// End of apc v.3.1.9
?>
