<?php

// Start of radius v.1.2.4

/**
 * (PECL radius &gt;= 1.1.0)<br/>
 * Creates a Radius handle for authentication
 * @link http://php.net/manual/en/function.radius-auth-open.php
 * @return resource a handle on success, false on error. This function only fails if
 * insufficient memory is available.
 * @jms-builtin
 */
function radius_auth_open () {}

/**
 * (PECL radius &gt;= 1.1.0)<br/>
 * Creates a Radius handle for accounting
 * @link http://php.net/manual/en/function.radius-acct-open.php
 * @return resource a handle on success, false on error. This function only fails if
 * insufficient memory is available.
 * @jms-builtin
 */
function radius_acct_open () {}

/**
 * (PECL radius &gt;= 1.1.0)<br/>
 * Frees all ressources
 * @link http://php.net/manual/en/function.radius-close.php
 * @param resource $radius_handle
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function radius_close ($radius_handle) {}

/**
 * (PECL radius &gt;= 1.1.0)<br/>
 * Returns an error message
 * @link http://php.net/manual/en/function.radius-strerror.php
 * @param resource $radius_handle
 * @return string error messages as string from failed radius functions.
 * @jms-builtin
 */
function radius_strerror ($radius_handle) {}

/**
 * (PECL radius &gt;= 1.1.0)<br/>
 * Causes the library to read the given configuration file
 * @link http://php.net/manual/en/function.radius-config.php
 * @param resource $radius_handle <p>
 * </p>
 * @param string $file <p>
 * The pathname of the configuration file is passed as the file argument
 * to <b>radius_config</b>. The library can also be
 * configured programmatically by calls to
 * <b>radius_add_server</b>.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function radius_config ($radius_handle, $file) {}

/**
 * (PECL radius &gt;= 1.1.0)<br/>
 * Adds a server
 * @link http://php.net/manual/en/function.radius-add-server.php
 * @param resource $radius_handle
 * @param string $hostname <p>
 * The <i>hostname</i> parameter specifies the server host,
 * either as a fully qualified domain name or as a dotted-quad IP address
 * in text form.
 * </p>
 * @param int $port <p>
 * The <i>port</i> specifies the UDP port to contact on
 * the server. If port is given as 0, the library looks up the
 * radius/udp or
 * radacct/udp service in the
 * network services database, and uses the port found there. If no entry
 * is found, the library uses the standard Radius ports, 1812 for
 * authentication and 1813 for accounting.
 * </p>
 * @param string $secret <p>
 * The shared secret for the server host is passed to the
 * <i>secret</i> parameter. The Radius protocol ignores
 * all but the leading 128 bytes of the shared secret.
 * </p>
 * @param int $timeout <p>
 * The timeout for receiving replies from the server is passed to the
 * <i>timeout</i> parameter, in units of seconds.
 * </p>
 * @param int $max_tries <p>
 * The maximum number of repeated requests to make before giving up is
 * passed into the <i>max_tries</i>.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function radius_add_server ($radius_handle, $hostname, $port, $secret, $timeout, $max_tries) {}

/**
 * (PECL radius &gt;= 1.1.0)<br/>
 * Create accounting or authentication request
 * @link http://php.net/manual/en/function.radius-create-request.php
 * @param resource $radius_handle
 * @param int $type <p>
 * Type is <b>RADIUS_ACCESS_REQUEST</b> or
 * <b>RADIUS_ACCOUNTING_REQUEST</b>.
 * </p>
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function radius_create_request ($radius_handle, $type) {}

/**
 * (PECL radius &gt;= 1.1.0)<br/>
 * Attaches a string attribute
 * @link http://php.net/manual/en/function.radius-put-string.php
 * @param resource $radius_handle
 * @param int $type
 * @param string $value
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function radius_put_string ($radius_handle, $type, $value) {}

/**
 * (PECL radius &gt;= 1.1.0)<br/>
 * Attaches an integer attribute
 * @link http://php.net/manual/en/function.radius-put-int.php
 * @param resource $radius_handle
 * @param int $type
 * @param int $value
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function radius_put_int ($radius_handle, $type, $value) {}

/**
 * (PECL radius &gt;= 1.1.0)<br/>
 * Attaches a binary attribute
 * @link http://php.net/manual/en/function.radius-put-attr.php
 * @param resource $radius_handle
 * @param int $type
 * @param string $value
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function radius_put_attr ($radius_handle, $type, $value) {}

/**
 * (PECL radius &gt;= 1.1.0)<br/>
 * Attaches an IP-Address attribute
 * @link http://php.net/manual/en/function.radius-put-addr.php
 * @param resource $radius_handle
 * @param int $type
 * @param string $addr
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function radius_put_addr ($radius_handle, $type, $addr) {}

/**
 * (PECL radius &gt;= 1.1.0)<br/>
 * Attaches a vendor specific string attribute
 * @link http://php.net/manual/en/function.radius-put-vendor-string.php
 * @param resource $radius_handle
 * @param int $vendor
 * @param int $type
 * @param string $value
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function radius_put_vendor_string ($radius_handle, $vendor, $type, $value) {}

/**
 * (PECL radius &gt;= 1.1.0)<br/>
 * Attaches a vendor specific integer attribute
 * @link http://php.net/manual/en/function.radius-put-vendor-int.php
 * @param resource $radius_handle
 * @param int $vendor
 * @param int $type
 * @param int $value
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function radius_put_vendor_int ($radius_handle, $vendor, $type, $value) {}

/**
 * (PECL radius &gt;= 1.1.0)<br/>
 * Attaches a vendor specific binary attribute
 * @link http://php.net/manual/en/function.radius-put-vendor-attr.php
 * @param resource $radius_handle
 * @param int $vendor
 * @param int $type
 * @param string $value
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function radius_put_vendor_attr ($radius_handle, $vendor, $type, $value) {}

/**
 * (PECL radius &gt;= 1.1.0)<br/>
 * Attaches a vendor specific IP-Address attribute
 * @link http://php.net/manual/en/function.radius-put-vendor-addr.php
 * @param resource $radius_handle
 * @param int $vendor
 * @param int $type
 * @param string $addr
 * @return bool true on success or false on failure.
 * @jms-builtin
 */
function radius_put_vendor_addr ($radius_handle, $vendor, $type, $addr) {}

/**
 * (PECL radius &gt;= 1.1.0)<br/>
 * Sends the request and waites for a reply
 * @link http://php.net/manual/en/function.radius-send-request.php
 * @param resource $radius_handle
 * @return int If a valid response is received, <b>radius_send_request</b>
 * returns the Radius code which specifies the type of the response. This will
 * typically be <b>RADIUS_ACCESS_ACCEPT</b>,
 * <b>RADIUS_ACCESS_REJECT</b>, or
 * <b>RADIUS_ACCESS_CHALLENGE</b>. If no valid response is
 * received, <b>radius_send_request</b> returns false.
 * @jms-builtin
 */
function radius_send_request ($radius_handle) {}

/**
 * (PECL radius &gt;= 1.1.0)<br/>
 * Extracts an attribute
 * @link http://php.net/manual/en/function.radius-get-attr.php
 * @param resource $radius_handle
 * @return mixed an associative array containing the attribute-type and the data, or
 * error number &lt;= 0.
 * @jms-builtin
 */
function radius_get_attr ($radius_handle) {}

/**
 * (PECL radius &gt;= 1.1.0)<br/>
 * Extracts a vendor specific attribute
 * @link http://php.net/manual/en/function.radius-get-vendor-attr.php
 * @param string $data
 * @return array an associative array containing the attribute-type, vendor and the
 * data, or false on error.
 * @jms-builtin
 */
function radius_get_vendor_attr ($data) {}

/**
 * (PECL radius &gt;= 1.1.0)<br/>
 * Converts raw data to IP-Address
 * @link http://php.net/manual/en/function.radius-cvt-addr.php
 * @param string $data
 * @return string
 * @jms-builtin
 */
function radius_cvt_addr ($data) {}

/**
 * (PECL radius &gt;= 1.1.0)<br/>
 * Converts raw data to integer
 * @link http://php.net/manual/en/function.radius-cvt-int.php
 * @param string $data
 * @return int
 * @jms-builtin
 */
function radius_cvt_int ($data) {}

/**
 * (PECL radius &gt;= 1.1.0)<br/>
 * Converts raw data to string
 * @link http://php.net/manual/en/function.radius-cvt-string.php
 * @param string $data
 * @return string
 * @jms-builtin
 */
function radius_cvt_string ($data) {}

/**
 * (PECL radius &gt;= 1.1.0)<br/>
 * Returns the request authenticator
 * @link http://php.net/manual/en/function.radius-request-authenticator.php
 * @param resource $radius_handle
 * @return string the request authenticator as string, or false on error.
 * @jms-builtin
 */
function radius_request_authenticator ($radius_handle) {}

/**
 * (PECL radius &gt;= 1.1.0)<br/>
 * Returns the shared secret
 * @link http://php.net/manual/en/function.radius-server-secret.php
 * @param resource $radius_handle
 * @return string the server's shared secret as string, or false on error.
 * @jms-builtin
 */
function radius_server_secret ($radius_handle) {}

/**
 * (PECL radius &gt;= 1.2.0)<br/>
 * Demangles data
 * @link http://php.net/manual/en/function.radius-demangle.php
 * @param resource $radius_handle
 * @param string $mangled
 * @return string the demangled string, or false on error.
 * @jms-builtin
 */
function radius_demangle ($radius_handle, $mangled) {}

/**
 * (PECL radius &gt;= 1.2.0)<br/>
 * Derives mppe-keys from mangled data
 * @link http://php.net/manual/en/function.radius-demangle-mppe-key.php
 * @param resource $radius_handle
 * @param string $mangled
 * @return string the demangled string, or false on error.
 * @jms-builtin
 */
function radius_demangle_mppe_key ($radius_handle, $mangled) {}


/**
 * Authentication Request
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_ACCESS_REQUEST', 1);

/**
 * Access accepted
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_ACCESS_ACCEPT', 2);

/**
 * Access rejected
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_ACCESS_REJECT', 3);

/**
 * Accounting request
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_ACCOUNTING_REQUEST', 4);

/**
 * Accounting response
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_ACCOUNTING_RESPONSE', 5);

/**
 * Accsess challenge
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_ACCESS_CHALLENGE', 11);

/**
 * Username
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_USER_NAME', 1);

/**
 * Password
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_USER_PASSWORD', 2);

/**
 * Chap Password: chappass = md5(ident + plaintextpass + challenge)
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_CHAP_PASSWORD', 3);

/**
 * NAS IP-Adress
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_NAS_IP_ADDRESS', 4);

/**
 * NAS Port
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_NAS_PORT', 5);

/**
 * <p>
 * Type of Service, one of:
 * <b>RADIUS_LOGIN</b>
 * <b>RADIUS_FRAMED</b>
 * <b>RADIUS_CALLBACK_LOGIN</b>
 * <b>RADIUS_CALLBACK_FRAMED</b>
 * <b>RADIUS_OUTBOUND</b>
 * <b>RADIUS_ADMINISTRATIVE</b>
 * <b>RADIUS_NAS_PROMPT</b>
 * <b>RADIUS_AUTHENTICATE_ONLY</b>
 * <b>RADIUS_CALLBACK_NAS_PROMPT</b>
 * </p>
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_SERVICE_TYPE', 6);
define ('RADIUS_LOGIN', 1);
define ('RADIUS_FRAMED', 2);
define ('RADIUS_CALLBACK_LOGIN', 3);
define ('RADIUS_CALLBACK_FRAMED', 4);
define ('RADIUS_OUTBOUND', 5);
define ('RADIUS_ADMINISTRATIVE', 6);
define ('RADIUS_NAS_PROMPT', 7);
define ('RADIUS_AUTHENTICATE_ONLY', 8);
define ('RADIUS_CALLBACK_NAS_PROMPT', 9);

/**
 * <p>
 * Framed Protocol, one of:
 * <b>RADIUS_PPP</b>
 * <b>RADIUS_SLIP</b>
 * <b>RADIUS_ARAP</b>
 * <b>RADIUS_GANDALF</b>
 * <b>RADIUS_XYLOGICS</b>
 * </p>
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_FRAMED_PROTOCOL', 7);
define ('RADIUS_PPP', 1);
define ('RADIUS_SLIP', 2);
define ('RADIUS_ARAP', 3);
define ('RADIUS_GANDALF', 4);
define ('RADIUS_XYLOGICS', 5);

/**
 * IP-Address
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_FRAMED_IP_ADDRESS', 8);

/**
 * Netmask
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_FRAMED_IP_NETMASK', 9);

/**
 * Routing
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_FRAMED_ROUTING', 10);

/**
 * Filter ID
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_FILTER_ID', 11);

/**
 * MTU
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_FRAMED_MTU', 12);

/**
 * <p>
 * Compression, one of:
 * <b>RADIUS_COMP_NONE</b>
 * <b>RADIUS_COMP_VJ</b>
 * <b>RADIUS_COMP_IPXHDR</b>
 * </p>
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_FRAMED_COMPRESSION', 13);
define ('RADIUS_COMP_NONE', 0);
define ('RADIUS_COMP_VJ', 1);
define ('RADIUS_COMP_IPXHDR', 2);

/**
 * Login IP Host
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_LOGIN_IP_HOST', 14);

/**
 * Login Service
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_LOGIN_SERVICE', 15);

/**
 * Login TCP Port
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_LOGIN_TCP_PORT', 16);

/**
 * Reply Message
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_REPLY_MESSAGE', 18);

/**
 * Callback Number
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_CALLBACK_NUMBER', 19);

/**
 * Callback ID
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_CALLBACK_ID', 20);

/**
 * Framed Route
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_FRAMED_ROUTE', 22);

/**
 * Framed IPX Network
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_FRAMED_IPX_NETWORK', 23);

/**
 * State
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_STATE', 24);

/**
 * Class
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_CLASS', 25);

/**
 * Vendor specific attribute
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_VENDOR_SPECIFIC', 26);

/**
 * Session timeout
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_SESSION_TIMEOUT', 27);

/**
 * Idle timeout
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_IDLE_TIMEOUT', 28);

/**
 * Termination action
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_TERMINATION_ACTION', 29);

/**
 * Called Station Id
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_CALLED_STATION_ID', 30);

/**
 * Calling Station Id
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_CALLING_STATION_ID', 31);

/**
 * NAS ID
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_NAS_IDENTIFIER', 32);

/**
 * Proxy State
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_PROXY_STATE', 33);

/**
 * Login LAT Service
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_LOGIN_LAT_SERVICE', 34);

/**
 * Login LAT Node
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_LOGIN_LAT_NODE', 35);

/**
 * Login LAT Group
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_LOGIN_LAT_GROUP', 36);

/**
 * Framed Appletalk Link
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_FRAMED_APPLETALK_LINK', 37);

/**
 * Framed Appletalk Network
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_FRAMED_APPLETALK_NETWORK', 38);

/**
 * Framed Appletalk Zone
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_FRAMED_APPLETALK_ZONE', 39);

/**
 * Challenge
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_CHAP_CHALLENGE', 60);

/**
 * <p>
 * NAS port type, one of:
 * <b>RADIUS_ASYNC</b>
 * <b>RADIUS_SYNC</b>
 * <b>RADIUS_ISDN_SYNC</b>
 * <b>RADIUS_ISDN_ASYNC_V120</b>
 * <b>RADIUS_ISDN_ASYNC_V110</b>
 * <b>RADIUS_VIRTUAL</b>
 * <b>RADIUS_PIAFS</b>
 * <b>RADIUS_HDLC_CLEAR_CHANNEL</b>
 * <b>RADIUS_X_25</b>
 * <b>RADIUS_X_75</b>
 * <b>RADIUS_G_3_FAX</b>
 * <b>RADIUS_SDSL</b>
 * <b>RADIUS_ADSL_CAP</b>
 * <b>RADIUS_ADSL_DMT</b>
 * <b>RADIUS_IDSL</b>
 * <b>RADIUS_ETHERNET</b>
 * <b>RADIUS_XDSL</b>
 * <b>RADIUS_CABLE</b>
 * <b>RADIUS_WIRELESS_OTHER</b>
 * <b>RADIUS_WIRELESS_IEEE_802_11</b>
 * </p>
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_NAS_PORT_TYPE', 61);
define ('RADIUS_ASYNC', 0);
define ('RADIUS_SYNC', 1);
define ('RADIUS_ISDN_SYNC', 2);
define ('RADIUS_ISDN_ASYNC_V120', 3);
define ('RADIUS_ISDN_ASYNC_V110', 4);
define ('RADIUS_VIRTUAL', 5);
define ('RADIUS_PIAFS', 6);
define ('RADIUS_HDLC_CLEAR_CHANNEL', 7);
define ('RADIUS_X_25', 8);
define ('RADIUS_X_75', 9);
define ('RADIUS_G_3_FAX', 10);
define ('RADIUS_SDSL', 11);
define ('RADIUS_ADSL_CAP', 12);
define ('RADIUS_ADSL_DMT', 13);
define ('RADIUS_IDSL', 14);
define ('RADIUS_ETHERNET', 15);
define ('RADIUS_XDSL', 16);
define ('RADIUS_CABLE', 17);
define ('RADIUS_WIRELESS_OTHER', 18);
define ('RADIUS_WIRELESS_IEEE_802_11', 19);

/**
 * Port Limit
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_PORT_LIMIT', 62);

/**
 * Login LAT Port
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_LOGIN_LAT_PORT', 63);

/**
 * Connect info
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_CONNECT_INFO', 77);
define ('RADIUS_NAS_IPV6_ADDRESS', 95);
define ('RADIUS_FRAMED_INTERFACE_ID', 96);
define ('RADIUS_FRAMED_IPV6_PREFIX', 97);
define ('RADIUS_LOGIN_IPV6_HOST', 98);
define ('RADIUS_FRAMED_IPV6_ROUTE', 99);
define ('RADIUS_FRAMED_IPV6_POOL', 100);

/**
 * <p>
 * Accounting status type, one of:
 * <b>RADIUS_START</b>
 * <b>RADIUS_STOP</b>
 * <b>RADIUS_ACCOUNTING_ON</b>
 * <b>RADIUS_ACCOUNTING_OFF</b>
 * </p>
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_ACCT_STATUS_TYPE', 40);
define ('RADIUS_START', 1);
define ('RADIUS_STOP', 2);
define ('RADIUS_ACCOUNTING_ON', 7);
define ('RADIUS_ACCOUNTING_OFF', 8);

/**
 * Accounting delay time
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_ACCT_DELAY_TIME', 41);

/**
 * Accounting input bytes
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_ACCT_INPUT_OCTETS', 42);

/**
 * Accounting output bytes
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_ACCT_OUTPUT_OCTETS', 43);

/**
 * Accounting session ID
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_ACCT_SESSION_ID', 44);

/**
 * <p>
 * Accounting authentic, one of:
 * <b>RADIUS_AUTH_RADIUS</b>
 * <b>RADIUS_AUTH_LOCAL</b>
 * <b>RADIUS_AUTH_REMOTE</b>
 * </p>
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_ACCT_AUTHENTIC', 45);
define ('RADIUS_AUTH_RADIUS', 1);
define ('RADIUS_AUTH_LOCAL', 2);
define ('RADIUS_AUTH_REMOTE', 3);

/**
 * Accounting session time
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_ACCT_SESSION_TIME', 46);

/**
 * Accounting input packets
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_ACCT_INPUT_PACKETS', 47);

/**
 * Accounting output packets
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_ACCT_OUTPUT_PACKETS', 48);

/**
 * <p>
 * Accounting terminate cause, one of:
 * <b>RADIUS_TERM_USER_REQUEST</b>
 * <b>RADIUS_TERM_LOST_CARRIER</b>
 * <b>RADIUS_TERM_LOST_SERVICE</b>
 * <b>RADIUS_TERM_IDLE_TIMEOUT</b>
 * <b>RADIUS_TERM_SESSION_TIMEOUT</b>
 * <b>RADIUS_TERM_ADMIN_RESET</b>
 * <b>RADIUS_TERM_ADMIN_REBOOT</b>
 * <b>RADIUS_TERM_PORT_ERROR</b>
 * <b>RADIUS_TERM_NAS_ERROR</b>
 * <b>RADIUS_TERM_NAS_REQUEST</b>
 * <b>RADIUS_TERM_NAS_REBOOT</b>
 * <b>RADIUS_TERM_PORT_UNNEEDED</b>
 * <b>RADIUS_TERM_PORT_PREEMPTED</b>
 * <b>RADIUS_TERM_PORT_SUSPENDED</b>
 * <b>RADIUS_TERM_SERVICE_UNAVAILABLE</b>
 * <b>RADIUS_TERM_CALLBACK</b>
 * <b>RADIUS_TERM_USER_ERROR</b>
 * <b>RADIUS_TERM_HOST_REQUEST</b>
 * </p>
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_ACCT_TERMINATE_CAUSE', 49);
define ('RADIUS_TERM_USER_REQUEST', 1);
define ('RADIUS_TERM_LOST_CARRIER', 2);
define ('RADIUS_TERM_LOST_SERVICE', 3);
define ('RADIUS_TERM_IDLE_TIMEOUT', 4);
define ('RADIUS_TERM_SESSION_TIMEOUT', 5);
define ('RADIUS_TERM_ADMIN_RESET', 6);
define ('RADIUS_TERM_ADMIN_REBOOT', 7);
define ('RADIUS_TERM_PORT_ERROR', 8);
define ('RADIUS_TERM_NAS_ERROR', 9);
define ('RADIUS_TERM_NAS_REQUEST', 10);
define ('RADIUS_TERM_NAS_REBOOT', 11);
define ('RADIUS_TERM_PORT_UNNEEDED', 12);
define ('RADIUS_TERM_PORT_PREEMPTED', 13);
define ('RADIUS_TERM_PORT_SUSPENDED', 14);
define ('RADIUS_TERM_SERVICE_UNAVAILABLE', 15);
define ('RADIUS_TERM_CALLBACK', 16);
define ('RADIUS_TERM_USER_ERROR', 17);
define ('RADIUS_TERM_HOST_REQUEST', 18);

/**
 * Accounting multi session ID
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_ACCT_MULTI_SESSION_ID', 50);

/**
 * Accounting link count
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_ACCT_LINK_COUNT', 51);

/**
 * <p>
 * Microsoft specific vendor attributes (RFC 2548), one of:
 * <b>RADIUS_MICROSOFT_MS_CHAP_RESPONSE</b>
 * <b>RADIUS_MICROSOFT_MS_CHAP_ERROR</b>
 * <b>RADIUS_MICROSOFT_MS_CHAP_PW_1</b>
 * <b>RADIUS_MICROSOFT_MS_CHAP_PW_2</b>
 * <b>RADIUS_MICROSOFT_MS_CHAP_LM_ENC_PW</b>
 * <b>RADIUS_MICROSOFT_MS_CHAP_NT_ENC_PW</b>
 * <b>RADIUS_MICROSOFT_MS_MPPE_ENCRYPTION_POLICY</b>
 * <b>RADIUS_MICROSOFT_MS_MPPE_ENCRYPTION_TYPES</b>
 * <b>RADIUS_MICROSOFT_MS_RAS_VENDOR</b>
 * <b>RADIUS_MICROSOFT_MS_CHAP_DOMAIN</b>
 * <b>RADIUS_MICROSOFT_MS_CHAP_CHALLENGE</b>
 * <b>RADIUS_MICROSOFT_MS_CHAP_MPPE_KEYS</b>
 * <b>RADIUS_MICROSOFT_MS_BAP_USAGE</b>
 * <b>RADIUS_MICROSOFT_MS_LINK_UTILIZATION_THRESHOLD</b>
 * <b>RADIUS_MICROSOFT_MS_LINK_DROP_TIME_LIMIT</b>
 * <b>RADIUS_MICROSOFT_MS_MPPE_SEND_KEY</b>
 * <b>RADIUS_MICROSOFT_MS_MPPE_RECV_KEY</b>
 * <b>RADIUS_MICROSOFT_MS_RAS_VERSION</b>
 * <b>RADIUS_MICROSOFT_MS_OLD_ARAP_PASSWORD</b>
 * <b>RADIUS_MICROSOFT_MS_NEW_ARAP_PASSWORD</b>
 * <b>RADIUS_MICROSOFT_MS_ARAP_PASSWORD_CHANGE_REASON</b>
 * <b>RADIUS_MICROSOFT_MS_FILTER</b>
 * <b>RADIUS_MICROSOFT_MS_ACCT_AUTH_TYPE</b>
 * <b>RADIUS_MICROSOFT_MS_ACCT_EAP_TYPE</b>
 * <b>RADIUS_MICROSOFT_MS_CHAP2_RESPONSE</b>
 * <b>RADIUS_MICROSOFT_MS_CHAP2_SUCCESS</b>
 * <b>RADIUS_MICROSOFT_MS_CHAP2_PW</b>
 * <b>RADIUS_MICROSOFT_MS_PRIMARY_DNS_SERVER</b>
 * <b>RADIUS_MICROSOFT_MS_SECONDARY_DNS_SERVER</b>
 * <b>RADIUS_MICROSOFT_MS_PRIMARY_NBNS_SERVER</b>
 * <b>RADIUS_MICROSOFT_MS_SECONDARY_NBNS_SERVER</b>
 * <b>RADIUS_MICROSOFT_MS_ARAP_CHALLENGE</b>
 * </p>
 * @link http://php.net/manual/en/radius.constants.php
 */
define ('RADIUS_VENDOR_MICROSOFT', 311);
define ('RADIUS_MICROSOFT_MS_CHAP_RESPONSE', 1);
define ('RADIUS_MICROSOFT_MS_CHAP_ERROR', 2);
define ('RADIUS_MICROSOFT_MS_CHAP_PW_1', 3);
define ('RADIUS_MICROSOFT_MS_CHAP_PW_2', 4);
define ('RADIUS_MICROSOFT_MS_CHAP_LM_ENC_PW', 5);
define ('RADIUS_MICROSOFT_MS_CHAP_NT_ENC_PW', 6);
define ('RADIUS_MICROSOFT_MS_MPPE_ENCRYPTION_POLICY', 7);
define ('RADIUS_MICROSOFT_MS_MPPE_ENCRYPTION_TYPES', 8);
define ('RADIUS_MICROSOFT_MS_RAS_VENDOR', 9);
define ('RADIUS_MICROSOFT_MS_CHAP_DOMAIN', 10);
define ('RADIUS_MICROSOFT_MS_CHAP_CHALLENGE', 11);
define ('RADIUS_MICROSOFT_MS_CHAP_MPPE_KEYS', 12);
define ('RADIUS_MICROSOFT_MS_BAP_USAGE', 13);
define ('RADIUS_MICROSOFT_MS_LINK_UTILIZATION_THRESHOLD', 14);
define ('RADIUS_MICROSOFT_MS_LINK_DROP_TIME_LIMIT', 15);
define ('RADIUS_MICROSOFT_MS_MPPE_SEND_KEY', 16);
define ('RADIUS_MICROSOFT_MS_MPPE_RECV_KEY', 17);
define ('RADIUS_MICROSOFT_MS_RAS_VERSION', 18);
define ('RADIUS_MICROSOFT_MS_OLD_ARAP_PASSWORD', 19);
define ('RADIUS_MICROSOFT_MS_NEW_ARAP_PASSWORD', 20);
define ('RADIUS_MICROSOFT_MS_ARAP_PASSWORD_CHANGE_REASON', 21);
define ('RADIUS_MICROSOFT_MS_FILTER', 22);
define ('RADIUS_MICROSOFT_MS_ACCT_AUTH_TYPE', 23);
define ('RADIUS_MICROSOFT_MS_ACCT_EAP_TYPE', 24);
define ('RADIUS_MICROSOFT_MS_CHAP2_RESPONSE', 25);
define ('RADIUS_MICROSOFT_MS_CHAP2_SUCCESS', 26);
define ('RADIUS_MICROSOFT_MS_CHAP2_PW', 27);
define ('RADIUS_MICROSOFT_MS_PRIMARY_DNS_SERVER', 28);
define ('RADIUS_MICROSOFT_MS_SECONDARY_DNS_SERVER', 29);
define ('RADIUS_MICROSOFT_MS_PRIMARY_NBNS_SERVER', 30);
define ('RADIUS_MICROSOFT_MS_SECONDARY_NBNS_SERVER', 31);
define ('RADIUS_MICROSOFT_MS_ARAP_CHALLENGE', 33);
define ('RADIUS_MPPE_KEY_LEN', 16);

// End of radius v.1.2.4
?>
