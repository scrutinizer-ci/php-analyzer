<?php

// Start of mongo v.1.5.0RC1

/** @jms-builtin */
class MongoClient  {
	const DEFAULT_HOST = "localhost";
	const DEFAULT_PORT = 27017;
	const VERSION = "1.5.0RC1";
	const RP_PRIMARY = "primary";
	const RP_PRIMARY_PREFERRED = "primaryPreferred";
	const RP_SECONDARY = "secondary";
	const RP_SECONDARY_PREFERRED = "secondaryPreferred";
	const RP_NEAREST = "nearest";

	public $connected;
	public $status;
	protected $server;
	protected $persistent;


	/**
	 * Creates a new database connection object
	 * @link http://www.php.net/manual/en/mongoclient.construct.php
	 * @param server[optional]
	 * @param options[optional]
	 */
	public function __construct ($serverarray , $options) {}

	/**
	 * Return info about all open connections
	 * @link http://www.php.net/manual/en/mongoclient.getconnections.php
	 * @return array An array of open connections.
	 */
	public static function getConnections () {}

	/**
	 * Connects to a database server
	 * @link http://www.php.net/manual/en/mongoclient.connect.php
	 * @return bool If the connection was successful.
	 */
	public function connect () {}

	/**
	 * String representation of this connection
	 * @link http://www.php.net/manual/en/mongoclient.tostring.php
	 * @return string hostname and port for this connection.
	 */
	public function __toString () {}

	/**
	 * Gets a database
	 * @link http://www.php.net/manual/en/mongoclient.get.php
	 * @param dbname string <p>
	 * The database name.
	 * </p>
	 * @return MongoDB a new db object.
	 */
	public function __get ($dbname) {}

	/**
	 * Gets a database
	 * @link http://www.php.net/manual/en/mongoclient.selectdb.php
	 * @param name string <p>
	 * The database name.
	 * </p>
	 * @return MongoDB a new database object.
	 */
	public function selectDB ($name) {}

	/**
	 * Gets a database collection
	 * @link http://www.php.net/manual/en/mongoclient.selectcollection.php
	 * @param db string <p>
	 * The database name.
	 * </p>
	 * @param collection string <p>
	 * The collection name.
	 * </p>
	 * @return MongoCollection a new collection object.
	 */
	public function selectCollection ($db, $collection) {}

	/**
	 * Get the read preference for this connection
	 * @link http://www.php.net/manual/en/mongoclient.getreadpreference.php
	 * @return array
	 */
	public function getReadPreference () {}

	/**
	 * Set the read preference for this connection
	 * @link http://www.php.net/manual/en/mongoclient.setreadpreference.php
	 * @param read_preference string
	 * @param tags array[optional]
	 * @return bool
	 */
	public function setReadPreference ($read_preference, array $tags = null) {}

	/**
	 * Drops a database [deprecated]
	 * @link http://www.php.net/manual/en/mongoclient.dropdb.php
	 * @param db mixed <p>
	 * The database to drop. Can be a MongoDB object or the name of the database.
	 * </p>
	 * @return array the database response.
	 */
	public function dropDB ($db) {}

	/**
	 * Lists all of the databases available.
	 * @link http://www.php.net/manual/en/mongoclient.listdbs.php
	 * @return array an associative array containing three fields. The first field is
	 * databases, which in turn contains an array. Each element
	 * of the array is an associative array corresponding to a database, giving th
	 * database's name, size, and if it's empty. The other two fields are
	 * totalSize (in bytes) and ok, which is 1
	 * if this method ran successfully.
	 */
	public function listDBs () {}

	/**
	 * Updates status for all associated hosts
	 * @link http://www.php.net/manual/en/mongoclient.gethosts.php
	 * @return array an array of information about the hosts in the set. Includes each
	 * host's hostname, its health (1 is healthy), its state (1 is primary, 2 is
	 * secondary, 0 is anything else), the amount of time it took to ping the
	 * server, and when the last ping occurred. For example, on a three-member
	 * replica set, it might look something like:
	 */
	public function getHosts () {}

	/**
	 * Closes this connection
	 * @link http://www.php.net/manual/en/mongoclient.close.php
	 * @param connection boolean|string[optional] <p>
	 * If connection is not given, or false then connection that would be
	 * selected for writes would be closed. In a single-node configuration,
	 * that is then the whole connection, but if you are connected to a
	 * replica set, close() will only close the
	 * connection to the primary server.
	 * </p>
	 * <p>
	 * If connection is true then all connections as known by the connection
	 * manager will be closed. This can include connections that are not
	 * referenced in the connection string used to create the object that
	 * you are calling close on.
	 * </p>
	 * <p>
	 * If connection is a string argument, then it will only close the
	 * connection identified by this hash. Hashes are identifiers for a
	 * connection and can be obtained by calling
	 * MongoClient::getConnections.
	 * </p>
	 * @return bool if the connection was successfully closed.
	 */
	public function close ($connection = null) {}

	/**
	 * Kills a specific cursor on the server
	 * @link http://www.php.net/manual/en/mongoclient.killcursor.php
	 * @param server_hash string <p>
	 * The server hash that has the cursor. This can be obtained through
	 * MongoCursor::info.
	 * </p>
	 * @param id int|MongoInt64 <p>
	 * The ID of the cursor to kill. You can either supply an int
	 * containing the 64 bit cursor ID, or an object of the
	 * MongoInt64 class. The latter is necessary on 32
	 * bit platforms (and Windows).
	 * </p>
	 * @return bool true if the method attempted to kill a cursor, and false if
	 * there was something wrong with the arguments (such as a wrong
	 * server_hash). The return status does not
	 * reflect where the cursor was actually killed as the server does
	 * not provide that information.
	 */
	public static function killCursor ($server_hash, $id) {}

}

/** @jms-builtin */
class Mongo extends MongoClient  {
	const DEFAULT_HOST = "localhost";
	const DEFAULT_PORT = 27017;
	const VERSION = "1.5.0RC1";
	const RP_PRIMARY = "primary";
	const RP_PRIMARY_PREFERRED = "primaryPreferred";
	const RP_SECONDARY = "secondary";
	const RP_SECONDARY_PREFERRED = "secondaryPreferred";
	const RP_NEAREST = "nearest";

	public $connected;
	public $status;
	protected $server;
	protected $persistent;


	/**
	 * The __construct purpose
	 * @link http://www.php.net/manual/en/mongo.construct.php
	 * @param server[optional]
	 * @param options[optional]
	 */
	public function __construct ($serverarray , $options) {}

	/**
	 * Connects with a database server
	 * @link http://www.php.net/manual/en/mongo.connectutil.php
	 * @return bool If the connection was successful.
	 */
	protected function connectUtil () {}

	/**
	 * Get slaveOkay setting for this connection
	 * @link http://www.php.net/manual/en/mongo.getslaveokay.php
	 * @return bool the value of slaveOkay for this instance.
	 */
	public function getSlaveOkay () {}

	/**
	 * Change slaveOkay setting for this connection
	 * @link http://www.php.net/manual/en/mongo.setslaveokay.php
	 * @param ok bool[optional] <p>
	 * If reads should be sent to secondary members of a replica set for all
	 * possible queries using this MongoClient instance.
	 * </p>
	 * @return bool the former value of slaveOkay for this instance.
	 */
	public function setSlaveOkay ($ok = null) {}

	public function lastError () {}

	public function prevError () {}

	public function resetError () {}

	public function forceError () {}

	/**
	 * Returns the address being used by this for slaveOkay reads
	 * @link http://www.php.net/manual/en/mongo.getslave.php
	 * @return string The address of the secondary this connection is using for reads.
	 * </p>
	 * <p>
	 * This returns &null; if this is not connected to a replica set or not yet
	 * initialized.
	 */
	public function getSlave () {}

	/**
	 * Choose a new secondary for slaveOkay reads
	 * @link http://www.php.net/manual/en/mongo.switchslave.php
	 * @return string The address of the secondary this connection is using for reads. This may be
	 * the same as the previous address as addresses are randomly chosen. It may
	 * return only one address if only one secondary (or only the primary) is
	 * available.
	 * </p>
	 * <p>
	 * For example, if we had a three member replica set with a primary, secondary,
	 * and arbiter this method would always return the address of the secondary.
	 * If the secondary became unavailable, this method would always return the
	 * address of the primary. If the primary also became unavailable, this method
	 * would throw an exception, as an arbiter cannot handle reads.
	 */
	public function switchSlave () {}

	/**
	 * Set the size for future connection pools.
	 * @link http://www.php.net/manual/en/mongo.setpoolsize.php
	 * @param size int <p>
	 * The max number of connections future pools will be able to create.
	 * Negative numbers mean that the pool will spawn an infinite number of
	 * connections.
	 * </p>
	 * @return bool the former value of pool size.
	 */
	public static function setPoolSize ($size) {}

	/**
	 * Get pool size for connection pools
	 * @link http://www.php.net/manual/en/mongo.getpoolsize.php
	 * @return int the current pool size.
	 */
	public static function getPoolSize () {}

	/**
	 * Returns information about all connection pools.
	 * @link http://www.php.net/manual/en/mongo.pooldebug.php
	 * @return array Each connection pool has an identifier, which starts with the host. For each
	 * pool, this function shows the following fields:
	 * in use
	 * <p>
	 * The number of connections currently being used by
	 * MongoClient instances.
	 * </p>
	 * in pool
	 * <p>
	 * The number of connections currently in the pool (not being used).
	 * </p>
	 * remaining
	 * <p>
	 * The number of connections that could be created by this pool. For
	 * example, suppose a pool had 5 connections remaining and 3 connections in
	 * the pool. We could create 8 new instances of
	 * MongoClient before we exhausted this pool
	 * (assuming no instances of MongoClient went out of
	 * scope, returning their connections to the pool).
	 * </p>
	 * <p>
	 * A negative number means that this pool will spawn unlimited connections.
	 * </p>
	 * <p>
	 * Before a pool is created, you can change the max number of connections by
	 * calling Mongo::setPoolSize. Once a pool is showing
	 * up in the output of this function, its size cannot be changed.
	 * </p>
	 * timeout
	 * <p>
	 * The socket timeout for connections in this pool. This is how long
	 * connections in this pool will attempt to connect to a server before
	 * giving up.
	 * </p>
	 */
	public static function poolDebug () {}

	/**
	 * Return info about all open connections
	 * @link http://www.php.net/manual/en/mongoclient.getconnections.php
	 * @return array An array of open connections.
	 */
	public static function getConnections () {}

	/**
	 * Connects to a database server
	 * @link http://www.php.net/manual/en/mongoclient.connect.php
	 * @return bool If the connection was successful.
	 */
	public function connect () {}

	/**
	 * String representation of this connection
	 * @link http://www.php.net/manual/en/mongoclient.tostring.php
	 * @return string hostname and port for this connection.
	 */
	public function __toString () {}

	/**
	 * Gets a database
	 * @link http://www.php.net/manual/en/mongoclient.get.php
	 * @param dbname string <p>
	 * The database name.
	 * </p>
	 * @return MongoDB a new db object.
	 */
	public function __get ($dbname) {}

	/**
	 * Gets a database
	 * @link http://www.php.net/manual/en/mongoclient.selectdb.php
	 * @param name string <p>
	 * The database name.
	 * </p>
	 * @return MongoDB a new database object.
	 */
	public function selectDB ($name) {}

	/**
	 * Gets a database collection
	 * @link http://www.php.net/manual/en/mongoclient.selectcollection.php
	 * @param db string <p>
	 * The database name.
	 * </p>
	 * @param collection string <p>
	 * The collection name.
	 * </p>
	 * @return MongoCollection a new collection object.
	 */
	public function selectCollection ($db, $collection) {}

	/**
	 * Get the read preference for this connection
	 * @link http://www.php.net/manual/en/mongoclient.getreadpreference.php
	 * @return array
	 */
	public function getReadPreference () {}

	/**
	 * Set the read preference for this connection
	 * @link http://www.php.net/manual/en/mongoclient.setreadpreference.php
	 * @param read_preference string
	 * @param tags array[optional]
	 * @return bool
	 */
	public function setReadPreference ($read_preference, array $tags = null) {}

	/**
	 * Drops a database [deprecated]
	 * @link http://www.php.net/manual/en/mongoclient.dropdb.php
	 * @param db mixed <p>
	 * The database to drop. Can be a MongoDB object or the name of the database.
	 * </p>
	 * @return array the database response.
	 */
	public function dropDB ($db) {}

	/**
	 * Lists all of the databases available.
	 * @link http://www.php.net/manual/en/mongoclient.listdbs.php
	 * @return array an associative array containing three fields. The first field is
	 * databases, which in turn contains an array. Each element
	 * of the array is an associative array corresponding to a database, giving th
	 * database's name, size, and if it's empty. The other two fields are
	 * totalSize (in bytes) and ok, which is 1
	 * if this method ran successfully.
	 */
	public function listDBs () {}

	/**
	 * Updates status for all associated hosts
	 * @link http://www.php.net/manual/en/mongoclient.gethosts.php
	 * @return array an array of information about the hosts in the set. Includes each
	 * host's hostname, its health (1 is healthy), its state (1 is primary, 2 is
	 * secondary, 0 is anything else), the amount of time it took to ping the
	 * server, and when the last ping occurred. For example, on a three-member
	 * replica set, it might look something like:
	 */
	public function getHosts () {}

	/**
	 * Closes this connection
	 * @link http://www.php.net/manual/en/mongoclient.close.php
	 * @param connection boolean|string[optional] <p>
	 * If connection is not given, or false then connection that would be
	 * selected for writes would be closed. In a single-node configuration,
	 * that is then the whole connection, but if you are connected to a
	 * replica set, close() will only close the
	 * connection to the primary server.
	 * </p>
	 * <p>
	 * If connection is true then all connections as known by the connection
	 * manager will be closed. This can include connections that are not
	 * referenced in the connection string used to create the object that
	 * you are calling close on.
	 * </p>
	 * <p>
	 * If connection is a string argument, then it will only close the
	 * connection identified by this hash. Hashes are identifiers for a
	 * connection and can be obtained by calling
	 * MongoClient::getConnections.
	 * </p>
	 * @return bool if the connection was successfully closed.
	 */
	public function close ($connection = null) {}

	/**
	 * Kills a specific cursor on the server
	 * @link http://www.php.net/manual/en/mongoclient.killcursor.php
	 * @param server_hash string <p>
	 * The server hash that has the cursor. This can be obtained through
	 * MongoCursor::info.
	 * </p>
	 * @param id int|MongoInt64 <p>
	 * The ID of the cursor to kill. You can either supply an int
	 * containing the 64 bit cursor ID, or an object of the
	 * MongoInt64 class. The latter is necessary on 32
	 * bit platforms (and Windows).
	 * </p>
	 * @return bool true if the method attempted to kill a cursor, and false if
	 * there was something wrong with the arguments (such as a wrong
	 * server_hash). The return status does not
	 * reflect where the cursor was actually killed as the server does
	 * not provide that information.
	 */
	public static function killCursor ($server_hash, $id) {}

}

/** @jms-builtin */
class MongoDB  {
	const PROFILING_OFF = 0;
	const PROFILING_SLOW = 1;
	const PROFILING_ON = 2;

	public $w;
	public $wtimeout;


	/**
	 * Creates a new database
	 * @link http://www.php.net/manual/en/mongodb.construct.php
	 * @param connection MongoClient
	 * @param database_name
	 */
	public function __construct (MongoClient $connection, $database_name) {}

	/**
	 * The name of this database
	 * @link http://www.php.net/manual/en/mongodb.--tostring.php
	 * @return string this database&apos;s name.
	 */
	public function __toString () {}

	/**
	 * Gets a collection
	 * @link http://www.php.net/manual/en/mongodb.get.php
	 * @param name string <p>
	 * The name of the collection.
	 * </p>
	 * @return MongoCollection the collection.
	 */
	public function __get ($name) {}

	/**
	 * Fetches toolkit for dealing with files stored in this database
	 * @link http://www.php.net/manual/en/mongodb.getgridfs.php
	 * @param prefix string[optional] <p>
	 * The prefix for the files and chunks collections.
	 * </p>
	 * @return MongoGridFS a new gridfs object for this database.
	 */
	public function getGridFS ($prefix = null) {}

	/**
	 * Get slaveOkay setting for this database
	 * @link http://www.php.net/manual/en/mongodb.getslaveokay.php
	 * @return bool the value of slaveOkay for this instance.
	 */
	public function getSlaveOkay () {}

	/**
	 * Change slaveOkay setting for this database
	 * @link http://www.php.net/manual/en/mongodb.setslaveokay.php
	 * @param ok bool[optional] <p>
	 * If reads should be sent to secondary members of a replica set for all
	 * possible queries using this MongoDB instance.
	 * </p>
	 * @return bool the former value of slaveOkay for this instance.
	 */
	public function setSlaveOkay ($ok = null) {}

	/**
	 * Get the read preference for this database
	 * @link http://www.php.net/manual/en/mongodb.getreadpreference.php
	 * @return array
	 */
	public function getReadPreference () {}

	/**
	 * Set the read preference for this database
	 * @link http://www.php.net/manual/en/mongodb.setreadpreference.php
	 * @param read_preference string
	 * @param tags array[optional]
	 * @return bool
	 */
	public function setReadPreference ($read_preference, array $tags = null) {}

	/**
	 * Get the write concern for this database
	 * @link http://www.php.net/manual/en/mongodb.getwriteconcern.php
	 * @return array
	 */
	public function getWriteConcern () {}

	/**
	 * Set the write concern for this database
	 * @link http://www.php.net/manual/en/mongodb.setwriteconcern.php
	 * @param w mixed
	 * @param wtimeout int[optional]
	 * @return bool
	 */
	public function setWriteConcern ($w, $wtimeout = null) {}

	/**
	 * Gets this database&apos;s profiling level
	 * @link http://www.php.net/manual/en/mongodb.getprofilinglevel.php
	 * @return int the profiling level.
	 */
	public function getProfilingLevel () {}

	/**
	 * Sets this database&apos;s profiling level
	 * @link http://www.php.net/manual/en/mongodb.setprofilinglevel.php
	 * @param level int <p>
	 * Profiling level.
	 * </p>
	 * @return int the previous profiling level.
	 */
	public function setProfilingLevel ($level) {}

	/**
	 * Drops this database
	 * @link http://www.php.net/manual/en/mongodb.drop.php
	 * @return array the database response.
	 */
	public function drop () {}

	/**
	 * Repairs and compacts this database
	 * @link http://www.php.net/manual/en/mongodb.repair.php
	 * @param preserve_cloned_files bool[optional] <p>
	 * If cloned files should be kept if the repair fails.
	 * </p>
	 * @param backup_original_files bool[optional] <p>
	 * If original files should be backed up.
	 * </p>
	 * @return array db response.
	 */
	public function repair ($preserve_cloned_files = null, $backup_original_files = null) {}

	/**
	 * Gets a collection
	 * @link http://www.php.net/manual/en/mongodb.selectcollection.php
	 * @param name string <p>
	 * The collection name.
	 * </p>
	 * @return MongoCollection a new collection object.
	 */
	public function selectCollection ($name) {}

	/**
	 * Creates a collection
	 * @link http://www.php.net/manual/en/mongodb.createcollection.php
	 * @param name string <p>
	 * The name of the collection.
	 * </p>
	 * @param options array[optional] <p>
	 * An array containing options for the collections. Each option is its own
	 * element in the options array, with the option name listed below being
	 * the key of the element. The supported options depend on the MongoDB
	 * server version. At the moment, the following options are supported:
	 * </p>
	 * <p>
	 * capped
	 * <p>
	 * If the collection should be a fixed size.
	 * </p>
	 * @return MongoCollection a collection object representing the new collection.
	 */
	public function createCollection ($name, array $options = null) {}

	/**
	 * Drops a collection [deprecated]
	 * @link http://www.php.net/manual/en/mongodb.dropcollection.php
	 * @param coll mixed <p>
	 * MongoCollection or name of collection to drop.
	 * </p>
	 * @return array the database response.
	 */
	public function dropCollection ($coll) {}

	/**
	 * Gets an array of all MongoCollections for this database
	 * @link http://www.php.net/manual/en/mongodb.listcollections.php
	 * @param includeSystemCollections bool[optional] <p>
	 * Include system collections.
	 * </p>
	 * @return array an array of MongoCollection objects.
	 */
	public function listCollections ($includeSystemCollections = null) {}

	/**
	 * Get all collections from this database
	 * @link http://www.php.net/manual/en/mongodb.getcollectionnames.php
	 * @param includeSystemCollections bool[optional] <p>
	 * Include system collections.
	 * </p>
	 * @return array the names of the all the collections in the database as an array.
	 */
	public function getCollectionNames ($includeSystemCollections = null) {}

	/**
	 * Creates a database reference
	 * @link http://www.php.net/manual/en/mongodb.createdbref.php
	 * @param collection string <p>
	 * The collection to which the database reference will point.
	 * </p>
	 * @param document_or_id mixed <p>
	 * If an array or object is given, its _id field will be
	 * used as the reference ID. If a MongoId or scalar
	 * is given, it will be used as the reference ID.
	 * </p>
	 * @return array a database reference array.
	 * </p>
	 * <p>
	 * If an array without an _id field was provided as the
	 * document_or_id parameter, &null; will be returned.
	 */
	public function createDBRef ($collection, $document_or_id) {}

	/**
	 * Fetches the document pointed to by a database reference
	 * @link http://www.php.net/manual/en/mongodb.getdbref.php
	 * @param ref array <p>
	 * A database reference.
	 * </p>
	 * @return array the document pointed to by the reference.
	 */
	public function getDBRef (array $ref) {}

	/**
	 * Runs JavaScript code on the database server.
	 * @link http://www.php.net/manual/en/mongodb.execute.php
	 * @param code mixed <p>
	 * MongoCode or string to execute.
	 * </p>
	 * @param args array[optional] <p>
	 * Arguments to be passed to code.
	 * </p>
	 * @return array the result of the evaluation.
	 */
	public function execute ($code, array $args = null) {}

	/**
	 * Execute a database command
	 * @link http://www.php.net/manual/en/mongodb.command.php
	 * @param command array <p>
	 * The query to send.
	 * </p>
	 * @param options array[optional] <p>
	 * This parameter is an associative array of the form
	 * array("optionname" => &lt;boolean&gt;, ...). Currently
	 * supported options are:
	 * &mongo.writes.parameters.timeout;
	 * </p>
	 * @return array database response. Every database response is always maximum one
	 * document, which means that the result of a database command can never
	 * exceed 16MB. The resulting document's structure depends on the command, but
	 * most results will have the ok field to indicate success
	 * or failure and results containing an array of each of
	 * the resulting documents.
	 */
	public function command (array $command, array $options = null) {}

	/**
	 * Check if there was an error on the most recent db operation performed
	 * @link http://www.php.net/manual/en/mongodb.lasterror.php
	 * @return array the error, if there was one.
	 */
	public function lastError () {}

	/**
	 * Checks for the last error thrown during a database operation
	 * @link http://www.php.net/manual/en/mongodb.preverror.php
	 * @return array the error and the number of operations ago it occurred.
	 */
	public function prevError () {}

	/**
	 * Clears any flagged errors on the database
	 * @link http://www.php.net/manual/en/mongodb.reseterror.php
	 * @return array the database response.
	 */
	public function resetError () {}

	/**
	 * Creates a database error
	 * @link http://www.php.net/manual/en/mongodb.forceerror.php
	 * @return bool the database response.
	 */
	public function forceError () {}

	/**
	 * Log in to this database
	 * @link http://www.php.net/manual/en/mongodb.authenticate.php
	 * @param username string <p>
	 * The username.
	 * </p>
	 * @param password string <p>
	 * The password (in plaintext).
	 * </p>
	 * @return array database response. If the login was successful, it will return
	 * 1);
	 * ?>
	 * ]]>
	 * If something went wrong, it will return
	 * 0, "errmsg" => "auth fails");
	 * ?>
	 * ]]>
	 * ("auth fails" could be another message, depending on database version and what
	 * when wrong).
	 */
	public function authenticate ($username, $password) {}

}

/** @jms-builtin */
class MongoCollection  {
	const ASCENDING = 1;
	const DESCENDING = -1;

	public $w;
	public $wtimeout;


	/**
	 * Creates a new collection
	 * @link http://www.php.net/manual/en/mongocollection.construct.php
	 * @param database MongoDB
	 * @param collection_name
	 */
	public function __construct (MongoDB $database, $collection_name) {}

	/**
	 * String representation of this collection
	 * @link http://www.php.net/manual/en/mongocollection.--tostring.php
	 * @return string the full name of this collection.
	 */
	public function __toString () {}

	/**
	 * Gets a collection
	 * @link http://www.php.net/manual/en/mongocollection.get.php
	 * @param name string <p>
	 * The next string in the collection name.
	 * </p>
	 * @return MongoCollection the collection.
	 */
	public function __get ($name) {}

	/**
	 * Returns this collection&apos;s name
	 * @link http://www.php.net/manual/en/mongocollection.getname.php
	 * @return string the name of this collection.
	 */
	public function getName () {}

	/**
	 * Get slaveOkay setting for this collection
	 * @link http://www.php.net/manual/en/mongocollection.getslaveokay.php
	 * @return bool the value of slaveOkay for this instance.
	 */
	public function getSlaveOkay () {}

	/**
	 * Change slaveOkay setting for this collection
	 * @link http://www.php.net/manual/en/mongocollection.setslaveokay.php
	 * @param ok bool[optional] <p>
	 * If reads should be sent to secondary members of a replica set for all
	 * possible queries using this MongoCollection
	 * instance.
	 * </p>
	 * @return bool the former value of slaveOkay for this instance.
	 */
	public function setSlaveOkay ($ok = null) {}

	/**
	 * Get the read preference for this collection
	 * @link http://www.php.net/manual/en/mongocollection.getreadpreference.php
	 * @return array
	 */
	public function getReadPreference () {}

	/**
	 * Set the read preference for this collection
	 * @link http://www.php.net/manual/en/mongocollection.setreadpreference.php
	 * @param read_preference string
	 * @param tags array[optional]
	 * @return bool
	 */
	public function setReadPreference ($read_preference, array $tags = null) {}

	/**
	 * Get the write concern for this collection
	 * @link http://www.php.net/manual/en/mongocollection.getwriteconcern.php
	 * @return array
	 */
	public function getWriteConcern () {}

	/**
	 * Set the write concern for this database
	 * @link http://www.php.net/manual/en/mongocollection.setwriteconcern.php
	 * @param w mixed
	 * @param wtimeout int[optional]
	 * @return bool
	 */
	public function setWriteConcern ($w, $wtimeout = null) {}

	/**
	 * Drops this collection
	 * @link http://www.php.net/manual/en/mongocollection.drop.php
	 * @return array the database response.
	 */
	public function drop () {}

	/**
	 * Validates this collection
	 * @link http://www.php.net/manual/en/mongocollection.validate.php
	 * @param scan_data bool[optional] <p>
	 * Only validate indices, not the base collection.
	 * </p>
	 * @return array the database&apos;s evaluation of this object.
	 */
	public function validate ($scan_data = null) {}

	/**
	 * Inserts a document into the collection
	 * @link http://www.php.net/manual/en/mongocollection.insert.php
	 * @param a array|object <p>
	 * An array or object. If an object is used, it may not have protected or
	 * private properties.
	 * </p>
	 * <p>
	 * If the parameter does not have an _id key or
	 * property, a new MongoId instance will be created
	 * and assigned to it. This special behavior does not mean that the
	 * parameter is passed by reference.
	 * </p>
	 * @param options array[optional] <p>
	 * Options for the insert.
	 * &mongo.writes.parameters.fsync;
	 * &mongo.writes.parameters.journal;
	 * &mongo.writes.parameters.sockettimeoutms;
	 * &mongo.writes.parameters.writeconcern;
	 * &mongo.writes.parameters.writeconcerntimeout;
	 * &mongo.writes.parameters.writeconcerntimeoutms;
	 * &mongo.writes.parameters.safe;
	 * &mongo.writes.parameters.timeout;
	 * </p>
	 * @return bool|array an array containing the status of the insertion if the
	 * "w" option is set. Otherwise, returns true if the
	 * inserted array is not empty (a MongoException will be
	 * thrown if the inserted array is empty).
	 * </p>
	 * <p>
	 * If an array is returned, the following keys may be present:
	 * ok
	 * <p>
	 * This should almost always be 1 (unless last_error itself failed).
	 * </p>
	 * err
	 * <p>
	 * If this field is non-null, an error occurred on the previous operation.
	 * If this field is set, it will be a string describing the error that
	 * occurred.
	 * </p>
	 * code
	 * <p>
	 * If a database error occurred, the relevant error code will be passed
	 * back to the client.
	 * </p>
	 * errmsg
	 * <p>
	 * This field is set if something goes wrong with a database command. It
	 * is coupled with ok being 0. For example, if
	 * w is set and times out, errmsg will be set to "timed
	 * out waiting for slaves" and ok will be 0. If this
	 * field is set, it will be a string describing the error that occurred.
	 * </p>
	 * n
	 * <p>
	 * If the last operation was an update, upsert, or a remove, the number
	 * of documents affected will be returned. For insert operations, this value
	 * is always 0.
	 * </p>
	 * wtimeout
	 * <p>
	 * If the previous option timed out waiting for replication.
	 * </p>
	 * waited
	 * <p>
	 * How long the operation waited before timing out.
	 * </p>
	 * wtime
	 * <p>
	 * If w was set and the operation succeeded, how long it took to
	 * replicate to w servers.
	 * </p>
	 * upserted
	 * <p>
	 * If an upsert occurred, this field will contain the new record's
	 * _id field. For upserts, either this field or
	 * updatedExisting will be present (unless an error
	 * occurred).
	 * </p>
	 * updatedExisting
	 * <p>
	 * If an upsert updated an existing element, this field will be true. For
	 * upserts, either this field or upserted will be present (unless an error
	 * occurred).
	 * </p>
	 */
	public function insert ($a, array $options = null) {}

	/**
	 * Inserts multiple documents into this collection
	 * @link http://www.php.net/manual/en/mongocollection.batchinsert.php
	 * @param a array <p>
	 * An array of arrays or objects. If any objects are used, they may not have
	 * protected or private properties.
	 * </p>
	 * <p>
	 * If the documents to insert do not have an _id key or
	 * property, a new MongoId instance will be created
	 * and assigned to it. See MongoCollection::insert for
	 * additional information on this behavior.
	 * </p>
	 * @param options array[optional] <p>
	 * Options for the inserts.
	 * <p>
	 * "continueOnError"
	 * </p>
	 * <p>
	 * Boolean, defaults to false. If set, the database will not stop
	 * processing a bulk insert if one fails (eg due to duplicate IDs).
	 * This makes bulk insert behave similarly to a series of single
	 * inserts, except that calling MongoDB::lastError
	 * will have an error set if any insert fails, not just the last one.
	 * If multiple errors occur, only the most recent will be reported by
	 * MongoDB::lastError.
	 * </p>
	 * <p>
	 * Please note that continueOnError affects errors
	 * on the database side only. If you try to insert a document that has
	 * errors (for example it contains a key with an empty name), then the
	 * document is not even transferred to the database as the driver
	 * detects this error and bails out.
	 * continueOnError has no effect on errors detected
	 * in the documents by the driver.
	 * </p>
	 * @return mixed If the w parameter is set to acknowledge the write,
	 * returns an associative array with the status of the inserts ("ok") and any
	 * error that may have occurred ("err"). Otherwise, returns true if the
	 * batch insert was successfully sent, false otherwise.
	 */
	public function batchInsert (array $a, array $options = null) {}

	/**
	 * Update records based on a given criteria
	 * @link http://www.php.net/manual/en/mongocollection.update.php
	 * @param criteria array <p>
	 * Description of the objects to update.
	 * </p>
	 * @param new_object array <p>
	 * The object with which to update the matching records.
	 * </p>
	 * @param options array[optional] <p>
	 * This parameter is an associative array of the form
	 * array("optionname" => &lt;boolean&gt;, ...). Currently
	 * supported options are:
	 * <p>
	 * "upsert"
	 * </p>
	 * <p>
	 * If no document matches $criteria, a new
	 * document will be inserted.
	 * </p>
	 * <p>
	 * If a new document would be inserted and
	 * $new_object contains atomic modifiers
	 * (i.e. $ operators), those operations will be
	 * applied to the $criteria parameter to create
	 * the new document. If $new_object does not
	 * contain atomic modifiers, it will be used as-is for the inserted
	 * document. See the upsert examples below for more information.
	 * </p>
	 * @return bool|array an array containing the status of the update if the
	 * "w" option is set. Otherwise, returns true.
	 * </p>
	 * <p>
	 * Fields in the status array are described in the documentation for
	 * MongoCollection::insert.
	 */
	public function update (array $criteria, array $new_object, array $options = null) {}

	/**
	 * Remove records from this collection
	 * @link http://www.php.net/manual/en/mongocollection.remove.php
	 * @param criteria array[optional] <p>
	 * Description of records to remove.
	 * </p>
	 * @param options array[optional] <p>
	 * Options for remove.
	 * &mongo.writes.parameters.writeconcern;
	 * <p>
	 * "justOne"
	 * </p>
	 * <p>
	 * Remove at most one record matching this criteria.
	 * </p>
	 * @return bool|array an array containing the status of the removal if the
	 * "w" option is set. Otherwise, returns true.
	 * </p>
	 * <p>
	 * Fields in the status array are described in the documentation for
	 * MongoCollection::insert.
	 */
	public function remove (array $criteria = null, array $options = null) {}

	/**
	 * Queries this collection, returning a <classname>MongoCursor</classname> for the result set
	 * @link http://www.php.net/manual/en/mongocollection.find.php
	 * @param query array[optional] <p>
	 * The fields for which to search. MongoDB's query language is quite
	 * extensive. The PHP driver will in almost all cases pass the query
	 * straight through to the server, so reading the MongoDB core docs on
	 * find is a good idea.
	 * </p>
	 * <p>
	 * Please make sure that for all special query operators (starting with
	 * $) you use single quotes so that PHP doesn't try to
	 * replace "$exists" with the value of the variable
	 * $exists.
	 * </p>
	 * @param fields array[optional] <p>
	 * Fields of the results to return. The array is in the format
	 * array('fieldname' => true, 'fieldname2' => true).
	 * The _id field is always returned.
	 * </p>
	 * @return MongoCursor a cursor for the search results.
	 */
	public function find (array $query = null, array $fields = null) {}

	/**
	 * Queries this collection, returning a single element
	 * @link http://www.php.net/manual/en/mongocollection.findone.php
	 * @param query array[optional] <p>
	 * The fields for which to search. MongoDB's query language is quite
	 * extensive. The PHP driver will in almost all cases pass the query
	 * straight through to the server, so reading the MongoDB core docs on
	 * find is a good idea.
	 * </p>
	 * <p>
	 * Please make sure that for all special query operaters (starting with
	 * $) you use single quotes so that PHP doesn't try to
	 * replace "$exists" with the value of the variable
	 * $exists.
	 * </p>
	 * @param fields array[optional] <p>
	 * Fields of the results to return. The array is in the format
	 * array('fieldname' => true, 'fieldname2' => true).
	 * The _id field is always returned.
	 * </p>
	 * @return array record matching the search or &null;.
	 */
	public function findOne (array $query = null, array $fields = null) {}

	/**
	 * Update a document and return it
	 * @link http://www.php.net/manual/en/mongocollection.findandmodify.php
	 * @param query array <p>
	 * The query criteria to search for.
	 * </p>
	 * @param update array[optional] <p>
	 * The update criteria.
	 * </p>
	 * @param fields array[optional] <p>
	 * Optionally only return these fields.
	 * </p>
	 * @param options array[optional] <p>
	 * An array of options to apply, such as remove the match document from the
	 * DB and return it.
	 * <tr valign="top">
	 * <td>Option</td>
	 * <td>&Description;</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>sort array</td>
	 * <td>
	 * Determines which document the operation will modify if the
	 * query selects multiple documents. findAndModify will modify the
	 * first document in the sort order specified by this argument.
	 * </td>
	 * </tr>
	 * <tr valign="top">
	 * <td>remove boolean</td>
	 * <td>
	 * Optional if update field exists. When true, removes the selected
	 * document. The default is false.
	 * </td>
	 * </tr>
	 * <tr valign="top">
	 * <td>update array</td>
	 * <td>
	 * Optional if remove field exists.
	 * Performs an update of the selected document.
	 * </td>
	 * </tr>
	 * <tr valign="top">
	 * <td>new boolean</td>
	 * <td>
	 * Optional. When true, returns the modified document rather than the
	 * original. The findAndModify method ignores the new option for
	 * remove operations. The default is false.
	 * </td>
	 * </tr>
	 * <tr valign="top">
	 * <td>upsert boolean</td>
	 * <td>
	 * Optional. Used in conjunction with the update field. When true, the
	 * findAndModify command creates a new document if the query returns
	 * no documents. The default is false. In MongoDB 2.2, the
	 * findAndModify command returns &null; when upsert is true.
	 * </td>
	 * </tr>
	 * <tr valign="top">
	 * <td></td>
	 * <td>
	 * </td>
	 * </tr>
	 * </p>
	 * @return array the original document, or the modified document when
	 * new is set.
	 */
	public function findAndModify (array $query, array $update = null, array $fields = null, array $options = null) {}

	/**
	 * Execute a database command and retrieve results through a cursor
	 * @link http://www.php.net/manual/en/mongocollection.commandcursor.php
	 * @param command array <p>
	 * The command query to send.
	 * </p>
	 * <p>
	 * It is possible to configure how many initial documents the server
	 * should return with the first result set. This configuration is made as
	 * part of the command query. The default initial batch size is
	 * 101. You can change it by adding the
	 * cursor key in the following way to the command:
	 * </p>
	 * commandCursor( [
	 * "aggregate" => "collectionName",
	 * "pipeline" => [
	 * ...
	 * ],
	 * "cursor" => [ "batchSize" => 4 ],
	 * ] );
	 * ]]>
	 * <p>
	 * If you do not set the initial
	 * batchSize option, then the PHP driver will implicitly
	 * add this for you with a value of 101
	 * </p>
	 * <p>
	 * This setting does only configure the first batch size. To configure the
	 * size of future batches, please use the
	 * MongoCommandCursor::batchSize method on the
	 * returned MongoCommandCursor object.
	 * </p>
	 * @return MongoCommandCursor a MongoCommandCursor object. Because this
	 * implements the Iterator interface you can
	 * iterate over each of the results as returned by the command query. The
	 * MongoCommandCursor also implements the
	 * MongoCursorInterface interface which adds the
	 * MongoCommandCursor::batchSize,
	 * MongoCommandCursor::dead,
	 * MongoCommandCursor::info methods.
	 */
	public function commandCursor (array $command) {}

	/**
	 * Creates an index on the given field(s)
	 * @link http://www.php.net/manual/en/mongocollection.createindex.php
	 * @param keys array <p>
	 * An array of fields by which to sort the index on. Each element in the
	 * array has as key the field name, and as value either
	 * 1 for ascending sort, -1 for
	 * descending sort, or any of the index plugins (currently,
	 * "text", "2d", or
	 * "2dsphere"").
	 * </p>
	 * @param options array[optional] <p>
	 * This parameter is an associative array of the form
	 * array("optionname" => &lt;boolean&gt;, ...). Currently
	 * supported options are:
	 * &mongo.writes.parameters.writeconcern;
	 * <p>
	 * "unique"
	 * </p>
	 * <p>
	 * Create a unique index.
	 * </p>
	 * <p>
	 * A unique index cannot be created on a field if multiple existing
	 * documents do not contain the field. The field is effectively &null;
	 * for these documents and thus already non-unique. Sparse indexing may
	 * be used to overcome this, since it will prevent documents without the
	 * field from being indexed.
	 * </p>
	 * @return bool an array containing the status of the index creation. The array
	 * contains whether the operation worked ("ok"), the amount
	 * of indexes before and after the operation
	 * ("numIndexesBefore" and
	 * "numIndexesAfter") and whether the collection that the
	 * index belongs to has been created
	 * ("createdCollectionAutomatically").
	 * </p>
	 * <p>
	 * With MongoDB 2.4 and earlier, a status document is only returned if the
	 * "w" option is set to 1—either through
	 * the connection string, or with . If
	 * "w" is not set to 1, it returns
	 * true. The fields in the status document are different, except for the
	 * "ok" field which signals whether the index creation
	 * worked.
	 */
	public function createIndex (array $keys, array $options = null) {}

	/**
	 * Creates an index on the given field(s), or does nothing if the index
   already exists
	 * @link http://www.php.net/manual/en/mongocollection.ensureindex.php
	 * @param key_keys string|array
	 * @param options array[optional] <p>
	 * This parameter is an associative array of the form
	 * array("optionname" => &lt;boolean&gt;, ...). Currently
	 * supported options are:
	 * &mongo.writes.parameters.writeconcern;
	 * <p>
	 * "unique"
	 * </p>
	 * <p>
	 * Create a unique index.
	 * </p>
	 * <p>
	 * A unique index cannot be created on a field if multiple existing
	 * documents do not contain the field. The field is effectively &null;
	 * for these documents and thus already non-unique. Sparse indexing may
	 * be used to overcome this, since it will prevent documents without the
	 * field from being indexed.
	 * </p>
	 * @return bool an array containing the status of the index creation if the
	 * "w" option is set. Otherwise, returns true.
	 * </p>
	 * <p>
	 * Fields in the status array are described in the documentation for
	 * MongoCollection::insert.
	 */
	public function ensureIndex ($key_keys, array $options = null) {}

	/**
	 * Deletes an index from this collection
	 * @link http://www.php.net/manual/en/mongocollection.deleteindex.php
	 * @param keys string|array <p>
	 * Field or fields from which to delete the index.
	 * </p>
	 * @return array the database response.
	 */
	public function deleteIndex ($keys) {}

	/**
	 * Delete all indices for this collection
	 * @link http://www.php.net/manual/en/mongocollection.deleteindexes.php
	 * @return array the database response.
	 */
	public function deleteIndexes () {}

	/**
	 * Returns information about indexes on this collection
	 * @link http://www.php.net/manual/en/mongocollection.getindexinfo.php
	 * @return array This function returns an array in which each element describes an index.
	 * Elements will contain the values name for the name of
	 * the index, ns for the namespace (a combination of the
	 * database and collection name), and key for a list of all
	 * fields in the index and their ordering. Additional values may be present for
	 * special indexes, such as unique or
	 * sparse.
	 */
	public function getIndexInfo () {}

	/**
	 * Counts the number of documents in this collection
	 * @link http://www.php.net/manual/en/mongocollection.count.php
	 * @param query array[optional] <p>
	 * Associative array or object with fields to match.
	 * </p>
	 * @param limit int[optional] <p>
	 * Specifies an upper limit to the number returned.
	 * </p>
	 * @param skip int[optional] <p>
	 * Specifies a number of results to skip before starting the count.
	 * </p>
	 * @return int the number of documents matching the query.
	 */
	public function count (array $query = null, $limit = null, $skip = null) {}

	/**
	 * Saves a document to this collection
	 * @link http://www.php.net/manual/en/mongocollection.save.php
	 * @param a array|object <p>
	 * Array or object to save. If an object is used, it may not have protected
	 * or private properties.
	 * </p>
	 * <p>
	 * If the parameter does not have an _id key or
	 * property, a new MongoId instance will be created
	 * and assigned to it. See MongoCollection::insert for
	 * additional information on this behavior.
	 * </p>
	 * @param options array[optional] <p>
	 * Options for the save.
	 * &mongo.writes.parameters.fsync;
	 * &mongo.writes.parameters.journal;
	 * &mongo.writes.parameters.sockettimeoutms;
	 * &mongo.writes.parameters.writeconcern;
	 * &mongo.writes.parameters.writeconcerntimeout;
	 * &mongo.writes.parameters.writeconcerntimeoutms;
	 * &mongo.writes.parameters.safe;
	 * &mongo.writes.parameters.timeout;
	 * </p>
	 * @return mixed If w was set, returns an array containing the status of the save.
	 * Otherwise, returns a boolean representing if the array was not empty (an empty array will not
	 * be inserted).
	 */
	public function save ($a, array $options = null) {}

	/**
	 * Creates a database reference
	 * @link http://www.php.net/manual/en/mongocollection.createdbref.php
	 * @param document_or_id mixed <p>
	 * If an array or object is given, its _id field will be
	 * used as the reference ID. If a MongoId or scalar
	 * is given, it will be used as the reference ID.
	 * </p>
	 * @return array a database reference array.
	 * </p>
	 * <p>
	 * If an array without an _id field was provided as the
	 * document_or_id parameter, &null; will be returned.
	 */
	public function createDBRef ($document_or_id) {}

	/**
	 * Fetches the document pointed to by a database reference
	 * @link http://www.php.net/manual/en/mongocollection.getdbref.php
	 * @param ref array <p>
	 * A database reference.
	 * </p>
	 * @return array the database document pointed to by the reference.
	 */
	public function getDBRef (array $ref) {}

	/**
	 * Converts keys specifying an index to its identifying string
	 * @link http://www.php.net/manual/en/mongocollection.toindexstring.php
	 * @param keys mixed <p>
	 * Field or fields to convert to the identifying string
	 * </p>
	 * @return string a string that describes the index.
	 */
	protected static function toIndexString ($keys) {}

	/**
	 * Performs an operation similar to SQL's GROUP BY command
	 * @link http://www.php.net/manual/en/mongocollection.group.php
	 * @param keys mixed <p>
	 * Fields to group by. If an array or non-code object is passed, it will be
	 * the key used to group results.
	 * </p>
	 * <p>1.0.4+: If keys is an instance of
	 * MongoCode, keys will be treated as
	 * a function that returns the key to group by (see the "Passing a
	 * keys function" example below).
	 * </p>
	 * @param initial array <p>
	 * Initial value of the aggregation counter object.
	 * </p>
	 * @param reduce MongoCode <p>
	 * A function that takes two arguments (the current document and the
	 * aggregation to this point) and does the aggregation.
	 * </p>
	 * @param options array[optional] <p>
	 * Optional parameters to the group command. Valid options include:
	 * </p>
	 * <p>
	 * "condition"
	 * </p>
	 * <p>
	 * Criteria for including a document in the aggregation.
	 * </p>
	 * @return array an array containing the result.
	 */
	public function group ($keys, array $initial, MongoCode $reduce, array $options = null) {}

	/**
	 * Retrieve a list of distinct values for the given key across a collection.
	 * @link http://www.php.net/manual/en/mongocollection.distinct.php
	 * @param key string <p>
	 * The key to use.
	 * </p>
	 * @param query array[optional] <p>
	 * An optional query parameters
	 * </p>
	 * @return array an array of distinct values, &return.falseforfailure;
	 */
	public function distinct ($key, array $query = null) {}

	/**
	 * Perform an aggregation using the aggregation framework
	 * @link http://www.php.net/manual/en/mongocollection.aggregate.php
	 * @param pipeline array <p>
	 * An array of pipeline operators.
	 * </p>
	 * @param options array[optional] <p>
	 * Array of command arguments, such as allowDiskUse, explain or cursor.
	 * </p>
	 * @return array The result of the aggregation as an array. The ok will
	 * be set to 1 on success, 0 on failure.
	 */
	public function aggregate (array $pipeline, array $options = null) {}

}

interface MongoCursorInterface extends Iterator, Traversable {

	/**
	 * @param number
	 */
	abstract public function batchSize ($number) {}

	abstract public function info () {}

	abstract public function dead () {}

	abstract public function current () {}

	abstract public function next () {}

	abstract public function key () {}

	abstract public function valid () {}

	abstract public function rewind () {}

}

/** @jms-builtin */
class MongoCursor implements MongoCursorInterface, Traversable, Iterator {
	public static $slaveOkay;
	public static $timeout;


	/**
	 * Create a new cursor
	 * @link http://www.php.net/manual/en/mongocursor.construct.php
	 * @param connection MongoClient
	 * @param database_and_collection_name
	 * @param query[optional]
	 * @param array_of_fields_OR_object[optional]
	 */
	public function __construct (MongoClient $connection, $database_and_collection_name, $query, $array_of_fields_OR_object) {}

	/**
	 * Checks if there are any more elements in this cursor
	 * @link http://www.php.net/manual/en/mongocursor.hasnext.php
	 * @return bool if there is another element.
	 */
	public function hasNext () {}

	/**
	 * Return the next object to which this cursor points, and advance the cursor
	 * @link http://www.php.net/manual/en/mongocursor.getnext.php
	 * @return array the next object.
	 */
	public function getNext () {}

	/**
	 * Limits the number of results returned
	 * @link http://www.php.net/manual/en/mongocursor.limit.php
	 * @param num int <p>
	 * The number of results to return.
	 * </p>
	 * @return MongoCursor this cursor.
	 */
	public function limit ($num) {}

	/**
	 * Limits the number of elements returned in one batch.
	 * @link http://www.php.net/manual/en/mongocursor.batchsize.php
	 * @param batchSize int <p>
	 * The number of results to return per batch. Each batch requires a
	 * round-trip to the server.
	 * </p>
	 * <p>
	 * If batchSize is 2 or
	 * more, it represents the size of each batch of objects retrieved.
	 * It can be adjusted to optimize performance and limit data transfer.
	 * </p>
	 * <p>
	 * If batchSize is 1 or negative, it
	 * will limit of number returned documents to the absolute value of batchSize,
	 * and the cursor will be closed. For example if
	 * batchSize is -10, then the server will return a maximum
	 * of 10 documents and as many as can fit in 4MB, then close the cursor.
	 * </p>
	 * <p>
	 * A batchSize of 1 is special, and
	 * means the same as -1, i.e. a value of
	 * 1 makes the cursor only capable of returning
	 * one document.
	 * </p>
	 * <p>
	 * Note that this feature is different from
	 * MongoCursor::limit in that documents must fit within a
	 * maximum size, and it removes the need to send a request to close the cursor
	 * server-side. The batch size can be changed even after a cursor is iterated,
	 * in which case the setting will apply on the next batch retrieval.
	 * </p>
	 * <p>
	 * This cannot override MongoDB's limit on the amount of data it will return to
	 * the client (i.e., if you set batch size to 1,000,000,000, MongoDB will still
	 * only return 4-16MB of results per batch).
	 * </p>
	 * <p>
	 * To ensure consistent behavior, the rules of
	 * MongoCursor::batchSize and
	 * MongoCursor::limit behave a
	 * little complex but work "as expected". The rules are: hard limits override
	 * soft limits with preference given to MongoCursor::limit
	 * over MongoCursor::batchSize. After that, whichever is
	 * set and lower than the other will take precedence. See below.
	 * section for some examples.
	 * </p>
	 * @return MongoCursor this cursor.
	 */
	public function batchSize ($batchSize) {}

	/**
	 * Skips a number of results
	 * @link http://www.php.net/manual/en/mongocursor.skip.php
	 * @param num int <p>
	 * The number of results to skip.
	 * </p>
	 * @return MongoCursor this cursor.
	 */
	public function skip ($num) {}

	/**
	 * Sets the fields for a query
	 * @link http://www.php.net/manual/en/mongocursor.fields.php
	 * @param f array <p>
	 * Fields to return (or not return).
	 * </p>
	 * @return MongoCursor this cursor.
	 */
	public function fields (array $f) {}

	/**
	 * @param ms
	 */
	public function maxTimeMS ($ms) {}

	/**
	 * Adds a top-level key/value pair to a query
	 * @link http://www.php.net/manual/en/mongocursor.addoption.php
	 * @param key string <p>
	 * Fieldname to add.
	 * </p>
	 * @param value mixed <p>
	 * Value to add.
	 * </p>
	 * @return MongoCursor this cursor.
	 */
	public function addOption ($key, $value) {}

	/**
	 * Use snapshot mode for the query
	 * @link http://www.php.net/manual/en/mongocursor.snapshot.php
	 * @return MongoCursor this cursor.
	 */
	public function snapshot () {}

	/**
	 * Sorts the results by given fields
	 * @link http://www.php.net/manual/en/mongocursor.sort.php
	 * @param fields array <p>
	 * An array of fields by which to sort. Each element in the array has as
	 * key the field name, and as value either 1 for
	 * ascending sort, or -1 for descending sort.
	 * </p>
	 * <p>
	 * Each result is first sorted on the first field in the array, then (if
	 * it exists) on the second field in the array, etc. This means that the
	 * order of the fields in the fields array is
	 * important. See also the examples section.
	 * </p>
	 * @return MongoCursor the same cursor that this method was called on.
	 */
	public function sort (array $fields) {}

	/**
	 * Gives the database a hint about the query
	 * @link http://www.php.net/manual/en/mongocursor.hint.php
	 * @param index mixed <p>
	 * Index to use for the query. If a string is passed, it should correspond
	 * to an index name. If an array or object is passed, it should correspond
	 * to the specification used to create the index (i.e. the first argument
	 * to MongoCollection::ensureIndex).
	 * </p>
	 * @return MongoCursor this cursor.
	 */
	public function hint ($index) {}

	/**
	 * Return an explanation of the query, often useful for optimization and debugging
	 * @link http://www.php.net/manual/en/mongocursor.explain.php
	 * @return array an explanation of the query.
	 */
	public function explain () {}

	/**
	 * Sets arbitrary flags in case there is no method available the specific flag
	 * @link http://www.php.net/manual/en/mongocursor.setflag.php
	 * @param flag int <p>
	 * Which flag to set. You can not set flag 6 (EXHAUST) as the driver does
	 * not know how to handle them. You will get a warning if you try to use
	 * this flag. For available flags, please refer to the wire protocol
	 * documentation.
	 * </p>
	 * @param set bool[optional] <p>
	 * Whether the flag should be set (true) or unset (false).
	 * </p>
	 * @return MongoCursor this cursor.
	 */
	public function setFlag ($flag, $set = null) {}

	/**
	 * Sets whether this query can be done on a secondary [deprecated]
	 * @link http://www.php.net/manual/en/mongocursor.slaveokay.php
	 * @param okay bool[optional] <p>
	 * If it is okay to query the secondary.
	 * </p>
	 * @return MongoCursor this cursor.
	 */
	public function slaveOkay ($okay = null) {}

	/**
	 * Sets whether this cursor will be left open after fetching the last results
	 * @link http://www.php.net/manual/en/mongocursor.tailable.php
	 * @param tail bool[optional] <p>
	 * If the cursor should be tailable.
	 * </p>
	 * @return MongoCursor this cursor.
	 */
	public function tailable ($tail = null) {}

	/**
	 * Sets whether this cursor will timeout
	 * @link http://www.php.net/manual/en/mongocursor.immortal.php
	 * @param liveForever bool[optional] <p>
	 * If the cursor should be immortal.
	 * </p>
	 * @return MongoCursor this cursor.
	 */
	public function immortal ($liveForever = null) {}

	/**
	 * Sets whether this cursor will wait for a while for a tailable cursor to return more data
	 * @link http://www.php.net/manual/en/mongocursor.awaitdata.php
	 * @param wait bool[optional] <p>
	 * If the cursor should wait for more data to become available.
	 * </p>
	 * @return MongoCursor this cursor.
	 */
	public function awaitData ($wait = null) {}

	/**
	 * If this query should fetch partial results from <emphasis>mongos</emphasis> if a shard is down
	 * @link http://www.php.net/manual/en/mongocursor.partial.php
	 * @param okay bool[optional] <p>
	 * If receiving partial results is okay.
	 * </p>
	 * @return MongoCursor this cursor.
	 */
	public function partial ($okay = null) {}

	/**
	 * Get the read preference for this query
	 * @link http://www.php.net/manual/en/mongocursor.getreadpreference.php
	 * @return array
	 */
	public function getReadPreference () {}

	/**
	 * Set the read preference for this query
	 * @link http://www.php.net/manual/en/mongocursor.setreadpreference.php
	 * @param read_preference string
	 * @param tags array[optional]
	 * @return MongoCursor this cursor.
	 */
	public function setReadPreference ($read_preference, array $tags = null) {}

	/**
	 * Sets a client-side timeout for this query
	 * @link http://www.php.net/manual/en/mongocursor.timeout.php
	 * @param ms int <p>
	 * The number of milliseconds for the cursor to wait for a response. Use
	 * -1 to wait forever. By default, the cursor will wait
	 * MongoCursor::$timeout milliseconds.
	 * </p>
	 * @return MongoCursor This cursor.
	 */
	public function timeout ($ms) {}

	/**
	 * Execute the query.
	 * @link http://www.php.net/manual/en/mongocursor.doquery.php
	 * @return void &null;.
	 */
	protected function doQuery () {}

	/**
	 * Gets the query, fields, limit, and skip for this cursor
	 * @link http://www.php.net/manual/en/mongocursor.info.php
	 * @return array the namespace, limit, skip, query, and fields for this cursor.
	 */
	public function info () {}

	/**
	 * Checks if there are documents that have not been sent yet from the database for this cursor
	 * @link http://www.php.net/manual/en/mongocursor.dead.php
	 * @return bool if there are more results that have not been sent to the client, yet.
	 */
	public function dead () {}

	/**
	 * Returns the current element
	 * @link http://www.php.net/manual/en/mongocursor.current.php
	 * @return array The current result as an associative array.
	 */
	public function current () {}

	/**
	 * Returns the current result&apos;s _id
	 * @link http://www.php.net/manual/en/mongocursor.key.php
	 * @return string The current result&apos;s _id as a string.
	 */
	public function key () {}

	/**
	 * Advances the cursor to the next result
	 * @link http://www.php.net/manual/en/mongocursor.next.php
	 * @return void &null;.
	 */
	public function next () {}

	/**
	 * Returns the cursor to the beginning of the result set
	 * @link http://www.php.net/manual/en/mongocursor.rewind.php
	 * @return void &null;.
	 */
	public function rewind () {}

	/**
	 * Checks if the cursor is reading a valid result.
	 * @link http://www.php.net/manual/en/mongocursor.valid.php
	 * @return bool If the current result is not null.
	 */
	public function valid () {}

	/**
	 * Clears the cursor
	 * @link http://www.php.net/manual/en/mongocursor.reset.php
	 * @return void &null;.
	 */
	public function reset () {}

	/**
	 * Counts the number of results for this query
	 * @link http://www.php.net/manual/en/mongocursor.count.php
	 * @param foundOnly bool[optional] <p>
	 * Send cursor limit and skip information to the count function, if applicable.
	 * </p>
	 * @return int The number of documents returned by this cursor's query.
	 */
	public function count ($foundOnly = null) {}

}

/** @jms-builtin */
class MongoCommandCursor implements MongoCursorInterface, Traversable, Iterator {

	/**
	 * Create a new command cursor
	 * @link http://www.php.net/manual/en/mongocommandcursor.construct.php
	 * @param connection MongoClient
	 * @param database_and_collection_name
	 * @param query[optional]
	 * @param array_of_fields_OR_object[optional]
	 */
	public function __construct (MongoClient $connection, $database_and_collection_name, $query, $array_of_fields_OR_object) {}

	/**
	 * Limits the number of elements returned in one batch.
	 * @link http://www.php.net/manual/en/mongocommandcursor.batchsize.php
	 * @param batchSize int <p>
	 * The number of results to return per batch. Each batch requires a
	 * round-trip to the server.
	 * </p>
	 * <p>
	 * This cannot override MongoDB's limit on the amount of data it will return to
	 * the client (i.e., if you set batch size to 1,000,000,000, MongoDB will still
	 * only return 4-16MB of results per batch).
	 * </p>
	 * @return MongoCommandCursor this cursor.
	 */
	public function batchSize ($batchSize) {}

	/**
	 * Gets the query, fields, limit, and skip for this cursor
	 * @link http://www.php.net/manual/en/mongocommandcursor.info.php
	 * @return array the namespace, limit, skip, query, fields, connection and iteration
	 * information for this cursor.
	 */
	public function info () {}

	/**
	 * Checks if there are documents that have not been sent yet from the database for this cursor
	 * @link http://www.php.net/manual/en/mongocommandcursor.dead.php
	 * @return bool if there are more results that have not been sent to the client, yet.
	 */
	public function dead () {}

	/**
	 * Returns the current element
	 * @link http://www.php.net/manual/en/mongocommandcursor.current.php
	 * @return array The current result as an associative array.
	 */
	public function current () {}

	/**
	 * Returns the current result&apos;s _id
	 * @link http://www.php.net/manual/en/mongocommandcursor.key.php
	 * @return string The current result&apos;s _id as a string.
	 */
	public function key () {}

	/**
	 * Advances the cursor to the next result
	 * @link http://www.php.net/manual/en/mongocommandcursor.next.php
	 * @return void &null;.
	 */
	public function next () {}

	/**
	 * Executes the command and resets the cursor to the start of the result set
	 * @link http://www.php.net/manual/en/mongocommandcursor.rewind.php
	 * @return array The raw server result document.
	 */
	public function rewind () {}

	/**
	 * Fetches a new result item, and returns whether it could
	 * @link http://www.php.net/manual/en/mongocommandcursor.valid.php
	 * @return bool true if a next item could be returned, and false otherwise.
	 */
	public function valid () {}

	public function reset () {}

}

/** @jms-builtin */
class MongoGridFS extends MongoCollection  {
	const ASCENDING = 1;
	const DESCENDING = -1;

	public $w;
	public $wtimeout;
	public $chunks;
	protected $filesName;
	protected $chunksName;


	/**
	 * Creates new file collections
	 * @link http://www.php.net/manual/en/mongogridfs.construct.php
	 */
	public function __construct () {}

	/**
	 * Drops the files and chunks collections
	 * @link http://www.php.net/manual/en/mongogridfs.drop.php
	 * @return array The database response.
	 */
	public function drop () {}

	/**
	 * Queries for files
	 * @link http://www.php.net/manual/en/mongogridfs.find.php
	 * @param query array[optional] <p>
	 * The query.
	 * </p>
	 * @param fields array[optional] <p>
	 * Fields to return.
	 * </p>
	 * @return MongoGridFSCursor A MongoGridFSCursor.
	 */
	public function find (array $query = null, array $fields = null) {}

	/**
	 * Stores a file in the database
	 * @link http://www.php.net/manual/en/mongogridfs.storefile.php
	 * @param filename string <p>
	 * Name of the file to store.
	 * </p>
	 * @param metadata array[optional] <p>
	 * Other metadata fields to include in the file document.
	 * </p>
	 * &mongo.gridfs.store.metadata.note;
	 * @param options array[optional] <p>
	 * Options for the store.
	 * &mongo.writes.parameters.writeconcern;
	 * </p>
	 * @return mixed
	 */
	public function storeFile ($filename, array $metadata = null, array $options = null) {}

	/**
	 * Stores a string of bytes in the database
	 * @link http://www.php.net/manual/en/mongogridfs.storebytes.php
	 * @param bytes string <p>
	 * String of bytes to store.
	 * </p>
	 * @param metadata array[optional] <p>
	 * Other metadata fields to include in the file document.
	 * </p>
	 * &mongo.gridfs.store.metadata.note;
	 * @param options array[optional] <p>
	 * Options for the store.
	 * &mongo.writes.parameters.writeconcern;
	 * </p>
	 * @return mixed
	 */
	public function storeBytes ($bytes, array $metadata = null, array $options = null) {}

	/**
	 * Returns a single file matching the criteria
	 * @link http://www.php.net/manual/en/mongogridfs.findone.php
	 * @param query mixed[optional] <p>
	 * The filename or criteria for which to search.
	 * </p>
	 * @param fields mixed[optional]
	 * @return MongoGridFSFile a MongoGridFSFile or &null;.
	 */
	public function findOne ($query = null, $fields = null) {}

	/**
	 * Removes files from the collections
	 * @link http://www.php.net/manual/en/mongogridfs.remove.php
	 * @param criteria array[optional]
	 * @param options array[optional] <p>
	 * Options for the remove. Valid options are:
	 * </p>
	 * &mongo.writes.parameters.writeconcern;
	 * @return bool if the removal was successfully sent to the database.
	 */
	public function remove (array $criteria = null, array $options = null) {}

	/**
	 * Stores an uploaded file in the database
	 * @link http://www.php.net/manual/en/mongogridfs.storeupload.php
	 * @param name string <p>
	 * The name of the uploaded file to store. This should correspond to the
	 * file field's name attribute in the HTML form.
	 * </p>
	 * @param metadata array[optional] <p>
	 * Other metadata fields to include in the file document.
	 * </p>
	 * &mongo.gridfs.store.metadata.note;
	 * <p>
	 * The filename index will be populated with the
	 * filename used.
	 * </p>
	 * @return mixed
	 */
	public function storeUpload ($name, array $metadata = null) {}

	/**
	 * Delete a file from the database
	 * @link http://www.php.net/manual/en/mongogridfs.delete.php
	 * @param id mixed <p>
	 * _id of the file to remove.
	 * </p>
	 * @return bool if the remove was successfully sent to the database.
	 */
	public function delete ($id) {}

	/**
	 * Retrieve a file from the database
	 * @link http://www.php.net/manual/en/mongogridfs.get.php
	 * @param id mixed <p>
	 * _id of the file to find.
	 * </p>
	 * @return MongoGridFSFile the file, if found, or &null;.
	 */
	public function get ($id) {}

	/**
	 * Stores a file in the database
	 * @link http://www.php.net/manual/en/mongogridfs.put.php
	 * @param filename string <p>
	 * Name of the file to store.
	 * </p>
	 * @param metadata array[optional] <p>
	 * Other metadata fields to include in the file document.
	 * </p>
	 * &mongo.gridfs.store.metadata.note;
	 * @return mixed
	 */
	public function put ($filename, array $metadata = null) {}

	/**
	 * String representation of this collection
	 * @link http://www.php.net/manual/en/mongocollection.--tostring.php
	 * @return string the full name of this collection.
	 */
	public function __toString () {}

	/**
	 * Gets a collection
	 * @link http://www.php.net/manual/en/mongocollection.get.php
	 * @param name string <p>
	 * The next string in the collection name.
	 * </p>
	 * @return MongoCollection the collection.
	 */
	public function __get ($name) {}

	/**
	 * Returns this collection&apos;s name
	 * @link http://www.php.net/manual/en/mongocollection.getname.php
	 * @return string the name of this collection.
	 */
	public function getName () {}

	/**
	 * Get slaveOkay setting for this collection
	 * @link http://www.php.net/manual/en/mongocollection.getslaveokay.php
	 * @return bool the value of slaveOkay for this instance.
	 */
	public function getSlaveOkay () {}

	/**
	 * Change slaveOkay setting for this collection
	 * @link http://www.php.net/manual/en/mongocollection.setslaveokay.php
	 * @param ok bool[optional] <p>
	 * If reads should be sent to secondary members of a replica set for all
	 * possible queries using this MongoCollection
	 * instance.
	 * </p>
	 * @return bool the former value of slaveOkay for this instance.
	 */
	public function setSlaveOkay ($ok = null) {}

	/**
	 * Get the read preference for this collection
	 * @link http://www.php.net/manual/en/mongocollection.getreadpreference.php
	 * @return array
	 */
	public function getReadPreference () {}

	/**
	 * Set the read preference for this collection
	 * @link http://www.php.net/manual/en/mongocollection.setreadpreference.php
	 * @param read_preference string
	 * @param tags array[optional]
	 * @return bool
	 */
	public function setReadPreference ($read_preference, array $tags = null) {}

	/**
	 * Get the write concern for this collection
	 * @link http://www.php.net/manual/en/mongocollection.getwriteconcern.php
	 * @return array
	 */
	public function getWriteConcern () {}

	/**
	 * Set the write concern for this database
	 * @link http://www.php.net/manual/en/mongocollection.setwriteconcern.php
	 * @param w mixed
	 * @param wtimeout int[optional]
	 * @return bool
	 */
	public function setWriteConcern ($w, $wtimeout = null) {}

	/**
	 * Validates this collection
	 * @link http://www.php.net/manual/en/mongocollection.validate.php
	 * @param scan_data bool[optional] <p>
	 * Only validate indices, not the base collection.
	 * </p>
	 * @return array the database&apos;s evaluation of this object.
	 */
	public function validate ($scan_data = null) {}

	/**
	 * Inserts a document into the collection
	 * @link http://www.php.net/manual/en/mongocollection.insert.php
	 * @param a array|object <p>
	 * An array or object. If an object is used, it may not have protected or
	 * private properties.
	 * </p>
	 * <p>
	 * If the parameter does not have an _id key or
	 * property, a new MongoId instance will be created
	 * and assigned to it. This special behavior does not mean that the
	 * parameter is passed by reference.
	 * </p>
	 * @param options array[optional] <p>
	 * Options for the insert.
	 * &mongo.writes.parameters.fsync;
	 * &mongo.writes.parameters.journal;
	 * &mongo.writes.parameters.sockettimeoutms;
	 * &mongo.writes.parameters.writeconcern;
	 * &mongo.writes.parameters.writeconcerntimeout;
	 * &mongo.writes.parameters.writeconcerntimeoutms;
	 * &mongo.writes.parameters.safe;
	 * &mongo.writes.parameters.timeout;
	 * </p>
	 * @return bool|array an array containing the status of the insertion if the
	 * "w" option is set. Otherwise, returns true if the
	 * inserted array is not empty (a MongoException will be
	 * thrown if the inserted array is empty).
	 * </p>
	 * <p>
	 * If an array is returned, the following keys may be present:
	 * ok
	 * <p>
	 * This should almost always be 1 (unless last_error itself failed).
	 * </p>
	 * err
	 * <p>
	 * If this field is non-null, an error occurred on the previous operation.
	 * If this field is set, it will be a string describing the error that
	 * occurred.
	 * </p>
	 * code
	 * <p>
	 * If a database error occurred, the relevant error code will be passed
	 * back to the client.
	 * </p>
	 * errmsg
	 * <p>
	 * This field is set if something goes wrong with a database command. It
	 * is coupled with ok being 0. For example, if
	 * w is set and times out, errmsg will be set to "timed
	 * out waiting for slaves" and ok will be 0. If this
	 * field is set, it will be a string describing the error that occurred.
	 * </p>
	 * n
	 * <p>
	 * If the last operation was an update, upsert, or a remove, the number
	 * of documents affected will be returned. For insert operations, this value
	 * is always 0.
	 * </p>
	 * wtimeout
	 * <p>
	 * If the previous option timed out waiting for replication.
	 * </p>
	 * waited
	 * <p>
	 * How long the operation waited before timing out.
	 * </p>
	 * wtime
	 * <p>
	 * If w was set and the operation succeeded, how long it took to
	 * replicate to w servers.
	 * </p>
	 * upserted
	 * <p>
	 * If an upsert occurred, this field will contain the new record's
	 * _id field. For upserts, either this field or
	 * updatedExisting will be present (unless an error
	 * occurred).
	 * </p>
	 * updatedExisting
	 * <p>
	 * If an upsert updated an existing element, this field will be true. For
	 * upserts, either this field or upserted will be present (unless an error
	 * occurred).
	 * </p>
	 */
	public function insert ($a, array $options = null) {}

	/**
	 * Inserts multiple documents into this collection
	 * @link http://www.php.net/manual/en/mongocollection.batchinsert.php
	 * @param a array <p>
	 * An array of arrays or objects. If any objects are used, they may not have
	 * protected or private properties.
	 * </p>
	 * <p>
	 * If the documents to insert do not have an _id key or
	 * property, a new MongoId instance will be created
	 * and assigned to it. See MongoCollection::insert for
	 * additional information on this behavior.
	 * </p>
	 * @param options array[optional] <p>
	 * Options for the inserts.
	 * <p>
	 * "continueOnError"
	 * </p>
	 * <p>
	 * Boolean, defaults to false. If set, the database will not stop
	 * processing a bulk insert if one fails (eg due to duplicate IDs).
	 * This makes bulk insert behave similarly to a series of single
	 * inserts, except that calling MongoDB::lastError
	 * will have an error set if any insert fails, not just the last one.
	 * If multiple errors occur, only the most recent will be reported by
	 * MongoDB::lastError.
	 * </p>
	 * <p>
	 * Please note that continueOnError affects errors
	 * on the database side only. If you try to insert a document that has
	 * errors (for example it contains a key with an empty name), then the
	 * document is not even transferred to the database as the driver
	 * detects this error and bails out.
	 * continueOnError has no effect on errors detected
	 * in the documents by the driver.
	 * </p>
	 * @return mixed If the w parameter is set to acknowledge the write,
	 * returns an associative array with the status of the inserts ("ok") and any
	 * error that may have occurred ("err"). Otherwise, returns true if the
	 * batch insert was successfully sent, false otherwise.
	 */
	public function batchInsert (array $a, array $options = null) {}

	/**
	 * Update records based on a given criteria
	 * @link http://www.php.net/manual/en/mongocollection.update.php
	 * @param criteria array <p>
	 * Description of the objects to update.
	 * </p>
	 * @param new_object array <p>
	 * The object with which to update the matching records.
	 * </p>
	 * @param options array[optional] <p>
	 * This parameter is an associative array of the form
	 * array("optionname" => &lt;boolean&gt;, ...). Currently
	 * supported options are:
	 * <p>
	 * "upsert"
	 * </p>
	 * <p>
	 * If no document matches $criteria, a new
	 * document will be inserted.
	 * </p>
	 * <p>
	 * If a new document would be inserted and
	 * $new_object contains atomic modifiers
	 * (i.e. $ operators), those operations will be
	 * applied to the $criteria parameter to create
	 * the new document. If $new_object does not
	 * contain atomic modifiers, it will be used as-is for the inserted
	 * document. See the upsert examples below for more information.
	 * </p>
	 * @return bool|array an array containing the status of the update if the
	 * "w" option is set. Otherwise, returns true.
	 * </p>
	 * <p>
	 * Fields in the status array are described in the documentation for
	 * MongoCollection::insert.
	 */
	public function update (array $criteria, array $new_object, array $options = null) {}

	/**
	 * Update a document and return it
	 * @link http://www.php.net/manual/en/mongocollection.findandmodify.php
	 * @param query array <p>
	 * The query criteria to search for.
	 * </p>
	 * @param update array[optional] <p>
	 * The update criteria.
	 * </p>
	 * @param fields array[optional] <p>
	 * Optionally only return these fields.
	 * </p>
	 * @param options array[optional] <p>
	 * An array of options to apply, such as remove the match document from the
	 * DB and return it.
	 * <tr valign="top">
	 * <td>Option</td>
	 * <td>&Description;</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>sort array</td>
	 * <td>
	 * Determines which document the operation will modify if the
	 * query selects multiple documents. findAndModify will modify the
	 * first document in the sort order specified by this argument.
	 * </td>
	 * </tr>
	 * <tr valign="top">
	 * <td>remove boolean</td>
	 * <td>
	 * Optional if update field exists. When true, removes the selected
	 * document. The default is false.
	 * </td>
	 * </tr>
	 * <tr valign="top">
	 * <td>update array</td>
	 * <td>
	 * Optional if remove field exists.
	 * Performs an update of the selected document.
	 * </td>
	 * </tr>
	 * <tr valign="top">
	 * <td>new boolean</td>
	 * <td>
	 * Optional. When true, returns the modified document rather than the
	 * original. The findAndModify method ignores the new option for
	 * remove operations. The default is false.
	 * </td>
	 * </tr>
	 * <tr valign="top">
	 * <td>upsert boolean</td>
	 * <td>
	 * Optional. Used in conjunction with the update field. When true, the
	 * findAndModify command creates a new document if the query returns
	 * no documents. The default is false. In MongoDB 2.2, the
	 * findAndModify command returns &null; when upsert is true.
	 * </td>
	 * </tr>
	 * <tr valign="top">
	 * <td></td>
	 * <td>
	 * </td>
	 * </tr>
	 * </p>
	 * @return array the original document, or the modified document when
	 * new is set.
	 */
	public function findAndModify (array $query, array $update = null, array $fields = null, array $options = null) {}

	/**
	 * Execute a database command and retrieve results through a cursor
	 * @link http://www.php.net/manual/en/mongocollection.commandcursor.php
	 * @param command array <p>
	 * The command query to send.
	 * </p>
	 * <p>
	 * It is possible to configure how many initial documents the server
	 * should return with the first result set. This configuration is made as
	 * part of the command query. The default initial batch size is
	 * 101. You can change it by adding the
	 * cursor key in the following way to the command:
	 * </p>
	 * commandCursor( [
	 * "aggregate" => "collectionName",
	 * "pipeline" => [
	 * ...
	 * ],
	 * "cursor" => [ "batchSize" => 4 ],
	 * ] );
	 * ]]>
	 * <p>
	 * If you do not set the initial
	 * batchSize option, then the PHP driver will implicitly
	 * add this for you with a value of 101
	 * </p>
	 * <p>
	 * This setting does only configure the first batch size. To configure the
	 * size of future batches, please use the
	 * MongoCommandCursor::batchSize method on the
	 * returned MongoCommandCursor object.
	 * </p>
	 * @return MongoCommandCursor a MongoCommandCursor object. Because this
	 * implements the Iterator interface you can
	 * iterate over each of the results as returned by the command query. The
	 * MongoCommandCursor also implements the
	 * MongoCursorInterface interface which adds the
	 * MongoCommandCursor::batchSize,
	 * MongoCommandCursor::dead,
	 * MongoCommandCursor::info methods.
	 */
	public function commandCursor (array $command) {}

	/**
	 * Creates an index on the given field(s)
	 * @link http://www.php.net/manual/en/mongocollection.createindex.php
	 * @param keys array <p>
	 * An array of fields by which to sort the index on. Each element in the
	 * array has as key the field name, and as value either
	 * 1 for ascending sort, -1 for
	 * descending sort, or any of the index plugins (currently,
	 * "text", "2d", or
	 * "2dsphere"").
	 * </p>
	 * @param options array[optional] <p>
	 * This parameter is an associative array of the form
	 * array("optionname" => &lt;boolean&gt;, ...). Currently
	 * supported options are:
	 * &mongo.writes.parameters.writeconcern;
	 * <p>
	 * "unique"
	 * </p>
	 * <p>
	 * Create a unique index.
	 * </p>
	 * <p>
	 * A unique index cannot be created on a field if multiple existing
	 * documents do not contain the field. The field is effectively &null;
	 * for these documents and thus already non-unique. Sparse indexing may
	 * be used to overcome this, since it will prevent documents without the
	 * field from being indexed.
	 * </p>
	 * @return bool an array containing the status of the index creation. The array
	 * contains whether the operation worked ("ok"), the amount
	 * of indexes before and after the operation
	 * ("numIndexesBefore" and
	 * "numIndexesAfter") and whether the collection that the
	 * index belongs to has been created
	 * ("createdCollectionAutomatically").
	 * </p>
	 * <p>
	 * With MongoDB 2.4 and earlier, a status document is only returned if the
	 * "w" option is set to 1—either through
	 * the connection string, or with . If
	 * "w" is not set to 1, it returns
	 * true. The fields in the status document are different, except for the
	 * "ok" field which signals whether the index creation
	 * worked.
	 */
	public function createIndex (array $keys, array $options = null) {}

	/**
	 * Creates an index on the given field(s), or does nothing if the index
   already exists
	 * @link http://www.php.net/manual/en/mongocollection.ensureindex.php
	 * @param key_keys string|array
	 * @param options array[optional] <p>
	 * This parameter is an associative array of the form
	 * array("optionname" => &lt;boolean&gt;, ...). Currently
	 * supported options are:
	 * &mongo.writes.parameters.writeconcern;
	 * <p>
	 * "unique"
	 * </p>
	 * <p>
	 * Create a unique index.
	 * </p>
	 * <p>
	 * A unique index cannot be created on a field if multiple existing
	 * documents do not contain the field. The field is effectively &null;
	 * for these documents and thus already non-unique. Sparse indexing may
	 * be used to overcome this, since it will prevent documents without the
	 * field from being indexed.
	 * </p>
	 * @return bool an array containing the status of the index creation if the
	 * "w" option is set. Otherwise, returns true.
	 * </p>
	 * <p>
	 * Fields in the status array are described in the documentation for
	 * MongoCollection::insert.
	 */
	public function ensureIndex ($key_keys, array $options = null) {}

	/**
	 * Deletes an index from this collection
	 * @link http://www.php.net/manual/en/mongocollection.deleteindex.php
	 * @param keys string|array <p>
	 * Field or fields from which to delete the index.
	 * </p>
	 * @return array the database response.
	 */
	public function deleteIndex ($keys) {}

	/**
	 * Delete all indices for this collection
	 * @link http://www.php.net/manual/en/mongocollection.deleteindexes.php
	 * @return array the database response.
	 */
	public function deleteIndexes () {}

	/**
	 * Returns information about indexes on this collection
	 * @link http://www.php.net/manual/en/mongocollection.getindexinfo.php
	 * @return array This function returns an array in which each element describes an index.
	 * Elements will contain the values name for the name of
	 * the index, ns for the namespace (a combination of the
	 * database and collection name), and key for a list of all
	 * fields in the index and their ordering. Additional values may be present for
	 * special indexes, such as unique or
	 * sparse.
	 */
	public function getIndexInfo () {}

	/**
	 * Counts the number of documents in this collection
	 * @link http://www.php.net/manual/en/mongocollection.count.php
	 * @param query array[optional] <p>
	 * Associative array or object with fields to match.
	 * </p>
	 * @param limit int[optional] <p>
	 * Specifies an upper limit to the number returned.
	 * </p>
	 * @param skip int[optional] <p>
	 * Specifies a number of results to skip before starting the count.
	 * </p>
	 * @return int the number of documents matching the query.
	 */
	public function count (array $query = null, $limit = null, $skip = null) {}

	/**
	 * Saves a document to this collection
	 * @link http://www.php.net/manual/en/mongocollection.save.php
	 * @param a array|object <p>
	 * Array or object to save. If an object is used, it may not have protected
	 * or private properties.
	 * </p>
	 * <p>
	 * If the parameter does not have an _id key or
	 * property, a new MongoId instance will be created
	 * and assigned to it. See MongoCollection::insert for
	 * additional information on this behavior.
	 * </p>
	 * @param options array[optional] <p>
	 * Options for the save.
	 * &mongo.writes.parameters.fsync;
	 * &mongo.writes.parameters.journal;
	 * &mongo.writes.parameters.sockettimeoutms;
	 * &mongo.writes.parameters.writeconcern;
	 * &mongo.writes.parameters.writeconcerntimeout;
	 * &mongo.writes.parameters.writeconcerntimeoutms;
	 * &mongo.writes.parameters.safe;
	 * &mongo.writes.parameters.timeout;
	 * </p>
	 * @return mixed If w was set, returns an array containing the status of the save.
	 * Otherwise, returns a boolean representing if the array was not empty (an empty array will not
	 * be inserted).
	 */
	public function save ($a, array $options = null) {}

	/**
	 * Creates a database reference
	 * @link http://www.php.net/manual/en/mongocollection.createdbref.php
	 * @param document_or_id mixed <p>
	 * If an array or object is given, its _id field will be
	 * used as the reference ID. If a MongoId or scalar
	 * is given, it will be used as the reference ID.
	 * </p>
	 * @return array a database reference array.
	 * </p>
	 * <p>
	 * If an array without an _id field was provided as the
	 * document_or_id parameter, &null; will be returned.
	 */
	public function createDBRef ($document_or_id) {}

	/**
	 * Fetches the document pointed to by a database reference
	 * @link http://www.php.net/manual/en/mongocollection.getdbref.php
	 * @param ref array <p>
	 * A database reference.
	 * </p>
	 * @return array the database document pointed to by the reference.
	 */
	public function getDBRef (array $ref) {}

	/**
	 * Converts keys specifying an index to its identifying string
	 * @link http://www.php.net/manual/en/mongocollection.toindexstring.php
	 * @param keys mixed <p>
	 * Field or fields to convert to the identifying string
	 * </p>
	 * @return string a string that describes the index.
	 */
	protected static function toIndexString ($keys) {}

	/**
	 * Performs an operation similar to SQL's GROUP BY command
	 * @link http://www.php.net/manual/en/mongocollection.group.php
	 * @param keys mixed <p>
	 * Fields to group by. If an array or non-code object is passed, it will be
	 * the key used to group results.
	 * </p>
	 * <p>1.0.4+: If keys is an instance of
	 * MongoCode, keys will be treated as
	 * a function that returns the key to group by (see the "Passing a
	 * keys function" example below).
	 * </p>
	 * @param initial array <p>
	 * Initial value of the aggregation counter object.
	 * </p>
	 * @param reduce MongoCode <p>
	 * A function that takes two arguments (the current document and the
	 * aggregation to this point) and does the aggregation.
	 * </p>
	 * @param options array[optional] <p>
	 * Optional parameters to the group command. Valid options include:
	 * </p>
	 * <p>
	 * "condition"
	 * </p>
	 * <p>
	 * Criteria for including a document in the aggregation.
	 * </p>
	 * @return array an array containing the result.
	 */
	public function group ($keys, array $initial, MongoCode $reduce, array $options = null) {}

	/**
	 * Retrieve a list of distinct values for the given key across a collection.
	 * @link http://www.php.net/manual/en/mongocollection.distinct.php
	 * @param key string <p>
	 * The key to use.
	 * </p>
	 * @param query array[optional] <p>
	 * An optional query parameters
	 * </p>
	 * @return array an array of distinct values, &return.falseforfailure;
	 */
	public function distinct ($key, array $query = null) {}

	/**
	 * Perform an aggregation using the aggregation framework
	 * @link http://www.php.net/manual/en/mongocollection.aggregate.php
	 * @param pipeline array <p>
	 * An array of pipeline operators.
	 * </p>
	 * @param options array[optional] <p>
	 * Array of command arguments, such as allowDiskUse, explain or cursor.
	 * </p>
	 * @return array The result of the aggregation as an array. The ok will
	 * be set to 1 on success, 0 on failure.
	 */
	public function aggregate (array $pipeline, array $options = null) {}

}

/** @jms-builtin */
class MongoGridFSFile  {
	public $file;
	protected $gridfs;


	/**
	 * Create a new GridFS file
	 * @link http://www.php.net/manual/en/mongogridfsfile.construct.php
	 */
	public function __construct () {}

	/**
	 * Returns this file&apos;s filename
	 * @link http://www.php.net/manual/en/mongogridfsfile.getfilename.php
	 * @return string the filename.
	 */
	public function getFilename () {}

	/**
	 * Returns this file&apos;s size
	 * @link http://www.php.net/manual/en/mongogridfsfile.getsize.php
	 * @return int this file's size
	 */
	public function getSize () {}

	/**
	 * Writes this file to the filesystem
	 * @link http://www.php.net/manual/en/mongogridfsfile.write.php
	 * @param filename string[optional] <p>
	 * The location to which to write the file. If none is given,
	 * the stored filename will be used.
	 * </p>
	 * @return int the number of bytes written.
	 */
	public function write ($filename = null) {}

	/**
	 * Returns this file&apos;s contents as a string of bytes
	 * @link http://www.php.net/manual/en/mongogridfsfile.getbytes.php
	 * @return string a string of the bytes in the file.
	 */
	public function getBytes () {}

	/**
	 * Returns a resource that can be used to read the stored file
	 * @link http://www.php.net/manual/en/mongogridfsfile.getresource.php
	 * @return stream a resource that can be used to read the file with
	 */
	public function getResource () {}

}

/** @jms-builtin */
class MongoGridFSCursor extends MongoCursor implements Iterator, Traversable, MongoCursorInterface {
	public static $slaveOkay;
	public static $timeout;
	protected $gridfs;


	/**
	 * Create a new cursor
	 * @link http://www.php.net/manual/en/mongogridfscursor.construct.php
	 */
	public function __construct () {}

	/**
	 * Return the next file to which this cursor points, and advance the cursor
	 * @link http://www.php.net/manual/en/mongogridfscursor.getnext.php
	 * @return MongoGridFSFile the next file.
	 */
	public function getNext () {}

	/**
	 * Returns the current file
	 * @link http://www.php.net/manual/en/mongogridfscursor.current.php
	 * @return MongoGridFSFile The current file.
	 */
	public function current () {}

	/**
	 * Checks if there are any more elements in this cursor
	 * @link http://www.php.net/manual/en/mongocursor.hasnext.php
	 * @return bool if there is another element.
	 */
	public function hasNext () {}

	/**
	 * Limits the number of results returned
	 * @link http://www.php.net/manual/en/mongocursor.limit.php
	 * @param num int <p>
	 * The number of results to return.
	 * </p>
	 * @return MongoCursor this cursor.
	 */
	public function limit ($num) {}

	/**
	 * Limits the number of elements returned in one batch.
	 * @link http://www.php.net/manual/en/mongocursor.batchsize.php
	 * @param batchSize int <p>
	 * The number of results to return per batch. Each batch requires a
	 * round-trip to the server.
	 * </p>
	 * <p>
	 * If batchSize is 2 or
	 * more, it represents the size of each batch of objects retrieved.
	 * It can be adjusted to optimize performance and limit data transfer.
	 * </p>
	 * <p>
	 * If batchSize is 1 or negative, it
	 * will limit of number returned documents to the absolute value of batchSize,
	 * and the cursor will be closed. For example if
	 * batchSize is -10, then the server will return a maximum
	 * of 10 documents and as many as can fit in 4MB, then close the cursor.
	 * </p>
	 * <p>
	 * A batchSize of 1 is special, and
	 * means the same as -1, i.e. a value of
	 * 1 makes the cursor only capable of returning
	 * one document.
	 * </p>
	 * <p>
	 * Note that this feature is different from
	 * MongoCursor::limit in that documents must fit within a
	 * maximum size, and it removes the need to send a request to close the cursor
	 * server-side. The batch size can be changed even after a cursor is iterated,
	 * in which case the setting will apply on the next batch retrieval.
	 * </p>
	 * <p>
	 * This cannot override MongoDB's limit on the amount of data it will return to
	 * the client (i.e., if you set batch size to 1,000,000,000, MongoDB will still
	 * only return 4-16MB of results per batch).
	 * </p>
	 * <p>
	 * To ensure consistent behavior, the rules of
	 * MongoCursor::batchSize and
	 * MongoCursor::limit behave a
	 * little complex but work "as expected". The rules are: hard limits override
	 * soft limits with preference given to MongoCursor::limit
	 * over MongoCursor::batchSize. After that, whichever is
	 * set and lower than the other will take precedence. See below.
	 * section for some examples.
	 * </p>
	 * @return MongoCursor this cursor.
	 */
	public function batchSize ($batchSize) {}

	/**
	 * Skips a number of results
	 * @link http://www.php.net/manual/en/mongocursor.skip.php
	 * @param num int <p>
	 * The number of results to skip.
	 * </p>
	 * @return MongoCursor this cursor.
	 */
	public function skip ($num) {}

	/**
	 * Sets the fields for a query
	 * @link http://www.php.net/manual/en/mongocursor.fields.php
	 * @param f array <p>
	 * Fields to return (or not return).
	 * </p>
	 * @return MongoCursor this cursor.
	 */
	public function fields (array $f) {}

	/**
	 * @param ms
	 */
	public function maxTimeMS ($ms) {}

	/**
	 * Adds a top-level key/value pair to a query
	 * @link http://www.php.net/manual/en/mongocursor.addoption.php
	 * @param key string <p>
	 * Fieldname to add.
	 * </p>
	 * @param value mixed <p>
	 * Value to add.
	 * </p>
	 * @return MongoCursor this cursor.
	 */
	public function addOption ($key, $value) {}

	/**
	 * Use snapshot mode for the query
	 * @link http://www.php.net/manual/en/mongocursor.snapshot.php
	 * @return MongoCursor this cursor.
	 */
	public function snapshot () {}

	/**
	 * Sorts the results by given fields
	 * @link http://www.php.net/manual/en/mongocursor.sort.php
	 * @param fields array <p>
	 * An array of fields by which to sort. Each element in the array has as
	 * key the field name, and as value either 1 for
	 * ascending sort, or -1 for descending sort.
	 * </p>
	 * <p>
	 * Each result is first sorted on the first field in the array, then (if
	 * it exists) on the second field in the array, etc. This means that the
	 * order of the fields in the fields array is
	 * important. See also the examples section.
	 * </p>
	 * @return MongoCursor the same cursor that this method was called on.
	 */
	public function sort (array $fields) {}

	/**
	 * Gives the database a hint about the query
	 * @link http://www.php.net/manual/en/mongocursor.hint.php
	 * @param index mixed <p>
	 * Index to use for the query. If a string is passed, it should correspond
	 * to an index name. If an array or object is passed, it should correspond
	 * to the specification used to create the index (i.e. the first argument
	 * to MongoCollection::ensureIndex).
	 * </p>
	 * @return MongoCursor this cursor.
	 */
	public function hint ($index) {}

	/**
	 * Return an explanation of the query, often useful for optimization and debugging
	 * @link http://www.php.net/manual/en/mongocursor.explain.php
	 * @return array an explanation of the query.
	 */
	public function explain () {}

	/**
	 * Sets arbitrary flags in case there is no method available the specific flag
	 * @link http://www.php.net/manual/en/mongocursor.setflag.php
	 * @param flag int <p>
	 * Which flag to set. You can not set flag 6 (EXHAUST) as the driver does
	 * not know how to handle them. You will get a warning if you try to use
	 * this flag. For available flags, please refer to the wire protocol
	 * documentation.
	 * </p>
	 * @param set bool[optional] <p>
	 * Whether the flag should be set (true) or unset (false).
	 * </p>
	 * @return MongoCursor this cursor.
	 */
	public function setFlag ($flag, $set = null) {}

	/**
	 * Sets whether this query can be done on a secondary [deprecated]
	 * @link http://www.php.net/manual/en/mongocursor.slaveokay.php
	 * @param okay bool[optional] <p>
	 * If it is okay to query the secondary.
	 * </p>
	 * @return MongoCursor this cursor.
	 */
	public function slaveOkay ($okay = null) {}

	/**
	 * Sets whether this cursor will be left open after fetching the last results
	 * @link http://www.php.net/manual/en/mongocursor.tailable.php
	 * @param tail bool[optional] <p>
	 * If the cursor should be tailable.
	 * </p>
	 * @return MongoCursor this cursor.
	 */
	public function tailable ($tail = null) {}

	/**
	 * Sets whether this cursor will timeout
	 * @link http://www.php.net/manual/en/mongocursor.immortal.php
	 * @param liveForever bool[optional] <p>
	 * If the cursor should be immortal.
	 * </p>
	 * @return MongoCursor this cursor.
	 */
	public function immortal ($liveForever = null) {}

	/**
	 * Sets whether this cursor will wait for a while for a tailable cursor to return more data
	 * @link http://www.php.net/manual/en/mongocursor.awaitdata.php
	 * @param wait bool[optional] <p>
	 * If the cursor should wait for more data to become available.
	 * </p>
	 * @return MongoCursor this cursor.
	 */
	public function awaitData ($wait = null) {}

	/**
	 * If this query should fetch partial results from <emphasis>mongos</emphasis> if a shard is down
	 * @link http://www.php.net/manual/en/mongocursor.partial.php
	 * @param okay bool[optional] <p>
	 * If receiving partial results is okay.
	 * </p>
	 * @return MongoCursor this cursor.
	 */
	public function partial ($okay = null) {}

	/**
	 * Get the read preference for this query
	 * @link http://www.php.net/manual/en/mongocursor.getreadpreference.php
	 * @return array
	 */
	public function getReadPreference () {}

	/**
	 * Set the read preference for this query
	 * @link http://www.php.net/manual/en/mongocursor.setreadpreference.php
	 * @param read_preference string
	 * @param tags array[optional]
	 * @return MongoCursor this cursor.
	 */
	public function setReadPreference ($read_preference, array $tags = null) {}

	/**
	 * Sets a client-side timeout for this query
	 * @link http://www.php.net/manual/en/mongocursor.timeout.php
	 * @param ms int <p>
	 * The number of milliseconds for the cursor to wait for a response. Use
	 * -1 to wait forever. By default, the cursor will wait
	 * MongoCursor::$timeout milliseconds.
	 * </p>
	 * @return MongoCursor This cursor.
	 */
	public function timeout ($ms) {}

	/**
	 * Execute the query.
	 * @link http://www.php.net/manual/en/mongocursor.doquery.php
	 * @return void &null;.
	 */
	protected function doQuery () {}

	/**
	 * Gets the query, fields, limit, and skip for this cursor
	 * @link http://www.php.net/manual/en/mongocursor.info.php
	 * @return array the namespace, limit, skip, query, and fields for this cursor.
	 */
	public function info () {}

	/**
	 * Checks if there are documents that have not been sent yet from the database for this cursor
	 * @link http://www.php.net/manual/en/mongocursor.dead.php
	 * @return bool if there are more results that have not been sent to the client, yet.
	 */
	public function dead () {}

	/**
	 * Returns the current result&apos;s _id
	 * @link http://www.php.net/manual/en/mongocursor.key.php
	 * @return string The current result&apos;s _id as a string.
	 */
	public function key () {}

	/**
	 * Advances the cursor to the next result
	 * @link http://www.php.net/manual/en/mongocursor.next.php
	 * @return void &null;.
	 */
	public function next () {}

	/**
	 * Returns the cursor to the beginning of the result set
	 * @link http://www.php.net/manual/en/mongocursor.rewind.php
	 * @return void &null;.
	 */
	public function rewind () {}

	/**
	 * Checks if the cursor is reading a valid result.
	 * @link http://www.php.net/manual/en/mongocursor.valid.php
	 * @return bool If the current result is not null.
	 */
	public function valid () {}

	/**
	 * Clears the cursor
	 * @link http://www.php.net/manual/en/mongocursor.reset.php
	 * @return void &null;.
	 */
	public function reset () {}

	/**
	 * Counts the number of results for this query
	 * @link http://www.php.net/manual/en/mongocursor.count.php
	 * @param foundOnly bool[optional] <p>
	 * Send cursor limit and skip information to the count function, if applicable.
	 * </p>
	 * @return int The number of documents returned by this cursor's query.
	 */
	public function count ($foundOnly = null) {}

}

/** @jms-builtin */
class MongoWriteBatch  {

	/**
	 * Description
	 * @link http://www.php.net/manual/en/mongowritebatch.construct.php
	 * @param collection MongoCollection
	 * @param batch_type[optional]
	 * @param write_options[optional]
	 */
	protected function __construct (MongoCollection $collection, $batch_typearray , $write_options) {}

	/**
	 * Adds an CRUD operation to a batch
	 * @link http://www.php.net/manual/en/mongowritebatch.add.php
	 * @param item array <p>
	 * <tr valign="top">
	 * <td>When current batch is</td>
	 * <td>Argument expectation</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>MongoWriteBatch::COMMAND_INSERT</td>
	 * <td>The document to add</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>MongoWriteBatch::COMMAND_UPDATE</td>
	 * <td>
	 * Raw update operation. Required keys are: array("q" => array("criteria"), "u" => array("new data"))
	 * Optionally with the "multi" and "upsert" keys as boolean values
	 * </td>
	 * </tr>
	 * <tr valign="top">
	 * <td>MongoWriteBatch::COMMAND_DELETE</td>
	 * <td>
	 * Raw delete operation. Required keys are: array("q" => array("criteria"), "limit" => 1)
	 * </td>
	 * </tr>
	 * </p>
	 * @return bool true on success, throws exception on failure.
	 */
	public function add (array $item) {}

	/**
	 * Description
	 * @link http://www.php.net/manual/en/mongowritebatch.execute.php
	 * @param write_options array <p>
	 * See MongoWriteBatch::__construct.
	 * </p>
	 * @return array an array containing statistical information for the full batch.
	 * If the batch had to be split into multiple batches, the return value will aggregate
	 * the values from individual batches and return only the totals.
	 * </p>
	 * <p>
	 * If the batch was empty, an array containing only the 'ok' field is returned (as true) although
	 * nothing will be shipped over the wire (NOOP).
	 * </p>
	 * <p>
	 * <tr valign="top">
	 * <td>Array key</td>
	 * <td>Value meaning</td>
	 * <td>Returned for batch type</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>nInserted</td>
	 * <td>Number of inserted documents</td>
	 * <td>MongoWriteBatch::COMMAND_INSERT batch</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>nMatched</td>
	 * <td>Number of documents matching the query criteria</td>
	 * <td>MongoWriteBatch::COMMAND_UPDATE batch</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>nModified</td>
	 * <td>Number of documents actually needed to be modied</td>
	 * <td>MongoWriteBatch::COMMAND_UPDATE batch</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>nUpserted</td>
	 * <td>Number of upserted documents</td>
	 * <td>MongoWriteBatch::COMMAND_UPDATE batch</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>nRemoved</td>
	 * <td>Number of documents removed</td>
	 * <td>MongoWriteBatch::COMMAND_DELETE batch</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>ok</td>
	 * <td>Command success indicator</td>
	 * <td>All</td>
	 * </tr>
	 */
	final public function execute (array $write_options) {}

}

/** @jms-builtin */
class MongoInsertBatch extends MongoWriteBatch  {

	/**
	 * Description
	 * @link http://www.php.net/manual/en/mongoinsertbatch.construct.php
	 * @param collection MongoCollection
	 * @param write_options[optional]
	 */
	public function __construct (MongoCollection $collectionarray , $write_options) {}

	/**
	 * Adds an CRUD operation to a batch
	 * @link http://www.php.net/manual/en/mongowritebatch.add.php
	 * @param item array <p>
	 * <tr valign="top">
	 * <td>When current batch is</td>
	 * <td>Argument expectation</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>MongoWriteBatch::COMMAND_INSERT</td>
	 * <td>The document to add</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>MongoWriteBatch::COMMAND_UPDATE</td>
	 * <td>
	 * Raw update operation. Required keys are: array("q" => array("criteria"), "u" => array("new data"))
	 * Optionally with the "multi" and "upsert" keys as boolean values
	 * </td>
	 * </tr>
	 * <tr valign="top">
	 * <td>MongoWriteBatch::COMMAND_DELETE</td>
	 * <td>
	 * Raw delete operation. Required keys are: array("q" => array("criteria"), "limit" => 1)
	 * </td>
	 * </tr>
	 * </p>
	 * @return bool true on success, throws exception on failure.
	 */
	public function add (array $item) {}

	/**
	 * Description
	 * @link http://www.php.net/manual/en/mongowritebatch.execute.php
	 * @param write_options array <p>
	 * See MongoWriteBatch::__construct.
	 * </p>
	 * @return array an array containing statistical information for the full batch.
	 * If the batch had to be split into multiple batches, the return value will aggregate
	 * the values from individual batches and return only the totals.
	 * </p>
	 * <p>
	 * If the batch was empty, an array containing only the 'ok' field is returned (as true) although
	 * nothing will be shipped over the wire (NOOP).
	 * </p>
	 * <p>
	 * <tr valign="top">
	 * <td>Array key</td>
	 * <td>Value meaning</td>
	 * <td>Returned for batch type</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>nInserted</td>
	 * <td>Number of inserted documents</td>
	 * <td>MongoWriteBatch::COMMAND_INSERT batch</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>nMatched</td>
	 * <td>Number of documents matching the query criteria</td>
	 * <td>MongoWriteBatch::COMMAND_UPDATE batch</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>nModified</td>
	 * <td>Number of documents actually needed to be modied</td>
	 * <td>MongoWriteBatch::COMMAND_UPDATE batch</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>nUpserted</td>
	 * <td>Number of upserted documents</td>
	 * <td>MongoWriteBatch::COMMAND_UPDATE batch</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>nRemoved</td>
	 * <td>Number of documents removed</td>
	 * <td>MongoWriteBatch::COMMAND_DELETE batch</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>ok</td>
	 * <td>Command success indicator</td>
	 * <td>All</td>
	 * </tr>
	 */
	final public function execute (array $write_options) {}

}

/** @jms-builtin */
class MongoUpdateBatch extends MongoWriteBatch  {

	/**
	 * Description
	 * @link http://www.php.net/manual/en/mongoupdatebatch.construct.php
	 * @param collection MongoCollection
	 * @param write_options[optional]
	 */
	public function __construct (MongoCollection $collectionarray , $write_options) {}

	/**
	 * Adds an CRUD operation to a batch
	 * @link http://www.php.net/manual/en/mongowritebatch.add.php
	 * @param item array <p>
	 * <tr valign="top">
	 * <td>When current batch is</td>
	 * <td>Argument expectation</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>MongoWriteBatch::COMMAND_INSERT</td>
	 * <td>The document to add</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>MongoWriteBatch::COMMAND_UPDATE</td>
	 * <td>
	 * Raw update operation. Required keys are: array("q" => array("criteria"), "u" => array("new data"))
	 * Optionally with the "multi" and "upsert" keys as boolean values
	 * </td>
	 * </tr>
	 * <tr valign="top">
	 * <td>MongoWriteBatch::COMMAND_DELETE</td>
	 * <td>
	 * Raw delete operation. Required keys are: array("q" => array("criteria"), "limit" => 1)
	 * </td>
	 * </tr>
	 * </p>
	 * @return bool true on success, throws exception on failure.
	 */
	public function add (array $item) {}

	/**
	 * Description
	 * @link http://www.php.net/manual/en/mongowritebatch.execute.php
	 * @param write_options array <p>
	 * See MongoWriteBatch::__construct.
	 * </p>
	 * @return array an array containing statistical information for the full batch.
	 * If the batch had to be split into multiple batches, the return value will aggregate
	 * the values from individual batches and return only the totals.
	 * </p>
	 * <p>
	 * If the batch was empty, an array containing only the 'ok' field is returned (as true) although
	 * nothing will be shipped over the wire (NOOP).
	 * </p>
	 * <p>
	 * <tr valign="top">
	 * <td>Array key</td>
	 * <td>Value meaning</td>
	 * <td>Returned for batch type</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>nInserted</td>
	 * <td>Number of inserted documents</td>
	 * <td>MongoWriteBatch::COMMAND_INSERT batch</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>nMatched</td>
	 * <td>Number of documents matching the query criteria</td>
	 * <td>MongoWriteBatch::COMMAND_UPDATE batch</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>nModified</td>
	 * <td>Number of documents actually needed to be modied</td>
	 * <td>MongoWriteBatch::COMMAND_UPDATE batch</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>nUpserted</td>
	 * <td>Number of upserted documents</td>
	 * <td>MongoWriteBatch::COMMAND_UPDATE batch</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>nRemoved</td>
	 * <td>Number of documents removed</td>
	 * <td>MongoWriteBatch::COMMAND_DELETE batch</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>ok</td>
	 * <td>Command success indicator</td>
	 * <td>All</td>
	 * </tr>
	 */
	final public function execute (array $write_options) {}

}

/** @jms-builtin */
class MongoDeleteBatch extends MongoWriteBatch  {

	/**
	 * Description
	 * @link http://www.php.net/manual/en/mongodeletebatch.construct.php
	 * @param collection MongoCollection
	 * @param write_options[optional]
	 */
	public function __construct (MongoCollection $collectionarray , $write_options) {}

	/**
	 * Adds an CRUD operation to a batch
	 * @link http://www.php.net/manual/en/mongowritebatch.add.php
	 * @param item array <p>
	 * <tr valign="top">
	 * <td>When current batch is</td>
	 * <td>Argument expectation</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>MongoWriteBatch::COMMAND_INSERT</td>
	 * <td>The document to add</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>MongoWriteBatch::COMMAND_UPDATE</td>
	 * <td>
	 * Raw update operation. Required keys are: array("q" => array("criteria"), "u" => array("new data"))
	 * Optionally with the "multi" and "upsert" keys as boolean values
	 * </td>
	 * </tr>
	 * <tr valign="top">
	 * <td>MongoWriteBatch::COMMAND_DELETE</td>
	 * <td>
	 * Raw delete operation. Required keys are: array("q" => array("criteria"), "limit" => 1)
	 * </td>
	 * </tr>
	 * </p>
	 * @return bool true on success, throws exception on failure.
	 */
	public function add (array $item) {}

	/**
	 * Description
	 * @link http://www.php.net/manual/en/mongowritebatch.execute.php
	 * @param write_options array <p>
	 * See MongoWriteBatch::__construct.
	 * </p>
	 * @return array an array containing statistical information for the full batch.
	 * If the batch had to be split into multiple batches, the return value will aggregate
	 * the values from individual batches and return only the totals.
	 * </p>
	 * <p>
	 * If the batch was empty, an array containing only the 'ok' field is returned (as true) although
	 * nothing will be shipped over the wire (NOOP).
	 * </p>
	 * <p>
	 * <tr valign="top">
	 * <td>Array key</td>
	 * <td>Value meaning</td>
	 * <td>Returned for batch type</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>nInserted</td>
	 * <td>Number of inserted documents</td>
	 * <td>MongoWriteBatch::COMMAND_INSERT batch</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>nMatched</td>
	 * <td>Number of documents matching the query criteria</td>
	 * <td>MongoWriteBatch::COMMAND_UPDATE batch</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>nModified</td>
	 * <td>Number of documents actually needed to be modied</td>
	 * <td>MongoWriteBatch::COMMAND_UPDATE batch</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>nUpserted</td>
	 * <td>Number of upserted documents</td>
	 * <td>MongoWriteBatch::COMMAND_UPDATE batch</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>nRemoved</td>
	 * <td>Number of documents removed</td>
	 * <td>MongoWriteBatch::COMMAND_DELETE batch</td>
	 * </tr>
	 * <tr valign="top">
	 * <td>ok</td>
	 * <td>Command success indicator</td>
	 * <td>All</td>
	 * </tr>
	 */
	final public function execute (array $write_options) {}

}

/** @jms-builtin */
class MongoId  {
	public $id;


	/**
	 * Creates a new id
	 * @link http://www.php.net/manual/en/mongoid.construct.php
	 */
	public function __construct () {}

	/**
	 * Returns a hexidecimal representation of this id
	 * @link http://www.php.net/manual/en/mongoid.tostring.php
	 * @return string This id.
	 */
	public function __toString () {}

	/**
	 * Create a dummy MongoId
	 * @link http://www.php.net/manual/en/mongoid.set-state.php
	 * @param props array <p>
	 * Theoretically, an array of properties used to create the new id.
	 * However, as MongoId instances have no properties, this is not used.
	 * </p>
	 * @return MongoId A new id with the value "000000000000000000000000".
	 */
	public static function __set_state (array $props) {}

	/**
	 * Gets the number of seconds since the epoch that this id was created
	 * @link http://www.php.net/manual/en/mongoid.gettimestamp.php
	 * @return int the number of seconds since the epoch that this id was created. There are only
	 * four bytes of timestamp stored, so MongoDate is a better choice
	 * for storing exact or wide-ranging times.
	 */
	public function getTimestamp () {}

	/**
	 * Gets the hostname being used for this machine's ids
	 * @link http://www.php.net/manual/en/mongoid.gethostname.php
	 * @return string the hostname.
	 */
	public static function getHostname () {}

	/**
	 * Gets the process ID
	 * @link http://www.php.net/manual/en/mongoid.getpid.php
	 * @return int the PID of the MongoId.
	 */
	public function getPID () {}

	/**
	 * Gets the incremented value to create this id
	 * @link http://www.php.net/manual/en/mongoid.getinc.php
	 * @return int the incremented value used to create this MongoId.
	 */
	public function getInc () {}

	/**
	 * Check if a value is a valid ObjectId
	 * @link http://www.php.net/manual/en/mongoid.isvalid.php
	 * @param value mixed <p>
	 * The value to check for validity.
	 * </p>
	 * @return bool true if value is a
	 * MongoId instance or a string consisting of exactly 24
	 * hexadecimal characters; otherwise, false is returned.
	 */
	public static function isValid ($value) {}

}

/** @jms-builtin */
class MongoCode  {
	public $code;
	public $scope;


	/**
	 * Creates a new code object
	 * @link http://www.php.net/manual/en/mongocode.construct.php
	 */
	public function __construct () {}

	/**
	 * Returns this code as a string
	 * @link http://www.php.net/manual/en/mongocode.tostring.php
	 * @return string This code, the scope is not returned.
	 */
	public function __toString () {}

}

/** @jms-builtin */
class MongoRegex  {
	public $regex;
	public $flags;


	/**
	 * Creates a new regular expression
	 * @link http://www.php.net/manual/en/mongoregex.construct.php
	 */
	public function __construct () {}

	/**
	 * A string representation of this regular expression
	 * @link http://www.php.net/manual/en/mongoregex.tostring.php
	 * @return string This regular expression in the form "/expr/flags".
	 */
	public function __toString () {}

}

/** @jms-builtin */
class MongoDate  {
	public $sec;
	public $usec;


	/**
	 * Creates a new date.
	 * @link http://www.php.net/manual/en/mongodate.construct.php
	 */
	public function __construct () {}

	/**
	 * Returns a string representation of this date
	 * @link http://www.php.net/manual/en/mongodate.tostring.php
	 * @return string This date.
	 */
	public function __toString () {}

}

/** @jms-builtin */
class MongoBinData  {
	const GENERIC = 0;
	const FUNC = 1;
	const BYTE_ARRAY = 2;
	const UUID = 3;
	const UUID_RFC4122 = 4;
	const MD5 = 5;
	const CUSTOM = 128;

	public $bin;
	public $type;


	/**
	 * Creates a new binary data object.
	 * @link http://www.php.net/manual/en/mongobindata.construct.php
	 */
	public function __construct () {}

	/**
	 * The string representation of this binary data object.
	 * @link http://www.php.net/manual/en/mongobindata.tostring.php
	 * @return string the string "&lt;Mongo Binary Data&gt;". To access the contents of a
	 * MongoBinData, use the bin field.
	 */
	public function __toString () {}

}

/** @jms-builtin */
class MongoDBRef  {

	/**
	 * Creates a new database reference
	 * @link http://www.php.net/manual/en/mongodbref.create.php
	 * @param collection string <p>
	 * Collection name (without the database name).
	 * </p>
	 * @param id mixed <p>
	 * The _id field of the object to which to link.
	 * </p>
	 * @param database string[optional] <p>
	 * Database name.
	 * </p>
	 * @return array the reference.
	 */
	public static function create ($collection, $id, $database = null) {}

	/**
	 * Checks if an array is a database reference
	 * @link http://www.php.net/manual/en/mongodbref.isref.php
	 * @param ref mixed <p>
	 * Array or object to check.
	 * </p>
	 * @return bool Returns true on success or false on failure.
	 */
	public static function isRef ($ref) {}

	/**
	 * Fetches the object pointed to by a reference
	 * @link http://www.php.net/manual/en/mongodbref.get.php
	 * @param db MongoDB <p>
	 * Database to use.
	 * </p>
	 * @param ref array <p>
	 * Reference to fetch.
	 * </p>
	 * @return array the document to which the reference refers or &null; if the document
	 * does not exist (the reference is broken).
	 */
	public static function get (MongoDB $db, array $ref) {}

}

/** @jms-builtin */
class MongoException extends Exception  {
	protected $message;
	protected $code;
	protected $file;
	protected $line;


	final private function __clone () {}

	/**
	 * @param message[optional]
	 * @param code[optional]
	 * @param previous[optional]
	 */
	public function __construct ($message, $code, $previous) {}

	final public function getMessage () {}

	final public function getCode () {}

	final public function getFile () {}

	final public function getLine () {}

	final public function getTrace () {}

	final public function getPrevious () {}

	final public function getTraceAsString () {}

	public function __toString () {}

}

/** @jms-builtin */
class MongoConnectionException extends MongoException  {
	protected $message;
	protected $code;
	protected $file;
	protected $line;


	final private function __clone () {}

	/**
	 * @param message[optional]
	 * @param code[optional]
	 * @param previous[optional]
	 */
	public function __construct ($message, $code, $previous) {}

	final public function getMessage () {}

	final public function getCode () {}

	final public function getFile () {}

	final public function getLine () {}

	final public function getTrace () {}

	final public function getPrevious () {}

	final public function getTraceAsString () {}

	public function __toString () {}

}

/** @jms-builtin */
class MongoCursorException extends MongoException  {
	protected $message;
	protected $code;
	protected $file;
	protected $line;
	private $host;


	/**
	 * The hostname of the server that encountered the error
	 * @link http://www.php.net/manual/en/mongocursorexception.gethost.php
	 * @return string the hostname, or NULL if the hostname is unknown.
	 */
	public function getHost () {}

	final private function __clone () {}

	/**
	 * @param message[optional]
	 * @param code[optional]
	 * @param previous[optional]
	 */
	public function __construct ($message, $code, $previous) {}

	final public function getMessage () {}

	final public function getCode () {}

	final public function getFile () {}

	final public function getLine () {}

	final public function getTrace () {}

	final public function getPrevious () {}

	final public function getTraceAsString () {}

	public function __toString () {}

}

/** @jms-builtin */
class MongoCursorTimeoutException extends MongoCursorException  {
	protected $message;
	protected $code;
	protected $file;
	protected $line;


	/**
	 * The hostname of the server that encountered the error
	 * @link http://www.php.net/manual/en/mongocursorexception.gethost.php
	 * @return string the hostname, or NULL if the hostname is unknown.
	 */
	public function getHost () {}

	final private function __clone () {}

	/**
	 * @param message[optional]
	 * @param code[optional]
	 * @param previous[optional]
	 */
	public function __construct ($message, $code, $previous) {}

	final public function getMessage () {}

	final public function getCode () {}

	final public function getFile () {}

	final public function getLine () {}

	final public function getTrace () {}

	final public function getPrevious () {}

	final public function getTraceAsString () {}

	public function __toString () {}

}

/** @jms-builtin */
class MongoGridFSException extends MongoException  {
	protected $message;
	protected $code;
	protected $file;
	protected $line;


	final private function __clone () {}

	/**
	 * @param message[optional]
	 * @param code[optional]
	 * @param previous[optional]
	 */
	public function __construct ($message, $code, $previous) {}

	final public function getMessage () {}

	final public function getCode () {}

	final public function getFile () {}

	final public function getLine () {}

	final public function getTrace () {}

	final public function getPrevious () {}

	final public function getTraceAsString () {}

	public function __toString () {}

}

/** @jms-builtin */
class MongoResultException extends MongoException  {
	protected $message;
	protected $code;
	protected $file;
	protected $line;
	public $document;
	private $host;


	/**
	 * Retrieve the full result document
	 * @link http://www.php.net/manual/en/mongoresultexception.getdocument.php
	 * @return array The full result document as an array, including partial data if available and
	 * additional keys.
	 */
	public function getDocument () {}

	public function getHost () {}

	final private function __clone () {}

	/**
	 * @param message[optional]
	 * @param code[optional]
	 * @param previous[optional]
	 */
	public function __construct ($message, $code, $previous) {}

	final public function getMessage () {}

	final public function getCode () {}

	final public function getFile () {}

	final public function getLine () {}

	final public function getTrace () {}

	final public function getPrevious () {}

	final public function getTraceAsString () {}

	public function __toString () {}

}

/** @jms-builtin */
class MongoWriteConcernException extends MongoCursorException  {
	protected $message;
	protected $code;
	protected $file;
	protected $line;
	private $document;


	/**
	 * Get the error document
	 * @link http://www.php.net/manual/en/mongowriteconcernexception.getdocument.php
	 * @return array A MongoDB document, if available, as an array.
	 */
	public function getDocument () {}

	/**
	 * The hostname of the server that encountered the error
	 * @link http://www.php.net/manual/en/mongocursorexception.gethost.php
	 * @return string the hostname, or NULL if the hostname is unknown.
	 */
	public function getHost () {}

	final private function __clone () {}

	/**
	 * @param message[optional]
	 * @param code[optional]
	 * @param previous[optional]
	 */
	public function __construct ($message, $code, $previous) {}

	final public function getMessage () {}

	final public function getCode () {}

	final public function getFile () {}

	final public function getLine () {}

	final public function getTrace () {}

	final public function getPrevious () {}

	final public function getTraceAsString () {}

	public function __toString () {}

}

/** @jms-builtin */
class MongoDuplicateKeyException extends MongoWriteConcernException  {
	protected $message;
	protected $code;
	protected $file;
	protected $line;


	/**
	 * Get the error document
	 * @link http://www.php.net/manual/en/mongowriteconcernexception.getdocument.php
	 * @return array A MongoDB document, if available, as an array.
	 */
	public function getDocument () {}

	/**
	 * The hostname of the server that encountered the error
	 * @link http://www.php.net/manual/en/mongocursorexception.gethost.php
	 * @return string the hostname, or NULL if the hostname is unknown.
	 */
	public function getHost () {}

	final private function __clone () {}

	/**
	 * @param message[optional]
	 * @param code[optional]
	 * @param previous[optional]
	 */
	public function __construct ($message, $code, $previous) {}

	final public function getMessage () {}

	final public function getCode () {}

	final public function getFile () {}

	final public function getLine () {}

	final public function getTrace () {}

	final public function getPrevious () {}

	final public function getTraceAsString () {}

	public function __toString () {}

}

/** @jms-builtin */
class MongoExecutionTimeoutException extends MongoException  {
	protected $message;
	protected $code;
	protected $file;
	protected $line;


	final private function __clone () {}

	/**
	 * @param message[optional]
	 * @param code[optional]
	 * @param previous[optional]
	 */
	public function __construct ($message, $code, $previous) {}

	final public function getMessage () {}

	final public function getCode () {}

	final public function getFile () {}

	final public function getLine () {}

	final public function getTrace () {}

	final public function getPrevious () {}

	final public function getTraceAsString () {}

	public function __toString () {}

}

/** @jms-builtin */
class MongoProtocolException extends MongoException  {
	protected $message;
	protected $code;
	protected $file;
	protected $line;


	final private function __clone () {}

	/**
	 * @param message[optional]
	 * @param code[optional]
	 * @param previous[optional]
	 */
	public function __construct ($message, $code, $previous) {}

	final public function getMessage () {}

	final public function getCode () {}

	final public function getFile () {}

	final public function getLine () {}

	final public function getTrace () {}

	final public function getPrevious () {}

	final public function getTraceAsString () {}

	public function __toString () {}

}

/** @jms-builtin */
class MongoTimestamp  {
	public $sec;
	public $inc;


	/**
	 * Creates a new timestamp.
	 * @link http://www.php.net/manual/en/mongotimestamp.construct.php
	 */
	public function __construct () {}

	/**
	 * Returns a string representation of this timestamp
	 * @link http://www.php.net/manual/en/mongotimestamp.tostring.php
	 * @return string The seconds since epoch represented by this timestamp.
	 */
	public function __toString () {}

}

/** @jms-builtin */
class MongoInt32  {
	public $value;


	/**
	 * Creates a new 32-bit integer.
	 * @link http://www.php.net/manual/en/mongoint32.construct.php
	 */
	public function __construct () {}

	/**
	 * Returns the string representation of this 32-bit integer.
	 * @link http://www.php.net/manual/en/mongoint32.tostring.php
	 * @return string the string representation of this integer.
	 */
	public function __toString () {}

}

/** @jms-builtin */
class MongoInt64  {
	public $value;


	/**
	 * Creates a new 64-bit integer.
	 * @link http://www.php.net/manual/en/mongoint64.construct.php
	 */
	public function __construct () {}

	/**
	 * Returns the string representation of this 64-bit integer.
	 * @link http://www.php.net/manual/en/mongoint64.tostring.php
	 * @return string the string representation of this integer.
	 */
	public function __toString () {}

}

/** @jms-builtin */
class MongoLog  {
	const NONE = 0;
	const WARNING = 1;
	const INFO = 2;
	const FINE = 4;
	const RS = 1;
	const POOL = 1;
	const PARSE = 16;
	const CON = 2;
	const IO = 4;
	const SERVER = 8;
	const ALL = 31;

	private static $level;
	private static $module;
	private static $callback;


	/**
	 * Sets logging level
	 * @link http://www.php.net/manual/en/mongolog.setlevel.php
	 * @param level int <p>
	 * The levels you would like to log.
	 * </p>
	 * @return void
	 */
	public static function setLevel ($level) {}

	/**
	 * Gets the log level
	 * @link http://www.php.net/manual/en/mongolog.getlevel.php
	 * @return int the current level.
	 */
	public static function getLevel () {}

	/**
	 * Sets driver functionality to log
	 * @link http://www.php.net/manual/en/mongolog.setmodule.php
	 * @param module int <p>
	 * The module(s) you would like to log.
	 * </p>
	 * @return void
	 */
	public static function setModule ($module) {}

	/**
	 * Gets the modules currently being logged
	 * @link http://www.php.net/manual/en/mongolog.getmodule.php
	 * @return int the modules currently being logged.
	 */
	public static function getModule () {}

	/**
	 * Set a callback function to be called on events
	 * @link http://www.php.net/manual/en/mongolog.setcallback.php
	 * @param log_function callable <p>
	 * The function to be called on events.
	 * </p>
	 * <p>
	 * The function should have the following prototype
	 * </p>
	 * <p>
	 * log_function
	 * intmodule
	 * intlevel
	 * stringmessage
	 * module
	 * One of the MongoLog
	 * module constants.
	 * @return void Returns true on success or false on failure.
	 */
	public static function setCallback ($log_function) {}

	/**
	 * Retrieve the previously set callback function name
	 * @link http://www.php.net/manual/en/mongolog.getcallback.php
	 * @return void the callback function name, or false if not set yet.
	 */
	public static function getCallback () {}

}

/** @jms-builtin */
class MongoPool  {

	/**
	 * Returns information about all connection pools.
	 * @link http://www.php.net/manual/en/mongopool.info.php
	 * @return array Each connection pool has an identifier, which starts with the host. For each
	 * pool, this function shows the following fields:
	 * in use
	 * <p>
	 * The number of connections currently being used by
	 * Mongo instances.
	 * </p>
	 * in pool
	 * <p>
	 * The number of connections currently in the pool (not being used).
	 * </p>
	 * remaining
	 * <p>
	 * The number of connections that could be created by this pool. For
	 * example, suppose a pool had 5 connections remaining and 3 connections in
	 * the pool. We could create 8 new instances of
	 * MongoClient before we exhausted this pool
	 * (assuming no instances of MongoClient went out of
	 * scope, returning their connections to the pool).
	 * </p>
	 * <p>
	 * A negative number means that this pool will spawn unlimited connections.
	 * </p>
	 * <p>
	 * Before a pool is created, you can change the max number of connections by
	 * calling Mongo::setPoolSize. Once a pool is showing
	 * up in the output of this function, its size cannot be changed.
	 * </p>
	 * total
	 * <p>
	 * The total number of connections allowed for this pool. This should be
	 * greater than or equal to "in use" + "in pool" (or -1).
	 * </p>
	 * timeout
	 * <p>
	 * The socket timeout for connections in this pool. This is how long
	 * connections in this pool will attempt to connect to a server before
	 * giving up.
	 * </p>
	 * waiting
	 * <p>
	 * If you have capped the pool size, workers requesting connections from
	 * the pool may block until other workers return their connections. This
	 * field shows how many milliseconds workers have blocked for connections to
	 * be released. If this number keeps increasing, you may want to use
	 * MongoPool::setSize to add more connections to your
	 * pool.
	 * </p>
	 */
	public static function info () {}

	/**
	 * Set the size for future connection pools.
	 * @link http://www.php.net/manual/en/mongopool.setsize.php
	 * @param size int <p>
	 * The max number of connections future pools will be able to create.
	 * Negative numbers mean that the pool will spawn an infinite number of
	 * connections.
	 * </p>
	 * @return bool the former value of pool size.
	 */
	public static function setSize ($size) {}

	/**
	 * Get pool size for connection pools
	 * @link http://www.php.net/manual/en/mongopool.getsize.php
	 * @return int the current pool size.
	 */
	public static function getSize () {}

}

/** @jms-builtin */
class MongoMaxKey  {
}

/** @jms-builtin */
class MongoMinKey  {
}

/**
 * Serializes a PHP variable into a BSON string
 * @link http://www.php.net/manual/en/function.bson-encode.php
 * @param anything mixed <p>
 * The variable to be serialized.
 * </p>
 * @return string the serialized string.
 * @jms-builtin
 */
function bson_encode ($anything) {}

/**
 * Deserializes a BSON object into a PHP array
 * @link http://www.php.net/manual/en/function.bson-decode.php
 * @param bson string <p>
 * The BSON to be deserialized.
 * </p>
 * @return array the deserialized BSON object.
 * @jms-builtin
 */
function bson_decode ($bson) {}

define ('MONGO_STREAMS', 1);
define ('MONGO_SUPPORTS_STREAMS', 1);
define ('MONGO_SUPPORTS_SSL', 0);
define ('MONGO_SUPPORTS_AUTH_MECHANISM_MONGODB_CR', 1);
define ('MONGO_SUPPORTS_AUTH_MECHANISM_MONGODB_X509', 1);
define ('MONGO_SUPPORTS_AUTH_MECHANISM_GSSAPI', 0);
define ('MONGO_SUPPORTS_AUTH_MECHANISM_PLAIN', 0);
define ('MONGO_STREAM_NOTIFY_TYPE_IO_INIT', 100);
define ('MONGO_STREAM_NOTIFY_TYPE_LOG', 200);
define ('MONGO_STREAM_NOTIFY_IO_READ', 111);
define ('MONGO_STREAM_NOTIFY_IO_WRITE', 112);
define ('MONGO_STREAM_NOTIFY_IO_PROGRESS', 7);
define ('MONGO_STREAM_NOTIFY_IO_COMPLETED', 8);
define ('MONGO_STREAM_NOTIFY_LOG_INSERT', 211);
define ('MONGO_STREAM_NOTIFY_LOG_QUERY', 212);
define ('MONGO_STREAM_NOTIFY_LOG_UPDATE', 213);
define ('MONGO_STREAM_NOTIFY_LOG_DELETE', 214);
define ('MONGO_STREAM_NOTIFY_LOG_GETMORE', 215);
define ('MONGO_STREAM_NOTIFY_LOG_KILLCURSOR', 216);
define ('MONGO_STREAM_NOTIFY_LOG_BATCHINSERT', 217);
define ('MONGO_STREAM_NOTIFY_LOG_RESPONSE_HEADER', 218);
define ('MONGO_STREAM_NOTIFY_LOG_WRITE_REPLY', 219);
define ('MONGO_STREAM_NOTIFY_LOG_CMD_INSERT', 220);
define ('MONGO_STREAM_NOTIFY_LOG_CMD_UPDATE', 221);
define ('MONGO_STREAM_NOTIFY_LOG_CMD_DELETE', 222);
define ('MONGO_STREAM_NOTIFY_LOG_WRITE_BATCH', 223);

// End of mongo v.1.5.0RC1
?>
