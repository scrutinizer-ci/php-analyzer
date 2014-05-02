<?php

// Start of aop v.0.3.0

/** @jms-builtin */
class AopJoinpoint  {

	public function &getArguments () {}

	public function getPropertyName () {}

	public function getPropertyValue () {}

	/**
	 * @param arguments
	 */
	public function setArguments (array $arguments) {}

	public function getKindOfAdvice () {}

	public function &getReturnedValue () {}

	public function &getAssignedValue () {}

	/**
	 * @param value
	 */
	public function setReturnedValue ($value) {}

	/**
	 * @param value
	 */
	public function setAssignedValue ($value) {}

	public function getPointcut () {}

	public function getObject () {}

	public function getClassName () {}

	public function getMethodName () {}

	public function getFunctionName () {}

	public function getException () {}

	public function process () {}

}

/**
 * @param pointcut
 * @param advice
 * @jms-builtin
 */
function aop_add_around ($pointcut, $advice) {}

/**
 * @param pointcut
 * @param advice
 * @jms-builtin
 */
function aop_add_before ($pointcut, $advice) {}

/**
 * @param pointcut
 * @param advice
 * @jms-builtin
 */
function aop_add_after ($pointcut, $advice) {}

/**
 * @param pointcut
 * @param advice
 * @jms-builtin
 */
function aop_add_after_returning ($pointcut, $advice) {}

/**
 * @param pointcut
 * @param advice
 * @jms-builtin
 */
function aop_add_after_throwing ($pointcut, $advice) {}

define ('AOP_KIND_BEFORE', 2);
define ('AOP_KIND_AFTER', 4);
define ('AOP_KIND_AROUND', 1);
define ('AOP_KIND_PROPERTY', 32);
define ('AOP_KIND_FUNCTION', 128);
define ('AOP_KIND_METHOD', 64);
define ('AOP_KIND_READ', 8);
define ('AOP_KIND_WRITE', 16);
define ('AOP_KIND_AROUND_WRITE_PROPERTY', 49);
define ('AOP_KIND_AROUND_READ_PROPERTY', 41);
define ('AOP_KIND_BEFORE_WRITE_PROPERTY', 50);
define ('AOP_KIND_BEFORE_READ_PROPERTY', 42);
define ('AOP_KIND_AFTER_WRITE_PROPERTY', 52);
define ('AOP_KIND_AFTER_READ_PROPERTY', 44);
define ('AOP_KIND_BEFORE_METHOD', 66);
define ('AOP_KIND_AFTER_METHOD', 68);
define ('AOP_KIND_AROUND_METHOD', 65);
define ('AOP_KIND_BEFORE_FUNCTION', 130);
define ('AOP_KIND_AFTER_FUNCTION', 132);
define ('AOP_KIND_AROUND_FUNCTION', 129);

// End of aop v.0.3.0
?>
