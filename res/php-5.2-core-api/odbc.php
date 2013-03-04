<?php

// Start of odbc v.1.0

/**
 * Toggle autocommit behaviour
 * @link http://www.php.net/manual/en/function.odbc-autocommit.php
 * @param connection_id resource &odbc.connection.id;
 * @param OnOff bool[optional] <p>
 * If OnOff is true, auto-commit is enabled, if
 * it is false auto-commit is disabled.
 * </p>
 * @return mixed Without the OnOff parameter, this function returns
 * auto-commit status for connection_id. Non-zero is
 * returned if auto-commit is on, 0 if it is off, or false if an error
 * occurs.
 * </p>
 * <p>
 * If OnOff is set, this function returns true on
 * success and false on failure.
 *
 * @jms-builtin
 */
function odbc_autocommit ($connection_id, $OnOff = null) {}

/**
 * Handling of binary column data
 * @link http://www.php.net/manual/en/function.odbc-binmode.php
 * @param result_id resource <p>
 * The result identifier.
 * </p>
 * <p>
 * If result_id is 0, the
 * settings apply as default for new results.
 * Default for longreadlen is 4096 and
 * mode defaults to
 * ODBC_BINMODE_RETURN. Handling of binary long
 * columns is also affected by odbc_longreadlen.
 * </p>
 * @param mode int <p>
 * Possible values for mode are:
 * ODBC_BINMODE_PASSTHRU: Passthru BINARY data
 * @return bool Returns true on success or false on failure.
 *
 * @jms-builtin
 */
function odbc_binmode ($result_id, $mode) {}

/**
 * Close an ODBC connection
 * @link http://www.php.net/manual/en/function.odbc-close.php
 * @param connection_id resource &odbc.connection.id;
 * @return void 
 *
 * @jms-builtin
 */
function odbc_close ($connection_id) {}

/**
 * Close all ODBC connections
 * @link http://www.php.net/manual/en/function.odbc-close-all.php
 * @return void 
 *
 * @jms-builtin
 */
function odbc_close_all () {}

/**
 * Lists the column names in specified tables
 * @link http://www.php.net/manual/en/function.odbc-columns.php
 * @param connection_id resource &odbc.connection.id;
 * @param qualifier string[optional] <p>
 * The qualifier.
 * </p>
 * @param schema string[optional] <p>
 * The owner.
 * </p>
 * @param table_name string[optional] <p>
 * The table name.
 * </p>
 * @param column_name string[optional] <p>
 * The column name.
 * </p>
 * @return resource an ODBC result identifier&return.falseforfailure;.
 * </p>
 * <p>
 * The result set has the following columns:
 * TABLE_QUALIFIER
 * TABLE_SCHEM
 * TABLE_NAME
 * COLUMN_NAME
 * DATA_TYPE
 * TYPE_NAME
 * PRECISION
 * LENGTH
 * SCALE
 * RADIX
 * NULLABLE
 * REMARKS
 * </p>
 * <p>
 * The result set is ordered by TABLE_QUALIFIER, TABLE_SCHEM and
 * TABLE_NAME.
 *
 * @jms-builtin
 */
function odbc_columns ($connection_id, $qualifier = null, $schema = null, $table_name = null, $column_name = null) {}

/**
 * Commit an ODBC transaction
 * @link http://www.php.net/manual/en/function.odbc-commit.php
 * @param connection_id resource &odbc.connection.id;
 * @return bool Returns true on success or false on failure.
 *
 * @jms-builtin
 */
function odbc_commit ($connection_id) {}

/**
 * Connect to a datasource
 * @link http://www.php.net/manual/en/function.odbc-connect.php
 * @param dsn string <p>
 * The database source name for the connection. Alternatively, a
 * DNS-less connection string can be used.
 * </p>
 * @param user string <p>
 * The username.
 * </p>
 * @param password string <p>
 * The password.
 * </p>
 * @param cursor_type int[optional] <p>
 * This sets the type of cursor to be used
 * for this connection. This parameter is not normally needed, but
 * can be useful for working around problems with some ODBC drivers.
 * </p>
 * The following constants are defined for cursortype:
 * <p>
 * SQL_CUR_USE_IF_NEEDED
 * @return resource an ODBC connection id or 0 (false) on
 * error.
 *
 * @jms-builtin
 */
function odbc_connect ($dsn, $user, $password, $cursor_type = null) {}

/**
 * Get cursorname
 * @link http://www.php.net/manual/en/function.odbc-cursor.php
 * @param result_id resource <p>
 * The result identifier.
 * </p>
 * @return string the cursor name, as a string.
 *
 * @jms-builtin
 */
function odbc_cursor ($result_id) {}

/**
 * Returns information about a current connection
 * @link http://www.php.net/manual/en/function.odbc-data-source.php
 * @param connection_id resource &odbc.connection.id;
 * @param fetch_type int <p>
 * The fetch_type can be one of two constant types:
 * SQL_FETCH_FIRST, SQL_FETCH_NEXT.
 * Use SQL_FETCH_FIRST the first time this function is
 * called, thereafter use the SQL_FETCH_NEXT.
 * </p>
 * @return array false on error, and an array upon success.
 *
 * @jms-builtin
 */
function odbc_data_source ($connection_id, $fetch_type) {}

/**
 * Execute a prepared statement
 * @link http://www.php.net/manual/en/function.odbc-execute.php
 * @param result_id resource <p>
 * The result id resource, from odbc_prepare.
 * </p>
 * @param parameters_array array[optional] <p>
 * Parameters in parameter_array will be
 * substituted for placeholders in the prepared statement in order.
 * Elements of this array will be converted to strings by calling this
 * function.
 * </p>
 * <p>
 * Any parameters in parameter_array which
 * start and end with single quotes will be taken as the name of a
 * file to read and send to the database server as the data for the
 * appropriate placeholder.
 * </p>
 * If you wish to store a string which actually begins and ends with
 * single quotes, you must add a space or other non-single-quote character
 * to the beginning or end of the parameter, which will prevent the
 * parameter from being taken as a file name. If this is not an option,
 * then you must use another mechanism to store the string, such as
 * executing the query directly with odbc_exec).
 * @return bool Returns true on success or false on failure.
 *
 * @jms-builtin
 */
function odbc_execute ($result_id, array $parameters_array = null) {}

/**
 * Get the last error code
 * @link http://www.php.net/manual/en/function.odbc-error.php
 * @param connection_id resource[optional] &odbc.connection.id;
 * @return string If connection_id is specified, the last state
 * of that connection is returned, else the last state of any connection
 * is returned.
 * </p>
 * <p>
 * This function returns meaningful value only if last odbc query failed
 * (i.e. odbc_exec returned false).
 *
 * @jms-builtin
 */
function odbc_error ($connection_id = null) {}

/**
 * Get the last error message
 * @link http://www.php.net/manual/en/function.odbc-errormsg.php
 * @param connection_id resource[optional] &odbc.connection.id;
 * @return string If connection_id is specified, the last state
 * of that connection is returned, else the last state of any connection
 * is returned.
 * </p>
 * <p>
 * This function returns meaningful value only if last odbc query failed
 * (i.e. odbc_exec returned false).
 *
 * @jms-builtin
 */
function odbc_errormsg ($connection_id = null) {}

/**
 * Prepare and execute a SQL statement
 * @link http://www.php.net/manual/en/function.odbc-exec.php
 * @param connection_id resource &odbc.connection.id;
 * @param query_string string <p>
 * The SQL statement.
 * </p>
 * @param flags int[optional] <p>
 * This parameter is currently not used.
 * </p>
 * @return resource an ODBC result identifier if the SQL command was executed
 * successfully, or false on error.
 *
 * @jms-builtin
 */
function odbc_exec ($connection_id, $query_string, $flags = null) {}

/**
 * Fetch a result row as an associative array
 * @link http://www.php.net/manual/en/function.odbc-fetch-array.php
 * @param result resource <p>
 * The result resource from odbc_exec.
 * </p>
 * @param rownumber int[optional] <p>
 * Optionally choose which row number to retrieve.
 * </p>
 * @return array an array that corresponds to the fetched row, or false if there 
 * are no more rows.
 *
 * @jms-builtin
 */
function odbc_fetch_array ($result, $rownumber = null) {}

/**
 * Fetch a result row as an object
 * @link http://www.php.net/manual/en/function.odbc-fetch-object.php
 * @param result resource <p>
 * The result resource from odbc_exec.
 * </p>
 * @param rownumber int[optional] <p>
 * Optionally choose which row number to retrieve.
 * </p>
 * @return object an object that corresponds to the fetched row, or false if there 
 * are no more rows.
 *
 * @jms-builtin
 */
function odbc_fetch_object ($result, $rownumber = null) {}

/**
 * Fetch a row
 * @link http://www.php.net/manual/en/function.odbc-fetch-row.php
 * @param result_id resource <p>
 * The result identifier.
 * </p>
 * @param row_number int[optional] <p>
 * If row_number is not specified,
 * odbc_fetch_row will try to fetch the next row in
 * the result set. Calls to odbc_fetch_row with and
 * without row_number can be mixed.
 * </p>
 * <p>
 * To step through the result more than once, you can call
 * odbc_fetch_row with
 * row_number 1, and then continue doing
 * odbc_fetch_row without
 * row_number to review the result. If a driver
 * doesn't support fetching rows by number, the
 * row_number parameter is ignored.
 * </p>
 * @return bool true if there was a row, false otherwise.
 *
 * @jms-builtin
 */
function odbc_fetch_row ($result_id, $row_number = null) {}

/**
 * Fetch one result row into array
 * @link http://www.php.net/manual/en/function.odbc-fetch-into.php
 * @param result_id resource <p>
 * The result resource.
 * </p>
 * @param result_array array <p>
 * The result array
 * that can be of any type since it will be converted to type
 * array. The array will contain the column values starting at array
 * index 0.
 * </p>
 * @param rownumber int[optional] <p>
 * The row number.
 * </p>
 * @return int the number of columns in the result;
 * false on error.
 *
 * @jms-builtin
 */
function odbc_fetch_into ($result_id, array &$result_array, $rownumber = null) {}

/**
 * Get the length (precision) of a field
 * @link http://www.php.net/manual/en/function.odbc-field-len.php
 * @param result_id resource <p>
 * The result identifier.
 * </p>
 * @param field_number int <p>
 * The field number. Field numbering starts at 1.
 * </p>
 * @return int the field name as a string, or false on error.
 *
 * @jms-builtin
 */
function odbc_field_len ($result_id, $field_number) {}

/**
 * Get the scale of a field
 * @link http://www.php.net/manual/en/function.odbc-field-scale.php
 * @param result_id resource <p>
 * The result identifier.
 * </p>
 * @param field_number int <p>
 * The field number. Field numbering starts at 1.
 * </p>
 * @return int the field scale as a integer, or false on error.
 *
 * @jms-builtin
 */
function odbc_field_scale ($result_id, $field_number) {}

/**
 * Get the columnname
 * @link http://www.php.net/manual/en/function.odbc-field-name.php
 * @param result_id resource <p>
 * The result identifier.
 * </p>
 * @param field_number int <p>
 * The field number. Field numbering starts at 1.
 * </p>
 * @return string the field name as a string, or false on error.
 *
 * @jms-builtin
 */
function odbc_field_name ($result_id, $field_number) {}

/**
 * Datatype of a field
 * @link http://www.php.net/manual/en/function.odbc-field-type.php
 * @param result_id resource <p>
 * The result identifier.
 * </p>
 * @param field_number int <p>
 * The field number. Field numbering starts at 1.
 * </p>
 * @return string the field type as a string, or false on error.
 *
 * @jms-builtin
 */
function odbc_field_type ($result_id, $field_number) {}

/**
 * Return column number
 * @link http://www.php.net/manual/en/function.odbc-field-num.php
 * @param result_id resource <p>
 * The result identifier.
 * </p>
 * @param field_name string <p>
 * The field name.
 * </p>
 * @return int the field number as a integer, or false on error.
 * Field numbering starts at 1.
 *
 * @jms-builtin
 */
function odbc_field_num ($result_id, $field_name) {}

/**
 * Free resources associated with a result
 * @link http://www.php.net/manual/en/function.odbc-free-result.php
 * @param result_id resource <p>
 * The result identifier.
 * </p>
 * @return bool Always returns true.
 *
 * @jms-builtin
 */
function odbc_free_result ($result_id) {}

/**
 * Retrieves information about data types supported by the data source
 * @link http://www.php.net/manual/en/function.odbc-gettypeinfo.php
 * @param connection_id resource &odbc.connection.id;
 * @param data_type int[optional] <p>
 * The data type, which can be used to restrict the information to a
 * single data type.
 * </p>
 * @return resource an ODBC result identifier or
 * false on failure.
 * </p>
 * <p>
 * The result set has the following columns:
 * TYPE_NAME
 * DATA_TYPE
 * PRECISION
 * LITERAL_PREFIX
 * LITERAL_SUFFIX
 * CREATE_PARAMS
 * NULLABLE
 * CASE_SENSITIVE
 * SEARCHABLE
 * UNSIGNED_ATTRIBUTE
 * MONEY
 * AUTO_INCREMENT
 * LOCAL_TYPE_NAME
 * MINIMUM_SCALE
 * MAXIMUM_SCALE
 * </p>
 * <p>
 * The result set is ordered by DATA_TYPE and TYPE_NAME.
 *
 * @jms-builtin
 */
function odbc_gettypeinfo ($connection_id, $data_type = null) {}

/**
 * Handling of LONG columns
 * @link http://www.php.net/manual/en/function.odbc-longreadlen.php
 * @param result_id resource <p>
 * The result identifier.
 * </p>
 * @param length int <p>
 * The number of bytes returned to PHP is controlled by the parameter
 * length. If it is set to 0, Long column data is passed through to the
 * client.
 * </p>
 * @return bool Returns true on success or false on failure.
 *
 * @jms-builtin
 */
function odbc_longreadlen ($result_id, $length) {}

/**
 * Checks if multiple results are available
 * @link http://www.php.net/manual/en/function.odbc-next-result.php
 * @param result_id resource <p>
 * The result identifier.
 * </p>
 * @return bool true if there are more result sets, false otherwise.
 *
 * @jms-builtin
 */
function odbc_next_result ($result_id) {}

/**
 * Number of columns in a result
 * @link http://www.php.net/manual/en/function.odbc-num-fields.php
 * @param result_id resource <p>
 * The result identifier returned by odbc_exec.
 * </p>
 * @return int the number of fields, or -1 on error.
 *
 * @jms-builtin
 */
function odbc_num_fields ($result_id) {}

/**
 * Number of rows in a result
 * @link http://www.php.net/manual/en/function.odbc-num-rows.php
 * @param result_id resource <p>
 * The result identifier returned by odbc_exec.
 * </p>
 * @return int the number of rows in an ODBC result.
 * This function will return -1 on error.
 *
 * @jms-builtin
 */
function odbc_num_rows ($result_id) {}

/**
 * Open a persistent database connection
 * @link http://www.php.net/manual/en/function.odbc-pconnect.php
 * @param dsn string 
 * @param user string 
 * @param password string 
 * @param cursor_type int[optional] 
 * @return resource an ODBC connection id or 0 (false) on
 * error.
 *
 * @jms-builtin
 */
function odbc_pconnect ($dsn, $user, $password, $cursor_type = null) {}

/**
 * Prepares a statement for execution
 * @link http://www.php.net/manual/en/function.odbc-prepare.php
 * @param connection_id resource &odbc.connection.id;
 * @param query_string string <p>
 * The query string statement being prepared.
 * </p>
 * @return resource an ODBC result identifier if the SQL command was prepared
 * successfully. Returns false on error.
 *
 * @jms-builtin
 */
function odbc_prepare ($connection_id, $query_string) {}

/**
 * Get result data
 * @link http://www.php.net/manual/en/function.odbc-result.php
 * @param result_id resource <p>
 * The ODBC resource.
 * </p>
 * @param field mixed <p>
 * The field name being retrieved. It can either be an integer containing
 * the column number of the field you want; or it can be a string
 * containing the name of the field.
 * </p>
 * @return mixed the string contents of the field, false on error, &null; for
 * NULL data, or true for binary data.
 *
 * @jms-builtin
 */
function odbc_result ($result_id, $field) {}

/**
 * Print result as HTML table
 * @link http://www.php.net/manual/en/function.odbc-result-all.php
 * @param result_id resource <p>
 * The result identifier.
 * </p>
 * @param format string[optional] <p>
 * Additional overall table formatting.
 * </p>
 * @return int the number of rows in the result or false on error.
 *
 * @jms-builtin
 */
function odbc_result_all ($result_id, $format = null) {}

/**
 * Rollback a transaction
 * @link http://www.php.net/manual/en/function.odbc-rollback.php
 * @param connection_id resource &odbc.connection.id;
 * @return bool Returns true on success or false on failure.
 *
 * @jms-builtin
 */
function odbc_rollback ($connection_id) {}

/**
 * Adjust ODBC settings
 * @link http://www.php.net/manual/en/function.odbc-setoption.php
 * @param id resource <p>
 * Is a connection id or result id on which to change the settings.
 * For SQLSetConnectOption(), this is a connection id.
 * For SQLSetStmtOption(), this is a result id.
 * </p>
 * @param function int <p>
 * Is the ODBC function to use. The value should be
 * 1 for SQLSetConnectOption() and
 * 2 for SQLSetStmtOption().
 * </p>
 * @param option int <p>
 * The option to set.
 * </p>
 * @param param int <p>
 * The value for the given option.
 * </p>
 * @return bool Returns true on success or false on failure.
 *
 * @jms-builtin
 */
function odbc_setoption ($id, $function, $option, $param) {}

/**
 * Retrieves special columns
 * @link http://www.php.net/manual/en/function.odbc-specialcolumns.php
 * @param connection_id resource &odbc.connection.id;
 * @param type int When the type argument is SQL_BEST_ROWID,
 * odbc_specialcolumns returns the
 * column or columns that uniquely identify each row in the table.
 * When the type argument is SQL_ROWVER,
 * odbc_specialcolumns returns the column or columns in the
 * specified table, if any, that are automatically updated by the data source
 * when any value in the row is updated by any transaction.
 * @param qualifier string <p>
 * The qualifier.
 * </p>
 * @param owner string <p>
 * The owner.
 * </p>
 * @param table string <p>
 * The table.
 * </p>
 * @param scope int <p>
 * The scope, which orders the result set.
 * </p>
 * @param nullable int <p>
 * The nullable option.
 * </p>
 * @return resource an ODBC result identifier or false on
 * failure.
 * </p>
 * <p>
 * The result set has the following columns:
 * SCOPE
 * COLUMN_NAME
 * DATA_TYPE
 * TYPE_NAME
 * PRECISION
 * LENGTH
 * SCALE
 * PSEUDO_COLUMN
 *
 * @jms-builtin
 */
function odbc_specialcolumns ($connection_id, $type, $qualifier, $owner, $table, $scope, $nullable) {}

/**
 * Retrieve statistics about a table
 * @link http://www.php.net/manual/en/function.odbc-statistics.php
 * @param connection_id resource &odbc.connection.id;
 * @param qualifier string <p>
 * The qualifier.
 * </p>
 * @param owner string <p>
 * The owner.
 * </p>
 * @param table_name string <p>
 * The table name.
 * </p>
 * @param unique int <p>
 * The unique attribute.
 * </p>
 * @param accuracy int <p>
 * The accuracy.
 * </p>
 * @return resource an ODBC result identifier&return.falseforfailure;.
 * </p>
 * <p>
 * The result set has the following columns:
 * TABLE_QUALIFIER
 * TABLE_OWNER
 * TABLE_NAME
 * NON_UNIQUE
 * INDEX_QUALIFIER
 * INDEX_NAME
 * TYPE
 * SEQ_IN_INDEX
 * COLUMN_NAME
 * COLLATION
 * CARDINALITY
 * PAGES
 * FILTER_CONDITION
 *
 * @jms-builtin
 */
function odbc_statistics ($connection_id, $qualifier, $owner, $table_name, $unique, $accuracy) {}

/**
 * Get the list of table names stored in a specific data source
 * @link http://www.php.net/manual/en/function.odbc-tables.php
 * @param connection_id resource &odbc.connection.id;
 * @param qualifier string[optional] <p>
 * The qualifier.
 * </p>
 * @param owner string[optional] <p>
 * The owner. Accepts search patterns ('%' to match zero or more
 * characters and '_' to match a single character).
 * </p>
 * @param name string[optional] <p>
 * The name. Accepts search patterns ('%' to match zero or more
 * characters and '_' to match a single character).
 * </p>
 * @param types string[optional] <p>
 * If table_type is not an empty string, it
 * must contain a list of comma-separated values for the types of
 * interest; each value may be enclosed in single quotes (') or
 * unquoted. For example, "'TABLE','VIEW'" or "TABLE, VIEW". If the
 * data source does not support a specified table type,
 * odbc_tables does not return any results for
 * that type.
 * </p>
 * @return resource an ODBC result identifier containing the information 
 * &return.falseforfailure;.
 * </p>
 * <p>
 * The result set has the following columns:
 * TABLE_QUALIFIER
 * TABLE_OWNER
 * TABLE_NAME
 * TABLE_TYPE
 * REMARKS
 *
 * @jms-builtin
 */
function odbc_tables ($connection_id, $qualifier = null, $owner = null, $name = null, $types = null) {}

/**
 * Gets the primary keys for a table
 * @link http://www.php.net/manual/en/function.odbc-primarykeys.php
 * @param connection_id resource &odbc.connection.id;
 * @param qualifier string <p>
 * </p>
 * @param owner string <p>
 * </p>
 * @param table string <p>
 * </p>
 * @return resource an ODBC result identifier&return.falseforfailure;.
 * </p>
 * <p>
 * The result set has the following columns:
 * TABLE_QUALIFIER
 * TABLE_OWNER
 * TABLE_NAME
 * COLUMN_NAME
 * KEY_SEQ
 * PK_NAME
 *
 * @jms-builtin
 */
function odbc_primarykeys ($connection_id, $qualifier, $owner, $table) {}

/**
 * Lists columns and associated privileges for the given table
 * @link http://www.php.net/manual/en/function.odbc-columnprivileges.php
 * @param connection_id resource &odbc.connection.id;
 * @param qualifier string <p>
 * The qualifier.
 * </p>
 * @param owner string <p>
 * The owner.
 * </p>
 * @param table_name string <p>
 * The table name.
 * </p>
 * @param column_name string <p>
 * The column_name argument accepts search
 * patterns ('%' to match zero or more characters and '_' to match a
 * single character).
 * </p>
 * @return resource an ODBC result identifier&return.falseforfailure;.
 * This result identifier can be used to fetch a list of columns and
 * associated privileges.
 * </p>
 * <p>
 * The result set has the following columns:
 * TABLE_QUALIFIER
 * TABLE_OWNER
 * TABLE_NAME
 * GRANTOR
 * GRANTEE
 * PRIVILEGE
 * IS_GRANTABLE
 * </p>
 * <p>
 * The result set is ordered by TABLE_QUALIFIER, TABLE_OWNER and
 * TABLE_NAME.
 *
 * @jms-builtin
 */
function odbc_columnprivileges ($connection_id, $qualifier, $owner, $table_name, $column_name) {}

/**
 * Lists tables and the privileges associated with each table
 * @link http://www.php.net/manual/en/function.odbc-tableprivileges.php
 * @param connection_id resource &odbc.connection.id;
 * @param qualifier string <p>
 * The qualifier.
 * </p>
 * @param owner string <p>
 * The owner. Accepts the following search patterns:
 * ('%' to match zero or more characters and '_' to match a single character)
 * </p>
 * @param name string <p>
 * The name. Accepts the following search patterns:
 * ('%' to match zero or more characters and '_' to match a single character)
 * </p>
 * @return resource An ODBC result identifier&return.falseforfailure;.
 * </p>
 * <p>
 * The result set has the following columns:
 * TABLE_QUALIFIER
 * TABLE_OWNER
 * TABLE_NAME
 * GRANTOR
 * GRANTEE
 * PRIVILEGE
 * IS_GRANTABLE
 *
 * @jms-builtin
 */
function odbc_tableprivileges ($connection_id, $qualifier, $owner, $name) {}

/**
 * Retrieves a list of foreign keys
 * @link http://www.php.net/manual/en/function.odbc-foreignkeys.php
 * @param connection_id resource &odbc.connection.id;
 * @param pk_qualifier string <p>
 * The primary key qualifier.
 * </p>
 * @param pk_owner string <p>
 * The primary key owner.
 * </p>
 * @param pk_table string <p>
 * The primary key table.
 * </p>
 * @param fk_qualifier string <p>
 * The foreign key qualifier.
 * </p>
 * @param fk_owner string <p>
 * The foreign key owner.
 * </p>
 * @param fk_table string <p>
 * The foreign key table.
 * </p>
 * @return resource an ODBC result identifier&return.falseforfailure;.
 * </p>
 * <p>
 * The result set has the following columns:
 * PKTABLE_QUALIFIER
 * PKTABLE_OWNER
 * PKTABLE_NAME
 * PKCOLUMN_NAME
 * FKTABLE_QUALIFIER
 * FKTABLE_OWNER
 * FKTABLE_NAME
 * FKCOLUMN_NAME
 * KEY_SEQ
 * UPDATE_RULE
 * DELETE_RULE
 * FK_NAME
 * PK_NAME
 * </p>
 * If pk_table contains a table name,
 * odbc_foreignkeys returns a result set
 * containing the primary key of the specified table and all of the
 * foreign keys that refer to it.
 * If fk_table contains a table name,
 * odbc_foreignkeys returns a result set
 * containing all of the foreign keys in the specified table and the
 * primary keys (in other tables) to which they refer.
 * If both pk_table and
 * fk_table contain table names,
 * odbc_foreignkeys returns the foreign keys in
 * the table specified in fk_table that refer
 * to the primary key of the table specified in
 * pk_table
 *
 * @jms-builtin
 */
function odbc_foreignkeys ($connection_id, $pk_qualifier, $pk_owner, $pk_table, $fk_qualifier, $fk_owner, $fk_table) {}

/**
 * Get the list of procedures stored in a specific data source
 * @link http://www.php.net/manual/en/function.odbc-procedures.php
 * @param connection_id resource &odbc.connection.id;
 * @return resource an ODBC
 * result identifier containing the information&return.falseforfailure;.
 * </p>
 * <p>
 * The result set has the following columns:
 * PROCEDURE_QUALIFIER
 * PROCEDURE_OWNER
 * PROCEDURE_NAME
 * NUM_INPUT_PARAMS
 * NUM_OUTPUT_PARAMS
 * NUM_RESULT_SETS
 * REMARKS
 * PROCEDURE_TYPE
 *
 * @jms-builtin
 */
function odbc_procedures ($connection_id) {}

/**
 * Retrieve information about parameters to procedures
 * @link http://www.php.net/manual/en/function.odbc-procedurecolumns.php
 * @param connection_id resource &odbc.connection.id;
 * @return resource the list of input and output parameters, as well as the
 * columns that make up the result set for the specified procedures. 
 * Returns an ODBC result identifier&return.falseforfailure;.
 * </p>
 * <p>
 * The result set has the following columns:
 * PROCEDURE_QUALIFIER
 * PROCEDURE_OWNER
 * PROCEDURE_NAME
 * COLUMN_NAME
 * COLUMN_TYPE
 * DATA_TYPE
 * TYPE_NAME
 * PRECISION
 * LENGTH
 * SCALE
 * RADIX
 * NULLABLE
 * REMARKS
 *
 * @jms-builtin
 */
function odbc_procedurecolumns ($connection_id) {}

/**
 * &Alias; <function>odbc_exec</function>
 * @link http://www.php.net/manual/en/function.odbc-do.php
 *
 * @jms-builtin
 */
function odbc_do () {}

/**
 * &Alias; <function>odbc_field_len</function>
 * @link http://www.php.net/manual/en/function.odbc-field-precision.php
 *
 * @jms-builtin
 */
function odbc_field_precision () {}

define ('ODBC_TYPE', "Win32");
define ('ODBC_BINMODE_PASSTHRU', 0);
define ('ODBC_BINMODE_RETURN', 1);
define ('ODBC_BINMODE_CONVERT', 2);
define ('SQL_ODBC_CURSORS', 110);
define ('SQL_CUR_USE_DRIVER', 2);
define ('SQL_CUR_USE_IF_NEEDED', 0);
define ('SQL_CUR_USE_ODBC', 1);
define ('SQL_CONCURRENCY', 7);
define ('SQL_CONCUR_READ_ONLY', 1);
define ('SQL_CONCUR_LOCK', 2);
define ('SQL_CONCUR_ROWVER', 3);
define ('SQL_CONCUR_VALUES', 4);
define ('SQL_CURSOR_TYPE', 6);
define ('SQL_CURSOR_FORWARD_ONLY', 0);
define ('SQL_CURSOR_KEYSET_DRIVEN', 1);
define ('SQL_CURSOR_DYNAMIC', 2);
define ('SQL_CURSOR_STATIC', 3);
define ('SQL_KEYSET_SIZE', 8);
define ('SQL_FETCH_FIRST', 2);
define ('SQL_FETCH_NEXT', 1);
define ('SQL_CHAR', 1);
define ('SQL_VARCHAR', 12);
define ('SQL_LONGVARCHAR', -1);
define ('SQL_DECIMAL', 3);
define ('SQL_NUMERIC', 2);
define ('SQL_BIT', -7);
define ('SQL_TINYINT', -6);
define ('SQL_SMALLINT', 5);
define ('SQL_INTEGER', 4);
define ('SQL_BIGINT', -5);
define ('SQL_REAL', 7);
define ('SQL_FLOAT', 6);
define ('SQL_DOUBLE', 8);
define ('SQL_BINARY', -2);
define ('SQL_VARBINARY', -3);
define ('SQL_LONGVARBINARY', -4);
define ('SQL_DATE', 9);
define ('SQL_TIME', 10);
define ('SQL_TIMESTAMP', 11);

// End of odbc v.1.0
?>
