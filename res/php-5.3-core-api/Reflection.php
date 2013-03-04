<?php

// Start of Reflection v.$Revision: 1.5 $

/** @jms-builtin */
class ReflectionException extends Exception  {
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
class Reflection  {

	/**
	 * Gets modifier names
	 * @link http://www.php.net/manual/en/reflection.getmodifiernames.php
	 * @param modifiers int <p>
	 * The modifiers to get, which is from a numeric value.
	 * </p>
	 * @return array<string> An array of modifier names.
	 */
	public static function getModifierNames ($modifiers) {}

	/**
	 * Exports
	 * @link http://www.php.net/manual/en/reflection.export.php
	 * @param reflector Reflector <p>
	 * &reflection.export.param.name;
	 * </p>
	 * @param return string[optional] <p>
	 * &reflection.export.param.return;
	 * </p>
	 * @return void &reflection.export.return;
	 */
	public static function export ($reflector, $return = null) {}

}

interface Reflector  {

	/**
	 * Exports
	 * @link http://www.php.net/manual/en/reflector.export.php
	 * @return string
	 */
	abstract public static function export () {}

	/**
	 * To string
	 * @link http://www.php.net/manual/en/reflector.tostring.php
	 * @return string
	 */
	abstract public function __toString () {}

}

abstract class ReflectionFunctionAbstract implements Reflector {

	public $name;

	/**
	 * Clones function
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.clone.php
	 * @return void
	 */
	final private function __clone () {}

	/**
	 * To string
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.tostring.php
	 * @return void The string.
	 */
	abstract public function __toString () {}

	/**
	 * Checks if function in namespace
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.innamespace.php
	 * @return bool true if it's in a namespace, otherwise false
	 */
	public function inNamespace () {}

	/**
	 * Checks if closure
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.isclosure.php
	 * @return bool true if it's a closure, otherwise false
	 */
	public function isClosure () {}

	/**
	 * Checks if deprecated
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.isdeprecated.php
	 * @return bool true if it's deprecated, otherwise false
	 */
	public function isDeprecated () {}

	/**
	 * Checks if is internal
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.isinternal.php
	 * @return bool true if it's internal, otherwise false
	 */
	public function isInternal () {}

	/**
	 * Checks if user defined
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.isuserdefined.php
	 * @return bool true if it's user-defined, otherwise false;
	 */
	public function isUserDefined () {}

	/**
	 * Gets doc comment
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getdoccomment.php
	 * @return string The doc comment if it exists, otherwise false
	 */
	public function getDocComment () {}

	/**
	 * Gets end line number
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getendline.php
	 * @return int The ending line number of the user defined class, or false if unknown.
	 */
	public function getEndLine () {}

	/**
	 * Gets extension info
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getextension.php
	 * @return ReflectionExtension The extension information, as a ReflectionExtension object.
	 */
	public function getExtension () {}

	/**
	 * Gets extension name
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getextensionname.php
	 * @return string The extensions name.
	 */
	public function getExtensionName () {}

	/**
	 * Gets file name
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getfilename.php
	 * @return string The file name.
	 */
	public function getFileName () {}

	/**
	 * Gets function name
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getname.php
	 * @return string The name of the function.
	 */
	public function getName () {}

	/**
	 * Gets namespace name
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getnamespacename.php
	 * @return string The namespace name.
	 */
	public function getNamespaceName () {}

	/**
	 * Gets number of parameters
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getnumberofparameters.php
	 * @return int The number of parameters.
	 */
	public function getNumberOfParameters () {}

	/**
	 * Gets number of required parameters
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getnumberofrequiredparameters.php
	 * @return int The number of required parameters.
	 */
	public function getNumberOfRequiredParameters () {}

	/**
	 * Gets parameters
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getparameters.php
	 * @return array<ReflectionParameter> The parameters, as a ReflectionParameter object.
	 */
	public function getParameters () {}

	/**
	 * Gets function short name
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getshortname.php
	 * @return string The short name of the function.
	 */
	public function getShortName () {}

	/**
	 * Gets starting line number
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getstartline.php
	 * @return int The starting line number.
	 */
	public function getStartLine () {}

	/**
	 * Gets static variables
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getstaticvariables.php
	 * @return array<string> An array of static variables.
	 */
	public function getStaticVariables () {}

	/**
	 * Checks if returns reference
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.returnsreference.php
	 * @return bool true if it returns a reference, otherwise false
	 */
	public function returnsReference () {}

}

/** @jms-builtin */
class ReflectionFunction extends ReflectionFunctionAbstract implements Reflector {
	const IS_DEPRECATED = 262144;

	public $name;


	/**
	 * Constructs a ReflectionFunction object
	 * @link http://www.php.net/manual/en/reflectionfunction.construct.php
	 * @param name
	 */
	public function __construct ($name) {}

	/**
	 * To string
	 * @link http://www.php.net/manual/en/reflectionfunction.tostring.php
	 * @return string
	 */
	public function __toString () {}

	/**
	 * Exports function
	 * @link http://www.php.net/manual/en/reflectionfunction.export.php
	 * @param name string <p>
	 * &reflection.export.param.name;
	 * </p>
	 * @param return string[optional] <p>
	 * &reflection.export.param.return;
	 * </p>
	 * @return string &reflection.export.return;
	 */
	public static function export ($name, $return = null) {}

	/**
	 * Checks if function is disabled
	 * @link http://www.php.net/manual/en/reflectionfunction.isdisabled.php
	 * @return bool true if it's disable, otherwise false
	 */
	public function isDisabled () {}

	/**
	 * Invokes function
	 * @link http://www.php.net/manual/en/reflectionfunction.invoke.php
	 * @param mixed $args <p>
	 * The passed in argument list. It accepts a variable number of
	 * arguments which are passed to the function much like
	 * call_user_func is.
	 * </p>
	 * @return mixed
	 *
	 * @jms-variable-parameters
	 */
	public function invoke ($args = null) {}

	/**
	 * Invokes function args
	 * @link http://www.php.net/manual/en/reflectionfunction.invokeargs.php
	 * @param args array <p>
	 * The args to invoke.
	 * </p>
	 * @return mixed
	 */
	public function invokeArgs (array $args) {}

	/**
	 * Clones function
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.clone.php
	 * @return void
	 */
	final private function __clone () {}

	/**
	 * Checks if function in namespace
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.innamespace.php
	 * @return bool true if it's in a namespace, otherwise false
	 */
	public function inNamespace () {}

	/**
	 * Checks if closure
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.isclosure.php
	 * @return bool true if it's a closure, otherwise false
	 */
	public function isClosure () {}

	/**
	 * Checks if deprecated
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.isdeprecated.php
	 * @return bool true if it's deprecated, otherwise false
	 */
	public function isDeprecated () {}

	/**
	 * Checks if is internal
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.isinternal.php
	 * @return bool true if it's internal, otherwise false
	 */
	public function isInternal () {}

	/**
	 * Checks if user defined
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.isuserdefined.php
	 * @return bool true if it's user-defined, otherwise false;
	 */
	public function isUserDefined () {}

	/**
	 * Gets doc comment
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getdoccomment.php
	 * @return string The doc comment if it exists, otherwise false
	 */
	public function getDocComment () {}

	/**
	 * Gets end line number
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getendline.php
	 * @return int The ending line number of the user defined class, or false if unknown.
	 */
	public function getEndLine () {}

	/**
	 * Gets extension info
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getextension.php
	 * @return ReflectionExtension The extension information, as a ReflectionExtension object.
	 */
	public function getExtension () {}

	/**
	 * Gets extension name
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getextensionname.php
	 * @return string The extensions name.
	 */
	public function getExtensionName () {}

	/**
	 * Gets file name
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getfilename.php
	 * @return string The file name.
	 */
	public function getFileName () {}

	/**
	 * Gets function name
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getname.php
	 * @return string The name of the function.
	 */
	public function getName () {}

	/**
	 * Gets namespace name
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getnamespacename.php
	 * @return string The namespace name.
	 */
	public function getNamespaceName () {}

	/**
	 * Gets number of parameters
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getnumberofparameters.php
	 * @return int The number of parameters.
	 */
	public function getNumberOfParameters () {}

	/**
	 * Gets number of required parameters
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getnumberofrequiredparameters.php
	 * @return int The number of required parameters.
	 */
	public function getNumberOfRequiredParameters () {}

	/**
	 * Gets parameters
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getparameters.php
	 * @return array<ReflectionParameter> The parameters, as a ReflectionParameter object.
	 */
	public function getParameters () {}

	/**
	 * Gets function short name
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getshortname.php
	 * @return string The short name of the function.
	 */
	public function getShortName () {}

	/**
	 * Gets starting line number
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getstartline.php
	 * @return int The starting line number.
	 */
	public function getStartLine () {}

	/**
	 * Gets static variables
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getstaticvariables.php
	 * @return array<string> An array of static variables.
	 */
	public function getStaticVariables () {}

	/**
	 * Checks if returns reference
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.returnsreference.php
	 * @return bool true if it returns a reference, otherwise false
	 */
	public function returnsReference () {}

}

/** @jms-builtin */
class ReflectionParameter implements Reflector {
	public $name;


	/**
	 * Clone
	 * @link http://www.php.net/manual/en/reflectionparameter.clone.php
	 * @return void
	 */
	final private function __clone () {}

	/**
	 * Exports
	 * @link http://www.php.net/manual/en/reflectionparameter.export.php
	 * @param function string <p>
	 * The function name.
	 * </p>
	 * @param parameter string <p>
	 * The parameter name.
	 * </p>
	 * @param return bool[optional] <p>
	 * &reflection.export.param.return;
	 * </p>
	 * @return string The exported reflection.
	 */
	public static function export ($function, $parameter, $return = null) {}

	/**
	 * Construct
	 * @link http://www.php.net/manual/en/reflectionparameter.construct.php
	 * @param function
	 * @param parameter
	 */
	public function __construct ($function, $parameter) {}

	/**
	 * To string
	 * @link http://www.php.net/manual/en/reflectionparameter.tostring.php
	 * @return string
	 */
	public function __toString () {}

	/**
	 * Gets parameter name
	 * @link http://www.php.net/manual/en/reflectionparameter.getname.php
	 * @return string The name of the reflected parameter.
	 */
	public function getName () {}

	/**
	 * Checks if passed by reference
	 * @link http://www.php.net/manual/en/reflectionparameter.ispassedbyreference.php
	 * @return bool true if the parameter is passed in by reference, otherwise false
	 */
	public function isPassedByReference () {}

	/**
	 * Gets declaring function
	 * @link http://www.php.net/manual/en/reflectionparameter.getdeclaringfunction.php
	 * @return ReflectionFunction A ReflectionFunction object.
	 */
	public function getDeclaringFunction () {}

	/**
	 * Gets declaring class
	 * @link http://www.php.net/manual/en/reflectionparameter.getdeclaringclass.php
	 * @return ReflectionClass A ReflectionClass object.
	 */
	public function getDeclaringClass () {}

	/**
	 * Get class
	 * @link http://www.php.net/manual/en/reflectionparameter.getclass.php
	 * @return ReflectionClass A ReflectionClass object.
	 */
	public function getClass () {}

	/**
	 * Checks if parameter expects an array
	 * @link http://www.php.net/manual/en/reflectionparameter.isarray.php
	 * @return bool true if an array is expected, false otherwise.
	 */
	public function isArray () {}

	/**
	 * Checks if null is allowed
	 * @link http://www.php.net/manual/en/reflectionparameter.allowsnull.php
	 * @return bool true if &null; is allowed, otherwise false
	 */
	public function allowsNull () {}

	/**
	 * Gets parameter position
	 * @link http://www.php.net/manual/en/reflectionparameter.getposition.php
	 * @return int The position of the parameter, left to right, starting at position #0.
	 */
	public function getPosition () {}

	/**
	 * Checks if optional
	 * @link http://www.php.net/manual/en/reflectionparameter.isoptional.php
	 * @return bool true if the parameter is optional, otherwise false
	 */
	public function isOptional () {}

	/**
	 * Checks if a default value is available
	 * @link http://www.php.net/manual/en/reflectionparameter.isdefaultvalueavailable.php
	 * @return bool true if a default value is available, otherwise false
	 */
	public function isDefaultValueAvailable () {}

	/**
	 * Gets default parameter value
	 * @link http://www.php.net/manual/en/reflectionparameter.getdefaultvalue.php
	 * @return mixed The parameters default value.
	 */
	public function getDefaultValue () {}

}

/** @jms-builtin */
class ReflectionMethod extends ReflectionFunctionAbstract implements Reflector {
	const IS_STATIC = 1;
	const IS_PUBLIC = 256;
	const IS_PROTECTED = 512;
	const IS_PRIVATE = 1024;
	const IS_ABSTRACT = 2;
	const IS_FINAL = 4;

	public $name;
	public $class;


	/**
	 * Export
	 * @link http://www.php.net/manual/en/reflectionmethod.export.php
	 * @param class string <p>
	 * The class name.
	 * </p>
	 * @param name string <p>
	 * The name of the method.
	 * </p>
	 * @param return bool[optional] <p>
	 * &reflection.export.param.return;
	 * </p>
	 * @return string &reflection.export.return;
	 */
	public static function export ($class, $name, $return = null) {}

	/**
	 * Constructs a ReflectionMethod
	 * @link http://www.php.net/manual/en/reflectionmethod.construct.php
	 * @param class_or_method
	 * @param name[optional]
	 */
	public function __construct ($class_or_method, $name) {}

	/**
	 * To string
	 * @link http://www.php.net/manual/en/reflectionmethod.tostring.php
	 * @return string
	 */
	public function __toString () {}

	/**
	 * Checks if method is public
	 * @link http://www.php.net/manual/en/reflectionmethod.ispublic.php
	 * @return bool true if the method is public, otherwise false
	 */
	public function isPublic () {}

	/**
	 * Checks if method is private
	 * @link http://www.php.net/manual/en/reflectionmethod.isprivate.php
	 * @return bool true if the method is private, otherwise false
	 */
	public function isPrivate () {}

	/**
	 * Checks if method is protected
	 * @link http://www.php.net/manual/en/reflectionmethod.isprotected.php
	 * @return bool true if the method is protected, otherwise false
	 */
	public function isProtected () {}

	/**
	 * Checks if method is abstract
	 * @link http://www.php.net/manual/en/reflectionmethod.isabstract.php
	 * @return bool true if the method is abstract, otherwise false
	 */
	public function isAbstract () {}

	/**
	 * Checks if method is final
	 * @link http://www.php.net/manual/en/reflectionmethod.isfinal.php
	 * @return bool true if the method is final, otherwise false
	 */
	public function isFinal () {}

	/**
	 * Checks if method is static
	 * @link http://www.php.net/manual/en/reflectionmethod.isstatic.php
	 * @return bool true if the method is static, otherwise false
	 */
	public function isStatic () {}

	/**
	 * Checks if method is a constructor
	 * @link http://www.php.net/manual/en/reflectionmethod.isconstructor.php
	 * @return bool true if the method is a constructor, otherwise false
	 */
	public function isConstructor () {}

	/**
	 * Checks if method is a destructor
	 * @link http://www.php.net/manual/en/reflectionmethod.isdestructor.php
	 * @return bool true if the method is a destructor, otherwise false;
	 */
	public function isDestructor () {}

	/**
	 * Gets modifiers
	 * @link http://www.php.net/manual/en/reflectionmethod.getmodifiers.php
	 * @return int A numeric representation of the modifiers.
	 */
	public function getModifiers () {}

	/**
	 * Invoke
	 * @link http://www.php.net/manual/en/reflectionmethod.invoke.php
	 * @param object $object <p>
	 * The object to invoke.
	 * </p>
	 * @param mixed $args <p>
	 * The passed in argument list. It accepts a variable number of
	 * arguments which are passed to the function much like
	 * call_user_func is.
	 * </p>
	 * @return mixed
	 *
	 * @jms-variable-parameters
	 */
	public function invoke ($object, $args = null) {}

	/**
	 * Invoke args
	 * @link http://www.php.net/manual/en/reflectionmethod.invokeargs.php
	 * @param object $object <p>
	 * </p>
	 * @param array $args <p>
	 * </p>
	 * @return mixed
	 */
	public function invokeArgs ($object, array $args) {}

	/**
	 * Gets declaring class
	 * @link http://www.php.net/manual/en/reflectionmethod.getdeclaringclass.php
	 * @return ReflectionClass A ReflectionClass.
	 */
	public function getDeclaringClass () {}

	/**
	 * Gets prototype
	 * @link http://www.php.net/manual/en/reflectionmethod.getprototype.php
	 * @return void The prototype.
	 */
	public function getPrototype () {}

	/**
	 * Set method accessibility
	 * @link http://www.php.net/manual/en/reflectionmethod.setaccessible.php
	 * @param accessible bool <p>
	 * true to allow accessibility, or false.
	 * </p>
	 * @return void
	 */
	public function setAccessible ($accessible) {}

	/**
	 * Clones function
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.clone.php
	 * @return void
	 */
	final private function __clone () {}

	/**
	 * Checks if function in namespace
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.innamespace.php
	 * @return bool true if it's in a namespace, otherwise false
	 */
	public function inNamespace () {}

	/**
	 * Checks if closure
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.isclosure.php
	 * @return bool true if it's a closure, otherwise false
	 */
	public function isClosure () {}

	/**
	 * Checks if deprecated
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.isdeprecated.php
	 * @return bool true if it's deprecated, otherwise false
	 */
	public function isDeprecated () {}

	/**
	 * Checks if is internal
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.isinternal.php
	 * @return bool true if it's internal, otherwise false
	 */
	public function isInternal () {}

	/**
	 * Checks if user defined
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.isuserdefined.php
	 * @return bool true if it's user-defined, otherwise false;
	 */
	public function isUserDefined () {}

	/**
	 * Gets doc comment
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getdoccomment.php
	 * @return string The doc comment if it exists, otherwise false
	 */
	public function getDocComment () {}

	/**
	 * Gets end line number
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getendline.php
	 * @return int The ending line number of the user defined class, or false if unknown.
	 */
	public function getEndLine () {}

	/**
	 * Gets extension info
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getextension.php
	 * @return ReflectionExtension The extension information, as a ReflectionExtension object.
	 */
	public function getExtension () {}

	/**
	 * Gets extension name
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getextensionname.php
	 * @return string The extensions name.
	 */
	public function getExtensionName () {}

	/**
	 * Gets file name
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getfilename.php
	 * @return string The file name.
	 */
	public function getFileName () {}

	/**
	 * Gets function name
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getname.php
	 * @return string The name of the function.
	 */
	public function getName () {}

	/**
	 * Gets namespace name
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getnamespacename.php
	 * @return string The namespace name.
	 */
	public function getNamespaceName () {}

	/**
	 * Gets number of parameters
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getnumberofparameters.php
	 * @return int The number of parameters.
	 */
	public function getNumberOfParameters () {}

	/**
	 * Gets number of required parameters
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getnumberofrequiredparameters.php
	 * @return int The number of required parameters.
	 */
	public function getNumberOfRequiredParameters () {}

	/**
	 * Gets parameters
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getparameters.php
	 * @return array<ReflectionParameter> The parameters, as a ReflectionParameter object.
	 */
	public function getParameters () {}

	/**
	 * Gets function short name
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getshortname.php
	 * @return string The short name of the function.
	 */
	public function getShortName () {}

	/**
	 * Gets starting line number
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getstartline.php
	 * @return int The starting line number.
	 */
	public function getStartLine () {}

	/**
	 * Gets static variables
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.getstaticvariables.php
	 * @return array<string> An array of static variables.
	 */
	public function getStaticVariables () {}

	/**
	 * Checks if returns reference
	 * @link http://www.php.net/manual/en/reflectionfunctionabstract.returnsreference.php
	 * @return bool true if it returns a reference, otherwise false
	 */
	public function returnsReference () {}

}

/** @jms-builtin */
class ReflectionClass implements Reflector {
	const IS_IMPLICIT_ABSTRACT = 16;
	const IS_EXPLICIT_ABSTRACT = 32;
	const IS_FINAL = 64;

	public $name;


	/**
	 * Clones object
	 * @link http://www.php.net/manual/en/reflectionclass.clone.php
	 * @return void
	 */
	final private function __clone () {}

	/**
	 * Exports a class
	 * @link http://www.php.net/manual/en/reflectionclass.export.php
	 * @param argument mixed <p>
	 * &reflection.export.param.name;
	 * </p>
	 * @param return bool[optional] <p>
	 * &reflection.export.param.return;
	 * </p>
	 * @return string &reflection.export.return;
	 */
	public static function export ($argument, $return = null) {}

	/**
	 * Constructs a ReflectionClass
	 * @link http://www.php.net/manual/en/reflectionclass.construct.php
	 * @param argument
	 */
	public function __construct ($argument) {}

	/**
	 * To string
	 * @link http://www.php.net/manual/en/reflectionclass.tostring.php
	 * @return string
	 */
	public function __toString () {}

	/**
	 * Gets class name
	 * @link http://www.php.net/manual/en/reflectionclass.getname.php
	 * @return string The class name.
	 */
	public function getName () {}

	/**
	 * Checks if internal
	 * @link http://www.php.net/manual/en/reflectionclass.isinternal.php
	 * @return bool Returns true on success or false on failure.
	 */
	public function isInternal () {}

	/**
	 * Checks if user defined
	 * @link http://www.php.net/manual/en/reflectionclass.isuserdefined.php
	 * @return bool Returns true on success or false on failure.
	 */
	public function isUserDefined () {}

	/**
	 * Checks if instantiable
	 * @link http://www.php.net/manual/en/reflectionclass.isinstantiable.php
	 * @return bool Returns true on success or false on failure.
	 */
	public function isInstantiable () {}

	/**
	 * Gets a filename
	 * @link http://www.php.net/manual/en/reflectionclass.getfilename.php
	 * @return string The filename.
	 */
	public function getFileName () {}

	/**
	 * Gets starting line number
	 * @link http://www.php.net/manual/en/reflectionclass.getstartline.php
	 * @return int The starting line number, as an integer.
	 */
	public function getStartLine () {}

	/**
	 * Gets end line
	 * @link http://www.php.net/manual/en/reflectionclass.getendline.php
	 * @return int The ending line number of the user defined class, or false if unknown.
	 */
	public function getEndLine () {}

	/**
	 * Gets doc comments
	 * @link http://www.php.net/manual/en/reflectionclass.getdoccomment.php
	 * @return string The doc comment if it exists, otherwise false
	 */
	public function getDocComment () {}

	/**
	 * Gets constructor
	 * @link http://www.php.net/manual/en/reflectionclass.getconstructor.php
	 * @return object A ReflectionMethod object.
	 */
	public function getConstructor () {}

	/**
	 * Checks if method is defined
	 * @link http://www.php.net/manual/en/reflectionclass.hasmethod.php
	 * @param name string <p>
	 * Name of the method being checked for.
	 * </p>
	 * @return bool true if it has the method, otherwise false
	 */
	public function hasMethod ($name) {}

	/**
	 * Gets a ReflectionMethod
	 * @link http://www.php.net/manual/en/reflectionclass.getmethod.php
	 * @param name string <p>
	 * The method name to reflect.
	 * </p>
	 * @return object A ReflectionMethod.
	 */
	public function getMethod ($name) {}

	/**
	 * Gets a list of methods
	 * @link http://www.php.net/manual/en/reflectionclass.getmethods.php
	 * @param filter string[optional] <p>
	 * Any combination of ReflectionMethod::IS_STATIC,
	 * ReflectionMethod::IS_PUBLIC,
	 * ReflectionMethod::IS_PROTECTED,
	 * ReflectionMethod::IS_PRIVATE,
	 * ReflectionMethod::IS_ABSTRACT,
	 * ReflectionMethod::IS_FINAL.
	 * </p>
	 * @return array<ReflectionMethod> An array of methods.
	 */
	public function getMethods ($filter = null) {}

	/**
	 * Checks if property is defined
	 * @link http://www.php.net/manual/en/reflectionclass.hasproperty.php
	 * @param name string <p>
	 * Name of the property being checked for.
	 * </p>
	 * @return bool true if it has the property, otherwise false
	 */
	public function hasProperty ($name) {}

	/**
	 * Gets property
	 * @link http://www.php.net/manual/en/reflectionclass.getproperty.php
	 * @param name string <p>
	 * The property name.
	 * </p>
	 * @return ReflectionProperty A ReflectionProperty.
	 */
	public function getProperty ($name) {}

	/**
	 * Gets properties
	 * @link http://www.php.net/manual/en/reflectionclass.getproperties.php
	 * @param filter int[optional] <p>
	 * The optional filter, for filtering desired property types. It's configured using
	 * the ReflectionProperty constants,
	 * and defaults to all property types.
	 * </p>
	 * @return array<ReflectionProperty> An array of ReflectionProperty objects.
	 */
	public function getProperties ($filter = null) {}

	/**
	 * Checks if constant is defined
	 * @link http://www.php.net/manual/en/reflectionclass.hasconstant.php
	 * @param name string <p>
	 * The name of the constant being checked for.
	 * </p>
	 * @return bool true if the constant is defined, otherwise false.
	 */
	public function hasConstant ($name) {}

	/**
	 * Gets constants
	 * @link http://www.php.net/manual/en/reflectionclass.getconstants.php
	 * @return array<string,scalar> An array of constants.
	 */
	public function getConstants () {}

	/**
	 * Gets defined constants
	 * @link http://www.php.net/manual/en/reflectionclass.getconstant.php
	 * @param name string <p>
	 * </p>
	 * @return mixed
	 */
	public function getConstant ($name) {}

	/**
	 * Gets the interfaces
	 * @link http://www.php.net/manual/en/reflectionclass.getinterfaces.php
	 * @return array<string,ReflectionClass> An associative array of interfaces, with keys as interface
	 * names and the array values as ReflectionClass objects.
	 */
	public function getInterfaces () {}

	/**
	 * Gets the interface names
	 * @link http://www.php.net/manual/en/reflectionclass.getinterfacenames.php
	 * @return array<string> A numerical array with interface names as the values.
	 */
	public function getInterfaceNames () {}

	/**
	 * Checks if interface
	 * @link http://www.php.net/manual/en/reflectionclass.isinterface.php
	 * @return bool Returns true on success or false on failure.
	 */
	public function isInterface () {}

	/**
	 * Checks if class is abstract
	 * @link http://www.php.net/manual/en/reflectionclass.isabstract.php
	 * @return bool Returns true on success or false on failure.
	 */
	public function isAbstract () {}

	/**
	 * Checks if class is final
	 * @link http://www.php.net/manual/en/reflectionclass.isfinal.php
	 * @return bool Returns true on success or false on failure.
	 */
	public function isFinal () {}

	/**
	 * Gets modifiers
	 * @link http://www.php.net/manual/en/reflectionclass.getmodifiers.php
	 * @return int
	 */
	public function getModifiers () {}

	/**
	 * Checks class for instance
	 * @link http://www.php.net/manual/en/reflectionclass.isinstance.php
	 * @param object object <p>
	 * The object being compared to.
	 * </p>
	 * @return bool Returns true on success or false on failure.
	 */
	public function isInstance ($object) {}

	/**
	 * New instance
	 * @link http://www.php.net/manual/en/reflectionclass.newinstance.php
	 * @param args mixed <p>
	 * Accepts a variable number of arguments which are passed to the function
	 * much like call_user_func.
	 * </p>
	 * @param _ mixed[optional]
	 * @return object
	 */
	public function newInstance ($args, $_ = null) {}

	/**
	 * New instance args
	 * @link http://www.php.net/manual/en/reflectionclass.newinstanceargs.php
	 * @param args array[optional] <p>
	 * </p>
	 * @return object
	 */
	public function newInstanceArgs (array $args = null) {}

	/**
	 * Gets parent class
	 * @link http://www.php.net/manual/en/reflectionclass.getparentclass.php
	 * @return object A ReflectionClass.
	 */
	public function getParentClass () {}

	/**
	 * Checks if a subclass
	 * @link http://www.php.net/manual/en/reflectionclass.issubclassof.php
	 * @param class string <p>
	 * The class name being checked against.
	 * </p>
	 * @return bool Returns true on success or false on failure.
	 */
	public function isSubclassOf ($class) {}

	/**
	 * Gets static properties
	 * @link http://www.php.net/manual/en/reflectionclass.getstaticproperties.php
	 * @return array<string> The static properties, as an array.
	 */
	public function getStaticProperties () {}

	/**
	 * Gets static property value
	 * @link http://www.php.net/manual/en/reflectionclass.getstaticpropertyvalue.php
	 * @param name string <p>
	 * </p>
	 * @param default string[optional] <p>
	 * </p>
	 * @return mixed
	 */
	public function getStaticPropertyValue ($name, $default = null) {}

	/**
	 * Sets static property value
	 * @link http://www.php.net/manual/en/reflectionclass.setstaticpropertyvalue.php
	 * @param name string <p>
	 * Property name.
	 * </p>
	 * @param value string <p>
	 * New property value.
	 * </p>
	 * @return void
	 */
	public function setStaticPropertyValue ($name, $value) {}

	/**
	 * Gets default properties
	 * @link http://www.php.net/manual/en/reflectionclass.getdefaultproperties.php
	 * @return array<string,null|scalar|array> An array of default properties.
	 */
	public function getDefaultProperties () {}

	/**
	 * Checks if iterateable
	 * @link http://www.php.net/manual/en/reflectionclass.isiterateable.php
	 * @return bool Returns true on success or false on failure.
	 */
	public function isIterateable () {}

	/**
	 * Implements interface
	 * @link http://www.php.net/manual/en/reflectionclass.implementsinterface.php
	 * @param interface string <p>
	 * The interface name.
	 * </p>
	 * @return bool Returns true on success or false on failure.
	 */
	public function implementsInterface ($interface) {}

	/**
	 * Gets extension info
	 * @link http://www.php.net/manual/en/reflectionclass.getextension.php
	 * @return ReflectionExtension A ReflectionExtension object.
	 */
	public function getExtension () {}

	/**
	 * Gets an extensions name
	 * @link http://www.php.net/manual/en/reflectionclass.getextensionname.php
	 * @return string The extensions name.
	 */
	public function getExtensionName () {}

	/**
	 * Checks if in namespace
	 * @link http://www.php.net/manual/en/reflectionclass.innamespace.php
	 * @return bool Returns true on success or false on failure.
	 */
	public function inNamespace () {}

	/**
	 * Gets namespace name
	 * @link http://www.php.net/manual/en/reflectionclass.getnamespacename.php
	 * @return string The namespace name.
	 */
	public function getNamespaceName () {}

	/**
	 * Gets short name
	 * @link http://www.php.net/manual/en/reflectionclass.getshortname.php
	 * @return string The class short name.
	 */
	public function getShortName () {}

}

/** @jms-builtin */
class ReflectionObject extends ReflectionClass implements Reflector {
	const IS_IMPLICIT_ABSTRACT = 16;
	const IS_EXPLICIT_ABSTRACT = 32;
	const IS_FINAL = 64;

	public $name;


	/**
	 * Export
	 * @link http://www.php.net/manual/en/reflectionobject.export.php
	 * @param argument string <p>
	 * &reflection.export.param.name;
	 * </p>
	 * @param return bool[optional] <p>
	 * &reflection.export.param.return;
	 * </p>
	 * @return string &reflection.export.return;
	 */
	public static function export ($argument, $return = null) {}

	/**
	 * Constructs a ReflectionObject
	 * @link http://www.php.net/manual/en/reflectionobject.construct.php
	 * @param argument
	 */
	public function __construct ($argument) {}

	/**
	 * Clones object
	 * @link http://www.php.net/manual/en/reflectionclass.clone.php
	 * @return void
	 */
	final private function __clone () {}

	/**
	 * To string
	 * @link http://www.php.net/manual/en/reflectionclass.tostring.php
	 * @return string
	 */
	public function __toString () {}

	/**
	 * Gets class name
	 * @link http://www.php.net/manual/en/reflectionclass.getname.php
	 * @return string The class name.
	 */
	public function getName () {}

	/**
	 * Checks if internal
	 * @link http://www.php.net/manual/en/reflectionclass.isinternal.php
	 * @return bool Returns true on success or false on failure.
	 */
	public function isInternal () {}

	/**
	 * Checks if user defined
	 * @link http://www.php.net/manual/en/reflectionclass.isuserdefined.php
	 * @return bool Returns true on success or false on failure.
	 */
	public function isUserDefined () {}

	/**
	 * Checks if instantiable
	 * @link http://www.php.net/manual/en/reflectionclass.isinstantiable.php
	 * @return bool Returns true on success or false on failure.
	 */
	public function isInstantiable () {}

	/**
	 * Gets a filename
	 * @link http://www.php.net/manual/en/reflectionclass.getfilename.php
	 * @return string The filename.
	 */
	public function getFileName () {}

	/**
	 * Gets starting line number
	 * @link http://www.php.net/manual/en/reflectionclass.getstartline.php
	 * @return int The starting line number, as an integer.
	 */
	public function getStartLine () {}

	/**
	 * Gets end line
	 * @link http://www.php.net/manual/en/reflectionclass.getendline.php
	 * @return int The ending line number of the user defined class, or false if unknown.
	 */
	public function getEndLine () {}

	/**
	 * Gets doc comments
	 * @link http://www.php.net/manual/en/reflectionclass.getdoccomment.php
	 * @return string The doc comment if it exists, otherwise false
	 */
	public function getDocComment () {}

	/**
	 * Gets constructor
	 * @link http://www.php.net/manual/en/reflectionclass.getconstructor.php
	 * @return object A ReflectionMethod object.
	 */
	public function getConstructor () {}

	/**
	 * Checks if method is defined
	 * @link http://www.php.net/manual/en/reflectionclass.hasmethod.php
	 * @param name string <p>
	 * Name of the method being checked for.
	 * </p>
	 * @return bool true if it has the method, otherwise false
	 */
	public function hasMethod ($name) {}

	/**
	 * Gets a ReflectionMethod
	 * @link http://www.php.net/manual/en/reflectionclass.getmethod.php
	 * @param name string <p>
	 * The method name to reflect.
	 * </p>
	 * @return object A ReflectionMethod.
	 */
	public function getMethod ($name) {}

	/**
	 * Gets a list of methods
	 * @link http://www.php.net/manual/en/reflectionclass.getmethods.php
	 * @param filter string[optional] <p>
	 * Any combination of ReflectionMethod::IS_STATIC,
	 * ReflectionMethod::IS_PUBLIC,
	 * ReflectionMethod::IS_PROTECTED,
	 * ReflectionMethod::IS_PRIVATE,
	 * ReflectionMethod::IS_ABSTRACT,
	 * ReflectionMethod::IS_FINAL.
	 * </p>
	 * @return array<ReflectionMethod> An array of methods.
	 */
	public function getMethods ($filter = null) {}

	/**
	 * Checks if property is defined
	 * @link http://www.php.net/manual/en/reflectionclass.hasproperty.php
	 * @param name string <p>
	 * Name of the property being checked for.
	 * </p>
	 * @return bool true if it has the property, otherwise false
	 */
	public function hasProperty ($name) {}

	/**
	 * Gets property
	 * @link http://www.php.net/manual/en/reflectionclass.getproperty.php
	 * @param name string <p>
	 * The property name.
	 * </p>
	 * @return ReflectionProperty A ReflectionProperty.
	 */
	public function getProperty ($name) {}

	/**
	 * Gets properties
	 * @link http://www.php.net/manual/en/reflectionclass.getproperties.php
	 * @param filter int[optional] <p>
	 * The optional filter, for filtering desired property types. It's configured using
	 * the ReflectionProperty constants,
	 * and defaults to all property types.
	 * </p>
	 * @return array<ReflectionProperty> An array of ReflectionProperty objects.
	 */
	public function getProperties ($filter = null) {}

	/**
	 * Checks if constant is defined
	 * @link http://www.php.net/manual/en/reflectionclass.hasconstant.php
	 * @param name string <p>
	 * The name of the constant being checked for.
	 * </p>
	 * @return bool true if the constant is defined, otherwise false.
	 */
	public function hasConstant ($name) {}

	/**
	 * Gets constants
	 * @link http://www.php.net/manual/en/reflectionclass.getconstants.php
	 * @return array<string,scalar> An array of constants.
	 */
	public function getConstants () {}

	/**
	 * Gets defined constants
	 * @link http://www.php.net/manual/en/reflectionclass.getconstant.php
	 * @param name string <p>
	 * </p>
	 * @return mixed
	 */
	public function getConstant ($name) {}

	/**
	 * Gets the interfaces
	 * @link http://www.php.net/manual/en/reflectionclass.getinterfaces.php
	 * @return array<string,ReflectionClass> An associative array of interfaces, with keys as interface
	 * names and the array values as ReflectionClass objects.
	 */
	public function getInterfaces () {}

	/**
	 * Gets the interface names
	 * @link http://www.php.net/manual/en/reflectionclass.getinterfacenames.php
	 * @return array<string> A numerical array with interface names as the values.
	 */
	public function getInterfaceNames () {}

	/**
	 * Checks if interface
	 * @link http://www.php.net/manual/en/reflectionclass.isinterface.php
	 * @return bool Returns true on success or false on failure.
	 */
	public function isInterface () {}

	/**
	 * Checks if class is abstract
	 * @link http://www.php.net/manual/en/reflectionclass.isabstract.php
	 * @return bool Returns true on success or false on failure.
	 */
	public function isAbstract () {}

	/**
	 * Checks if class is final
	 * @link http://www.php.net/manual/en/reflectionclass.isfinal.php
	 * @return bool Returns true on success or false on failure.
	 */
	public function isFinal () {}

	/**
	 * Gets modifiers
	 * @link http://www.php.net/manual/en/reflectionclass.getmodifiers.php
	 * @return int
	 */
	public function getModifiers () {}

	/**
	 * Checks class for instance
	 * @link http://www.php.net/manual/en/reflectionclass.isinstance.php
	 * @param object object <p>
	 * The object being compared to.
	 * </p>
	 * @return bool Returns true on success or false on failure.
	 */
	public function isInstance ($object) {}

	/**
	 * New instance
	 * @link http://www.php.net/manual/en/reflectionclass.newinstance.php
	 * @param args mixed <p>
	 * Accepts a variable number of arguments which are passed to the function
	 * much like call_user_func.
	 * </p>
	 * @param _ mixed[optional]
	 * @return object
	 */
	public function newInstance ($args, $_ = null) {}

	/**
	 * New instance args
	 * @link http://www.php.net/manual/en/reflectionclass.newinstanceargs.php
	 * @param args array[optional] <p>
	 * </p>
	 * @return object
	 */
	public function newInstanceArgs (array $args = null) {}

	/**
	 * Gets parent class
	 * @link http://www.php.net/manual/en/reflectionclass.getparentclass.php
	 * @return object A ReflectionClass.
	 */
	public function getParentClass () {}

	/**
	 * Checks if a subclass
	 * @link http://www.php.net/manual/en/reflectionclass.issubclassof.php
	 * @param class string <p>
	 * The class name being checked against.
	 * </p>
	 * @return bool Returns true on success or false on failure.
	 */
	public function isSubclassOf ($class) {}

	/**
	 * Gets static properties
	 * @link http://www.php.net/manual/en/reflectionclass.getstaticproperties.php
	 * @return array<string> The static properties, as an array.
	 */
	public function getStaticProperties () {}

	/**
	 * Gets static property value
	 * @link http://www.php.net/manual/en/reflectionclass.getstaticpropertyvalue.php
	 * @param name string <p>
	 * </p>
	 * @param default string[optional] <p>
	 * </p>
	 * @return mixed
	 */
	public function getStaticPropertyValue ($name, $default = null) {}

	/**
	 * Sets static property value
	 * @link http://www.php.net/manual/en/reflectionclass.setstaticpropertyvalue.php
	 * @param name string <p>
	 * Property name.
	 * </p>
	 * @param value string <p>
	 * New property value.
	 * </p>
	 * @return void
	 */
	public function setStaticPropertyValue ($name, $value) {}

	/**
	 * Gets default properties
	 * @link http://www.php.net/manual/en/reflectionclass.getdefaultproperties.php
	 * @return array<string,null|scalar|array> An array of default properties.
	 */
	public function getDefaultProperties () {}

	/**
	 * Checks if iterateable
	 * @link http://www.php.net/manual/en/reflectionclass.isiterateable.php
	 * @return bool Returns true on success or false on failure.
	 */
	public function isIterateable () {}

	/**
	 * Implements interface
	 * @link http://www.php.net/manual/en/reflectionclass.implementsinterface.php
	 * @param interface string <p>
	 * The interface name.
	 * </p>
	 * @return bool Returns true on success or false on failure.
	 */
	public function implementsInterface ($interface) {}

	/**
	 * Gets extension info
	 * @link http://www.php.net/manual/en/reflectionclass.getextension.php
	 * @return ReflectionExtension A ReflectionExtension object.
	 */
	public function getExtension () {}

	/**
	 * Gets an extensions name
	 * @link http://www.php.net/manual/en/reflectionclass.getextensionname.php
	 * @return string The extensions name.
	 */
	public function getExtensionName () {}

	/**
	 * Checks if in namespace
	 * @link http://www.php.net/manual/en/reflectionclass.innamespace.php
	 * @return bool Returns true on success or false on failure.
	 */
	public function inNamespace () {}

	/**
	 * Gets namespace name
	 * @link http://www.php.net/manual/en/reflectionclass.getnamespacename.php
	 * @return string The namespace name.
	 */
	public function getNamespaceName () {}

	/**
	 * Gets short name
	 * @link http://www.php.net/manual/en/reflectionclass.getshortname.php
	 * @return string The class short name.
	 */
	public function getShortName () {}

}

/** @jms-builtin */
class ReflectionProperty implements Reflector {
	const IS_STATIC = 1;
	const IS_PUBLIC = 256;
	const IS_PROTECTED = 512;
	const IS_PRIVATE = 1024;

	public $name;
	public $class;


	/**
	 * Clone
	 * @link http://www.php.net/manual/en/reflectionproperty.clone.php
	 * @return void
	 */
	final private function __clone () {}

	/**
	 * Export
	 * @link http://www.php.net/manual/en/reflectionproperty.export.php
	 * @param class mixed
	 * @param name string <p>
	 * The property name.
	 * </p>
	 * @param return bool[optional] <p>
	 * &reflection.export.param.return;
	 * </p>
	 * @return string
	 */
	public static function export ($class, $name, $return = null) {}

	/**
	 * Construct a ReflectionProperty object
	 * @link http://www.php.net/manual/en/reflectionproperty.construct.php
	 * @param class[optional]
	 * @param name[optional]
	 */
	public function __construct ($class, $name) {}

	/**
	 * To string
	 * @link http://www.php.net/manual/en/reflectionproperty.tostring.php
	 * @return string
	 */
	public function __toString () {}

	/**
	 * Gets property name
	 * @link http://www.php.net/manual/en/reflectionproperty.getname.php
	 * @return string The name of the reflected property.
	 */
	public function getName () {}

	/**
	 * Gets value
	 * @link http://www.php.net/manual/en/reflectionproperty.getvalue.php
	 * @param object string[optional] <p>
	 * The object being reflected.
	 * </p>
	 * @return mixed The current value of the property.
	 */
	public function getValue ($object = null) {}

	/**
	 * Set property value
	 * @link http://www.php.net/manual/en/reflectionproperty.setvalue.php
	 * @param object object <p>
	 * The object name.
	 * </p>
	 * @param value mixed <p>
	 * The new value.
	 * </p>
	 * @return void
	 */
	public function setValue ($object, $value) {}

	/**
	 * Checks if property is public
	 * @link http://www.php.net/manual/en/reflectionproperty.ispublic.php
	 * @return bool true if the property is public, false otherwise.
	 */
	public function isPublic () {}

	/**
	 * Checks if property is private
	 * @link http://www.php.net/manual/en/reflectionproperty.isprivate.php
	 * @return bool true if the property is private, false otherwise.
	 */
	public function isPrivate () {}

	/**
	 * Checks if property is protected
	 * @link http://www.php.net/manual/en/reflectionproperty.isprotected.php
	 * @return bool true if the property is protected, false otherwise.
	 */
	public function isProtected () {}

	/**
	 * Checks if property is static
	 * @link http://www.php.net/manual/en/reflectionproperty.isstatic.php
	 * @return bool true if the property is static, false otherwise.
	 */
	public function isStatic () {}

	/**
	 * Checks if default value
	 * @link http://www.php.net/manual/en/reflectionproperty.isdefault.php
	 * @return bool true if the property was declared at compile-time, or false if
	 * it was created at run-time.
	 */
	public function isDefault () {}

	/**
	 * Gets modifiers
	 * @link http://www.php.net/manual/en/reflectionproperty.getmodifiers.php
	 * @return int A numeric representation of the modifiers.
	 */
	public function getModifiers () {}

	/**
	 * Gets declaring class
	 * @link http://www.php.net/manual/en/reflectionproperty.getdeclaringclass.php
	 * @return ReflectionClass A ReflectionClass object.
	 */
	public function getDeclaringClass () {}

	/**
	 * Gets doc comment
	 * @link http://www.php.net/manual/en/reflectionproperty.getdoccomment.php
	 * @return string The doc comment.
	 */
	public function getDocComment () {}

	/**
	 * Set property accessibility
	 * @link http://www.php.net/manual/en/reflectionproperty.setaccessible.php
	 * @param accessible bool <p>
	 * true to allow accessibility, or false.
	 * </p>
	 * @return void
	 */
	public function setAccessible ($accessible) {}

}

/** @jms-builtin */
class ReflectionExtension implements Reflector {
	public $name;


	/**
	 * Clones
	 * @link http://www.php.net/manual/en/reflectionextension.clone.php
	 * @return void
	 */
	final private function __clone () {}

	/**
	 * Export
	 * @link http://www.php.net/manual/en/reflectionextension.export.php
	 * @param name string <p>
	 * &reflection.export.param.name;
	 * </p>
	 * @param return string[optional] <p>
	 * &reflection.export.param.return;
	 * </p>
	 * @return string &reflection.export.return;
	 */
	public static function export ($name, $return = null) {}

	/**
	 * Constructs a ReflectionExtension
	 * @link http://www.php.net/manual/en/reflectionextension.construct.php
	 * @param name
	 */
	public function __construct ($name) {}

	/**
	 * To string
	 * @link http://www.php.net/manual/en/reflectionextension.tostring.php
	 * @return string A string.
	 */
	public function __toString () {}

	/**
	 * Gets extension name
	 * @link http://www.php.net/manual/en/reflectionextension.getname.php
	 * @return string The extensions name.
	 */
	public function getName () {}

	/**
	 * Gets extension version
	 * @link http://www.php.net/manual/en/reflectionextension.getversion.php
	 * @return string The version of the extension.
	 */
	public function getVersion () {}

	/**
	 * Gets extension functions
	 * @link http://www.php.net/manual/en/reflectionextension.getfunctions.php
	 * @return array<string,ReflectionFunction> An associative array of ReflectionFunction objects,
	 * for each function defined in the extension with the keys being the function
	 * names. If no function are defined, an empty array is returned.
	 */
	public function getFunctions () {}

	/**
	 * Gets constants
	 * @link http://www.php.net/manual/en/reflectionextension.getconstants.php
	 * @return array<string,scalar> An associative array with constant names as keys.
	 */
	public function getConstants () {}

	/**
	 * Gets extension ini entries
	 * @link http://www.php.net/manual/en/reflectionextension.getinientries.php
	 * @return array<string,scalar> An associative array with the ini entries as keys,
	 * with their defined values as values.
	 */
	public function getINIEntries () {}

	/**
	 * Gets classes
	 * @link http://www.php.net/manual/en/reflectionextension.getclasses.php
	 * @return array<string,ReflectionClass> An array of ReflectionClass objects, one
	 * for each class within the extension. If no classes are defined,
	 * an empty array is returned.
	 */
	public function getClasses () {}

	/**
	 * Gets class names
	 * @link http://www.php.net/manual/en/reflectionextension.getclassnames.php
	 * @return array<string> An array of class names, as defined in the extension.
	 * If no classes are defined, an empty array is returned.
	 */
	public function getClassNames () {}

	/**
	 * Gets dependencies
	 * @link http://www.php.net/manual/en/reflectionextension.getdependencies.php
	 * @return array An associative array with dependencies as keys and
	 * either Required or Conflicts
	 * as the values.
	 */
	public function getDependencies () {}

	/**
	 * Gets extension info
	 * @link http://www.php.net/manual/en/reflectionextension.info.php
	 * @return string Information about the extension.
	 */
	public function info () {}

}
// End of Reflection v.$Revision: 1.5 $
?>
