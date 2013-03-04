<?php

// Start of Core v.5.4.3-4~precise+1

/** @jms-builtin */
class stdClass  {
}

/**
 * Interface to detect if a class is traversable using foreach.
 * @link http://php.net/manual/en/class.traversable.php
 */
interface Traversable  {
}

/**
 * Interface to create an external Iterator.
 * @link http://php.net/manual/en/class.iteratoraggregate.php
 */
interface IteratorAggregate extends Traversable {

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Retrieve an external iterator
	 * @link http://php.net/manual/en/iteratoraggregate.getiterator.php
	 * @return Traversable An instance of an object implementing <b>Iterator</b> or
	 * <b>Traversable</b>
	 */
	abstract public function getIterator ();

}

/**
 * Interface for external iterators or objects that can be iterated
 * themselves internally.
 * @link http://php.net/manual/en/class.iterator.php
 */
interface Iterator extends Traversable {

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the current element
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 */
	abstract public function current ();

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Move forward to next element
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 */
	abstract public function next ();

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Return the key of the current element
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return scalar scalar on success, or <b>NULL</b> on failure.
	 */
	abstract public function key ();

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Checks if current position is valid
	 * @link http://php.net/manual/en/iterator.valid.php
	 * @return boolean The return value will be casted to boolean and then evaluated.
	 * Returns <b>TRUE</b> on success or <b>FALSE</b> on failure.
	 */
	abstract public function valid ();

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Rewind the Iterator to the first element
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 */
	abstract public function rewind ();

}

/**
 * Interface to provide accessing objects as arrays.
 * @link http://php.net/manual/en/class.arrayaccess.php
 */
interface ArrayAccess  {

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Whether a offset exists
	 * @link http://php.net/manual/en/arrayaccess.offsetexists.php
	 * @param mixed $offset <p>
	 * An offset to check for.
	 * </p>
	 * @return boolean <b>TRUE</b> on success or <b>FALSE</b> on failure.
	 * </p>
	 * <p>
	 * The return value will be casted to boolean if non-boolean was returned.
	 */
	abstract public function offsetExists ($offset);

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to retrieve
	 * @link http://php.net/manual/en/arrayaccess.offsetget.php
	 * @param mixed $offset <p>
	 * The offset to retrieve.
	 * </p>
	 * @return mixed Can return all value types.
	 */
	abstract public function offsetGet ($offset);

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to set
	 * @link http://php.net/manual/en/arrayaccess.offsetset.php
	 * @param mixed $offset <p>
	 * The offset to assign the value to.
	 * </p>
	 * @param mixed $value <p>
	 * The value to set.
	 * </p>
	 * @return void No value is returned.
	 */
	abstract public function offsetSet ($offset, $value);

	/**
	 * (PHP 5 &gt;= 5.0.0)<br/>
	 * Offset to unset
	 * @link http://php.net/manual/en/arrayaccess.offsetunset.php
	 * @param mixed $offset <p>
	 * The offset to unset.
	 * </p>
	 * @return void No value is returned.
	 */
	abstract public function offsetUnset ($offset);

}

/**
 * Interface for customized serializing.
 * @link http://php.net/manual/en/class.serializable.php
 */
interface Serializable  {

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * String representation of object
	 * @link http://php.net/manual/en/serializable.serialize.php
	 * @return string the string representation of the object or <b>NULL</b>
	 */
	abstract public function serialize ();

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Constructs the object
	 * @link http://php.net/manual/en/serializable.unserialize.php
	 * @param string $serialized <p>
	 * The string representation of the object.
	 * </p>
	 * @return void The return value from this method is ignored.
	 */
	abstract public function unserialize ($serialized);

}

/**
 * <b>Exception</b> is the base class for
 * all Exceptions.
 * @link http://php.net/manual/en/class.exception.php
 * @jms-builtin
 */
class Exception  {
	protected $message;
	private $string;
	protected $code;
	protected $file;
	protected $line;
	private $trace;
	private $previous;


	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Clone the exception
	 * @link http://php.net/manual/en/exception.clone.php
	 * @return void No value is returned.
	 */
	final private function __clone () {}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Construct the exception
	 * @link http://php.net/manual/en/exception.construct.php
	 * @param $message [optional]
	 * @param $code [optional]
	 * @param $previous [optional]
	 */
	public function __construct ($message, $code, $previous) {}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Gets the Exception message
	 * @link http://php.net/manual/en/exception.getmessage.php
	 * @return string the Exception message as a string.
	 */
	final public function getMessage () {}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Gets the Exception code
	 * @link http://php.net/manual/en/exception.getcode.php
	 * @return mixed the exception code as integer in
	 * <b>Exception</b> but possibly as other type in
	 * <b>Exception</b> descendants (for example as
	 * string in <b>PDOException</b>).
	 */
	final public function getCode () {}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Gets the file in which the exception occurred
	 * @link http://php.net/manual/en/exception.getfile.php
	 * @return string the filename in which the exception was created.
	 */
	final public function getFile () {}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Gets the line in which the exception occurred
	 * @link http://php.net/manual/en/exception.getline.php
	 * @return int the line number where the exception was created.
	 */
	final public function getLine () {}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Gets the stack trace
	 * @link http://php.net/manual/en/exception.gettrace.php
	 * @return array the Exception stack trace as an array.
	 */
	final public function getTrace () {}

	/**
	 * (PHP 5 &gt;= 5.3.0)<br/>
	 * Returns previous Exception
	 * @link http://php.net/manual/en/exception.getprevious.php
	 * @return Exception the previous <b>Exception</b> if available
	 * or <b>NULL</b> otherwise.
	 */
	final public function getPrevious () {}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Gets the stack trace as a string
	 * @link http://php.net/manual/en/exception.gettraceasstring.php
	 * @return string the Exception stack trace as a string.
	 */
	final public function getTraceAsString () {}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * String representation of the exception
	 * @link http://php.net/manual/en/exception.tostring.php
	 * @return string the string representation of the exception.
	 */
	public function __toString () {}

}

/**
 * An Error Exception.
 * @link http://php.net/manual/en/class.errorexception.php
 * @jms-builtin
 */
class ErrorException extends Exception  {
	protected $message;
	protected $code;
	protected $file;
	protected $line;
	protected $severity;


	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Constructs the exception
	 * @link http://php.net/manual/en/errorexception.construct.php
	 * @param $message [optional]
	 * @param $code [optional]
	 * @param $severity [optional]
	 * @param $filename [optional]
	 * @param $lineno [optional]
	 * @param $previous [optional]
	 */
	public function __construct ($message, $code, $severity, $filename, $lineno, $previous) {}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Gets the exception severity
	 * @link http://php.net/manual/en/errorexception.getseverity.php
	 * @return int the severity level of the exception.
	 */
	final public function getSeverity () {}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Clone the exception
	 * @link http://php.net/manual/en/exception.clone.php
	 * @return void No value is returned.
	 */
	final private function __clone () {}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Gets the Exception message
	 * @link http://php.net/manual/en/exception.getmessage.php
	 * @return string the Exception message as a string.
	 */
	final public function getMessage () {}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Gets the Exception code
	 * @link http://php.net/manual/en/exception.getcode.php
	 * @return mixed the exception code as integer in
	 * <b>Exception</b> but possibly as other type in
	 * <b>Exception</b> descendants (for example as
	 * string in <b>PDOException</b>).
	 */
	final public function getCode () {}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Gets the file in which the exception occurred
	 * @link http://php.net/manual/en/exception.getfile.php
	 * @return string the filename in which the exception was created.
	 */
	final public function getFile () {}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Gets the line in which the exception occurred
	 * @link http://php.net/manual/en/exception.getline.php
	 * @return int the line number where the exception was created.
	 */
	final public function getLine () {}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Gets the stack trace
	 * @link http://php.net/manual/en/exception.gettrace.php
	 * @return array the Exception stack trace as an array.
	 */
	final public function getTrace () {}

	/**
	 * (PHP 5 &gt;= 5.3.0)<br/>
	 * Returns previous Exception
	 * @link http://php.net/manual/en/exception.getprevious.php
	 * @return Exception the previous <b>Exception</b> if available
	 * or <b>NULL</b> otherwise.
	 */
	final public function getPrevious () {}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * Gets the stack trace as a string
	 * @link http://php.net/manual/en/exception.gettraceasstring.php
	 * @return string the Exception stack trace as a string.
	 */
	final public function getTraceAsString () {}

	/**
	 * (PHP 5 &gt;= 5.1.0)<br/>
	 * String representation of the exception
	 * @link http://php.net/manual/en/exception.tostring.php
	 * @return string the string representation of the exception.
	 */
	public function __toString () {}

}

/**
 * Class used to represent anonymous
 * functions.
 * @link http://php.net/manual/en/class.closure.php
 * @jms-builtin
 */
final class Closure  {

	/**
	 * (No version information available, might only be in SVN)<br/>
	 * Constructor that disallows instantiation
	 * @link http://php.net/manual/en/closure.construct.php
	 */
	private function __construct () {}

	/**
	 * (No version information available, might only be in SVN)<br/>
	 * Duplicates a closure with a specific bound object and class scope
	 * @link http://php.net/manual/en/closure.bind.php
	 * @param Closure $closure <p>
	 * The anonymous functions to bind.
	 * </p>
	 * @param object $newthis <p>
	 * The object to which the given anonymous function should be bound, or
	 * <b>NULL</b> for the closure to be unbound.
	 * </p>
	 * @param mixed $newscope [optional] <p>
	 * The class scope to which associate the closure is to be associated, or
	 * 'static' to keep the current one. If an object is given, the type of the
	 * object will be used instead. This determines the visibility of protected
	 * and private methods of the bound object.
	 * </p>
	 * @return Closure a new <b>Closure</b> object or <b>FALSE</b> on failure
	 */
	public static function bind (Closure $closure, $newthis, $newscope = null) {}

	/**
	 * (No version information available, might only be in SVN)<br/>
	 * Duplicates the closure with a new bound object and class scope
	 * @link http://php.net/manual/en/closure.bindto.php
	 * @param object $newthis <p>
	 * The object to which the given anonymous function should be bound, or
	 * <b>NULL</b> for the closure to be unbound.
	 * </p>
	 * @param mixed $newscope [optional] <p>
	 * The class scope to which associate the closure is to be associated, or
	 * 'static' to keep the current one. If an object is given, the type of the
	 * object will be used instead. This determines the visibility of protected
	 * and private methods of the bound object.
	 * </p>
	 * @return Closure the newly created <b>Closure</b> object
	 * or <b>FALSE</b> on failure
	 */
	public function bindTo ($newthis, $newscope = null) {}

}

/**
 * (PHP 4, PHP 5)<br/>
 * Gets the version of the current Zend engine
 * @link http://php.net/manual/en/function.zend-version.php
 * @return string the Zend Engine version number, as a string.
 * @jms-builtin
 */
function zend_version () {}

/**
 * (PHP 4, PHP 5)<br/>
 * Returns the number of arguments passed to the function
 * @link http://php.net/manual/en/function.func-num-args.php
 * @return int the number of arguments passed into the current user-defined
 * function.
 * @jms-builtin
 */
function func_num_args () {}

/**
 * (PHP 4, PHP 5)<br/>
 * Return an item from the argument list
 * @link http://php.net/manual/en/function.func-get-arg.php
 * @param int $arg_num <p>
 * The argument offset. Function arguments are counted starting from
 * zero.
 * </p>
 * @return mixed the specified argument, or <b>FALSE</b> on error.
 * @jms-builtin
 */
function func_get_arg ($arg_num) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Returns an array comprising a function's argument list
 * @link http://php.net/manual/en/function.func-get-args.php
 * @return array an array in which each element is a copy of the corresponding
 * member of the current user-defined function's argument list.
 * @jms-builtin
 */
function func_get_args () {}

/**
 * (PHP 4, PHP 5)<br/>
 * Get string length
 * @link http://php.net/manual/en/function.strlen.php
 * @param string $string <p>
 * The string being measured for length.
 * </p>
 * @return int The length of the <i>string</i> on success,
 * and 0 if the <i>string</i> is empty.
 * @jms-builtin
 */
function strlen ($string) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Binary safe string comparison
 * @link http://php.net/manual/en/function.strcmp.php
 * @param string $str1 <p>
 * The first string.
 * </p>
 * @param string $str2 <p>
 * The second string.
 * </p>
 * @return int &lt; 0 if <i>str1</i> is less than
 * <i>str2</i>; &gt; 0 if <i>str1</i>
 * is greater than <i>str2</i>, and 0 if they are
 * equal.
 * @jms-builtin
 */
function strcmp ($str1, $str2) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Binary safe string comparison of the first n characters
 * @link http://php.net/manual/en/function.strncmp.php
 * @param string $str1 <p>
 * The first string.
 * </p>
 * @param string $str2 <p>
 * The second string.
 * </p>
 * @param int $len <p>
 * Number of characters to use in the comparison.
 * </p>
 * @return int &lt; 0 if <i>str1</i> is less than
 * <i>str2</i>; &gt; 0 if <i>str1</i>
 * is greater than <i>str2</i>, and 0 if they are
 * equal.
 * @jms-builtin
 */
function strncmp ($str1, $str2, $len) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Binary safe case-insensitive string comparison
 * @link http://php.net/manual/en/function.strcasecmp.php
 * @param string $str1 <p>
 * The first string
 * </p>
 * @param string $str2 <p>
 * The second string
 * </p>
 * @return int &lt; 0 if <i>str1</i> is less than
 * <i>str2</i>; &gt; 0 if <i>str1</i>
 * is greater than <i>str2</i>, and 0 if they are
 * equal.
 * @jms-builtin
 */
function strcasecmp ($str1, $str2) {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * Binary safe case-insensitive string comparison of the first n characters
 * @link http://php.net/manual/en/function.strncasecmp.php
 * @param string $str1 <p>
 * The first string.
 * </p>
 * @param string $str2 <p>
 * The second string.
 * </p>
 * @param int $len <p>
 * The length of strings to be used in the comparison.
 * </p>
 * @return int &lt; 0 if <i>str1</i> is less than
 * <i>str2</i>; &gt; 0 if <i>str1</i> is
 * greater than <i>str2</i>, and 0 if they are equal.
 * @jms-builtin
 */
function strncasecmp ($str1, $str2, $len) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Return the current key and value pair from an array and advance the array cursor
 * @link http://php.net/manual/en/function.each.php
 * @param array $array <p>
 * The input array.
 * </p>
 * @return array the current key and value pair from the array
 * <i>array</i>. This pair is returned in a four-element
 * array, with the keys 0, 1,
 * key, and value. Elements
 * 0 and key contain the key name of
 * the array element, and 1 and value
 * contain the data.
 * </p>
 * <p>
 * If the internal pointer for the array points past the end of the
 * array contents, <b>each</b> returns
 * <b>FALSE</b>.
 * @jms-builtin
 */
function each (array &$array) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Sets which PHP errors are reported
 * @link http://php.net/manual/en/function.error-reporting.php
 * @param int $level [optional] <p>
 * The new error_reporting
 * level. It takes on either a bitmask, or named constants. Using named
 * constants is strongly encouraged to ensure compatibility for future
 * versions. As error levels are added, the range of integers increases,
 * so older integer-based error levels will not always behave as expected.
 * </p>
 * <p>
 * The available error level constants and the actual
 * meanings of these error levels are described in the
 * predefined constants.
 * </p>
 * @return int the old error_reporting
 * level or the current level if no <i>level</i> parameter is
 * given.
 * @jms-builtin
 */
function error_reporting ($level = null) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Defines a named constant
 * @link http://php.net/manual/en/function.define.php
 * @param string $name <p>
 * The name of the constant.
 * </p>
 * @param mixed $value <p>
 * The value of the constant; only scalar and null values are allowed.
 * Scalar values are integer,
 * float, string or boolean values. It is
 * possible to define resource constants, however it is not recommended
 * and may cause unpredictable behavior.
 * </p>
 * @param bool $case_insensitive [optional] <p>
 * If set to <b>TRUE</b>, the constant will be defined case-insensitive.
 * The default behavior is case-sensitive; i.e.
 * CONSTANT and Constant represent
 * different values.
 * </p>
 * <p>
 * Case-insensitive constants are stored as lower-case.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function define ($name, $value, $case_insensitive = false) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Checks whether a given named constant exists
 * @link http://php.net/manual/en/function.defined.php
 * @param string $name <p>
 * The constant name.
 * </p>
 * @return bool <b>TRUE</b> if the named constant given by <i>name</i>
 * has been defined, <b>FALSE</b> otherwise.
 * @jms-builtin
 */
function defined ($name) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Returns the name of the class of an object
 * @link http://php.net/manual/en/function.get-class.php
 * @param object $object [optional] <p>
 * The tested object. This parameter may be omitted when inside a class.
 * </p>
 * @return string the name of the class of which <i>object</i> is an
 * instance. Returns <b>FALSE</b> if <i>object</i> is not an
 * object.
 * </p>
 * <p>
 * If <i>object</i> is omitted when inside a class, the
 * name of that class is returned.
 * @jms-builtin
 */
function get_class ($object = null) {}

/**
 * (PHP 5 &gt;= 5.3.0)<br/>
 * the "Late Static Binding" class name
 * @link http://php.net/manual/en/function.get-called-class.php
 * @return string the class name. Returns <b>FALSE</b> if called from outside a class.
 * @jms-builtin
 */
function get_called_class () {}

/**
 * (PHP 4, PHP 5)<br/>
 * Retrieves the parent class name for object or class
 * @link http://php.net/manual/en/function.get-parent-class.php
 * @param mixed $object [optional] <p>
 * The tested object or class name
 * </p>
 * @return string the name of the parent class of the class of which
 * <i>object</i> is an instance or the name.
 * </p>
 * <p>
 * If the object does not have a parent or the class given does not exist <b>FALSE</b> will be returned.
 * </p>
 * <p>
 * If called without parameter outside object, this function returns <b>FALSE</b>.
 * @jms-builtin
 */
function get_parent_class ($object = null) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Checks if the class method exists
 * @link http://php.net/manual/en/function.method-exists.php
 * @param mixed $object <p>
 * An object instance or a class name
 * </p>
 * @param string $method_name <p>
 * The method name
 * </p>
 * @return bool <b>TRUE</b> if the method given by <i>method_name</i>
 * has been defined for the given <i>object</i>, <b>FALSE</b>
 * otherwise.
 * @jms-builtin
 */
function method_exists ($object, $method_name) {}

/**
 * (PHP 5 &gt;= 5.1.0)<br/>
 * Checks if the object or class has a property
 * @link http://php.net/manual/en/function.property-exists.php
 * @param mixed $class <p>
 * The class name or an object of the class to test for
 * </p>
 * @param string $property <p>
 * The name of the property
 * </p>
 * @return bool <b>TRUE</b> if the property exists, <b>FALSE</b> if it doesn't exist or
 * <b>NULL</b> in case of an error.
 * @jms-builtin
 */
function property_exists ($class, $property) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Checks if the class has been defined
 * @link http://php.net/manual/en/function.class-exists.php
 * @param string $class_name <p>
 * The class name. The name is matched in a case-insensitive manner.
 * </p>
 * @param bool $autoload [optional] <p>
 * Whether or not to call __autoload by default.
 * </p>
 * @return bool <b>TRUE</b> if <i>class_name</i> is a defined class,
 * <b>FALSE</b> otherwise.
 * @jms-builtin
 */
function class_exists ($class_name, $autoload = true) {}

/**
 * (PHP 5 &gt;= 5.0.2)<br/>
 * Checks if the interface has been defined
 * @link http://php.net/manual/en/function.interface-exists.php
 * @param string $interface_name <p>
 * The interface name
 * </p>
 * @param bool $autoload [optional] <p>
 * Whether to call __autoload or not by default.
 * </p>
 * @return bool <b>TRUE</b> if the interface given by
 * <i>interface_name</i> has been defined, <b>FALSE</b> otherwise.
 * @jms-builtin
 */
function interface_exists ($interface_name, $autoload = true) {}

/**
 * (No version information available, might only be in SVN)<br/>
 * Checks if the trait exists
 * @link http://php.net/manual/en/function.trait-exists.php
 * @param string $traitname <p>
 * Name of the trait to check
 * </p>
 * @param bool $autoload [optional] <p>
 * Whether to autoload if not already loaded.
 * </p>
 * @return bool <b>TRUE</b> if trait exists, <b>FALSE</b> if not, <b>NULL</b> in case of an error.
 * @jms-builtin
 */
function trait_exists ($traitname, $autoload = null) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Return <b>TRUE</b> if the given function has been defined
 * @link http://php.net/manual/en/function.function-exists.php
 * @param string $function_name <p>
 * The function name, as a string.
 * </p>
 * @return bool <b>TRUE</b> if <i>function_name</i> exists and is a
 * function, <b>FALSE</b> otherwise.
 * </p>
 * <p>
 * This function will return <b>FALSE</b> for constructs, such as
 * <b>include_once</b> and <b>echo</b>.
 * @jms-builtin
 */
function function_exists ($function_name) {}

/**
 * (PHP 5 &gt;= 5.3.0)<br/>
 * Creates an alias for a class
 * @link http://php.net/manual/en/function.class-alias.php
 * @param string $original [optional] <p>
 * The original class.
 * </p>
 * @param string $alias [optional] <p>
 * The alias name for the class.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function class_alias ($original = null, $alias = null) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Returns an array with the names of included or required files
 * @link http://php.net/manual/en/function.get-included-files.php
 * @return array an array of the names of all files.
 * </p>
 * <p>
 * The script originally called is considered an "included file," so it will
 * be listed together with the files referenced by
 * <b>include</b> and family.
 * </p>
 * <p>
 * Files that are included or required multiple times only show up once in
 * the returned array.
 * @jms-builtin
 */
function get_included_files () {}

/**
 * (PHP 4, PHP 5)<br/>
 * Alias of <b>get_included_files</b>
 * @link http://php.net/manual/en/function.get-required-files.php
 * @jms-builtin
 */
function get_required_files () {}

/**
 * (PHP 4, PHP 5)<br/>
 * Checks if the object has this class as one of its parents
 * @link http://php.net/manual/en/function.is-subclass-of.php
 * @param mixed $object <p>
 * A class name or an object instance
 * </p>
 * @param string $class_name <p>
 * The class name
 * </p>
 * @param bool $allow_string [optional] <p>
 * If this parameter set to false, string class name as <i>object</i>
 * is not allowed. This also prevents from calling autoloader if the class doesn't exist.
 * </p>
 * @return bool This function returns <b>TRUE</b> if the object <i>object</i>,
 * belongs to a class which is a subclass of
 * <i>class_name</i>, <b>FALSE</b> otherwise.
 * @jms-builtin
 */
function is_subclass_of ($object, $class_name, $allow_string = '&true;') {}

/**
 * (PHP 4 &gt;= 4.2.0, PHP 5)<br/>
 * Checks if the object is of this class or has this class as one of its parents
 * @link http://php.net/manual/en/function.is-a.php
 * @param object $object <p>
 * The tested object
 * </p>
 * @param string $class_name <p>
 * The class name
 * </p>
 * @param bool $allow_string [optional] <p>
 * If this parameter set to false, string class name as <i>object</i>
 * is not allowed. This also prevents from calling autoloader if the class doesn't exist.
 * </p>
 * @return bool <b>TRUE</b> if the object is of this class or has this class as one of
 * its parents, <b>FALSE</b> otherwise.
 * @jms-builtin
 */
function is_a ($object, $class_name, $allow_string = '&false;') {}

/**
 * (PHP 4, PHP 5)<br/>
 * Get the default properties of the class
 * @link http://php.net/manual/en/function.get-class-vars.php
 * @param string $class_name <p>
 * The class name
 * </p>
 * @return array an associative array of declared properties visible from the
 * current scope, with their default value.
 * The resulting array elements are in the form of
 * varname => value.
 * In case of an error, it returns <b>FALSE</b>.
 * @jms-builtin
 */
function get_class_vars ($class_name) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Gets the properties of the given object
 * @link http://php.net/manual/en/function.get-object-vars.php
 * @param object $object <p>
 * An object instance.
 * </p>
 * @return array an associative array of defined object accessible non-static properties
 * for the specified <i>object</i> in scope. If a property have
 * not been assigned a value, it will be returned with a <b>NULL</b> value.
 * @jms-builtin
 */
function get_object_vars ($object) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Gets the class methods' names
 * @link http://php.net/manual/en/function.get-class-methods.php
 * @param mixed $class_name <p>
 * The class name or an object instance
 * </p>
 * @return array an array of method names defined for the class specified by
 * <i>class_name</i>. In case of an error, it returns <b>NULL</b>.
 * @jms-builtin
 */
function get_class_methods ($class_name) {}

/**
 * (PHP 4 &gt;= 4.0.1, PHP 5)<br/>
 * Generates a user-level error/warning/notice message
 * @link http://php.net/manual/en/function.trigger-error.php
 * @param string $error_msg <p>
 * The designated error message for this error. It's limited to 1024
 * characters in length. Any additional characters beyond 1024 will be
 * truncated.
 * </p>
 * @param int $error_type [optional] <p>
 * The designated error type for this error. It only works with the E_USER
 * family of constants, and will default to <b>E_USER_NOTICE</b>.
 * </p>
 * @return bool This function returns <b>FALSE</b> if wrong <i>error_type</i> is
 * specified, <b>TRUE</b> otherwise.
 * @jms-builtin
 */
function trigger_error ($error_msg, $error_type = 'E_USER_NOTICE') {}

/**
 * (PHP 4, PHP 5)<br/>
 * Alias of <b>trigger_error</b>
 * @link http://php.net/manual/en/function.user-error.php
 * @param $message
 * @param $error_type [optional]
 * @jms-builtin
 */
function user_error ($message, $error_type) {}

/**
 * (PHP 4 &gt;= 4.0.1, PHP 5)<br/>
 * Sets a user-defined error handler function
 * @link http://php.net/manual/en/function.set-error-handler.php
 * @param callable $error_handler <p>
 * The user function needs to accept two parameters: the error code, and a
 * string describing the error. Then there are three optional parameters
 * that may be supplied: the filename in which the error occurred, the
 * line number in which the error occurred, and the context in which the
 * error occurred (an array that points to the active symbol table at the
 * point the error occurred). The function can be shown as:
 * </p>
 * <p>
 * <b>handler</b>
 * <b>int<i>errno</i></b>
 * <b>string<i>errstr</i></b>
 * <b>string<i>errfile</i></b>
 * <b>int<i>errline</i></b>
 * <b>array<i>errcontext</i></b>
 * <i>errno</i>
 * The first parameter, <i>errno</i>, contains the
 * level of the error raised, as an integer.
 * @param int $error_types [optional] <p>
 * Can be used to mask the triggering of the
 * <i>error_handler</i> function just like the error_reporting ini setting
 * controls which errors are shown. Without this mask set the
 * <i>error_handler</i> will be called for every error
 * regardless to the setting of the error_reporting setting.
 * </p>
 * @return mixed a string containing the previously defined error handler (if any). If
 * the built-in error handler is used <b>NULL</b> is returned. <b>NULL</b> is also returned
 * in case of an error such as an invalid callback. If the previous error handler
 * was a class method, this function will return an indexed array with the class
 * and the method name.
 * @jms-builtin
 */
function set_error_handler (callable $error_handler, $error_types = 'E_ALL | E_STRICT') {}

/**
 * (PHP 4 &gt;= 4.0.1, PHP 5)<br/>
 * Restores the previous error handler function
 * @link http://php.net/manual/en/function.restore-error-handler.php
 * @return bool This function always returns <b>TRUE</b>.
 * @jms-builtin
 */
function restore_error_handler () {}

/**
 * (PHP 5)<br/>
 * Sets a user-defined exception handler function
 * @link http://php.net/manual/en/function.set-exception-handler.php
 * @param callable $exception_handler <p>
 * Name of the function to be called when an uncaught exception occurs.
 * This function must be defined before calling
 * <b>set_exception_handler</b>. This handler function
 * needs to accept one parameter, which will be the exception object that
 * was thrown.
 * </p>
 * <p>
 * <b>NULL</b> may be passed instead, to reset this handler to its
 * default state.
 * </p>
 * @return callable the name of the previously defined exception handler, or <b>NULL</b> on error. If
 * no previous handler was defined, <b>NULL</b> is also returned. If <b>NULL</b> is passed,
 * resetting the handler to its default state, <b>TRUE</b> is returned.
 * @jms-builtin
 */
function set_exception_handler (callable $exception_handler) {}

/**
 * (PHP 5)<br/>
 * Restores the previously defined exception handler function
 * @link http://php.net/manual/en/function.restore-exception-handler.php
 * @return bool This function always returns <b>TRUE</b>.
 * @jms-builtin
 */
function restore_exception_handler () {}

/**
 * (PHP 4, PHP 5)<br/>
 * Returns an array with the name of the defined classes
 * @link http://php.net/manual/en/function.get-declared-classes.php
 * @return array an array of the names of the declared classes in the current
 * script.
 * </p>
 * <p>
 * Note that depending on what extensions you have compiled or
 * loaded into PHP, additional classes could be present. This means that
 * you will not be able to define your own classes using these
 * names. There is a list of predefined classes in the Predefined Classes section of
 * the appendices.
 * @jms-builtin
 */
function get_declared_classes () {}

/**
 * (No version information available, might only be in SVN)<br/>
 * Returns an array of all declared traits
 * @link http://php.net/manual/en/function.get-declared-traits.php
 * @return array an array with names of all declared traits in values.
 * Returns <b>NULL</b> in case of a failure.
 * @jms-builtin
 */
function get_declared_traits () {}

/**
 * (PHP 5)<br/>
 * Returns an array of all declared interfaces
 * @link http://php.net/manual/en/function.get-declared-interfaces.php
 * @return array an array of the names of the declared interfaces in the current
 * script.
 * @jms-builtin
 */
function get_declared_interfaces () {}

/**
 * (PHP 4 &gt;= 4.0.4, PHP 5)<br/>
 * Returns an array of all defined functions
 * @link http://php.net/manual/en/function.get-defined-functions.php
 * @return array an multidimensional array containing a list of all defined
 * functions, both built-in (internal) and user-defined. The internal
 * functions will be accessible via $arr["internal"], and
 * the user defined ones using $arr["user"] (see example
 * below).
 * @jms-builtin
 */
function get_defined_functions () {}

/**
 * (PHP 4 &gt;= 4.0.4, PHP 5)<br/>
 * Returns an array of all defined variables
 * @link http://php.net/manual/en/function.get-defined-vars.php
 * @return array A multidimensional array with all the variables.
 * @jms-builtin
 */
function get_defined_vars () {}

/**
 * (PHP 4 &gt;= 4.0.1, PHP 5)<br/>
 * Create an anonymous (lambda-style) function
 * @link http://php.net/manual/en/function.create-function.php
 * @param string $args <p>
 * The function arguments.
 * </p>
 * @param string $code <p>
 * The function code.
 * </p>
 * @return string a unique function name as a string, or <b>FALSE</b> on error.
 * @jms-builtin
 */
function create_function ($args, $code) {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * Returns the resource type
 * @link http://php.net/manual/en/function.get-resource-type.php
 * @param resource $handle <p>
 * The evaluated resource handle.
 * </p>
 * @return string If the given <i>handle</i> is a resource, this function
 * will return a string representing its type. If the type is not identified
 * by this function, the return value will be the string
 * Unknown.
 * </p>
 * <p>
 * This function will return <b>FALSE</b> and generate an error if
 * <i>handle</i> is not a resource.
 * @jms-builtin
 */
function get_resource_type ($handle) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Returns an array with the names of all modules compiled and loaded
 * @link http://php.net/manual/en/function.get-loaded-extensions.php
 * @param bool $zend_extensions [optional] <p>
 * Only return Zend extensions, if not then regular extensions, like
 * mysqli are listed. Defaults to <b>FALSE</b> (return regular extensions).
 * </p>
 * @return array an indexed array of all the modules names.
 * @jms-builtin
 */
function get_loaded_extensions ($zend_extensions = false) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Find out whether an extension is loaded
 * @link http://php.net/manual/en/function.extension-loaded.php
 * @param string $name <p>
 * The extension name.
 * </p>
 * <p>
 * You can see the names of various extensions by using
 * <b>phpinfo</b> or if you're using the
 * CGI or CLI version of
 * PHP you can use the -m switch to
 * list all available extensions:
 * <pre>
 * $ php -m
 * [PHP Modules]
 * xml
 * tokenizer
 * standard
 * sockets
 * session
 * posix
 * pcre
 * overload
 * mysql
 * mbstring
 * ctype
 * [Zend Modules]
 * </pre>
 * </p>
 * @return bool <b>TRUE</b> if the extension identified by <i>name</i>
 * is loaded, <b>FALSE</b> otherwise.
 * @jms-builtin
 */
function extension_loaded ($name) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Returns an array with the names of the functions of a module
 * @link http://php.net/manual/en/function.get-extension-funcs.php
 * @param string $module_name <p>
 * The module name.
 * </p>
 * <p>
 * This parameter must be in lowercase.
 * </p>
 * @return array an array with all the functions, or <b>FALSE</b> if
 * <i>module_name</i> is not a valid extension.
 * @jms-builtin
 */
function get_extension_funcs ($module_name) {}

/**
 * (PHP 4 &gt;= 4.1.0, PHP 5)<br/>
 * Returns an associative array with the names of all the constants and their values
 * @link http://php.net/manual/en/function.get-defined-constants.php
 * @param bool $categorize [optional] <p>
 * Causing this function to return a multi-dimensional
 * array with categories in the keys of the first dimension and constants
 * and their values in the second dimension.
 * <code>
 * define("MY_CONSTANT", 1);
 * print_r(get_defined_constants(true));
 * </code>
 * The above example will output
 * something similar to:</p>
 * <pre>
 * Array
 * (
 * [Core] => Array
 * (
 * [E_ERROR] => 1
 * [E_WARNING] => 2
 * [E_PARSE] => 4
 * [E_NOTICE] => 8
 * [E_CORE_ERROR] => 16
 * [E_CORE_WARNING] => 32
 * [E_COMPILE_ERROR] => 64
 * [E_COMPILE_WARNING] => 128
 * [E_USER_ERROR] => 256
 * [E_USER_WARNING] => 512
 * [E_USER_NOTICE] => 1024
 * [E_ALL] => 2047
 * [TRUE] => 1
 * )
 * [pcre] => Array
 * (
 * [PREG_PATTERN_ORDER] => 1
 * [PREG_SET_ORDER] => 2
 * [PREG_OFFSET_CAPTURE] => 256
 * [PREG_SPLIT_NO_EMPTY] => 1
 * [PREG_SPLIT_DELIM_CAPTURE] => 2
 * [PREG_SPLIT_OFFSET_CAPTURE] => 4
 * [PREG_GREP_INVERT] => 1
 * )
 * [user] => Array
 * (
 * [MY_CONSTANT] => 1
 * )
 * )
 * </pre>
 * </p>
 * @return array
 * @jms-builtin
 */
function get_defined_constants ($categorize = false) {}

/**
 * (PHP 4 &gt;= 4.3.0, PHP 5)<br/>
 * Generates a backtrace
 * @link http://php.net/manual/en/function.debug-backtrace.php
 * @param int $options [optional] <p>
 * As of 5.3.6, this parameter is a bitmask for the following options:
 * <table>
 * <b>debug_backtrace</b> options
 * <tr valign="top">
 * <td>DEBUG_BACKTRACE_PROVIDE_OBJECT</td>
 * <td>
 * Whether or not to populate the "object" index.
 * </td>
 * </tr>
 * <tr valign="top">
 * <td>DEBUG_BACKTRACE_IGNORE_ARGS</td>
 * <td>
 * Whether or not to omit the "args" index, and thus all the function/method arguments,
 * to save memory.
 * </td>
 * </tr>
 * </table>
 * Before 5.3.6, the only values recognized are <b>TRUE</b> or <b>FALSE</b>, which are the same as
 * setting or not setting the <b>DEBUG_BACKTRACE_PROVIDE_OBJECT</b> option respectively.
 * </p>
 * @param int $limit [optional] <p>
 * As of 5.4.0, this parameter can be used to limit the number of stack frames returned.
 * By default (<i>limit</i>=0) it returns all stack frames.
 * </p>
 * @return array an array of associative arrays. The possible returned elements
 * are as follows:
 * </p>
 * <p>
 * <table>
 * Possible returned elements from <b>debug_backtrace</b>
 * <tr valign="top">
 * <td>Name</td>
 * <td>Type</td>
 * <td>Description</td>
 * </tr>
 * <tr valign="top">
 * <td>function</td>
 * <td>string</td>
 * <td>
 * The current function name. See also
 * __FUNCTION__.
 * </td>
 * </tr>
 * <tr valign="top">
 * <td>line</td>
 * <td>integer</td>
 * <td>
 * The current line number. See also
 * __LINE__.
 * </td>
 * </tr>
 * <tr valign="top">
 * <td>file</td>
 * <td>string</td>
 * <td>
 * The current file name. See also
 * __FILE__.
 * </td>
 * </tr>
 * <tr valign="top">
 * <td>class</td>
 * <td>string</td>
 * <td>
 * The current class name. See also
 * __CLASS__
 * </td>
 * </tr>
 * <tr valign="top">
 * <td>object</td>
 * <td>object</td>
 * <td>
 * The current object.
 * </td>
 * </tr>
 * <tr valign="top">
 * <td>type</td>
 * <td>string</td>
 * <td>
 * The current call type. If a method call, "->" is returned. If a static
 * method call, "::" is returned. If a function call, nothing is returned.
 * </td>
 * </tr>
 * <tr valign="top">
 * <td>args</td>
 * <td>array</td>
 * <td>
 * If inside a function, this lists the functions arguments. If
 * inside an included file, this lists the included file name(s).
 * </td>
 * </tr>
 * </table>
 * @jms-builtin
 */
function debug_backtrace ($options = 'DEBUG_BACKTRACE_PROVIDE_OBJECT', $limit = 0) {}

/**
 * (PHP 5)<br/>
 * Prints a backtrace
 * @link http://php.net/manual/en/function.debug-print-backtrace.php
 * @param int $options [optional] <p>
 * As of 5.3.6, this parameter is a bitmask for the following options:
 * <table>
 * <b>debug_print_backtrace</b> options
 * <tr valign="top">
 * <td>DEBUG_BACKTRACE_IGNORE_ARGS</td>
 * <td>
 * Whether or not to omit the "args" index, and thus all the function/method arguments,
 * to save memory.
 * </td>
 * </tr>
 * </table>
 * </p>
 * @param int $limit [optional] <p>
 * As of 5.4.0, this parameter can be used to limit the number of stack frames printed.
 * By default (<i>limit</i>=0) it prints all stack frames.
 * </p>
 * @return void No value is returned.
 * @jms-builtin
 */
function debug_print_backtrace ($options = 0, $limit = 0) {}

/**
 * (PHP 5 &gt;= 5.3.0)<br/>
 * Forces collection of any existing garbage cycles
 * @link http://php.net/manual/en/function.gc-collect-cycles.php
 * @return int number of collected cycles.
 * @jms-builtin
 */
function gc_collect_cycles () {}

/**
 * (PHP 5 &gt;= 5.3.0)<br/>
 * Returns status of the circular reference collector
 * @link http://php.net/manual/en/function.gc-enabled.php
 * @return bool <b>TRUE</b> if the garbage collector is enabled, <b>FALSE</b> otherwise.
 * @jms-builtin
 */
function gc_enabled () {}

/**
 * (PHP 5 &gt;= 5.3.0)<br/>
 * Activates the circular reference collector
 * @link http://php.net/manual/en/function.gc-enable.php
 * @return void No value is returned.
 * @jms-builtin
 */
function gc_enable () {}

/**
 * (PHP 5 &gt;= 5.3.0)<br/>
 * Deactivates the circular reference collector
 * @link http://php.net/manual/en/function.gc-disable.php
 * @return void No value is returned.
 * @jms-builtin
 */
function gc_disable () {}


/**
 * Fatal run-time errors. These indicate errors that can not be
 * recovered from, such as a memory allocation problem.
 * Execution of the script is halted.
 * @link http://php.net/manual/en/errorfunc.constants.php
 */
define ('E_ERROR', 1);

/**
 * Catchable fatal error. It indicates that a probably dangerous error
 * occured, but did not leave the Engine in an unstable state. If the error
 * is not caught by a user defined handle (see also
 * <b>set_error_handler</b>), the application aborts as it
 * was an <b>E_ERROR</b>.
 * @link http://php.net/manual/en/errorfunc.constants.php
 */
define ('E_RECOVERABLE_ERROR', 4096);

/**
 * Run-time warnings (non-fatal errors). Execution of the script is not
 * halted.
 * @link http://php.net/manual/en/errorfunc.constants.php
 */
define ('E_WARNING', 2);

/**
 * Compile-time parse errors. Parse errors should only be generated by
 * the parser.
 * @link http://php.net/manual/en/errorfunc.constants.php
 */
define ('E_PARSE', 4);

/**
 * Run-time notices. Indicate that the script encountered something that
 * could indicate an error, but could also happen in the normal course of
 * running a script.
 * @link http://php.net/manual/en/errorfunc.constants.php
 */
define ('E_NOTICE', 8);

/**
 * Enable to have PHP suggest changes
 * to your code which will ensure the best interoperability
 * and forward compatibility of your code.
 * @link http://php.net/manual/en/errorfunc.constants.php
 */
define ('E_STRICT', 2048);

/**
 * Run-time notices. Enable this to receive warnings about code
 * that will not work in future versions.
 * @link http://php.net/manual/en/errorfunc.constants.php
 */
define ('E_DEPRECATED', 8192);

/**
 * Fatal errors that occur during PHP's initial startup. This is like an
 * <b>E_ERROR</b>, except it is generated by the core of PHP.
 * @link http://php.net/manual/en/errorfunc.constants.php
 */
define ('E_CORE_ERROR', 16);

/**
 * Warnings (non-fatal errors) that occur during PHP's initial startup.
 * This is like an <b>E_WARNING</b>, except it is generated
 * by the core of PHP.
 * @link http://php.net/manual/en/errorfunc.constants.php
 */
define ('E_CORE_WARNING', 32);

/**
 * Fatal compile-time errors. This is like an <b>E_ERROR</b>,
 * except it is generated by the Zend Scripting Engine.
 * @link http://php.net/manual/en/errorfunc.constants.php
 */
define ('E_COMPILE_ERROR', 64);

/**
 * Compile-time warnings (non-fatal errors). This is like an
 * <b>E_WARNING</b>, except it is generated by the Zend
 * Scripting Engine.
 * @link http://php.net/manual/en/errorfunc.constants.php
 */
define ('E_COMPILE_WARNING', 128);

/**
 * User-generated error message. This is like an
 * <b>E_ERROR</b>, except it is generated in PHP code by
 * using the PHP function <b>trigger_error</b>.
 * @link http://php.net/manual/en/errorfunc.constants.php
 */
define ('E_USER_ERROR', 256);

/**
 * User-generated warning message. This is like an
 * <b>E_WARNING</b>, except it is generated in PHP code by
 * using the PHP function <b>trigger_error</b>.
 * @link http://php.net/manual/en/errorfunc.constants.php
 */
define ('E_USER_WARNING', 512);

/**
 * User-generated notice message. This is like an
 * <b>E_NOTICE</b>, except it is generated in PHP code by
 * using the PHP function <b>trigger_error</b>.
 * @link http://php.net/manual/en/errorfunc.constants.php
 */
define ('E_USER_NOTICE', 1024);

/**
 * User-generated warning message. This is like an
 * <b>E_DEPRECATED</b>, except it is generated in PHP code by
 * using the PHP function <b>trigger_error</b>.
 * @link http://php.net/manual/en/errorfunc.constants.php
 */
define ('E_USER_DEPRECATED', 16384);

/**
 * All errors and warnings, as supported, except of level
 * <b>E_STRICT</b> prior to PHP 5.4.0.
 * @link http://php.net/manual/en/errorfunc.constants.php
 */
define ('E_ALL', 32767);
define ('DEBUG_BACKTRACE_PROVIDE_OBJECT', 1);
define ('DEBUG_BACKTRACE_IGNORE_ARGS', 2);
define ('TRUE', true);
define ('FALSE', false);
define ('NULL', null);
define ('ZEND_THREAD_SAFE', false);
define ('ZEND_DEBUG_BUILD', false);
define ('PHP_VERSION', "5.4.3-4~precise+1");
define ('PHP_MAJOR_VERSION', 5);
define ('PHP_MINOR_VERSION', 4);
define ('PHP_RELEASE_VERSION', 3);
define ('PHP_EXTRA_VERSION', "-4~precise+1");
define ('PHP_VERSION_ID', 50403);
define ('PHP_ZTS', 0);
define ('PHP_DEBUG', 0);
define ('PHP_OS', "Linux");
define ('PHP_SAPI', "cli");
define ('DEFAULT_INCLUDE_PATH', ".:/usr/share/php:/usr/share/pear");
define ('PEAR_INSTALL_DIR', "/usr/share/php");
define ('PEAR_EXTENSION_DIR', "/usr/lib/php5/20100525");
define ('PHP_EXTENSION_DIR', "/usr/lib/php5/20100525");
define ('PHP_PREFIX', "/usr");
define ('PHP_BINDIR', "/usr/bin");
define ('PHP_MANDIR', "/usr/share/man");
define ('PHP_LIBDIR', "/usr/lib/php5");
define ('PHP_DATADIR', "${prefix}/share");
define ('PHP_SYSCONFDIR', "/etc");
define ('PHP_LOCALSTATEDIR', "/var");
define ('PHP_CONFIG_FILE_PATH', "/etc/php5/cli");
define ('PHP_CONFIG_FILE_SCAN_DIR', "/etc/php5/cli/conf.d");
define ('PHP_SHLIB_SUFFIX', "so");
define ('PHP_EOL', "\n");
define ('PHP_MAXPATHLEN', 4096);
define ('PHP_INT_MAX', 9223372036854775807);
define ('PHP_INT_SIZE', 8);
define ('PHP_BINARY', "/usr/bin/php5");
define ('PHP_OUTPUT_HANDLER_START', 1);
define ('PHP_OUTPUT_HANDLER_WRITE', 0);
define ('PHP_OUTPUT_HANDLER_FLUSH', 4);
define ('PHP_OUTPUT_HANDLER_CLEAN', 2);
define ('PHP_OUTPUT_HANDLER_FINAL', 8);
define ('PHP_OUTPUT_HANDLER_CONT', 0);
define ('PHP_OUTPUT_HANDLER_END', 8);
define ('PHP_OUTPUT_HANDLER_CLEANABLE', 16);
define ('PHP_OUTPUT_HANDLER_FLUSHABLE', 32);
define ('PHP_OUTPUT_HANDLER_REMOVABLE', 64);
define ('PHP_OUTPUT_HANDLER_STDFLAGS', 112);
define ('PHP_OUTPUT_HANDLER_STARTED', 4096);
define ('PHP_OUTPUT_HANDLER_DISABLED', 8192);
define ('UPLOAD_ERR_OK', 0);
define ('UPLOAD_ERR_INI_SIZE', 1);
define ('UPLOAD_ERR_FORM_SIZE', 2);
define ('UPLOAD_ERR_PARTIAL', 3);
define ('UPLOAD_ERR_NO_FILE', 4);
define ('UPLOAD_ERR_NO_TMP_DIR', 6);
define ('UPLOAD_ERR_CANT_WRITE', 7);
define ('UPLOAD_ERR_EXTENSION', 8);
define ('STDIN', "Resource id #1");
define ('STDOUT', "Resource id #2");
define ('STDERR', "Resource id #3");

// End of Core v.5.4.3-4~precise+1
?>
