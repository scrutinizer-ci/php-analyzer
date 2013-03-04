<?php

// Start of com_dotnet v.0.1

/** @jms-builtin */
class COMPersistHelper  {

	public function __construct () {}

	public function GetCurFileName () {}

	public function SaveToFile () {}

	public function LoadFromFile () {}

	public function GetMaxStreamSize () {}

	public function InitNew () {}

	public function LoadFromStream () {}

	public function SaveToStream () {}

}

/** @jms-builtin */
class com_exception extends Exception  {
	protected $message;
	protected $code;
	protected $file;
	protected $line;


	final private function __clone () {}

	/**
	 * @param message[optional]
	 * @param code[optional]
	 */
	public function __construct ($message, $code) {}

	final public function getMessage () {}

	final public function getCode () {}

	final public function getFile () {}

	final public function getLine () {}

	final public function getTrace () {}

	final public function getTraceAsString () {}

	public function __toString () {}

}

/** @jms-builtin */
class com_safearray_proxy  {
}

/** @jms-builtin */
class variant  {
}

/** @jms-builtin */
class com extends variant  {
}

/** @jms-builtin */
class dotnet extends variant  {
}

/**
 * Assigns a new value for a variant object
 * @link http://www.php.net/manual/en/function.variant-set.php
 * @param variant variant <p>
 * The variant.
 * </p>
 * @param value mixed <p>
 * </p>
 * @return void 
 *
 * @jms-builtin
 */
function variant_set (variant $variant, $value) {}

/**
 * "Adds" two variant values together and returns the result
 * @link http://www.php.net/manual/en/function.variant-add.php
 * @param left mixed <p>
 * The left operand.
 * </p>
 * @param right mixed <p>
 * The right operand.
 * </p>
 * @return mixed the result.
 *
 * @jms-builtin
 */
function variant_add ($left, $right) {}

/**
 * concatenates two variant values together and returns the result
 * @link http://www.php.net/manual/en/function.variant-cat.php
 * @param left mixed <p>
 * The left operand.
 * </p>
 * @param right mixed <p>
 * The right operand.
 * </p>
 * @return mixed the result of the concatenation.
 *
 * @jms-builtin
 */
function variant_cat ($left, $right) {}

/**
 * Subtracts the value of the right variant from the left variant value
 * @link http://www.php.net/manual/en/function.variant-sub.php
 * @param left mixed <p>
 * The left operand.
 * </p>
 * @param right mixed <p>
 * The right operand.
 * </p>
 * @return mixed <table>
 * Variant Subtraction Rules
 * <tr valign="top">
 * <td>If</td>
 * <td>Then</td>
 * </tr>
 * <tr valign="top">
 * <td>Both expressions are of the string type</td>
 * <td>Subtraction</td>
 * </tr>
 * <tr valign="top">
 * <td>One expression is a string type and the other a
 * character</td>
 * <td>Subtraction</td>
 * </tr>
 * <tr valign="top">
 * <td>One expression is numeric and the other is a string</td>
 * <td>Subtraction.</td>
 * </tr>
 * <tr valign="top">
 * <td>Both expressions are numeric</td>
 * <td>Subtraction</td>
 * </tr>
 * <tr valign="top">
 * <td>Either expression is NULL</td>
 * <td>NULL is returned</td>
 * </tr>
 * <tr valign="top">
 * <td>Both expressions are empty</td>
 * <td>Empty string is returned</td>
 * </tr>
 * </table>
 *
 * @jms-builtin
 */
function variant_sub ($left, $right) {}

/**
 * Multiplies the values of the two variants
 * @link http://www.php.net/manual/en/function.variant-mul.php
 * @param left mixed <p>
 * The left operand.
 * </p>
 * @param right mixed <p>
 * The right operand.
 * </p>
 * @return mixed <table>
 * Variant Multiplication Rules
 * <tr valign="top">
 * <td>If</td>
 * <td>Then</td>
 * </tr>
 * <tr valign="top">
 * <td>Both expressions are of the string, date, character, boolean type</td>
 * <td>Multiplication</td>
 * </tr>
 * <tr valign="top">
 * <td>One expression is a string type and the other a
 * character</td>
 * <td>Multiplication</td>
 * </tr>
 * <tr valign="top">
 * <td>One expression is numeric and the other is a string</td>
 * <td>Multiplication</td>
 * </tr>
 * <tr valign="top">
 * <td>Both expressions are numeric</td>
 * <td>Multiplication</td>
 * </tr>
 * <tr valign="top">
 * <td>Either expression is NULL</td>
 * <td>NULL is returned</td>
 * </tr>
 * <tr valign="top">
 * <td>Both expressions are empty</td>
 * <td>Empty string is returned</td>
 * </tr>
 * </table>
 *
 * @jms-builtin
 */
function variant_mul ($left, $right) {}

/**
 * Performs a bitwise AND operation between two variants
 * @link http://www.php.net/manual/en/function.variant-and.php
 * @param left mixed <p>
 * The left operand.
 * </p>
 * @param right mixed <p>
 * The right operand.
 * </p>
 * @return mixed <table>
 * Variant AND Rules
 * <tr valign="top">
 * <td>If left is</td>
 * <td>If right is</td>
 * <td>then the result is</td>
 * </tr>
 * <tr valign="top"><td>true</td><td>true</td><td>true</td></tr>
 * <tr valign="top"><td>true</td><td>false</td><td>false</td></tr>
 * <tr valign="top"><td>true</td><td>&null;</td><td>&null;</td></tr>
 * <tr valign="top"><td>false</td><td>true</td><td>false</td></tr>
 * <tr valign="top"><td>false</td><td>false</td><td>false</td></tr>
 * <tr valign="top"><td>false</td><td>&null;</td><td>false</td></tr>
 * <tr valign="top"><td>&null;</td><td>true</td><td>&null;</td></tr>
 * <tr valign="top"><td>&null;</td><td>false</td><td>false</td></tr>
 * <tr valign="top"><td>&null;</td><td>&null;</td><td>&null;</td></tr>
 * </table>
 *
 * @jms-builtin
 */
function variant_and ($left, $right) {}

/**
 * Returns the result from dividing two variants
 * @link http://www.php.net/manual/en/function.variant-div.php
 * @param left mixed <p>
 * The left operand.
 * </p>
 * @param right mixed <p>
 * The right operand.
 * </p>
 * @return mixed <table>
 * Variant Division Rules
 * <tr valign="top">
 * <td>If</td>
 * <td>Then</td>
 * </tr>
 * <tr valign="top">
 * <td>Both expressions are of the string, date, character, boolean type</td>
 * <td>Double is returned</td>
 * </tr>
 * <tr valign="top">
 * <td>One expression is a string type and the other a
 * character</td>
 * <td>Division and a double is returned</td>
 * </tr>
 * <tr valign="top">
 * <td>One expression is numeric and the other is a string</td>
 * <td>Division and a double is returned.</td>
 * </tr>
 * <tr valign="top">
 * <td>Both expressions are numeric</td>
 * <td>Division and a double is returned</td>
 * </tr>
 * <tr valign="top">
 * <td>Either expression is NULL</td>
 * <td>NULL is returned</td>
 * </tr>
 * <tr valign="top">
 * <td>right is empty and
 * left is anything but empty</td>
 * <td>A com_exception with code DISP_E_DIVBYZERO
 * is thrown</td>
 * </tr>
 * <tr valign="top">
 * <td>left is empty and
 * right is anything but empty.</td>
 * <td>0 as type double is returned</td>
 * </tr>
 * <tr valign="top">
 * <td>Both expressions are empty</td>
 * <td>A com_exception with code DISP_E_OVERFLOW
 * is thrown</td>
 * </tr>
 * </table>
 *
 * @jms-builtin
 */
function variant_div ($left, $right) {}

/**
 * Performs a bitwise equivalence on two variants
 * @link http://www.php.net/manual/en/function.variant-eqv.php
 * @param left mixed <p>
 * The left operand.
 * </p>
 * @param right mixed <p>
 * The right operand.
 * </p>
 * @return mixed If each bit in left is equal to the corresponding
 * bit in right then true is returned, otherwise
 * false is returned.
 *
 * @jms-builtin
 */
function variant_eqv ($left, $right) {}

/**
 * Converts variants to integers and then returns the result from dividing them
 * @link http://www.php.net/manual/en/function.variant-idiv.php
 * @param left mixed <p>
 * The left operand.
 * </p>
 * @param right mixed <p>
 * The right operand.
 * </p>
 * @return mixed <table>
 * Variant Integer Division Rules
 * <tr valign="top">
 * <td>If</td>
 * <td>Then</td>
 * </tr>
 * <tr valign="top">
 * <td>Both expressions are of the string, date, character, boolean type</td>
 * <td>Division and integer is returned</td>
 * </tr>
 * <tr valign="top">
 * <td>One expression is a string type and the other a
 * character</td>
 * <td>Division</td>
 * </tr>
 * <tr valign="top">
 * <td>One expression is numeric and the other is a string</td>
 * <td>Division</td>
 * </tr>
 * <tr valign="top">
 * <td>Both expressions are numeric</td>
 * <td>Division</td>
 * </tr>
 * <tr valign="top">
 * <td>Either expression is NULL</td>
 * <td>NULL is returned</td>
 * </tr>
 * <tr valign="top">
 * <td>Both expressions are empty</td>
 * <td>A com_exception with code DISP_E_DIVBYZERO
 * is thrown</td>
 * </tr>
 * </table>
 *
 * @jms-builtin
 */
function variant_idiv ($left, $right) {}

/**
 * Performs a bitwise implication on two variants
 * @link http://www.php.net/manual/en/function.variant-imp.php
 * @param left mixed <p>
 * The left operand.
 * </p>
 * @param right mixed <p>
 * The right operand.
 * </p>
 * @return mixed <table>
 * Variant Implication Table
 * <tr valign="top">
 * <td>If left is</td>
 * <td>If right is</td>
 * <td>then the result is</td>
 * </tr>
 * <tr valign="top"><td>true</td><td>true</td><td>true</td></tr>
 * <tr valign="top"><td>true</td><td>false</td><td>true</td></tr>
 * <tr valign="top"><td>true</td><td>&null;</td><td>true</td></tr>
 * <tr valign="top"><td>false</td><td>true</td><td>true</td></tr>
 * <tr valign="top"><td>false</td><td>false</td><td>true</td></tr>
 * <tr valign="top"><td>false</td><td>&null;</td><td>true</td></tr>
 * <tr valign="top"><td>&null;</td><td>true</td><td>true</td></tr>
 * <tr valign="top"><td>&null;</td><td>false</td><td>&null;</td></tr>
 * <tr valign="top"><td>&null;</td><td>&null;</td><td>&null;</td></tr>
 * </table>
 *
 * @jms-builtin
 */
function variant_imp ($left, $right) {}

/**
 * Divides two variants and returns only the remainder
 * @link http://www.php.net/manual/en/function.variant-mod.php
 * @param left mixed <p>
 * The left operand.
 * </p>
 * @param right mixed <p>
 * The right operand.
 * </p>
 * @return mixed the remainder of the division.
 *
 * @jms-builtin
 */
function variant_mod ($left, $right) {}

/**
 * Performs a logical disjunction on two variants
 * @link http://www.php.net/manual/en/function.variant-or.php
 * @param left mixed <p>
 * The left operand.
 * </p>
 * @param right mixed <p>
 * The right operand.
 * </p>
 * @return mixed <table>
 * Variant OR Rules
 * <tr valign="top">
 * <td>If left is</td>
 * <td>If right is</td>
 * <td>then the result is</td>
 * </tr>
 * <tr valign="top"><td>true</td><td>true</td><td>true</td></tr>
 * <tr valign="top"><td>true</td><td>false</td><td>true</td></tr>
 * <tr valign="top"><td>true</td><td>&null;</td><td>true</td></tr>
 * <tr valign="top"><td>false</td><td>true</td><td>true</td></tr>
 * <tr valign="top"><td>false</td><td>false</td><td>false</td></tr>
 * <tr valign="top"><td>false</td><td>&null;</td><td>&null;</td></tr>
 * <tr valign="top"><td>&null;</td><td>true</td><td>true</td></tr>
 * <tr valign="top"><td>&null;</td><td>false</td><td>&null;</td></tr>
 * <tr valign="top"><td>&null;</td><td>&null;</td><td>&null;</td></tr>
 * </table>
 *
 * @jms-builtin
 */
function variant_or ($left, $right) {}

/**
 * Returns the result of performing the power function with two variants
 * @link http://www.php.net/manual/en/function.variant-pow.php
 * @param left mixed <p>
 * The left operand.
 * </p>
 * @param right mixed <p>
 * The right operand.
 * </p>
 * @return mixed the result of left to the power of
 * right.
 *
 * @jms-builtin
 */
function variant_pow ($left, $right) {}

/**
 * Performs a logical exclusion on two variants
 * @link http://www.php.net/manual/en/function.variant-xor.php
 * @param left mixed <p>
 * The left operand.
 * </p>
 * @param right mixed <p>
 * The right operand.
 * </p>
 * @return mixed <table>
 * Variant XOR Rules
 * <tr valign="top">
 * <td>If left is</td>
 * <td>If right is</td>
 * <td>then the result is</td>
 * </tr>
 * <tr valign="top"><td>true</td><td>true</td><td>false</td></tr>
 * <tr valign="top"><td>true</td><td>false</td><td>true</td></tr>
 * <tr valign="top"><td>false</td><td>true</td><td>true</td></tr>
 * <tr valign="top"><td>false</td><td>false</td><td>false</td></tr>
 * <tr valign="top"><td>&null;</td><td>&null;</td><td>&null;</td></tr>
 * </table>
 *
 * @jms-builtin
 */
function variant_xor ($left, $right) {}

/**
 * Returns the absolute value of a variant
 * @link http://www.php.net/manual/en/function.variant-abs.php
 * @param val mixed <p>
 * The variant.
 * </p>
 * @return mixed the absolute value of val.
 *
 * @jms-builtin
 */
function variant_abs ($val) {}

/**
 * Returns the integer portion of a variant
 * @link http://www.php.net/manual/en/function.variant-fix.php
 * @param variant mixed <p>
 * The variant.
 * </p>
 * @return mixed If variant is negative, then the first negative
 * integer greater than or equal to the variant is returned, otherwise
 * returns the integer portion of the value of
 * variant.
 *
 * @jms-builtin
 */
function variant_fix ($variant) {}

/**
 * Returns the integer portion of a variant
 * @link http://www.php.net/manual/en/function.variant-int.php
 * @param variant mixed <p>
 * The variant.
 * </p>
 * @return mixed If variant is negative, then the first negative
 * integer greater than or equal to the variant is returned, otherwise
 * returns the integer portion of the value of
 * variant.
 *
 * @jms-builtin
 */
function variant_int ($variant) {}

/**
 * Performs logical negation on a variant
 * @link http://www.php.net/manual/en/function.variant-neg.php
 * @param variant mixed <p>
 * The variant.
 * </p>
 * @return mixed the result of the logical negation.
 *
 * @jms-builtin
 */
function variant_neg ($variant) {}

/**
 * Performs bitwise not negation on a variant
 * @link http://www.php.net/manual/en/function.variant-not.php
 * @param variant mixed <p>
 * The variant.
 * </p>
 * @return mixed the bitwise not negation. If variant is
 * &null;, the result will also be &null;.
 *
 * @jms-builtin
 */
function variant_not ($variant) {}

/**
 * Rounds a variant to the specified number of decimal places
 * @link http://www.php.net/manual/en/function.variant-round.php
 * @param variant mixed <p>
 * The variant.
 * </p>
 * @param decimals int <p>
 * Number of decimal places.
 * </p>
 * @return mixed the rounded value.
 *
 * @jms-builtin
 */
function variant_round ($variant, $decimals) {}

/**
 * Compares two variants
 * @link http://www.php.net/manual/en/function.variant-cmp.php
 * @param left mixed <p>
 * The left operand.
 * </p>
 * @param right mixed <p>
 * The right operand.
 * </p>
 * @param lcid int[optional] <p>
 * A valid Locale Identifier to use when comparing strings (this affects
 * string collation).
 * </p>
 * @param flags int[optional] <p>
 * flags can be one or more of the following values
 * OR'd together, and affects string comparisons:
 * <table>
 * Variant Comparision Flags
 * <tr valign="top">
 * <td>value</td>
 * <td>meaning</td>
 * </tr>
 * <tr valign="top">
 * <td>NORM_IGNORECASE</td>
 * <td>Compare case insensitively</td>
 * </tr>
 * <tr valign="top">
 * <td>NORM_IGNORENONSPACE</td>
 * <td>Ignore nonspacing characters</td>
 * </tr>
 * <tr valign="top">
 * <td>NORM_IGNORESYMBOLS</td>
 * <td>Ignore symbols</td>
 * </tr>
 * <tr valign="top">
 * <td>NORM_IGNOREWIDTH</td>
 * <td>Ignore string width</td>
 * </tr>
 * <tr valign="top">
 * <td>NORM_IGNOREKANATYPE</td>
 * <td>Ignore Kana type</td>
 * </tr>
 * <tr valign="top">
 * <td>NORM_IGNOREKASHIDA</td>
 * <td>Ignore Arabic kashida characters</td>
 * </tr>
 * </table>
 * </p>
 * @return int one of the following:
 * <table>
 * Variant Comparision Results
 * <tr valign="top">
 * <td>value</td>
 * <td>meaning</td>
 * </tr>
 * <tr valign="top">
 * <td>VARCMP_LT</td>
 * <td>left is less than
 * right
 * </td>
 * </tr>
 * <tr valign="top">
 * <td>VARCMP_EQ</td>
 * <td>left is equal to
 * right
 * </td>
 * </tr>
 * <tr valign="top">
 * <td>VARCMP_GT</td>
 * <td>left is greater than
 * right
 * </td>
 * </tr>
 * <tr valign="top">
 * <td>VARCMP_NULL</td>
 * <td>Either left,
 * right or both are &null;
 * </td>
 * </tr>
 * </table>
 *
 * @jms-builtin
 */
function variant_cmp ($left, $right, $lcid = null, $flags = null) {}

/**
 * Converts a variant date/time value to Unix timestamp
 * @link http://www.php.net/manual/en/function.variant-date-to-timestamp.php
 * @param variant variant <p>
 * The variant.
 * </p>
 * @return int a unix timestamp.
 *
 * @jms-builtin
 */
function variant_date_to_timestamp (variant $variant) {}

/**
 * Returns a variant date representation of a Unix timestamp
 * @link http://www.php.net/manual/en/function.variant-date-from-timestamp.php
 * @param timestamp int <p>
 * A unix timestamp.
 * </p>
 * @return variant a VT_DATE variant.
 *
 * @jms-builtin
 */
function variant_date_from_timestamp ($timestamp) {}

/**
 * Returns the type of a variant object
 * @link http://www.php.net/manual/en/function.variant-get-type.php
 * @param variant variant <p>
 * The variant object.
 * </p>
 * @return int This function returns an integer value that indicates the type of
 * variant, which can be an instance of
 * , or
 * classes. The return value can be compared
 * to one of the VT_XXX constants.
 * </p>
 * <p>
 * The return value for COM and DOTNET objects will usually be
 * VT_DISPATCH; the only reason this function works for
 * those classes is because COM and DOTNET are descendants of VARIANT.
 * </p>
 * <p>
 * In PHP versions prior to 5, you could obtain this information from
 * instances of the VARIANT class ONLY, by reading a fake
 * type property. See the class for more information on
 * this.
 *
 * @jms-builtin
 */
function variant_get_type (variant $variant) {}

/**
 * Convert a variant into another type "in-place"
 * @link http://www.php.net/manual/en/function.variant-set-type.php
 * @param variant variant <p>
 * The variant.
 * </p>
 * @param type int <p>
 * </p>
 * @return void 
 *
 * @jms-builtin
 */
function variant_set_type (variant $variant, $type) {}

/**
 * Convert a variant into a new variant object of another type
 * @link http://www.php.net/manual/en/function.variant-cast.php
 * @param variant variant <p>
 * The variant.
 * </p>
 * @param type int <p>
 * type should be one of the
 * VT_XXX constants.
 * </p>
 * @return variant a VT_DATE variant.
 *
 * @jms-builtin
 */
function variant_cast (variant $variant, $type) {}

/**
 * Generate a globally unique identifier (GUID)
 * @link http://www.php.net/manual/en/function.com-create-guid.php
 * @return string the GUID as a string.
 *
 * @jms-builtin
 */
function com_create_guid () {}

/**
 * Connect events from a COM object to a PHP object
 * @link http://www.php.net/manual/en/function.com-event-sink.php
 * @param comobject variant <p>
 * </p>
 * @param sinkobject object <p>
 * sinkobject should be an instance of a class with
 * methods named after those of the desired dispinterface; you may use
 * com_print_typeinfo to help generate a template class
 * for this purpose.
 * </p>
 * @param sinkinterface mixed[optional] <p>
 * PHP will attempt to use the default dispinterface type specified by
 * the typelibrary associated with comobject, but
 * you may override this choice by setting
 * sinkinterface to the name of the dispinterface
 * that you want to use.
 * </p>
 * @return bool Returns true on success or false on failure.
 *
 * @jms-builtin
 */
function com_event_sink (variant $comobject, $sinkobject, $sinkinterface = null) {}

/**
 * Print out a PHP class definition for a dispatchable interface
 * @link http://www.php.net/manual/en/function.com-print-typeinfo.php
 * @param comobject object <p>
 * comobject should be either an instance of a COM
 * object, or be the name of a typelibrary (which will be resolved according
 * to the rules set out in com_load_typelib).
 * </p>
 * @param dispinterface string[optional] <p>
 * The name of an IDispatch descendant interface that you want to display.
 * </p>
 * @param wantsink bool[optional] <p>
 * If set to true, the corresponding sink interface will be displayed
 * instead.
 * </p>
 * @return bool Returns true on success or false on failure.
 *
 * @jms-builtin
 */
function com_print_typeinfo ($comobject, $dispinterface = null, $wantsink = null) {}

/**
 * Process COM messages, sleeping for up to timeoutms milliseconds
 * @link http://www.php.net/manual/en/function.com-message-pump.php
 * @param timeoutms int[optional] <p>
 * The timeout, in milliseconds.
 * </p>
 * <p>
 * If you do not specify a value for timeoutms,
 * then 0 will be assumed. A 0 value means that no waiting will be
 * performed; if there are messages pending they will be dispatched as
 * before; if there are no messages pending, the function will return
 * false immediately without sleeping.
 * </p>
 * @return bool If a message or messages arrives before the timeout, they will be
 * dispatched, and the function will return true. If the timeout occurs and
 * no messages were processed, the return value will be false.
 *
 * @jms-builtin
 */
function com_message_pump ($timeoutms = null) {}

/**
 * Loads a Typelib
 * @link http://www.php.net/manual/en/function.com-load-typelib.php
 * @param typelib_name string <p>
 * typelib_name can be one of the following:
 * <p>
 * The filename of a .tlb file or the executable module
 * that contains the type library.
 * </p>
 * @param case_insensitive bool[optional] <p>
 * The case_insensitive behaves in the same way as
 * the parameter with the same name in the define
 * function.
 * </p>
 * @return bool Returns true on success or false on failure.
 *
 * @jms-builtin
 */
function com_load_typelib ($typelib_name, $case_insensitive = null) {}

/**
 * Returns a handle to an already running instance of a COM object
 * @link http://www.php.net/manual/en/function.com-get-active-object.php
 * @param progid string <p>
 * progid must be either the ProgID or CLSID for
 * the object that you want to access (for example
 * Word.Application).
 * </p>
 * @param code_page int[optional] <p>
 * Acts in precisely the same way that it does for the class.
 * </p>
 * @return variant If the requested object is running, it will be returned to your script
 * just like any other COM object.
 *
 * @jms-builtin
 */
function com_get_active_object ($progid, $code_page = null) {}

define ('CLSCTX_INPROC_SERVER', 1);
define ('CLSCTX_INPROC_HANDLER', 2);
define ('CLSCTX_LOCAL_SERVER', 4);
define ('CLSCTX_REMOTE_SERVER', 16);
define ('CLSCTX_SERVER', 21);
define ('CLSCTX_ALL', 23);
define ('VT_NULL', 1);
define ('VT_EMPTY', 0);
define ('VT_UI1', 17);
define ('VT_I1', 16);
define ('VT_UI2', 18);
define ('VT_I2', 2);
define ('VT_UI4', 19);
define ('VT_I4', 3);
define ('VT_R4', 4);
define ('VT_R8', 5);
define ('VT_BOOL', 11);
define ('VT_ERROR', 10);
define ('VT_CY', 6);
define ('VT_DATE', 7);
define ('VT_BSTR', 8);
define ('VT_DECIMAL', 14);
define ('VT_UNKNOWN', 13);
define ('VT_DISPATCH', 9);
define ('VT_VARIANT', 12);
define ('VT_INT', 22);
define ('VT_UINT', 23);
define ('VT_ARRAY', 8192);
define ('VT_BYREF', 16384);
define ('CP_ACP', 0);
define ('CP_MACCP', 2);
define ('CP_OEMCP', 1);
define ('CP_UTF7', 65000);
define ('CP_UTF8', 65001);
define ('CP_SYMBOL', 42);
define ('CP_THREAD_ACP', 3);
define ('VARCMP_LT', 0);
define ('VARCMP_EQ', 1);
define ('VARCMP_GT', 2);
define ('VARCMP_NULL', 3);
define ('NORM_IGNORECASE', 1);
define ('NORM_IGNORENONSPACE', 2);
define ('NORM_IGNORESYMBOLS', 4);
define ('NORM_IGNOREWIDTH', 131072);
define ('NORM_IGNOREKANATYPE', 65536);
define ('DISP_E_DIVBYZERO', -2147352558);
define ('DISP_E_OVERFLOW', -2147352566);
define ('DISP_E_BADINDEX', -2147352565);
define ('MK_E_UNAVAILABLE', -2147221021);

// End of com_dotnet v.0.1
?>
