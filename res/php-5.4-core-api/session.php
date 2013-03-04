<?php

// Start of session v.

/**
 * <b>SessionHandlerInterface</b> is an
 * interface which defines a
 * prototype for creating a custom session handler. In order to pass a custom
 * session handler to <b>session_set_save_handler</b> using its
 * OOP invocation, the class must implement this interface.
 * @link http://php.net/manual/en/class.sessionhandlerinterface.php
 */
interface SessionHandlerInterface  {

	/**
	 * (PHP 5 &gt;= 5.4.0)<br/>
	 * Initialize session
	 * @link http://php.net/manual/en/sessionhandlerinterface.open.php
	 * @param string $save_path <p>
	 * The path where to store/retrieve the session.
	 * </p>
	 * @param string $session_id <p>
	 * The session id.
	 * </p>
	 * @return bool The return value (usually <b>TRUE</b> on success, <b>FALSE</b> on failure). Note this value is returned internally to PHP for processing.
	 */
	abstract public function open ($save_path, $session_id);

	/**
	 * (PHP 5 &gt;= 5.4.0)<br/>
	 * Close the session
	 * @link http://php.net/manual/en/sessionhandlerinterface.close.php
	 * @return bool The return value (usually <b>TRUE</b> on success, <b>FALSE</b> on failure). Note this value is returned internally to PHP for processing.
	 */
	abstract public function close ();

	/**
	 * (PHP 5 &gt;= 5.4.0)<br/>
	 * Read session data
	 * @link http://php.net/manual/en/sessionhandlerinterface.read.php
	 * @param string $session_id <p>
	 * The session id.
	 * </p>
	 * @return string an encoded string of the read data. If nothing was read, it must return an empty string. Note this value is returned internally to PHP for processing.
	 */
	abstract public function read ($session_id);

	/**
	 * (PHP 5 &gt;= 5.4.0)<br/>
	 * Write session data
	 * @link http://php.net/manual/en/sessionhandlerinterface.write.php
	 * @param string $session_id <p>
	 * The session id.
	 * </p>
	 * @param string $session_data <p>
	 * The encoded session data. This data is the result of the PHP internally encoding the $_SESSION superglobal to a serialized
	 * string and passing it as this parameter. Please note sessions use an alternative serialization method.
	 * </p>
	 * @return bool The return value (usually <b>TRUE</b> on success, <b>FALSE</b> on failure). Note this value is returned internally to PHP for processing.
	 */
	abstract public function write ($session_id, $session_data);

	/**
	 * (PHP 5 &gt;= 5.4.0)<br/>
	 * Destroy a session
	 * @link http://php.net/manual/en/sessionhandlerinterface.destroy.php
	 * @param string $session_id <p>
	 * The session ID being destroyed.
	 * </p>
	 * @return bool The return value (usually <b>TRUE</b> on success, <b>FALSE</b> on failure). Note this value is returned internally to PHP for processing.
	 */
	abstract public function destroy ($session_id);

	/**
	 * (PHP 5 &gt;= 5.4.0)<br/>
	 * Cleanup old sessions
	 * @link http://php.net/manual/en/sessionhandlerinterface.gc.php
	 * @param string $maxlifetime <p>
	 * Sessions that have not updated for the last <i>maxlifetime</i> seconds will be removed.
	 * </p>
	 * @return bool The return value (usually <b>TRUE</b> on success, <b>FALSE</b> on failure). Note this value is returned internally to PHP for processing.
	 */
	abstract public function gc ($maxlifetime);

}

/**
 * <b>SessionHandler</b> a special class that can be used to expose
 * the current internal PHP session save handler by inheritance. There are six methods which
 * wrap the six internal session save handler callbacks (<i>open</i>, <i>close</i>,
 * <i>read</i>, <i>write</i>, <i>destroy</i> and <i>gc</i>).
 * By default, this class will wrap whatever internal save handler is set as as defined by the
 * session.save_handler configuration directive which is usually
 * <i>files</i> by default.
 * Other internal session save handlers are provided by PHP extensions such as SQLite (as <i>sqlite</i>),
 * Memcache (as <i>memcache</i>), and Memcached (as <i>memcached</i>).
 * @link http://php.net/manual/en/class.sessionhandler.php
 * @jms-builtin
 */
class SessionHandler implements SessionHandlerInterface {

	/**
	 * (PHP 5 &gt;= 5.4.0)<br/>
	 * Initialize session
	 * @link http://php.net/manual/en/sessionhandler.open.php
	 * @param string $save_path <p>
	 * The path where to store/retrieve the session.
	 * </p>
	 * @param string $session_id <p>
	 * The session id.
	 * </p>
	 * @return bool The return value (usually <b>TRUE</b> on success, <b>FALSE</b> on failure). Note this value is returned internally to PHP for processing.
	 */
	public function open ($save_path, $session_id) {}

	/**
	 * (PHP 5 &gt;= 5.4.0)<br/>
	 * Close the session
	 * @link http://php.net/manual/en/sessionhandler.close.php
	 * @return bool The return value (usually <b>TRUE</b> on success, <b>FALSE</b> on failure). Note this value is returned internally to PHP for processing.
	 */
	public function close () {}

	/**
	 * (PHP 5 &gt;= 5.4.0)<br/>
	 * Read session data
	 * @link http://php.net/manual/en/sessionhandler.read.php
	 * @param string $session_id <p>
	 * The session id to read data for.
	 * </p>
	 * @return string an encoded string of the read data. If nothing was read, it must return an empty string. Note this value is returned internally to PHP for processing.
	 */
	public function read ($session_id) {}

	/**
	 * (PHP 5 &gt;= 5.4.0)<br/>
	 * Write session data
	 * @link http://php.net/manual/en/sessionhandler.write.php
	 * @param string $session_id <p>
	 * The session id.
	 * </p>
	 * @param string $session_data <p>
	 * The encoded session data. This data is the result of the PHP internally encoding the $_SESSION superglobal to a serialized
	 * string and passing it as this parameter. Please note sessions use an alternative serialization method.
	 * </p>
	 * @return bool The return value (usually <b>TRUE</b> on success, <b>FALSE</b> on failure). Note this value is returned internally to PHP for processing.
	 */
	public function write ($session_id, $session_data) {}

	/**
	 * (PHP 5 &gt;= 5.4.0)<br/>
	 * Destroy a session
	 * @link http://php.net/manual/en/sessionhandler.destroy.php
	 * @param string $session_id <p>
	 * The session ID being destroyed.
	 * </p>
	 * @return bool The return value (usually <b>TRUE</b> on success, <b>FALSE</b> on failure). Note this value is returned internally to PHP for processing.
	 */
	public function destroy ($session_id) {}

	/**
	 * (PHP 5 &gt;= 5.4.0)<br/>
	 * Cleanup old sessions
	 * @link http://php.net/manual/en/sessionhandler.gc.php
	 * @param int $maxlifetime <p>
	 * Sessions that have not updated for the last <i>maxlifetime</i> seconds will be removed.
	 * </p>
	 * @return bool The return value (usually <b>TRUE</b> on success, <b>FALSE</b> on failure). Note this value is returned internally to PHP for processing.
	 */
	public function gc ($maxlifetime) {}

}

/**
 * (PHP 4, PHP 5)<br/>
 * Get and/or set the current session name
 * @link http://php.net/manual/en/function.session-name.php
 * @param string $name [optional] <p>
 * The session name references the name of the session, which is
 * used in cookies and URLs (e.g. PHPSESSID). It
 * should contain only alphanumeric characters; it should be short and
 * descriptive (i.e. for users with enabled cookie warnings).
 * If <i>name</i> is specified, the name of the current
 * session is changed to its value.
 * </p>
 * <p>
 * <p>
 * The session name can't consist of digits only, at least one letter
 * must be present. Otherwise a new session id is generated every time.
 * </p>
 * </p>
 * @return string the name of the current session.
 * @jms-builtin
 */
function session_name ($name = null) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Get and/or set the current session module
 * @link http://php.net/manual/en/function.session-module-name.php
 * @param string $module [optional] <p>
 * If <i>module</i> is specified, that module will be
 * used instead.
 * </p>
 * @return string the name of the current session module.
 * @jms-builtin
 */
function session_module_name ($module = null) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Get and/or set the current session save path
 * @link http://php.net/manual/en/function.session-save-path.php
 * @param string $path [optional] <p>
 * Session data path. If specified, the path to which data is saved will
 * be changed. <b>session_save_path</b> needs to be called
 * before <b>session_start</b> for that purpose.
 * </p>
 * <p>
 * <p>
 * On some operating systems, you may want to specify a path on a
 * filesystem that handles lots of small files efficiently. For example,
 * on Linux, reiserfs may provide better performance than ext2fs.
 * </p>
 * </p>
 * @return string the path of the current directory used for data storage.
 * @jms-builtin
 */
function session_save_path ($path = null) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Get and/or set the current session id
 * @link http://php.net/manual/en/function.session-id.php
 * @param string $id [optional] <p>
 * If <i>id</i> is specified, it will replace the current
 * session id. <b>session_id</b> needs to be called before
 * <b>session_start</b> for that purpose. Depending on the
 * session handler, not all characters are allowed within the session id.
 * For example, the file session handler only allows characters in the
 * range a-z A-Z 0-9 , (comma) and - (minus)!
 * </p>
 * When using session cookies, specifying an <i>id</i>
 * for <b>session_id</b> will always send a new cookie
 * when <b>session_start</b> is called, regardless if the
 * current session id is identical to the one being set.
 * @return string <b>session_id</b> returns the session id for the current
 * session or the empty string ("") if there is no current
 * session (no current session id exists).
 * @jms-builtin
 */
function session_id ($id = null) {}

/**
 * (PHP 4 &gt;= 4.3.2, PHP 5)<br/>
 * Update the current session id with a newly generated one
 * @link http://php.net/manual/en/function.session-regenerate-id.php
 * @param bool $delete_old_session [optional] <p>
 * Whether to delete the old associated session file or not.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function session_regenerate_id ($delete_old_session = false) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Decodes session data from a session encoded string
 * @link http://php.net/manual/en/function.session-decode.php
 * @param string $data <p>
 * The encoded data to be stored.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function session_decode ($data) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Encodes the current session data as a session encoded string
 * @link http://php.net/manual/en/function.session-encode.php
 * @return string the contents of the current session encoded.
 * @jms-builtin
 */
function session_encode () {}

/**
 * (PHP 4, PHP 5)<br/>
 * Start new or resume existing session
 * @link http://php.net/manual/en/function.session-start.php
 * @return bool This function returns <b>TRUE</b> if a session was successfully started,
 * otherwise <b>FALSE</b>.
 * @jms-builtin
 */
function session_start () {}

/**
 * (PHP 4, PHP 5)<br/>
 * Destroys all data registered to a session
 * @link http://php.net/manual/en/function.session-destroy.php
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function session_destroy () {}

/**
 * (PHP 4, PHP 5)<br/>
 * Free all session variables
 * @link http://php.net/manual/en/function.session-unset.php
 * @return void No value is returned.
 * @jms-builtin
 */
function session_unset () {}

/**
 * (PHP 4, PHP 5)<br/>
 * Sets user-level session storage functions
 * @link http://php.net/manual/en/function.session-set-save-handler.php
 * @param callable $open
 * @param callable $close
 * @param callable $read
 * @param callable $write
 * @param callable $destroy
 * @param callable $gc
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function session_set_save_handler (callable $open, callable $close, callable $read, callable $write, callable $destroy, callable $gc) {}

/**
 * (PHP 4 &gt;= 4.0.3, PHP 5)<br/>
 * Get and/or set the current cache limiter
 * @link http://php.net/manual/en/function.session-cache-limiter.php
 * @param string $cache_limiter [optional] <p>
 * If <i>cache_limiter</i> is specified, the name of the
 * current cache limiter is changed to the new value.
 * </p>
 * <table>
 * Possible values
 * <tr valign="top">
 * <td>Value</td>
 * <td>Headers sent</td>
 * </tr>
 * <tr valign="top">
 * <td>public</td>
 * <td>
 * <pre>
 * Expires: (sometime in the future, according session.cache_expire)
 * Cache-Control: public, max-age=(sometime in the future, according to session.cache_expire)
 * Last-Modified: (the timestamp of when the session was last saved)
 * </pre>
 * </td>
 * </tr>
 * <tr valign="top">
 * <td>private_no_expire</td>
 * <td>
 * <pre>
 * Cache-Control: private, max-age=(session.cache_expire in the future), pre-check=(session.cache_expire in the future)
 * Last-Modified: (the timestamp of when the session was last saved)
 * </pre>
 * </td>
 * </tr>
 * <tr valign="top">
 * <td>private</td>
 * <td>
 * <pre>
 * Expires: Thu, 19 Nov 1981 08:52:00 GMT
 * Cache-Control: private, max-age=(session.cache_expire in the future), pre-check=(session.cache_expire in the future)
 * Last-Modified: (the timestamp of when the session was last saved)
 * </pre>
 * </td>
 * </tr>
 * <tr valign="top">
 * <td>nocache</td>
 * <td>
 * <pre>
 * Expires: Thu, 19 Nov 1981 08:52:00 GMT
 * Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0
 * Pragma: no-cache
 * </pre>
 * </td>
 * </tr>
 * </table>
 * @return string the name of the current cache limiter.
 * @jms-builtin
 */
function session_cache_limiter ($cache_limiter = null) {}

/**
 * (PHP 4 &gt;= 4.2.0, PHP 5)<br/>
 * Return current cache expire
 * @link http://php.net/manual/en/function.session-cache-expire.php
 * @param string $new_cache_expire [optional] <p>
 * If <i>new_cache_expire</i> is given, the current cache
 * expire is replaced with <i>new_cache_expire</i>.
 * </p>
 * <p>
 * Setting <i>new_cache_expire</i> is of value only, if
 * session.cache_limiter is set to a value
 * different from nocache.
 * </p>
 * @return int the current setting of session.cache_expire.
 * The value returned should be read in minutes, defaults to 180.
 * @jms-builtin
 */
function session_cache_expire ($new_cache_expire = null) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Set the session cookie parameters
 * @link http://php.net/manual/en/function.session-set-cookie-params.php
 * @param int $lifetime <p>
 * Lifetime of the
 * session cookie, defined in seconds.
 * </p>
 * @param string $path [optional] <p>
 * Path on the domain where
 * the cookie will work. Use a single slash ('/') for all paths on the
 * domain.
 * </p>
 * @param string $domain [optional] <p>
 * Cookie domain, for
 * example 'www.php.net'. To make cookies visible on all subdomains then
 * the domain must be prefixed with a dot like '.php.net'.
 * </p>
 * @param bool $secure [optional] <p>
 * If <b>TRUE</b> cookie will only be sent over
 * secure connections.
 * </p>
 * @param bool $httponly [optional] <p>
 * If set to <b>TRUE</b> then PHP will attempt to send the
 * httponly
 * flag when setting the session cookie.
 * </p>
 * @return void No value is returned.
 * @jms-builtin
 */
function session_set_cookie_params ($lifetime, $path = null, $domain = null, $secure = false, $httponly = false) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Get the session cookie parameters
 * @link http://php.net/manual/en/function.session-get-cookie-params.php
 * @return array an array with the current session cookie information, the array
 * contains the following items:
 * "lifetime" - The
 * lifetime of the cookie in seconds.
 * "path" - The path where
 * information is stored.
 * "domain" - The domain
 * of the cookie.
 * "secure" - The cookie
 * should only be sent over secure connections.
 * "httponly" - The
 * cookie can only be accessed through the HTTP protocol.
 * @jms-builtin
 */
function session_get_cookie_params () {}

/**
 * (PHP 4 &gt;= 4.0.4, PHP 5)<br/>
 * Write session data and end session
 * @link http://php.net/manual/en/function.session-write-close.php
 * @return void No value is returned.
 * @jms-builtin
 */
function session_write_close () {}

/**
 * (PHP &gt;=5.4.0)<br/>
 * Returns the current session status
 * @link http://php.net/manual/en/function.session-status.php
 * @return mixed <b>PHP_SESSION_DISABLED</b> if sessions are disabled.
 * <b>PHP_SESSION_NONE</b> if sessions are enabled, but none exists.
 * <b>PHP_SESSION_ACTIVE</b> if sessions are enabled, and one exists.
 * @jms-builtin
 */
function session_status () {}

/**
 * (PHP &gt;=5.4.0)<br/>
 * Session shutdown function
 * @link http://php.net/manual/en/function.session-register-shutdown.php
 * @return void <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function session_register_shutdown () {}

/**
 * (PHP 4 &gt;= 4.4.0, PHP 5)<br/>
 * Alias of <b>session_write_close</b>
 * @link http://php.net/manual/en/function.session-commit.php
 * @jms-builtin
 */
function session_commit () {}


/**
 * Since PHP 5.4.0. Return value of <b>session_status</b> if sessions are disabled.
 * @link http://php.net/manual/en/session.constants.php
 */
define ('PHP_SESSION_DISABLED', 0);

/**
 * Since PHP 5.4.0. Return value of <b>session_status</b> if sessions are enabled,
 * but no session exists.
 * @link http://php.net/manual/en/session.constants.php
 */
define ('PHP_SESSION_NONE', 1);

/**
 * Since PHP 5.4.0. Return value of <b>session_status</b> if sessions are enabled,
 * and a session exists.
 * @link http://php.net/manual/en/session.constants.php
 */
define ('PHP_SESSION_ACTIVE', 2);

// End of session v.
?>
