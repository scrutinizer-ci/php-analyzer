<?php

// Start of interbase v.

/**
 * (PHP 4, PHP 5)<br/>
 * Open a connection to an InterBase database
 * @link http://php.net/manual/en/function.ibase-connect.php
 * @param string $database [optional] <p>
 * The <i>database</i> argument has to be a valid path to
 * database file on the server it resides on. If the server is not local,
 * it must be prefixed with either 'hostname:' (TCP/IP), '//hostname/'
 * (NetBEUI) or 'hostname@' (IPX/SPX), depending on the connection
 * protocol used.
 * </p>
 * @param string $username [optional] <p>
 * The user name. Can be set with the
 * ibase.default_user <i>php.ini</i> directive.
 * </p>
 * @param string $password [optional] <p>
 * The password for <i>username</i>. Can be set with the
 * ibase.default_password <i>php.ini</i> directive.
 * </p>
 * @param string $charset [optional] <p>
 * <i>charset</i> is the default character set for a
 * database.
 * </p>
 * @param int $buffers [optional] <p>
 * <i>buffers</i> is the number of database buffers to
 * allocate for the server-side cache. If 0 or omitted, server chooses
 * its own default.
 * </p>
 * @param int $dialect [optional] <p>
 * <i>dialect</i> selects the default SQL dialect for any
 * statement executed within a connection, and it defaults to the highest
 * one supported by client libraries. Functional only with InterBase 6
 * and up.
 * </p>
 * @param string $role [optional] <p>
 * Functional only with InterBase 5 and up.
 * </p>
 * @param int $sync [optional]
 * @return resource an InterBase link identifier on success, or <b>FALSE</b> on error.
 * @jms-builtin
 */
function ibase_connect ($database = null, $username = null, $password = null, $charset = null, $buffers = null, $dialect = null, $role = null, $sync = null) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Open a persistent connection to an InterBase database
 * @link http://php.net/manual/en/function.ibase-pconnect.php
 * @param string $database [optional] <p>
 * The <i>database</i> argument has to be a valid path to
 * database file on the server it resides on. If the server is not local,
 * it must be prefixed with either 'hostname:' (TCP/IP), '//hostname/'
 * (NetBEUI) or 'hostname@' (IPX/SPX), depending on the connection
 * protocol used.
 * </p>
 * @param string $username [optional] <p>
 * The user name. Can be set with the
 * ibase.default_user <i>php.ini</i> directive.
 * </p>
 * @param string $password [optional] <p>
 * The password for <i>username</i>. Can be set with the
 * ibase.default_password <i>php.ini</i> directive.
 * </p>
 * @param string $charset [optional] <p>
 * <i>charset</i> is the default character set for a
 * database.
 * </p>
 * @param int $buffers [optional] <p>
 * <i>buffers</i> is the number of database buffers to
 * allocate for the server-side cache. If 0 or omitted, server chooses
 * its own default.
 * </p>
 * @param int $dialect [optional] <p>
 * <i>dialect</i> selects the default SQL dialect for any
 * statement executed within a connection, and it defaults to the highest
 * one supported by client libraries. Functional only with InterBase 6
 * and up.
 * </p>
 * @param string $role [optional] <p>
 * Functional only with InterBase 5 and up.
 * </p>
 * @param int $sync [optional]
 * @return resource an InterBase link identifier on success, or <b>FALSE</b> on error.
 * @jms-builtin
 */
function ibase_pconnect ($database = null, $username = null, $password = null, $charset = null, $buffers = null, $dialect = null, $role = null, $sync = null) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Close a connection to an InterBase database
 * @link http://php.net/manual/en/function.ibase-close.php
 * @param resource $connection_id [optional] <p>
 * An InterBase link identifier returned from
 * <b>ibase_connect</b>. If omitted, the last opened link
 * is assumed.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function ibase_close ($connection_id = null) {}

/**
 * (PHP 5)<br/>
 * Drops a database
 * @link http://php.net/manual/en/function.ibase-drop-db.php
 * @param resource $connection [optional] <p>
 * An InterBase link identifier. If omitted, the last opened link is
 * assumed.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function ibase_drop_db ($connection = null) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Execute a query on an InterBase database
 * @link http://php.net/manual/en/function.ibase-query.php
 * @param resource $link_identifier [optional] <p>
 * An InterBase link identifier. If omitted, the last opened link is
 * assumed.
 * </p>
 * @param string $query <p>
 * An InterBase query.
 * </p>
 * @param int $bind_args [optional]
 * @return resource If the query raises an error, returns <b>FALSE</b>. If it is successful and
 * there is a (possibly empty) result set (such as with a SELECT query),
 * returns a result identifier. If the query was successful and there were
 * no results, returns <b>TRUE</b>.
 * </p>
 * <p>
 * In PHP 5.0.0 and up, this function will return the number of rows
 * affected by the query for INSERT, UPDATE and DELETE statements. In order
 * to retain backward compatibility, it will return <b>TRUE</b> for these
 * statements if the query succeeded without affecting any rows.
 * @jms-builtin
 */
function ibase_query ($link_identifier = null, $query, $bind_args = null) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Fetch a row from an InterBase database
 * @link http://php.net/manual/en/function.ibase-fetch-row.php
 * @param resource $result_identifier <p>
 * An InterBase result identifier.
 * </p>
 * @param int $fetch_flag [optional] <p>
 * <i>fetch_flag</i> is a combination of the constants
 * <b>IBASE_TEXT</b> and <b>IBASE_UNIXTIME</b>
 * ORed together. Passing <b>IBASE_TEXT</b> will cause this
 * function to return BLOB contents instead of BLOB ids. Passing
 * <b>IBASE_UNIXTIME</b> will cause this function to return
 * date/time values as Unix timestamps instead of as formatted strings.
 * </p>
 * @return array an array that corresponds to the fetched row, or <b>FALSE</b> if there
 * are no more rows. Each result column is stored in an array offset,
 * starting at offset 0.
 * @jms-builtin
 */
function ibase_fetch_row ($result_identifier, $fetch_flag = 0) {}

/**
 * (PHP 4 &gt;= 4.3.0, PHP 5)<br/>
 * Fetch a result row from a query as an associative array
 * @link http://php.net/manual/en/function.ibase-fetch-assoc.php
 * @param resource $result <p>
 * The result handle.
 * </p>
 * @param int $fetch_flag [optional] <p>
 * <i>fetch_flag</i> is a combination of the constants
 * <b>IBASE_TEXT</b> and <b>IBASE_UNIXTIME</b>
 * ORed together. Passing <b>IBASE_TEXT</b> will cause this
 * function to return BLOB contents instead of BLOB ids. Passing
 * <b>IBASE_UNIXTIME</b> will cause this function to return
 * date/time values as Unix timestamps instead of as formatted strings.
 * </p>
 * @return array an associative array that corresponds to the fetched row.
 * Subsequent calls will return the next row in the result set, or <b>FALSE</b> if
 * there are no more rows.
 * @jms-builtin
 */
function ibase_fetch_assoc ($result, $fetch_flag = 0) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Get an object from a InterBase database
 * @link http://php.net/manual/en/function.ibase-fetch-object.php
 * @param resource $result_id <p>
 * An InterBase result identifier obtained either by
 * <b>ibase_query</b> or <b>ibase_execute</b>.
 * </p>
 * @param int $fetch_flag [optional] <p>
 * <i>fetch_flag</i> is a combination of the constants
 * <b>IBASE_TEXT</b> and <b>IBASE_UNIXTIME</b>
 * ORed together. Passing <b>IBASE_TEXT</b> will cause this
 * function to return BLOB contents instead of BLOB ids. Passing
 * <b>IBASE_UNIXTIME</b> will cause this function to return
 * date/time values as Unix timestamps instead of as formatted strings.
 * </p>
 * @return object an object with the next row information, or <b>FALSE</b> if there are
 * no more rows.
 * @jms-builtin
 */
function ibase_fetch_object ($result_id, $fetch_flag = 0) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Free a result set
 * @link http://php.net/manual/en/function.ibase-free-result.php
 * @param resource $result_identifier <p>
 * A result set created by <b>ibase_query</b> or
 * <b>ibase_execute</b>.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function ibase_free_result ($result_identifier) {}

/**
 * (PHP 5)<br/>
 * Assigns a name to a result set
 * @link http://php.net/manual/en/function.ibase-name-result.php
 * @param resource $result <p>
 * An InterBase result set.
 * </p>
 * @param string $name <p>
 * The name to be assigned.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function ibase_name_result ($result, $name) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Prepare a query for later binding of parameter placeholders and execution
 * @link http://php.net/manual/en/function.ibase-prepare.php
 * @param string $query <p>
 * An InterBase query.
 * </p>
 * @return resource a prepared query handle, or <b>FALSE</b> on error.
 * @jms-builtin
 */
function ibase_prepare ($query) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Execute a previously prepared query
 * @link http://php.net/manual/en/function.ibase-execute.php
 * @param resource $query <p>
 * An InterBase query prepared by <b>ibase_prepare</b>.
 * </p>
 * @param mixed $bind_arg [optional]
 * @param mixed $_ [optional]
 * @return resource If the query raises an error, returns <b>FALSE</b>. If it is successful and
 * there is a (possibly empty) result set (such as with a SELECT query),
 * returns a result identifier. If the query was successful and there were
 * no results, returns <b>TRUE</b>.
 * </p>
 * <p>
 * In PHP 5.0.0 and up, this function returns the number of rows affected by
 * the query (if > 0 and applicable to the statement type). A query that
 * succeeded, but did not affect any rows (e.g. an UPDATE of a non-existent
 * record) will return <b>TRUE</b>.
 * @jms-builtin
 */
function ibase_execute ($query, $bind_arg = null, $_ = null) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Free memory allocated by a prepared query
 * @link http://php.net/manual/en/function.ibase-free-query.php
 * @param resource $query <p>
 * A query prepared with <b>ibase_prepare</b>.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function ibase_free_query ($query) {}

/**
 * (PHP 5)<br/>
 * Increments the named generator and returns its new value
 * @link http://php.net/manual/en/function.ibase-gen-id.php
 * @param string $generator
 * @param int $increment [optional]
 * @param resource $link_identifier [optional]
 * @return mixed new generator value as integer, or as string if the value is too big.
 * @jms-builtin
 */
function ibase_gen_id ($generator, $increment = 1, $link_identifier = null) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Get the number of fields in a result set
 * @link http://php.net/manual/en/function.ibase-num-fields.php
 * @param resource $result_id <p>
 * An InterBase result identifier.
 * </p>
 * @return int the number of fields as an integer.
 * @jms-builtin
 */
function ibase_num_fields ($result_id) {}

/**
 * (PHP 5)<br/>
 * Return the number of parameters in a prepared query
 * @link http://php.net/manual/en/function.ibase-num-params.php
 * @param resource $query <p>
 * The prepared query handle.
 * </p>
 * @return int the number of parameters as an integer.
 * @jms-builtin
 */
function ibase_num_params ($query) {}

/**
 * (PHP 5)<br/>
 * Return the number of rows that were affected by the previous query
 * @link http://php.net/manual/en/function.ibase-affected-rows.php
 * @param resource $link_identifier [optional] <p>
 * A transaction context. If <i>link_identifier</i> is a
 * connection resource, its default transaction is used.
 * </p>
 * @return int the number of rows as an integer.
 * @jms-builtin
 */
function ibase_affected_rows ($link_identifier = null) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Get information about a field
 * @link http://php.net/manual/en/function.ibase-field-info.php
 * @param resource $result <p>
 * An InterBase result identifier.
 * </p>
 * @param int $field_number <p>
 * Field offset.
 * </p>
 * @return array an array with the following keys: name,
 * alias, relation,
 * length and type.
 * @jms-builtin
 */
function ibase_field_info ($result, $field_number) {}

/**
 * (PHP 5)<br/>
 * Return information about a parameter in a prepared query
 * @link http://php.net/manual/en/function.ibase-param-info.php
 * @param resource $query <p>
 * An InterBase prepared query handle.
 * </p>
 * @param int $param_number <p>
 * Parameter offset.
 * </p>
 * @return array an array with the following keys: name,
 * alias, relation,
 * length and type.
 * @jms-builtin
 */
function ibase_param_info ($query, $param_number) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Begin a transaction
 * @link http://php.net/manual/en/function.ibase-trans.php
 * @param int $trans_args [optional] <p>
 * <i>trans_args</i> can be a combination of
 * <b>IBASE_READ</b>,
 * <b>IBASE_WRITE</b>,
 * <b>IBASE_COMMITTED</b>,
 * <b>IBASE_CONSISTENCY</b>,
 * <b>IBASE_CONCURRENCY</b>,
 * <b>IBASE_REC_VERSION</b>,
 * <b>IBASE_REC_NO_VERSION</b>,
 * <b>IBASE_WAIT</b> and
 * <b>IBASE_NOWAIT</b>.
 * </p>
 * @param resource $link_identifier [optional] <p>
 * An InterBase link identifier. If omitted, the last opened link is
 * assumed.
 * </p>
 * @return resource a transaction handle, or <b>FALSE</b> on error.
 * @jms-builtin
 */
function ibase_trans ($trans_args = null, $link_identifier = null) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Commit a transaction
 * @link http://php.net/manual/en/function.ibase-commit.php
 * @param resource $link_or_trans_identifier [optional] <p>
 * If called without an argument, this function commits the default
 * transaction of the default link. If the argument is a connection
 * identifier, the default transaction of the corresponding connection
 * will be committed. If the argument is a transaction identifier, the
 * corresponding transaction will be committed.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function ibase_commit ($link_or_trans_identifier = null) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Roll back a transaction
 * @link http://php.net/manual/en/function.ibase-rollback.php
 * @param resource $link_or_trans_identifier [optional] <p>
 * If called without an argument, this function rolls back the default
 * transaction of the default link. If the argument is a connection
 * identifier, the default transaction of the corresponding connection
 * will be rolled back. If the argument is a transaction identifier, the
 * corresponding transaction will be rolled back.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function ibase_rollback ($link_or_trans_identifier = null) {}

/**
 * (PHP 5)<br/>
 * Commit a transaction without closing it
 * @link http://php.net/manual/en/function.ibase-commit-ret.php
 * @param resource $link_or_trans_identifier [optional] <p>
 * If called without an argument, this function commits the default
 * transaction of the default link. If the argument is a connection
 * identifier, the default transaction of the corresponding connection
 * will be committed. If the argument is a transaction identifier, the
 * corresponding transaction will be committed. The transaction context
 * will be retained, so statements executed from within this transaction
 * will not be invalidated.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function ibase_commit_ret ($link_or_trans_identifier = null) {}

/**
 * (PHP 5)<br/>
 * Roll back a transaction without closing it
 * @link http://php.net/manual/en/function.ibase-rollback-ret.php
 * @param resource $link_or_trans_identifier [optional] <p>
 * If called without an argument, this function rolls back the default
 * transaction of the default link. If the argument is a connection
 * identifier, the default transaction of the corresponding connection
 * will be rolled back. If the argument is a transaction identifier, the
 * corresponding transaction will be rolled back. The transaction context
 * will be retained, so statements executed from within this transaction
 * will not be invalidated.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function ibase_rollback_ret ($link_or_trans_identifier = null) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Return blob length and other useful info
 * @link http://php.net/manual/en/function.ibase-blob-info.php
 * @param resource $link_identifier <p>
 * An InterBase link identifier. If omitted, the last opened link is
 * assumed.
 * </p>
 * @param string $blob_id <p>
 * A BLOB id.
 * </p>
 * @return array an array containing information about a BLOB. The information returned
 * consists of the length of the BLOB, the number of segments it contains, the size
 * of the largest segment, and whether it is a stream BLOB or a segmented BLOB.
 * @jms-builtin
 */
function ibase_blob_info ($link_identifier, $blob_id) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Create a new blob for adding data
 * @link http://php.net/manual/en/function.ibase-blob-create.php
 * @param resource $link_identifier [optional] <p>
 * An InterBase link identifier. If omitted, the last opened link is
 * assumed.
 * </p>
 * @return resource a BLOB handle for later use with
 * <b>ibase_blob_add</b> or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function ibase_blob_create ($link_identifier = null) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Add data into a newly created blob
 * @link http://php.net/manual/en/function.ibase-blob-add.php
 * @param resource $blob_handle <p>
 * A blob handle opened with <b>ibase_blob_create</b>.
 * </p>
 * @param string $data <p>
 * The data to be added.
 * </p>
 * @return void No value is returned.
 * @jms-builtin
 */
function ibase_blob_add ($blob_handle, $data) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Cancel creating blob
 * @link http://php.net/manual/en/function.ibase-blob-cancel.php
 * @param resource $blob_handle <p>
 * A BLOB handle opened with <b>ibase_blob_create</b>.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function ibase_blob_cancel ($blob_handle) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Close blob
 * @link http://php.net/manual/en/function.ibase-blob-close.php
 * @param resource $blob_handle <p>
 * A BLOB handle opened with <b>ibase_blob_create</b> or
 * <b>ibase_blob_open</b>.
 * </p>
 * @return mixed If the BLOB was being read, this function returns <b>TRUE</b> on success, if
 * the BLOB was being written to, this function returns a string containing
 * the BLOB id that has been assigned to it by the database. On failure, this
 * function returns <b>FALSE</b>.
 * @jms-builtin
 */
function ibase_blob_close ($blob_handle) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Open blob for retrieving data parts
 * @link http://php.net/manual/en/function.ibase-blob-open.php
 * @param resource $link_identifier <p>
 * An InterBase link identifier. If omitted, the last opened link is
 * assumed.
 * </p>
 * @param string $blob_id <p>
 * A BLOB id.
 * </p>
 * @return resource a BLOB handle for later use with
 * <b>ibase_blob_get</b> or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function ibase_blob_open ($link_identifier, $blob_id) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Get len bytes data from open blob
 * @link http://php.net/manual/en/function.ibase-blob-get.php
 * @param resource $blob_handle <p>
 * A BLOB handle opened with <b>ibase_blob_open</b>.
 * </p>
 * @param int $len <p>
 * Size of returned data.
 * </p>
 * @return string at most <i>len</i> bytes from the BLOB, or <b>FALSE</b>
 * on failure.
 * @jms-builtin
 */
function ibase_blob_get ($blob_handle, $len) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Output blob contents to browser
 * @link http://php.net/manual/en/function.ibase-blob-echo.php
 * @param string $blob_id
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function ibase_blob_echo ($blob_id) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Create blob, copy file in it, and close it
 * @link http://php.net/manual/en/function.ibase-blob-import.php
 * @param resource $link_identifier <p>
 * An InterBase link identifier. If omitted, the last opened link is
 * assumed.
 * </p>
 * @param resource $file_handle <p>
 * The file handle is a handle returned by <b>fopen</b>.
 * </p>
 * @return string the BLOB id on success, or <b>FALSE</b> on error.
 * @jms-builtin
 */
function ibase_blob_import ($link_identifier, $file_handle) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Return error messages
 * @link http://php.net/manual/en/function.ibase-errmsg.php
 * @return string the error message as a string, or <b>FALSE</b> if no error occurred.
 * @jms-builtin
 */
function ibase_errmsg () {}

/**
 * (PHP 5)<br/>
 * Return an error code
 * @link http://php.net/manual/en/function.ibase-errcode.php
 * @return int the error code as an integer, or <b>FALSE</b> if no error occurred.
 * @jms-builtin
 */
function ibase_errcode () {}

/**
 * (PHP 4 &gt;= 4.2.0, PHP 5)<br/>
 * Add a user to a security database (only for IB6 or later)
 * @link http://php.net/manual/en/function.ibase-add-user.php
 * @param resource $service_handle
 * @param string $user_name
 * @param string $password
 * @param string $first_name [optional]
 * @param string $middle_name [optional]
 * @param string $last_name [optional]
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function ibase_add_user ($service_handle, $user_name, $password, $first_name = null, $middle_name = null, $last_name = null) {}

/**
 * (PHP 4 &gt;= 4.2.0, PHP 5)<br/>
 * Modify a user to a security database (only for IB6 or later)
 * @link http://php.net/manual/en/function.ibase-modify-user.php
 * @param resource $service_handle
 * @param string $user_name
 * @param string $password
 * @param string $first_name [optional]
 * @param string $middle_name [optional]
 * @param string $last_name [optional]
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function ibase_modify_user ($service_handle, $user_name, $password, $first_name = null, $middle_name = null, $last_name = null) {}

/**
 * (PHP 4 &gt;= 4.2.0, PHP 5)<br/>
 * Delete a user from a security database (only for IB6 or later)
 * @link http://php.net/manual/en/function.ibase-delete-user.php
 * @param resource $service_handle
 * @param string $user_name
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function ibase_delete_user ($service_handle, $user_name) {}

/**
 * (PHP 5)<br/>
 * Connect to the service manager
 * @link http://php.net/manual/en/function.ibase-service-attach.php
 * @param string $host
 * @param string $dba_username
 * @param string $dba_password
 * @return resource
 * @jms-builtin
 */
function ibase_service_attach ($host, $dba_username, $dba_password) {}

/**
 * (PHP 5)<br/>
 * Disconnect from the service manager
 * @link http://php.net/manual/en/function.ibase-service-detach.php
 * @param resource $service_handle
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function ibase_service_detach ($service_handle) {}

/**
 * (PHP 5)<br/>
 * Initiates a backup task in the service manager and returns immediately
 * @link http://php.net/manual/en/function.ibase-backup.php
 * @param resource $service_handle
 * @param string $source_db
 * @param string $dest_file
 * @param int $options [optional]
 * @param bool $verbose [optional]
 * @return mixed
 * @jms-builtin
 */
function ibase_backup ($service_handle, $source_db, $dest_file, $options = 0, $verbose = false) {}

/**
 * (PHP 5)<br/>
 * Initiates a restore task in the service manager and returns immediately
 * @link http://php.net/manual/en/function.ibase-restore.php
 * @param resource $service_handle
 * @param string $source_file
 * @param string $dest_db
 * @param int $options [optional]
 * @param bool $verbose [optional]
 * @return mixed
 * @jms-builtin
 */
function ibase_restore ($service_handle, $source_file, $dest_db, $options = 0, $verbose = false) {}

/**
 * (PHP 5)<br/>
 * Execute a maintenance command on the database server
 * @link http://php.net/manual/en/function.ibase-maintain-db.php
 * @param resource $service_handle
 * @param string $db
 * @param int $action
 * @param int $argument [optional]
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function ibase_maintain_db ($service_handle, $db, $action, $argument = 0) {}

/**
 * (PHP 5)<br/>
 * Request statistics about a database
 * @link http://php.net/manual/en/function.ibase-db-info.php
 * @param resource $service_handle
 * @param string $db
 * @param int $action
 * @param int $argument [optional]
 * @return string
 * @jms-builtin
 */
function ibase_db_info ($service_handle, $db, $action, $argument = 0) {}

/**
 * (PHP 5)<br/>
 * Request information about a database server
 * @link http://php.net/manual/en/function.ibase-server-info.php
 * @param resource $service_handle
 * @param int $action
 * @return string
 * @jms-builtin
 */
function ibase_server_info ($service_handle, $action) {}

/**
 * (PHP 5)<br/>
 * Wait for an event to be posted by the database
 * @link http://php.net/manual/en/function.ibase-wait-event.php
 * @param string $event_name1 <p>
 * The event name.
 * </p>
 * @param string $event_name2 [optional]
 * @param string $_ [optional]
 * @return string the name of the event that was posted.
 * @jms-builtin
 */
function ibase_wait_event ($event_name1, $event_name2 = null, $_ = null) {}

/**
 * (PHP 5)<br/>
 * Register a callback function to be called when events are posted
 * @link http://php.net/manual/en/function.ibase-set-event-handler.php
 * @param callable $event_handler <p>
 * The callback is called with the event name and the link resource as
 * arguments whenever one of the specified events is posted by the
 * database.
 * </p>
 * <p>
 * The callback must return <b>FALSE</b> if the event handler should be
 * canceled. Any other return value is ignored. This function accepts up
 * to 15 event arguments.
 * </p>
 * @param string $event_name1 <p>
 * An event name.
 * </p>
 * @param string $event_name2 [optional] <p>
 * At most 15 events allowed.
 * </p>
 * @param string $_ [optional]
 * @return resource The return value is an event resource. This resource can be used to free
 * the event handler using <b>ibase_free_event_handler</b>.
 * @jms-builtin
 */
function ibase_set_event_handler (callable $event_handler, $event_name1, $event_name2 = null, $_ = null) {}

/**
 * (PHP 5)<br/>
 * Cancels a registered event handler
 * @link http://php.net/manual/en/function.ibase-free-event-handler.php
 * @param resource $event <p>
 * An event resource, created by
 * <b>ibase_set_event_handler</b>.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function ibase_free_event_handler ($event) {}

/**
 * @param $database
 * @param $username [optional]
 * @param $password [optional]
 * @param $charset [optional]
 * @param $buffers [optional]
 * @param $dialect [optional]
 * @param $role [optional]
 * @jms-builtin
 */
function fbird_connect ($database, $username, $password, $charset, $buffers, $dialect, $role) {}

/**
 * @param $database
 * @param $username [optional]
 * @param $password [optional]
 * @param $charset [optional]
 * @param $buffers [optional]
 * @param $dialect [optional]
 * @param $role [optional]
 * @jms-builtin
 */
function fbird_pconnect ($database, $username, $password, $charset, $buffers, $dialect, $role) {}

/**
 * @param $link_identifier [optional]
 * @jms-builtin
 */
function fbird_close ($link_identifier) {}

/**
 * @param $link_identifier [optional]
 * @jms-builtin
 */
function fbird_drop_db ($link_identifier) {}

/**
 * @param $link_identifier [optional]
 * @param $link_identifier [optional]
 * @param $query [optional]
 * @param $bind_arg [optional]
 * @param $bind_arg [optional]
 * @jms-builtin
 */
function fbird_query ($link_identifier, $link_identifier, $query, $bind_arg, $bind_arg) {}

/**
 * @param $result
 * @param $fetch_flags [optional]
 * @jms-builtin
 */
function fbird_fetch_row ($result, $fetch_flags) {}

/**
 * @param $result
 * @param $fetch_flags [optional]
 * @jms-builtin
 */
function fbird_fetch_assoc ($result, $fetch_flags) {}

/**
 * @param $result
 * @param $fetch_flags [optional]
 * @jms-builtin
 */
function fbird_fetch_object ($result, $fetch_flags) {}

/**
 * @param $result
 * @jms-builtin
 */
function fbird_free_result ($result) {}

/**
 * @param $result
 * @param $name
 * @jms-builtin
 */
function fbird_name_result ($result, $name) {}

/**
 * @param $link_identifier [optional]
 * @param $query [optional]
 * @jms-builtin
 */
function fbird_prepare ($link_identifier, $query) {}

/**
 * @param $query
 * @param $bind_arg [optional]
 * @param $bind_arg [optional]
 * @jms-builtin
 */
function fbird_execute ($query, $bind_arg, $bind_arg) {}

/**
 * @param $query
 * @jms-builtin
 */
function fbird_free_query ($query) {}

/**
 * @param $generator
 * @param $increment [optional]
 * @param $link_identifier [optional]
 * @jms-builtin
 */
function fbird_gen_id ($generator, $increment, $link_identifier) {}

/**
 * @param $query_result
 * @jms-builtin
 */
function fbird_num_fields ($query_result) {}

/**
 * @param $query
 * @jms-builtin
 */
function fbird_num_params ($query) {}

/**
 * @param $link_identifier [optional]
 * @jms-builtin
 */
function fbird_affected_rows ($link_identifier) {}

/**
 * @param $query_result
 * @param $field_number
 * @jms-builtin
 */
function fbird_field_info ($query_result, $field_number) {}

/**
 * @param $query
 * @param $field_number
 * @jms-builtin
 */
function fbird_param_info ($query, $field_number) {}

/**
 * @param $trans_args [optional]
 * @param $link_identifier [optional]
 * @param $trans_args [optional]
 * @param $link_identifier [optional]
 * @jms-builtin
 */
function fbird_trans ($trans_args, $link_identifier, $trans_args, $link_identifier) {}

/**
 * @param $link_identifier
 * @jms-builtin
 */
function fbird_commit ($link_identifier) {}

/**
 * @param $link_identifier
 * @jms-builtin
 */
function fbird_rollback ($link_identifier) {}

/**
 * @param $link_identifier
 * @jms-builtin
 */
function fbird_commit_ret ($link_identifier) {}

/**
 * @param $link_identifier
 * @jms-builtin
 */
function fbird_rollback_ret ($link_identifier) {}

/**
 * @param $link_identifier [optional]
 * @param $blob_id [optional]
 * @jms-builtin
 */
function fbird_blob_info ($link_identifier, $blob_id) {}

/**
 * @param $link_identifier [optional]
 * @jms-builtin
 */
function fbird_blob_create ($link_identifier) {}

/**
 * @param $blob_handle
 * @param $data
 * @jms-builtin
 */
function fbird_blob_add ($blob_handle, $data) {}

/**
 * @param $blob_handle
 * @jms-builtin
 */
function fbird_blob_cancel ($blob_handle) {}

/**
 * @param $blob_handle
 * @jms-builtin
 */
function fbird_blob_close ($blob_handle) {}

/**
 * @param $link_identifier [optional]
 * @param $blob_id [optional]
 * @jms-builtin
 */
function fbird_blob_open ($link_identifier, $blob_id) {}

/**
 * @param $blob_handle
 * @param $len
 * @jms-builtin
 */
function fbird_blob_get ($blob_handle, $len) {}

/**
 * @param $link_identifier [optional]
 * @param $blob_id [optional]
 * @jms-builtin
 */
function fbird_blob_echo ($link_identifier, $blob_id) {}

/**
 * @param $link_identifier [optional]
 * @param $file [optional]
 * @jms-builtin
 */
function fbird_blob_import ($link_identifier, $file) {} 

/** @jms-builtin */
function fbird_errmsg () {} 

/** @jms-builtin */
function fbird_errcode () {}

/**
 * @param $service_handle
 * @param $user_name
 * @param $password
 * @param $first_name [optional]
 * @param $middle_name [optional]
 * @param $last_name [optional]
 * @jms-builtin
 */
function fbird_add_user ($service_handle, $user_name, $password, $first_name, $middle_name, $last_name) {}

/**
 * @param $service_handle
 * @param $user_name
 * @param $password
 * @param $first_name [optional]
 * @param $middle_name [optional]
 * @param $last_name [optional]
 * @jms-builtin
 */
function fbird_modify_user ($service_handle, $user_name, $password, $first_name, $middle_name, $last_name) {}

/**
 * @param $service_handle
 * @param $user_name
 * @param $password
 * @param $first_name [optional]
 * @param $middle_name [optional]
 * @param $last_name [optional]
 * @jms-builtin
 */
function fbird_delete_user ($service_handle, $user_name, $password, $first_name, $middle_name, $last_name) {}

/**
 * @param $host
 * @param $dba_username
 * @param $dba_password
 * @jms-builtin
 */
function fbird_service_attach ($host, $dba_username, $dba_password) {}

/**
 * @param $service_handle
 * @jms-builtin
 */
function fbird_service_detach ($service_handle) {}

/**
 * @param $service_handle
 * @param $source_db
 * @param $dest_file
 * @param $options [optional]
 * @param $verbose [optional]
 * @jms-builtin
 */
function fbird_backup ($service_handle, $source_db, $dest_file, $options, $verbose) {}

/**
 * @param $service_handle
 * @param $source_file
 * @param $dest_db
 * @param $options [optional]
 * @param $verbose [optional]
 * @jms-builtin
 */
function fbird_restore ($service_handle, $source_file, $dest_db, $options, $verbose) {}

/**
 * @param $service_handle
 * @param $db
 * @param $action
 * @param $argument [optional]
 * @jms-builtin
 */
function fbird_maintain_db ($service_handle, $db, $action, $argument) {}

/**
 * @param $service_handle
 * @param $db
 * @param $action
 * @param $argument [optional]
 * @jms-builtin
 */
function fbird_db_info ($service_handle, $db, $action, $argument) {}

/**
 * @param $service_handle
 * @param $action
 * @jms-builtin
 */
function fbird_server_info ($service_handle, $action) {}

/**
 * @param $link_identifier
 * @param $event [optional]
 * @param $event2 [optional]
 * @jms-builtin
 */
function fbird_wait_event ($link_identifier, $event, $event2) {}

/**
 * @param $link_identifier
 * @param $handler
 * @param $event [optional]
 * @param $event2 [optional]
 * @jms-builtin
 */
function fbird_set_event_handler ($link_identifier, $handler, $event, $event2) {}

/**
 * @param $event
 * @jms-builtin
 */
function fbird_free_event_handler ($event) {}

define ('IBASE_DEFAULT', 0);
define ('IBASE_CREATE', 0);
define ('IBASE_TEXT', 1);
define ('IBASE_FETCH_BLOBS', 1);
define ('IBASE_FETCH_ARRAYS', 2);
define ('IBASE_UNIXTIME', 4);
define ('IBASE_WRITE', 1);
define ('IBASE_READ', 2);
define ('IBASE_COMMITTED', 8);
define ('IBASE_CONSISTENCY', 16);
define ('IBASE_CONCURRENCY', 4);
define ('IBASE_REC_VERSION', 64);
define ('IBASE_REC_NO_VERSION', 32);
define ('IBASE_NOWAIT', 256);
define ('IBASE_WAIT', 128);
define ('IBASE_BKP_IGNORE_CHECKSUMS', 1);
define ('IBASE_BKP_IGNORE_LIMBO', 2);
define ('IBASE_BKP_METADATA_ONLY', 4);
define ('IBASE_BKP_NO_GARBAGE_COLLECT', 8);
define ('IBASE_BKP_OLD_DESCRIPTIONS', 16);
define ('IBASE_BKP_NON_TRANSPORTABLE', 32);

/**
 * Options to <b>ibase_backup</b>
 * @link http://php.net/manual/en/ibase.constants.php
 */
define ('IBASE_BKP_CONVERT', 64);
define ('IBASE_RES_DEACTIVATE_IDX', 256);
define ('IBASE_RES_NO_SHADOW', 512);
define ('IBASE_RES_NO_VALIDITY', 1024);
define ('IBASE_RES_ONE_AT_A_TIME', 2048);
define ('IBASE_RES_REPLACE', 4096);
define ('IBASE_RES_CREATE', 8192);

/**
 * Options to <b>ibase_restore</b>
 * @link http://php.net/manual/en/ibase.constants.php
 */
define ('IBASE_RES_USE_ALL_SPACE', 16384);
define ('IBASE_PRP_PAGE_BUFFERS', 5);
define ('IBASE_PRP_SWEEP_INTERVAL', 6);
define ('IBASE_PRP_SHUTDOWN_DB', 7);
define ('IBASE_PRP_DENY_NEW_TRANSACTIONS', 10);
define ('IBASE_PRP_DENY_NEW_ATTACHMENTS', 9);
define ('IBASE_PRP_RESERVE_SPACE', 11);
define ('IBASE_PRP_RES_USE_FULL', 35);
define ('IBASE_PRP_RES', 36);
define ('IBASE_PRP_WRITE_MODE', 12);
define ('IBASE_PRP_WM_ASYNC', 37);
define ('IBASE_PRP_WM_SYNC', 38);
define ('IBASE_PRP_ACCESS_MODE', 13);
define ('IBASE_PRP_AM_READONLY', 39);
define ('IBASE_PRP_AM_READWRITE', 40);
define ('IBASE_PRP_SET_SQL_DIALECT', 14);
define ('IBASE_PRP_ACTIVATE', 256);
define ('IBASE_PRP_DB_ONLINE', 512);
define ('IBASE_RPR_CHECK_DB', 16);
define ('IBASE_RPR_IGNORE_CHECKSUM', 32);
define ('IBASE_RPR_KILL_SHADOWS', 64);
define ('IBASE_RPR_MEND_DB', 4);
define ('IBASE_RPR_VALIDATE_DB', 1);
define ('IBASE_RPR_FULL', 128);

/**
 * Options to <b>ibase_maintain_db</b>
 * @link http://php.net/manual/en/ibase.constants.php
 */
define ('IBASE_RPR_SWEEP_DB', 2);
define ('IBASE_STS_DATA_PAGES', 1);
define ('IBASE_STS_DB_LOG', 2);
define ('IBASE_STS_HDR_PAGES', 4);
define ('IBASE_STS_IDX_PAGES', 8);

/**
 * Options to <b>ibase_db_info</b>
 * @link http://php.net/manual/en/ibase.constants.php
 */
define ('IBASE_STS_SYS_RELATIONS', 16);
define ('IBASE_SVC_SERVER_VERSION', 55);
define ('IBASE_SVC_IMPLEMENTATION', 56);
define ('IBASE_SVC_GET_ENV', 59);
define ('IBASE_SVC_GET_ENV_LOCK', 60);
define ('IBASE_SVC_GET_ENV_MSG', 61);
define ('IBASE_SVC_USER_DBPATH', 58);
define ('IBASE_SVC_SVR_DB_INFO', 50);

/**
 * Options to <b>ibase_server_info</b>
 * @link http://php.net/manual/en/ibase.constants.php
 */
define ('IBASE_SVC_GET_USERS', 68);

// End of interbase v.
?>
