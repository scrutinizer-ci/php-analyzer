<?php

// Start of curl v.

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * Initialize a cURL session
 * @link http://php.net/manual/en/function.curl-init.php
 * @param string $url [optional] <p>
 * If provided, the <b>CURLOPT_URL</b> option will be set
 * to its value. You can manually set this using the
 * <b>curl_setopt</b> function.
 * </p>
 * <p>
 * The file protocol is disabled by cURL if
 * open_basedir is set.
 * </p>
 * @return resource a cURL handle on success, <b>FALSE</b> on errors.
 * @jms-builtin
 */
function curl_init ($url = null) {}

/**
 * (PHP 5)<br/>
 * Copy a cURL handle along with all of its preferences
 * @link http://php.net/manual/en/function.curl-copy-handle.php
 * @param resource $ch
 * @return resource a new cURL handle.
 * @jms-builtin
 */
function curl_copy_handle ($ch) {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * Gets cURL version information
 * @link http://php.net/manual/en/function.curl-version.php
 * @param int $age [optional]
 * @return array an associative array with the following elements:
 * <tr valign="top">
 * <td>Indice</td>
 * <td>Value description</td>
 * </tr>
 * <tr valign="top">
 * <td>version_number</td>
 * <td>cURL 24 bit version number</td>
 * </tr>
 * <tr valign="top">
 * <td>version</td>
 * <td>cURL version number, as a string</td>
 * </tr>
 * <tr valign="top">
 * <td>ssl_version_number</td>
 * <td>OpenSSL 24 bit version number</td>
 * </tr>
 * <tr valign="top">
 * <td>ssl_version</td>
 * <td>OpenSSL version number, as a string</td>
 * </tr>
 * <tr valign="top">
 * <td>libz_version</td>
 * <td>zlib version number, as a string</td>
 * </tr>
 * <tr valign="top">
 * <td>host</td>
 * <td>Information about the host where cURL was built</td>
 * </tr>
 * <tr valign="top">
 * <td>age</td>
 * <td></td>
 * </tr>
 * <tr valign="top">
 * <td>features</td>
 * <td>A bitmask of the CURL_VERSION_XXX constants</td>
 * </tr>
 * <tr valign="top">
 * <td>protocols</td>
 * <td>An array of protocols names supported by cURL</td>
 * </tr>
 * @jms-builtin
 */
function curl_version ($age = 'CURLVERSION_NOW') {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * Set an option for a cURL transfer
 * @link http://php.net/manual/en/function.curl-setopt.php
 * @param resource $ch
 * @param int $option <p>
 * The CURLOPT_XXX option to set.
 * </p>
 * @param mixed $value <p>
 * The value to be set on <i>option</i>.
 * </p>
 * <p>
 * <i>value</i> should be a bool for the
 * following values of the <i>option</i> parameter:
 * <tr valign="top">
 * Option</td>
 * Set <i>value</i> to</td>
 * Notes</td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_AUTOREFERER</b></td>
 * <b>TRUE</b> to automatically set the Referer: field in
 * requests where it follows a Location: redirect.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_BINARYTRANSFER</b></td>
 * <b>TRUE</b> to return the raw output when
 * <b>CURLOPT_RETURNTRANSFER</b> is used.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_COOKIESESSION</b></td>
 * <b>TRUE</b> to mark this as a new cookie "session". It will force libcurl
 * to ignore all cookies it is about to load that are "session cookies"
 * from the previous session. By default, libcurl always stores and
 * loads all cookies, independent if they are session cookies or not.
 * Session cookies are cookies without expiry date and they are meant
 * to be alive and existing for this "session" only.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_CERTINFO</b></td>
 * <b>TRUE</b> to output SSL certification information to STDERR
 * on secure transfers.
 * </td>
 * Available since PHP 5.3.2. Requires <b>CURLOPT_VERBOSE</b> to be
 * on to have an effect.
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_CRLF</b></td>
 * <b>TRUE</b> to convert Unix newlines to CRLF newlines
 * on transfers.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_DNS_USE_GLOBAL_CACHE</b></td>
 * <b>TRUE</b> to use a global DNS cache. This option is
 * not thread-safe and is enabled by default.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_FAILONERROR</b></td>
 * <b>TRUE</b> to fail silently if the HTTP code returned
 * is greater than or equal to 400. The default behavior is to return
 * the page normally, ignoring the code.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_FILETIME</b></td>
 * <b>TRUE</b> to attempt to retrieve the modification
 * date of the remote document. This value can be retrieved using
 * the <i>CURLINFO_FILETIME</i> option with
 * <b>curl_getinfo</b>.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_FOLLOWLOCATION</b></td>
 * <b>TRUE</b> to follow any
 * "Location: " header that the server sends as
 * part of the HTTP header (note this is recursive, PHP will follow as
 * many "Location: " headers that it is sent,
 * unless <b>CURLOPT_MAXREDIRS</b> is set).
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_FORBID_REUSE</b></td>
 * <b>TRUE</b> to force the connection to explicitly
 * close when it has finished processing, and not be pooled for reuse.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_FRESH_CONNECT</b></td>
 * <b>TRUE</b> to force the use of a new connection
 * instead of a cached one.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_FTP_USE_EPRT</b></td>
 * <b>TRUE</b> to use EPRT (and LPRT) when doing active
 * FTP downloads. Use <b>FALSE</b> to disable EPRT and LPRT and use PORT
 * only.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_FTP_USE_EPSV</b></td>
 * <b>TRUE</b> to first try an EPSV command for FTP
 * transfers before reverting back to PASV. Set to <b>FALSE</b>
 * to disable EPSV.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_FTP_CREATE_MISSING_DIRS</b></td>
 * <b>TRUE</b> to create missing directories when an FTP operation
 * encounters a path that currently doesn't exist.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_FTPAPPEND</b></td>
 * <b>TRUE</b> to append to the remote file instead of
 * overwriting it.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_FTPASCII</b></td>
 * An alias of
 * <b>CURLOPT_TRANSFERTEXT</b>. Use that instead.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_FTPLISTONLY</b></td>
 * <b>TRUE</b> to only list the names of an FTP
 * directory.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_HEADER</b></td>
 * <b>TRUE</b> to include the header in the output.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <td><b>CURLINFO_HEADER_OUT</b></td>
 * <b>TRUE</b> to track the handle's request string.
 * </td>
 * Available since PHP 5.1.3. The <b>CURLINFO_</b>
 * prefix is intentional.
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_HTTPGET</b></td>
 * <b>TRUE</b> to reset the HTTP request method to GET.
 * Since GET is the default, this is only necessary if the request
 * method has been changed.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_HTTPPROXYTUNNEL</b></td>
 * <b>TRUE</b> to tunnel through a given HTTP proxy.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_MUTE</b></td>
 * <b>TRUE</b> to be completely silent with regards to
 * the cURL functions.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_NETRC</b></td>
 * <b>TRUE</b> to scan the ~/.netrc
 * file to find a username and password for the remote site that
 * a connection is being established with.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_NOBODY</b></td>
 * <b>TRUE</b> to exclude the body from the output.
 * Request method is then set to HEAD. Changing this to <b>FALSE</b> does
 * not change it to GET.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_NOPROGRESS</b></td>
 * <p>
 * <b>TRUE</b> to disable the progress meter for cURL transfers.
 * <p>
 * PHP automatically sets this option to <b>TRUE</b>, this should only be
 * changed for debugging purposes.
 * </p>
 * </p></td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_NOSIGNAL</b></td>
 * <b>TRUE</b> to ignore any cURL function that causes a
 * signal to be sent to the PHP process. This is turned on by default
 * in multi-threaded SAPIs so timeout options can still be used.
 * </td>
 * Added in cURL 7.10.
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_POST</b></td>
 * <b>TRUE</b> to do a regular HTTP POST. This POST is the
 * normal application/x-www-form-urlencoded kind,
 * most commonly used by HTML forms.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_PUT</b></td>
 * <b>TRUE</b> to HTTP PUT a file. The file to PUT must
 * be set with <b>CURLOPT_INFILE</b> and
 * <b>CURLOPT_INFILESIZE</b>.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_RETURNTRANSFER</b></td>
 * <b>TRUE</b> to return the transfer as a string of the
 * return value of <b>curl_exec</b> instead of outputting
 * it out directly.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_SSL_VERIFYPEER</b></td>
 * <b>FALSE</b> to stop cURL from verifying the peer's
 * certificate. Alternate certificates to verify against can be
 * specified with the <b>CURLOPT_CAINFO</b> option
 * or a certificate directory can be specified with the
 * <b>CURLOPT_CAPATH</b> option.
 * </td>
 * <b>TRUE</b> by default as of cURL 7.10. Default bundle installed as of
 * cURL 7.10.
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_TRANSFERTEXT</b></td>
 * <b>TRUE</b> to use ASCII mode for FTP transfers.
 * For LDAP, it retrieves data in plain text instead of HTML. On
 * Windows systems, it will not set STDOUT to binary
 * mode.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_UNRESTRICTED_AUTH</b></td>
 * <b>TRUE</b> to keep sending the username and password
 * when following locations (using
 * <b>CURLOPT_FOLLOWLOCATION</b>), even when the
 * hostname has changed.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_UPLOAD</b></td>
 * <b>TRUE</b> to prepare for an upload.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_VERBOSE</b></td>
 * <b>TRUE</b> to output verbose information. Writes
 * output to STDERR, or the file specified using
 * <b>CURLOPT_STDERR</b>.
 * </td>
 * </td>
 * </tr>
 * </p>
 * <p>
 * <i>value</i> should be an integer for the
 * following values of the <i>option</i> parameter:
 * <tr valign="top">
 * <td>Option</td>
 * <td>Set <i>value</i> to</td>
 * <td>Notes</td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_BUFFERSIZE</b></td>
 * The size of the buffer to use for each read. There is no guarantee
 * this request will be fulfilled, however.
 * </td>
 * Added in cURL 7.10.
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_CLOSEPOLICY</b></td>
 * Either
 * <i>CURLCLOSEPOLICY_LEAST_RECENTLY_USED</i> or
 * <i>CURLCLOSEPOLICY_OLDEST</i>.
 * There are three other CURLCLOSEPOLICY_
 * constants, but cURL does not support them yet.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <td><b>CURLOPT_CONNECTTIMEOUT</b></td>
 * The number of seconds to wait while trying to connect. Use 0 to
 * wait indefinitely.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_CONNECTTIMEOUT_MS</b></td>
 * The number of milliseconds to wait while trying to connect. Use 0 to
 * wait indefinitely.
 * If libcurl is built to use the standard system name resolver, that
 * portion of the connect will still use full-second resolution for
 * timeouts with a minimum timeout allowed of one second.
 * </td>
 * Added in cURL 7.16.2. Available since PHP 5.2.3.
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_DNS_CACHE_TIMEOUT</b></td>
 * The number of seconds to keep DNS entries in memory. This
 * option is set to 120 (2 minutes) by default.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_FTPSSLAUTH</b></td>
 * The FTP authentication method (when is activated):
 * CURLFTPAUTH_SSL (try SSL first),
 * CURLFTPAUTH_TLS (try TLS first), or
 * CURLFTPAUTH_DEFAULT (let cURL decide).
 * </td>
 * Added in cURL 7.12.2.
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_HTTP_VERSION</b></td>
 * <i>CURL_HTTP_VERSION_NONE</i> (default, lets CURL
 * decide which version to use),
 * <i>CURL_HTTP_VERSION_1_0</i> (forces HTTP/1.0),
 * or <i>CURL_HTTP_VERSION_1_1</i> (forces HTTP/1.1).
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_HTTPAUTH</b></td>
 * <p>
 * The HTTP authentication method(s) to use. The options are:
 * <i>CURLAUTH_BASIC</i>,
 * <i>CURLAUTH_DIGEST</i>,
 * <i>CURLAUTH_GSSNEGOTIATE</i>,
 * <i>CURLAUTH_NTLM</i>,
 * <i>CURLAUTH_ANY</i>, and
 * <i>CURLAUTH_ANYSAFE</i>.
 * </p>
 * <p>
 * The bitwise | (or) operator can be used to combine
 * more than one method. If this is done, cURL will poll the server to see
 * what methods it supports and pick the best one.
 * </p>
 * <p>
 * <i>CURLAUTH_ANY</i> is an alias for
 * CURLAUTH_BASIC | CURLAUTH_DIGEST | CURLAUTH_GSSNEGOTIATE | CURLAUTH_NTLM.
 * </p>
 * <p>
 * <i>CURLAUTH_ANYSAFE</i> is an alias for
 * CURLAUTH_DIGEST | CURLAUTH_GSSNEGOTIATE | CURLAUTH_NTLM.
 * </p>
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_INFILESIZE</b></td>
 * The expected size, in bytes, of the file when uploading a file to
 * a remote site. Note that using this option will not stop libcurl
 * from sending more data, as exactly what is sent depends on
 * <b>CURLOPT_READFUNCTION</b>.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_LOW_SPEED_LIMIT</b></td>
 * The transfer speed, in bytes per second, that the transfer should be
 * below during the count of <b>CURLOPT_LOW_SPEED_TIME</b>
 * seconds before PHP considers the transfer too slow and aborts.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_LOW_SPEED_TIME</b></td>
 * The number of seconds the transfer speed should be below
 * <b>CURLOPT_LOW_SPEED_LIMIT</b> before PHP considers
 * the transfer too slow and aborts.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_MAXCONNECTS</b></td>
 * The maximum amount of persistent connections that are allowed.
 * When the limit is reached,
 * <b>CURLOPT_CLOSEPOLICY</b> is used to determine
 * which connection to close.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_MAXREDIRS</b></td>
 * The maximum amount of HTTP redirections to follow. Use this option
 * alongside <b>CURLOPT_FOLLOWLOCATION</b>.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_PORT</b></td>
 * An alternative port number to connect to.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_PROTOCOLS</b></td>
 * <p>
 * Bitmask of <b>CURLPROTO_*</b> values. If used, this bitmask
 * limits what protocols libcurl may use in the transfer. This allows you to have
 * a libcurl built to support a wide range of protocols but still limit specific
 * transfers to only be allowed to use a subset of them. By default libcurl will
 * accept all protocols it supports.
 * See also <b>CURLOPT_REDIR_PROTOCOLS</b>.
 * </p>
 * <p>
 * Valid protocol options are:
 * <i>CURLPROTO_HTTP</i>,
 * <i>CURLPROTO_HTTPS</i>,
 * <i>CURLPROTO_FTP</i>,
 * <i>CURLPROTO_FTPS</i>,
 * <i>CURLPROTO_SCP</i>,
 * <i>CURLPROTO_SFTP</i>,
 * <i>CURLPROTO_TELNET</i>,
 * <i>CURLPROTO_LDAP</i>,
 * <i>CURLPROTO_LDAPS</i>,
 * <i>CURLPROTO_DICT</i>,
 * <i>CURLPROTO_FILE</i>,
 * <i>CURLPROTO_TFTP</i>,
 * <i>CURLPROTO_ALL</i>
 * </p>
 * </td>
 * Added in cURL 7.19.4.
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_PROXYAUTH</b></td>
 * The HTTP authentication method(s) to use for the proxy connection.
 * Use the same bitmasks as described in
 * <b>CURLOPT_HTTPAUTH</b>. For proxy authentication,
 * only <i>CURLAUTH_BASIC</i> and
 * <i>CURLAUTH_NTLM</i> are currently supported.
 * </td>
 * Added in cURL 7.10.7.
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_PROXYPORT</b></td>
 * The port number of the proxy to connect to. This port number can
 * also be set in <b>CURLOPT_PROXY</b>.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_PROXYTYPE</b></td>
 * Either <i>CURLPROXY_HTTP</i> (default) or
 * <i>CURLPROXY_SOCKS5</i>.
 * </td>
 * Added in cURL 7.10.
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_REDIR_PROTOCOLS</b></td>
 * Bitmask of <b>CURLPROTO_*</b> values. If used, this bitmask
 * limits what protocols libcurl may use in a transfer that it follows to in
 * a redirect when <b>CURLOPT_FOLLOWLOCATION</b> is enabled.
 * This allows you to limit specific transfers to only be allowed to use a subset
 * of protocols in redirections. By default libcurl will allow all protocols
 * except for FILE and SCP. This is a difference compared to pre-7.19.4 versions
 * which unconditionally would follow to all protocols supported.
 * See also <b>CURLOPT_PROTOCOLS</b> for protocol constant values.
 * </td>
 * Added in cURL 7.19.4.
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_RESUME_FROM</b></td>
 * The offset, in bytes, to resume a transfer from.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_SSL_VERIFYHOST</b></td>
 * 1 to check the existence of a common name in the
 * SSL peer certificate. 2 to check the existence of
 * a common name and also verify that it matches the hostname
 * provided. In production environments the value of this option
 * should be kept at 2 (default value).
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_SSLVERSION</b></td>
 * The SSL version (2 or 3) to use. By default PHP will try to determine
 * this itself, although in some cases this must be set manually.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_TIMECONDITION</b></td>
 * How <b>CURLOPT_TIMEVALUE</b> is treated.
 * Use <i>CURL_TIMECOND_IFMODSINCE</i> to return the
 * page only if it has been modified since the time specified in
 * <b>CURLOPT_TIMEVALUE</b>. If it hasn't been modified,
 * a "304 Not Modified" header will be returned
 * assuming <b>CURLOPT_HEADER</b> is <b>TRUE</b>.
 * Use <i>CURL_TIMECOND_IFUNMODSINCE</i> for the reverse
 * effect. <i>CURL_TIMECOND_IFMODSINCE</i> is the
 * default.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_TIMEOUT</b></td>
 * The maximum number of seconds to allow cURL functions to execute.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_TIMEOUT_MS</b></td>
 * The maximum number of milliseconds to allow cURL functions to
 * execute.
 * If libcurl is built to use the standard system name resolver, that
 * portion of the connect will still use full-second resolution for
 * timeouts with a minimum timeout allowed of one second.
 * </td>
 * Added in cURL 7.16.2. Available since PHP 5.2.3.
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_TIMEVALUE</b></td>
 * The time in seconds since January 1st, 1970. The time will be used
 * by <b>CURLOPT_TIMECONDITION</b>. By default,
 * <i>CURL_TIMECOND_IFMODSINCE</i> is used.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_MAX_RECV_SPEED_LARGE</b></td>
 * If a download exceeds this speed (counted in bytes per second) on
 * cumulative average during the transfer, the transfer will pause to
 * keep the average rate less than or equal to the parameter value.
 * Defaults to unlimited speed.
 * </td>
 * Added in cURL 7.15.5. Available since PHP 5.4.0.
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_MAX_SEND_SPEED_LARGE</b></td>
 * If an upload exceeds this speed (counted in bytes per second) on
 * cumulative average during the transfer, the transfer will pause to
 * keep the average rate less than or equal to the parameter value.
 * Defaults to unlimited speed.
 * </td>
 * Added in cURL 7.15.5. Available since PHP 5.4.0.
 * </td>
 * </tr>
 * 	 <tr valign="top">
 * <b>CURLOPT_SSH_AUTH_TYPES</b></td>
 * A bitmask consisting of one or more of
 * 		<b>CURLSSH_AUTH_PUBLICKEY</b>,
 * 		<b>CURLSSH_AUTH_PASSWORD</b>,
 * 		<b>CURLSSH_AUTH_HOST</b>,
 * 		<b>CURLSSH_AUTH_KEYBOARD</b>. Set to
 * 		<b>CURLSSH_AUTH_ANY</b> to let libcurl pick one.
 * </td>
 * Added in cURL 7.16.1.
 * </td>
 * </tr>
 * </p>
 * <p>
 * <i>value</i> should be a string for the
 * following values of the <i>option</i> parameter:
 * <tr valign="top">
 * <td>Option</td>
 * <td>Set <i>value</i> to</td>
 * <td>Notes</td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_CAINFO</b></td>
 * The name of a file holding one or more certificates to verify the
 * peer with. This only makes sense when used in combination with
 * <b>CURLOPT_SSL_VERIFYPEER</b>.
 * </td>
 * Requires absolute path.
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_CAPATH</b></td>
 * A directory that holds multiple CA certificates. Use this option
 * alongside <b>CURLOPT_SSL_VERIFYPEER</b>.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_COOKIE</b></td>
 * The contents of the "Cookie: " header to be
 * used in the HTTP request.
 * Note that multiple cookies are separated with a semicolon followed
 * by a space (e.g., "fruit=apple; colour=red")
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_COOKIEFILE</b></td>
 * The name of the file containing the cookie data. The cookie file can
 * be in Netscape format, or just plain HTTP-style headers dumped into
 * a file.
 * If the name is an empty string, no cookies are loaded, but cookie
 * handling is still enabled.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_COOKIEJAR</b></td>
 * The name of a file to save all internal cookies to when the handle is closed,
 * e.g. after a call to curl_close.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_CUSTOMREQUEST</b></td>
 * <p>
 * A custom request method to use instead of
 * "GET" or "HEAD" when doing
 * a HTTP request. This is useful for doing
 * "DELETE" or other, more obscure HTTP requests.
 * Valid values are things like "GET",
 * "POST", "CONNECT" and so on;
 * i.e. Do not enter a whole HTTP request line here. For instance,
 * entering "GET /index.html HTTP/1.0\r\n\r\n"
 * would be incorrect.
 * <p>
 * Don't do this without making sure the server supports the custom
 * request method first.
 * </p>
 * </p></td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_EGDSOCKET</b></td>
 * Like <b>CURLOPT_RANDOM_FILE</b>, except a filename
 * to an Entropy Gathering Daemon socket.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_ENCODING</b></td>
 * The contents of the "Accept-Encoding: " header.
 * This enables decoding of the response. Supported encodings are
 * "identity", "deflate", and
 * "gzip". If an empty string, "",
 * is set, a header containing all supported encoding types is sent.
 * </td>
 * Added in cURL 7.10.
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_FTPPORT</b></td>
 * The value which will be used to get the IP address to use
 * for the FTP "POST" instruction. The "POST" instruction tells
 * the remote server to connect to our specified IP address. The
 * string may be a plain IP address, a hostname, a network
 * interface name (under Unix), or just a plain '-' to use the
 * systems default IP address.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_INTERFACE</b></td>
 * The name of the outgoing network interface to use. This can be an
 * interface name, an IP address or a host name.
 * </td>
 * </td>
 * </tr>
 * 	 <tr valign="top">
 * <b>CURLOPT_KEYPASSWD</b></td>
 * The password required to use the <b>CURLOPT_SSLKEY</b>
 * 		or <b>CURLOPT_SSH_PRIVATE_KEYFILE</b> private key.
 * </td>
 * Added in cURL 7.16.1.
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_KRB4LEVEL</b></td>
 * The KRB4 (Kerberos 4) security level. Any of the following values
 * (in order from least to most powerful) are valid:
 * "clear",
 * "safe",
 * "confidential",
 * "private"..
 * If the string does not match one of these,
 * "private" is used. Setting this option to <b>NULL</b>
 * will disable KRB4 security. Currently KRB4 security only works
 * with FTP transactions.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_POSTFIELDS</b></td>
 * The full data to post in a HTTP "POST" operation.
 * To post a file, prepend a filename with @ and
 * use the full path. The filetype can be explicitly specified by
 * following the filename with the type in the format
 * ';type=mimetype'. This parameter can either be
 * passed as a urlencoded string like 'para1=val1&#38;#38;para2=val2&#38;#38;...'
 * or as an array with the field name as key and field data as value.
 * If <i>value</i> is an array, the
 * Content-Type header will be set to
 * multipart/form-data.
 * As of PHP 5.2.0, <i>value</i> must be an array if
 * files are passed to this option with the @ prefix.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_PROXY</b></td>
 * The HTTP proxy to tunnel requests through.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_PROXYUSERPWD</b></td>
 * A username and password formatted as
 * "[username]:[password]" to use for the
 * connection to the proxy.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_RANDOM_FILE</b></td>
 * A filename to be used to seed the random number generator for SSL.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_RANGE</b></td>
 * Range(s) of data to retrieve in the format
 * "X-Y" where X or Y are optional. HTTP transfers
 * also support several intervals, separated with commas in the format
 * "X-Y,N-M".
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_REFERER</b></td>
 * The contents of the "Referer: " header to be used
 * in a HTTP request.
 * </td>
 * </td>
 * </tr>
 * 	 <tr valign="top">
 * <b>CURLOPT_SSH_HOST_PUBLIC_KEY_MD5</b></td>
 * A string containing 32 hexadecimal digits. The string should be the
 * 		MD5 checksum of the remote host's public key, and libcurl will reject
 * 		the connection to the host unless the md5sums match.
 * 		This option is only for SCP and SFTP transfers.
 * </td>
 * Added in cURL 7.17.1.
 * </td>
 * </tr>
 * 	 <tr valign="top">
 * <b>CURLOPT_SSH_PUBLIC_KEYFILE</b></td>
 * The file name for your public key. If not used, libcurl defaults to
 * 		$HOME/.ssh/id_dsa.pub if the HOME environment variable is set,
 * 		and just "id_dsa.pub" in the current directory if HOME is not set.
 * </td>
 * Added in cURL 7.16.1.
 * </td>
 * </tr>
 * 	 <tr valign="top">
 * <b>CURLOPT_SSH_PRIVATE_KEYFILE</b></td>
 * The file name for your private key. If not used, libcurl defaults to
 * 		$HOME/.ssh/id_dsa if the HOME environment variable is set,
 * 		and just "id_dsa" in the current directory if HOME is not set.
 * 		If the file is password-protected, set the password with
 * 		<b>CURLOPT_KEYPASSWD</b>.
 * </td>
 * Added in cURL 7.16.1.
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_SSL_CIPHER_LIST</b></td>
 * A list of ciphers to use for SSL. For example,
 * RC4-SHA and TLSv1 are valid
 * cipher lists.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_SSLCERT</b></td>
 * The name of a file containing a PEM formatted certificate.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_SSLCERTPASSWD</b></td>
 * The password required to use the
 * <b>CURLOPT_SSLCERT</b> certificate.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_SSLCERTTYPE</b></td>
 * The format of the certificate. Supported formats are
 * "PEM" (default), "DER",
 * and "ENG".
 * </td>
 * Added in cURL 7.9.3.
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_SSLENGINE</b></td>
 * The identifier for the crypto engine of the private SSL key
 * specified in <b>CURLOPT_SSLKEY</b>.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_SSLENGINE_DEFAULT</b></td>
 * The identifier for the crypto engine used for asymmetric crypto
 * operations.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_SSLKEY</b></td>
 * The name of a file containing a private SSL key.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_SSLKEYPASSWD</b></td>
 * <p>
 * The secret password needed to use the private SSL key specified in
 * <b>CURLOPT_SSLKEY</b>.
 * <p>
 * Since this option contains a sensitive password, remember to keep
 * the PHP script it is contained within safe.
 * </p>
 * </p></td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_SSLKEYTYPE</b></td>
 * The key type of the private SSL key specified in
 * <b>CURLOPT_SSLKEY</b>. Supported key types are
 * "PEM" (default), "DER",
 * and "ENG".
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_URL</b></td>
 * The URL to fetch. This can also be set when initializing a
 * session with <b>curl_init</b>.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_USERAGENT</b></td>
 * The contents of the "User-Agent: " header to be
 * used in a HTTP request.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_USERPWD</b></td>
 * A username and password formatted as
 * "[username]:[password]" to use for the
 * connection.
 * </td>
 * </td>
 * </tr>
 * </p>
 * <p>
 * <i>value</i> should be an array for the
 * following values of the <i>option</i> parameter:
 * <tr valign="top">
 * <td>Option</td>
 * <td>Set <i>value</i> to</td>
 * <td>Notes</td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_HTTP200ALIASES</b></td>
 * An array of HTTP 200 responses that will be treated as valid
 * responses and not as errors.
 * </td>
 * Added in cURL 7.10.3.
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_HTTPHEADER</b></td>
 * An array of HTTP header fields to set, in the format
 * array('Content-type: text/plain', 'Content-length: 100')
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_POSTQUOTE</b></td>
 * An array of FTP commands to execute on the server after the FTP
 * request has been performed.
 * </td>
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_QUOTE</b></td>
 * An array of FTP commands to execute on the server prior to the FTP
 * request.
 * </td>
 * </td>
 * </tr>
 * </p>
 * <p>
 * <i>value</i> should be a stream resource (using
 * <b>fopen</b>, for example) for the following values of the
 * <i>option</i> parameter:
 * <tr valign="top">
 * <td>Option</td>
 * <td>Set <i>value</i> to</td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_FILE</b></td>
 * The file that the transfer should be written to. The default
 * is STDOUT (the browser window).
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_INFILE</b></td>
 * The file that the transfer should be read from when uploading.
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_STDERR</b></td>
 * An alternative location to output errors to instead of
 * STDERR.
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_WRITEHEADER</b></td>
 * The file that the header part of the transfer is written to.
 * </td>
 * </tr>
 * </p>
 * <p>
 * <i>value</i> should be a string that is the name of a valid
 * callback function for the following values of the
 * <i>option</i> parameter:
 * <tr valign="top">
 * <td>Option</td>
 * <td>Set <i>value</i> to</td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_HEADERFUNCTION</b></td>
 * The name of a callback function where the callback function takes
 * two parameters. The first is the cURL resource, the second is a
 * string with the header data to be written. The header data must
 * be written when using this callback function. Return the number of
 * bytes written.
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_PASSWDFUNCTION</b></td>
 * The name of a callback function where the callback function takes
 * three parameters. The first is the cURL resource, the second is a
 * string containing a password prompt, and the third is the maximum
 * password length. Return the string containing the password.
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_PROGRESSFUNCTION</b></td>
 * The name of a callback function where the callback function takes
 * three parameters. The first is the cURL resource, the second is a
 * file-descriptor resource, and the third is length. Return the
 * string containing the data.
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_READFUNCTION</b></td>
 * The name of a callback function where the callback function takes
 * three parameters. The first is the cURL resource, the second is a
 * stream resource provided to cURL through the option
 * <b>CURLOPT_INFILE</b>, and the third is the maximum
 * amount of data to be read. The callback function must return a string
 * with a length equal or smaller than the amount of data requested,
 * typically by reading it from the passed stream resource. It should
 * return an empty string to signal EOF.
 * </td>
 * </tr>
 * <tr valign="top">
 * <b>CURLOPT_WRITEFUNCTION</b></td>
 * The name of a callback function where the callback function takes
 * two parameters. The first is the cURL resource, and the second is a
 * string with the data to be written. The data must be saved by using
 * this callback function. It must return the exact number of bytes written
 * or the transfer will be aborted with an error.
 * </td>
 * </tr>
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function curl_setopt ($ch, $option, $value) {}

/**
 * (PHP 5 &gt;= 5.1.3)<br/>
 * Set multiple options for a cURL transfer
 * @link http://php.net/manual/en/function.curl-setopt-array.php
 * @param resource $ch
 * @param array $options <p>
 * An array specifying which options to set and their values.
 * The keys should be valid <b>curl_setopt</b> constants or
 * their integer equivalents.
 * </p>
 * @return bool <b>TRUE</b> if all options were successfully set. If an option could
 * not be successfully set, <b>FALSE</b> is immediately returned, ignoring any
 * future options in the <i>options</i> array.
 * @jms-builtin
 */
function curl_setopt_array ($ch, array $options) {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * Perform a cURL session
 * @link http://php.net/manual/en/function.curl-exec.php
 * @param resource $ch
 * @return mixed <b>TRUE</b> on success or <b>FALSE</b> on failure. However, if the <b>CURLOPT_RETURNTRANSFER</b>
 * option is set, it will return
 * the result on success, <b>FALSE</b> on failure.
 * @jms-builtin
 */
function curl_exec ($ch) {}

/**
 * (PHP 4 &gt;= 4.0.4, PHP 5)<br/>
 * Get information regarding a specific transfer
 * @link http://php.net/manual/en/function.curl-getinfo.php
 * @param resource $ch
 * @param int $opt [optional] <p>
 * This may be one of the following constants:
 * <b>CURLINFO_EFFECTIVE_URL</b> - Last effective URL
 * @return mixed If <i>opt</i> is given, returns its value as a string.
 * Otherwise, returns an associative array with the following elements
 * (which correspond to <i>opt</i>), or <b>FALSE</b> on failure:
 * "url"
 * "content_type"
 * "http_code"
 * "header_size"
 * "request_size"
 * "filetime"
 * "ssl_verify_result"
 * "redirect_count"
 * "total_time"
 * "namelookup_time"
 * "connect_time"
 * "pretransfer_time"
 * "size_upload"
 * "size_download"
 * "speed_download"
 * "speed_upload"
 * "download_content_length"
 * "upload_content_length"
 * "starttransfer_time"
 * "redirect_time"
 * "certinfo"
 * "request_header" (This is only set if the <b>CURLINFO_HEADER_OUT</b>
 * is set by a previous call to <b>curl_setopt</b>)
 * @jms-builtin
 */
function curl_getinfo ($ch, $opt = 0) {}

/**
 * (PHP 4 &gt;= 4.0.3, PHP 5)<br/>
 * Return a string containing the last error for the current session
 * @link http://php.net/manual/en/function.curl-error.php
 * @param resource $ch
 * @return string the error message or '' (the empty string) if no
 * error occurred.
 * @jms-builtin
 */
function curl_error ($ch) {}

/**
 * (PHP 4 &gt;= 4.0.3, PHP 5)<br/>
 * Return the last error number
 * @link http://php.net/manual/en/function.curl-errno.php
 * @param resource $ch
 * @return int the error number or 0 (zero) if no error
 * occurred.
 * @jms-builtin
 */
function curl_errno ($ch) {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * Close a cURL session
 * @link http://php.net/manual/en/function.curl-close.php
 * @param resource $ch
 * @return void No value is returned.
 * @jms-builtin
 */
function curl_close ($ch) {}

/**
 * (PHP 5)<br/>
 * Returns a new cURL multi handle
 * @link http://php.net/manual/en/function.curl-multi-init.php
 * @return resource a cURL multi handle resource on success, <b>FALSE</b> on failure.
 * @jms-builtin
 */
function curl_multi_init () {}

/**
 * (PHP 5)<br/>
 * Add a normal cURL handle to a cURL multi handle
 * @link http://php.net/manual/en/function.curl-multi-add-handle.php
 * @param resource $mh
 * @param resource $ch
 * @return int 0 on success, or one of the <b>CURLM_XXX</b> errors
 * code.
 * @jms-builtin
 */
function curl_multi_add_handle ($mh, $ch) {}

/**
 * (PHP 5)<br/>
 * Remove a multi handle from a set of cURL handles
 * @link http://php.net/manual/en/function.curl-multi-remove-handle.php
 * @param resource $mh
 * @param resource $ch
 * @return int 0 on success, or one of the <b>CURLM_XXX</b> error
 * codes.
 * @jms-builtin
 */
function curl_multi_remove_handle ($mh, $ch) {}

/**
 * (PHP 5)<br/>
 * Wait for activity on any curl_multi connection
 * @link http://php.net/manual/en/function.curl-multi-select.php
 * @param resource $mh
 * @param float $timeout [optional] <p>
 * Time, in seconds, to wait for a response.
 * </p>
 * @return int On success, returns the number of descriptors contained in
 * the descriptor sets. On failure, this function will return -1 on a select failure or timeout (from the underlying select system call).
 * @jms-builtin
 */
function curl_multi_select ($mh, $timeout = 1.0) {}

/**
 * (PHP 5)<br/>
 * Run the sub-connections of the current cURL handle
 * @link http://php.net/manual/en/function.curl-multi-exec.php
 * @param resource $mh
 * @param int $still_running <p>
 * A reference to a flag to tell whether the operations are still running.
 * </p>
 * @return int A cURL code defined in the cURL Predefined Constants.
 * </p>
 * <p>
 * This only returns errors regarding the whole multi stack. There might still have
 * occurred problems on individual transfers even when this function returns
 * <b>CURLM_OK</b>.
 * @jms-builtin
 */
function curl_multi_exec ($mh, &$still_running) {}

/**
 * (PHP 5)<br/>
 * Return the content of a cURL handle if <b>CURLOPT_RETURNTRANSFER</b> is set
 * @link http://php.net/manual/en/function.curl-multi-getcontent.php
 * @param resource $ch
 * @return string Return the content of a cURL handle if <b>CURLOPT_RETURNTRANSFER</b> is set.
 * @jms-builtin
 */
function curl_multi_getcontent ($ch) {}

/**
 * (PHP 5)<br/>
 * Get information about the current transfers
 * @link http://php.net/manual/en/function.curl-multi-info-read.php
 * @param resource $mh
 * @param int $msgs_in_queue [optional] <p>
 * Number of messages that are still in the queue
 * </p>
 * @return array On success, returns an associative array for the message, <b>FALSE</b> on failure.
 * </p>
 * <p>
 * <table>
 * Contents of the returned array
 * <tr valign="top">
 * <td>Key:</td>
 * <td>Value:</td>
 * </tr>
 * <tr valign="top">
 * <td>msg</td>
 * <td>The <b>CURLMSG_DONE</b> constant. Other return values
 * are currently not available.</td>
 * </tr>
 * <tr valign="top">
 * <td>result</td>
 * <td>One of the <b>CURLE_*</b> constants. If everything is
 * OK, the <b>CURLE_OK</b> will be the result.</td>
 * </tr>
 * <tr valign="top">
 * <td>handle</td>
 * <td>Resource of type curl indicates the handle which it concerns.</td>
 * </tr>
 * </table>
 * @jms-builtin
 */
function curl_multi_info_read ($mh, &$msgs_in_queue = null) {}

/**
 * (PHP 5)<br/>
 * Close a set of cURL handles
 * @link http://php.net/manual/en/function.curl-multi-close.php
 * @param resource $mh
 * @return void No value is returned.
 * @jms-builtin
 */
function curl_multi_close ($mh) {}

define ('CURLOPT_IPRESOLVE', 113);
define ('CURL_IPRESOLVE_WHATEVER', 0);
define ('CURL_IPRESOLVE_V4', 1);
define ('CURL_IPRESOLVE_V6', 2);
define ('CURLOPT_DNS_USE_GLOBAL_CACHE', 91);
define ('CURLOPT_DNS_CACHE_TIMEOUT', 92);
define ('CURLOPT_PORT', 3);
define ('CURLOPT_FILE', 10001);
define ('CURLOPT_READDATA', 10009);
define ('CURLOPT_INFILE', 10009);
define ('CURLOPT_INFILESIZE', 14);
define ('CURLOPT_URL', 10002);
define ('CURLOPT_PROXY', 10004);
define ('CURLOPT_VERBOSE', 41);
define ('CURLOPT_HEADER', 42);
define ('CURLOPT_HTTPHEADER', 10023);
define ('CURLOPT_NOPROGRESS', 43);

/**
 * Available since PHP 5.3.0
 * @link http://php.net/manual/en/curl.constants.php
 */
define ('CURLOPT_PROGRESSFUNCTION', 20056);
define ('CURLOPT_NOBODY', 44);
define ('CURLOPT_FAILONERROR', 45);
define ('CURLOPT_UPLOAD', 46);
define ('CURLOPT_POST', 47);
define ('CURLOPT_FTPLISTONLY', 48);
define ('CURLOPT_FTPAPPEND', 50);
define ('CURLOPT_NETRC', 51);

/**
 * This constant is not available when open_basedir
 * or safe_mode are enabled.
 * @link http://php.net/manual/en/curl.constants.php
 */
define ('CURLOPT_FOLLOWLOCATION', 52);
define ('CURLOPT_PUT', 54);
define ('CURLOPT_USERPWD', 10005);
define ('CURLOPT_PROXYUSERPWD', 10006);
define ('CURLOPT_RANGE', 10007);
define ('CURLOPT_TIMEOUT', 13);
define ('CURLOPT_TIMEOUT_MS', 155);
define ('CURLOPT_POSTFIELDS', 10015);
define ('CURLOPT_REFERER', 10016);
define ('CURLOPT_USERAGENT', 10018);
define ('CURLOPT_FTPPORT', 10017);
define ('CURLOPT_FTP_USE_EPSV', 85);
define ('CURLOPT_LOW_SPEED_LIMIT', 19);
define ('CURLOPT_LOW_SPEED_TIME', 20);
define ('CURLOPT_RESUME_FROM', 21);
define ('CURLOPT_COOKIE', 10022);

/**
 * Available since PHP 5.1.0
 * @link http://php.net/manual/en/curl.constants.php
 */
define ('CURLOPT_COOKIESESSION', 96);

/**
 * Available since PHP 5.1.0
 * @link http://php.net/manual/en/curl.constants.php
 */
define ('CURLOPT_AUTOREFERER', 58);
define ('CURLOPT_SSLCERT', 10025);
define ('CURLOPT_SSLCERTPASSWD', 10026);
define ('CURLOPT_WRITEHEADER', 10029);
define ('CURLOPT_SSL_VERIFYHOST', 81);
define ('CURLOPT_COOKIEFILE', 10031);
define ('CURLOPT_SSLVERSION', 32);
define ('CURLOPT_TIMECONDITION', 33);
define ('CURLOPT_TIMEVALUE', 34);
define ('CURLOPT_CUSTOMREQUEST', 10036);
define ('CURLOPT_STDERR', 10037);
define ('CURLOPT_TRANSFERTEXT', 53);
define ('CURLOPT_RETURNTRANSFER', 19913);
define ('CURLOPT_QUOTE', 10028);
define ('CURLOPT_POSTQUOTE', 10039);
define ('CURLOPT_INTERFACE', 10062);
define ('CURLOPT_KRB4LEVEL', 10063);
define ('CURLOPT_HTTPPROXYTUNNEL', 61);
define ('CURLOPT_FILETIME', 69);
define ('CURLOPT_WRITEFUNCTION', 20011);
define ('CURLOPT_READFUNCTION', 20012);
define ('CURLOPT_HEADERFUNCTION', 20079);
define ('CURLOPT_MAXREDIRS', 68);
define ('CURLOPT_MAXCONNECTS', 71);
define ('CURLOPT_CLOSEPOLICY', 72);
define ('CURLOPT_FRESH_CONNECT', 74);
define ('CURLOPT_FORBID_REUSE', 75);
define ('CURLOPT_RANDOM_FILE', 10076);
define ('CURLOPT_EGDSOCKET', 10077);
define ('CURLOPT_CONNECTTIMEOUT', 78);
define ('CURLOPT_CONNECTTIMEOUT_MS', 156);
define ('CURLOPT_SSL_VERIFYPEER', 64);
define ('CURLOPT_CAINFO', 10065);
define ('CURLOPT_CAPATH', 10097);
define ('CURLOPT_COOKIEJAR', 10082);
define ('CURLOPT_SSL_CIPHER_LIST', 10083);
define ('CURLOPT_BINARYTRANSFER', 19914);
define ('CURLOPT_NOSIGNAL', 99);
define ('CURLOPT_PROXYTYPE', 101);
define ('CURLOPT_BUFFERSIZE', 98);
define ('CURLOPT_HTTPGET', 80);
define ('CURLOPT_HTTP_VERSION', 84);
define ('CURLOPT_SSLKEY', 10087);
define ('CURLOPT_SSLKEYTYPE', 10088);
define ('CURLOPT_SSLKEYPASSWD', 10026);
define ('CURLOPT_SSLENGINE', 10089);
define ('CURLOPT_SSLENGINE_DEFAULT', 90);
define ('CURLOPT_SSLCERTTYPE', 10086);
define ('CURLOPT_CRLF', 27);
define ('CURLOPT_ENCODING', 10102);
define ('CURLOPT_PROXYPORT', 59);
define ('CURLOPT_UNRESTRICTED_AUTH', 105);
define ('CURLOPT_FTP_USE_EPRT', 106);

/**
 * Available since PHP 5.2.1
 * @link http://php.net/manual/en/curl.constants.php
 */
define ('CURLOPT_TCP_NODELAY', 121);
define ('CURLOPT_HTTP200ALIASES', 10104);
define ('CURL_TIMECOND_IFMODSINCE', 1);
define ('CURL_TIMECOND_IFUNMODSINCE', 2);
define ('CURL_TIMECOND_LASTMOD', 3);

/**
 * Available since PHP 5.4.0 and cURL 7.15.5
 * @link http://php.net/manual/en/curl.constants.php
 */
define ('CURLOPT_MAX_RECV_SPEED_LARGE', 30146);

/**
 * Available since PHP 5.4.0 and cURL 7.15.5
 * @link http://php.net/manual/en/curl.constants.php
 */
define ('CURLOPT_MAX_SEND_SPEED_LARGE', 30145);
define ('CURLOPT_HTTPAUTH', 107);
define ('CURLAUTH_BASIC', 1);
define ('CURLAUTH_DIGEST', 2);
define ('CURLAUTH_GSSNEGOTIATE', 4);
define ('CURLAUTH_NTLM', 8);
define ('CURLAUTH_ANY', -17);
define ('CURLAUTH_ANYSAFE', -18);
define ('CURLOPT_PROXYAUTH', 111);
define ('CURLOPT_FTP_CREATE_MISSING_DIRS', 110);

/**
 * Available since PHP 5.2.4
 * @link http://php.net/manual/en/curl.constants.php
 */
define ('CURLOPT_PRIVATE', 10103);
define ('CURLCLOSEPOLICY_LEAST_RECENTLY_USED', 2);
define ('CURLCLOSEPOLICY_LEAST_TRAFFIC', 3);
define ('CURLCLOSEPOLICY_SLOWEST', 4);
define ('CURLCLOSEPOLICY_CALLBACK', 5);
define ('CURLCLOSEPOLICY_OLDEST', 1);
define ('CURLINFO_EFFECTIVE_URL', 1048577);
define ('CURLINFO_HTTP_CODE', 2097154);
define ('CURLINFO_HEADER_SIZE', 2097163);
define ('CURLINFO_REQUEST_SIZE', 2097164);
define ('CURLINFO_TOTAL_TIME', 3145731);
define ('CURLINFO_NAMELOOKUP_TIME', 3145732);
define ('CURLINFO_CONNECT_TIME', 3145733);
define ('CURLINFO_PRETRANSFER_TIME', 3145734);
define ('CURLINFO_SIZE_UPLOAD', 3145735);
define ('CURLINFO_SIZE_DOWNLOAD', 3145736);
define ('CURLINFO_SPEED_DOWNLOAD', 3145737);
define ('CURLINFO_SPEED_UPLOAD', 3145738);
define ('CURLINFO_FILETIME', 2097166);
define ('CURLINFO_SSL_VERIFYRESULT', 2097165);
define ('CURLINFO_CONTENT_LENGTH_DOWNLOAD', 3145743);
define ('CURLINFO_CONTENT_LENGTH_UPLOAD', 3145744);
define ('CURLINFO_STARTTRANSFER_TIME', 3145745);
define ('CURLINFO_CONTENT_TYPE', 1048594);
define ('CURLINFO_REDIRECT_TIME', 3145747);
define ('CURLINFO_REDIRECT_COUNT', 2097172);

/**
 * Available since PHP 5.1.3
 * @link http://php.net/manual/en/curl.constants.php
 */
define ('CURLINFO_HEADER_OUT', 2);

/**
 * Available since PHP 5.2.4
 * @link http://php.net/manual/en/curl.constants.php
 */
define ('CURLINFO_PRIVATE', 1048597);
define ('CURLINFO_CERTINFO', 4194338);
define ('CURLINFO_REDIRECT_URL', 1048607);
define ('CURL_VERSION_IPV6', 1);
define ('CURL_VERSION_KERBEROS4', 2);
define ('CURL_VERSION_SSL', 4);
define ('CURL_VERSION_LIBZ', 8);
define ('CURLVERSION_NOW', 3);
define ('CURLE_OK', 0);
define ('CURLE_UNSUPPORTED_PROTOCOL', 1);
define ('CURLE_FAILED_INIT', 2);
define ('CURLE_URL_MALFORMAT', 3);
define ('CURLE_URL_MALFORMAT_USER', 4);
define ('CURLE_COULDNT_RESOLVE_PROXY', 5);
define ('CURLE_COULDNT_RESOLVE_HOST', 6);
define ('CURLE_COULDNT_CONNECT', 7);
define ('CURLE_FTP_WEIRD_SERVER_REPLY', 8);
define ('CURLE_FTP_ACCESS_DENIED', 9);
define ('CURLE_FTP_USER_PASSWORD_INCORRECT', 10);
define ('CURLE_FTP_WEIRD_PASS_REPLY', 11);
define ('CURLE_FTP_WEIRD_USER_REPLY', 12);
define ('CURLE_FTP_WEIRD_PASV_REPLY', 13);
define ('CURLE_FTP_WEIRD_227_FORMAT', 14);
define ('CURLE_FTP_CANT_GET_HOST', 15);
define ('CURLE_FTP_CANT_RECONNECT', 16);
define ('CURLE_FTP_COULDNT_SET_BINARY', 17);
define ('CURLE_PARTIAL_FILE', 18);
define ('CURLE_FTP_COULDNT_RETR_FILE', 19);
define ('CURLE_FTP_WRITE_ERROR', 20);
define ('CURLE_FTP_QUOTE_ERROR', 21);
define ('CURLE_HTTP_NOT_FOUND', 22);
define ('CURLE_WRITE_ERROR', 23);
define ('CURLE_MALFORMAT_USER', 24);
define ('CURLE_FTP_COULDNT_STOR_FILE', 25);
define ('CURLE_READ_ERROR', 26);
define ('CURLE_OUT_OF_MEMORY', 27);
define ('CURLE_OPERATION_TIMEOUTED', 28);
define ('CURLE_FTP_COULDNT_SET_ASCII', 29);
define ('CURLE_FTP_PORT_FAILED', 30);
define ('CURLE_FTP_COULDNT_USE_REST', 31);
define ('CURLE_FTP_COULDNT_GET_SIZE', 32);
define ('CURLE_HTTP_RANGE_ERROR', 33);
define ('CURLE_HTTP_POST_ERROR', 34);
define ('CURLE_SSL_CONNECT_ERROR', 35);
define ('CURLE_FTP_BAD_DOWNLOAD_RESUME', 36);
define ('CURLE_FILE_COULDNT_READ_FILE', 37);
define ('CURLE_LDAP_CANNOT_BIND', 38);
define ('CURLE_LDAP_SEARCH_FAILED', 39);
define ('CURLE_LIBRARY_NOT_FOUND', 40);
define ('CURLE_FUNCTION_NOT_FOUND', 41);
define ('CURLE_ABORTED_BY_CALLBACK', 42);
define ('CURLE_BAD_FUNCTION_ARGUMENT', 43);
define ('CURLE_BAD_CALLING_ORDER', 44);
define ('CURLE_HTTP_PORT_FAILED', 45);
define ('CURLE_BAD_PASSWORD_ENTERED', 46);
define ('CURLE_TOO_MANY_REDIRECTS', 47);
define ('CURLE_UNKNOWN_TELNET_OPTION', 48);
define ('CURLE_TELNET_OPTION_SYNTAX', 49);
define ('CURLE_OBSOLETE', 50);
define ('CURLE_SSL_PEER_CERTIFICATE', 51);
define ('CURLE_GOT_NOTHING', 52);
define ('CURLE_SSL_ENGINE_NOTFOUND', 53);
define ('CURLE_SSL_ENGINE_SETFAILED', 54);
define ('CURLE_SEND_ERROR', 55);
define ('CURLE_RECV_ERROR', 56);
define ('CURLE_SHARE_IN_USE', 57);
define ('CURLE_SSL_CERTPROBLEM', 58);
define ('CURLE_SSL_CIPHER', 59);
define ('CURLE_SSL_CACERT', 60);
define ('CURLE_BAD_CONTENT_ENCODING', 61);
define ('CURLE_LDAP_INVALID_URL', 62);
define ('CURLE_FILESIZE_EXCEEDED', 63);
define ('CURLE_FTP_SSL_FAILED', 64);
define ('CURLPROXY_HTTP', 0);
define ('CURLPROXY_SOCKS4', 4);
define ('CURLPROXY_SOCKS5', 5);
define ('CURL_NETRC_OPTIONAL', 1);
define ('CURL_NETRC_IGNORED', 0);
define ('CURL_NETRC_REQUIRED', 2);
define ('CURL_HTTP_VERSION_NONE', 0);
define ('CURL_HTTP_VERSION_1_0', 1);
define ('CURL_HTTP_VERSION_1_1', 2);
define ('CURLM_CALL_MULTI_PERFORM', -1);
define ('CURLM_OK', 0);
define ('CURLM_BAD_HANDLE', 1);
define ('CURLM_BAD_EASY_HANDLE', 2);
define ('CURLM_OUT_OF_MEMORY', 3);
define ('CURLM_INTERNAL_ERROR', 4);
define ('CURLMSG_DONE', 1);

/**
 * Available since PHP 5.1.0
 * @link http://php.net/manual/en/curl.constants.php
 */
define ('CURLOPT_FTPSSLAUTH', 129);

/**
 * Available since PHP 5.1.0
 * @link http://php.net/manual/en/curl.constants.php
 */
define ('CURLFTPAUTH_DEFAULT', 0);

/**
 * Available since PHP 5.1.0
 * @link http://php.net/manual/en/curl.constants.php
 */
define ('CURLFTPAUTH_SSL', 1);

/**
 * Available since PHP 5.1.0
 * @link http://php.net/manual/en/curl.constants.php
 */
define ('CURLFTPAUTH_TLS', 2);

/**
 * Available since PHP 5.2.0
 * @link http://php.net/manual/en/curl.constants.php
 */
define ('CURLOPT_FTP_SSL', 119);

/**
 * Available since PHP 5.2.0
 * @link http://php.net/manual/en/curl.constants.php
 */
define ('CURLFTPSSL_NONE', 0);

/**
 * Available since PHP 5.2.0
 * @link http://php.net/manual/en/curl.constants.php
 */
define ('CURLFTPSSL_TRY', 1);

/**
 * Available since PHP 5.2.0
 * @link http://php.net/manual/en/curl.constants.php
 */
define ('CURLFTPSSL_CONTROL', 2);

/**
 * Available since PHP 5.2.0
 * @link http://php.net/manual/en/curl.constants.php
 */
define ('CURLFTPSSL_ALL', 3);
define ('CURLOPT_CERTINFO', 172);
define ('CURLOPT_POSTREDIR', 161);
define ('CURLSSH_AUTH_NONE', 0);
define ('CURLSSH_AUTH_PUBLICKEY', 1);
define ('CURLSSH_AUTH_PASSWORD', 2);
define ('CURLSSH_AUTH_HOST', 4);
define ('CURLSSH_AUTH_KEYBOARD', 8);
define ('CURLSSH_AUTH_DEFAULT', -1);
define ('CURLOPT_SSH_AUTH_TYPES', 151);
define ('CURLOPT_KEYPASSWD', 10026);
define ('CURLOPT_SSH_PUBLIC_KEYFILE', 10152);
define ('CURLOPT_SSH_PRIVATE_KEYFILE', 10153);
define ('CURLOPT_SSH_HOST_PUBLIC_KEY_MD5', 10162);
define ('CURLE_SSH', 79);
define ('CURLOPT_REDIR_PROTOCOLS', 182);
define ('CURLOPT_PROTOCOLS', 181);
define ('CURLPROTO_HTTP', 1);
define ('CURLPROTO_HTTPS', 2);
define ('CURLPROTO_FTP', 4);
define ('CURLPROTO_FTPS', 8);
define ('CURLPROTO_SCP', 16);
define ('CURLPROTO_SFTP', 32);
define ('CURLPROTO_TELNET', 64);
define ('CURLPROTO_LDAP', 128);
define ('CURLPROTO_LDAPS', 256);
define ('CURLPROTO_DICT', 512);
define ('CURLPROTO_FILE', 1024);
define ('CURLPROTO_TFTP', 2048);
define ('CURLPROTO_ALL', -1);
define ('CURLOPT_FTP_FILEMETHOD', 138);
define ('CURLOPT_FTP_SKIP_PASV_IP', 137);
define ('CURLFTPMETHOD_MULTICWD', 1);
define ('CURLFTPMETHOD_NOCWD', 2);
define ('CURLFTPMETHOD_SINGLECWD', 3);

// End of curl v.
?>
