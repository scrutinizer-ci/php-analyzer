<?php

// Start of xmlreader v.0.1

/**
 * The XMLReader extension is an XML Pull parser. The reader acts as a
 * cursor going forward on the document stream and stopping at each node
 * on the way.
 * @link http://php.net/manual/en/class.xmlreader.php
 * @jms-builtin
 */
class XMLReader  {
	const NONE = 0;
	const ELEMENT = 1;
	const ATTRIBUTE = 2;
	const TEXT = 3;
	const CDATA = 4;
	const ENTITY_REF = 5;
	const ENTITY = 6;
	const PI = 7;
	const COMMENT = 8;
	const DOC = 9;
	const DOC_TYPE = 10;
	const DOC_FRAGMENT = 11;
	const NOTATION = 12;
	const WHITESPACE = 13;
	const SIGNIFICANT_WHITESPACE = 14;
	const END_ELEMENT = 15;
	const END_ENTITY = 16;
	const XML_DECLARATION = 17;
	const LOADDTD = 1;
	const DEFAULTATTRS = 2;
	const VALIDATE = 3;
	const SUBST_ENTITIES = 4;


	/**
	 * (PHP 5 &gt;= 5.1.2)<br/>
	 * Close the XMLReader input
	 * @link http://php.net/manual/en/xmlreader.close.php
	 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
	 */
	public function close () {}

	/**
	 * (PHP 5 &gt;= 5.1.2)<br/>
	 * Get the value of a named attribute
	 * @link http://php.net/manual/en/xmlreader.getattribute.php
	 * @param string $name <p>
	 * The name of the attribute.
	 * </p>
	 * @return string The value of the attribute, or <b>NULL</b> if no attribute with the given
	 * <i>name</i> is found or not positioned on an element node.
	 */
	public function getAttribute ($name) {}

	/**
	 * (PHP 5 &gt;= 5.1.2)<br/>
	 * Get the value of an attribute by index
	 * @link http://php.net/manual/en/xmlreader.getattributeno.php
	 * @param int $index <p>
	 * The position of the attribute.
	 * </p>
	 * @return string The value of the attribute, or an empty string if no attribute exists
	 * at <i>index</i> or not positioned of element.
	 */
	public function getAttributeNo ($index) {}

	/**
	 * (PHP 5 &gt;= 5.1.2)<br/>
	 * Get the value of an attribute by localname and URI
	 * @link http://php.net/manual/en/xmlreader.getattributens.php
	 * @param string $localName <p>
	 * The local name.
	 * </p>
	 * @param string $namespaceURI <p>
	 * The namespace URI.
	 * </p>
	 * @return string The value of the attribute, or an empty string if no attribute with the
	 * given <i>localName</i> and
	 * <i>namespaceURI</i> is found or not positioned of element.
	 */
	public function getAttributeNs ($localName, $namespaceURI) {}

	/**
	 * (PHP 5 &gt;= 5.1.2)<br/>
	 * Indicates if specified property has been set
	 * @link http://php.net/manual/en/xmlreader.getparserproperty.php
	 * @param int $property <p>
	 * One of the parser option
	 * constants.
	 * </p>
	 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
	 */
	public function getParserProperty ($property) {}

	/**
	 * (PHP 5 &gt;= 5.1.2)<br/>
	 * Indicates if the parsed document is valid
	 * @link http://php.net/manual/en/xmlreader.isvalid.php
	 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
	 */
	public function isValid () {}

	/**
	 * (PHP 5 &gt;= 5.1.2)<br/>
	 * Lookup namespace for a prefix
	 * @link http://php.net/manual/en/xmlreader.lookupnamespace.php
	 * @param string $prefix <p>
	 * String containing the prefix.
	 * </p>
	 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
	 */
	public function lookupNamespace ($prefix) {}

	/**
	 * (PHP 5 &gt;= 5.1.2)<br/>
	 * Move cursor to an attribute by index
	 * @link http://php.net/manual/en/xmlreader.movetoattributeno.php
	 * @param int $index <p>
	 * The position of the attribute.
	 * </p>
	 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
	 */
	public function moveToAttributeNo ($index) {}

	/**
	 * (PHP 5 &gt;= 5.1.2)<br/>
	 * Move cursor to a named attribute
	 * @link http://php.net/manual/en/xmlreader.movetoattribute.php
	 * @param string $name <p>
	 * The name of the attribute.
	 * </p>
	 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
	 */
	public function moveToAttribute ($name) {}

	/**
	 * (PHP 5 &gt;= 5.1.2)<br/>
	 * Move cursor to a named attribute
	 * @link http://php.net/manual/en/xmlreader.movetoattributens.php
	 * @param string $localName <p>
	 * The local name.
	 * </p>
	 * @param string $namespaceURI <p>
	 * The namespace URI.
	 * </p>
	 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
	 */
	public function moveToAttributeNs ($localName, $namespaceURI) {}

	/**
	 * (PHP 5 &gt;= 5.1.2)<br/>
	 * Position cursor on the parent Element of current Attribute
	 * @link http://php.net/manual/en/xmlreader.movetoelement.php
	 * @return bool <b>TRUE</b> if successful and <b>FALSE</b> if it fails or not positioned on
	 * Attribute when this method is called.
	 */
	public function moveToElement () {}

	/**
	 * (PHP 5 &gt;= 5.1.2)<br/>
	 * Position cursor on the first Attribute
	 * @link http://php.net/manual/en/xmlreader.movetofirstattribute.php
	 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
	 */
	public function moveToFirstAttribute () {}

	/**
	 * (PHP 5 &gt;= 5.1.2)<br/>
	 * Position cursor on the next Attribute
	 * @link http://php.net/manual/en/xmlreader.movetonextattribute.php
	 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
	 */
	public function moveToNextAttribute () {}

	/**
	 * (PHP 5 &gt;= 5.1.2)<br/>
	 * Set the URI containing the XML to parse
	 * @link http://php.net/manual/en/xmlreader.open.php
	 * @param string $URI <p>
	 * URI pointing to the document.
	 * </p>
	 * @param string $encoding [optional] <p>
	 * The document encoding or <b>NULL</b>.
	 * </p>
	 * @param int $options [optional] <p>
	 * A bitmask of the LIBXML_*
	 * constants.
	 * </p>
	 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure. If called statically, returns an
	 * <b>XMLReader</b> or <b>FALSE</b> on failure.
	 */
	public function open ($URI, $encoding = null, $options = 0) {}

	/**
	 * (PHP 5 &gt;= 5.1.2)<br/>
	 * Move to next node in document
	 * @link http://php.net/manual/en/xmlreader.read.php
	 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
	 */
	public function read () {}

	/**
	 * (PHP 5 &gt;= 5.1.2)<br/>
	 * Move cursor to next node skipping all subtrees
	 * @link http://php.net/manual/en/xmlreader.next.php
	 * @param string $localname [optional] <p>
	 * The name of the next node to move to.
	 * </p>
	 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
	 */
	public function next ($localname = null) {}

	/**
	 * (PHP 5 &gt;= 5.2.0)<br/>
	 * Retrieve XML from current node
	 * @link http://php.net/manual/en/xmlreader.readinnerxml.php
	 * @return string the contents of the current node as a string. Empty string on failure.
	 */
	public function readInnerXml () {}

	/**
	 * (PHP 5 &gt;= 5.2.0)<br/>
	 * Retrieve XML from current node, including it self
	 * @link http://php.net/manual/en/xmlreader.readouterxml.php
	 * @return string the contents of current node, including itself, as a string. Empty string on failure.
	 */
	public function readOuterXml () {}

	/**
	 * (PHP 5 &gt;= 5.2.0)<br/>
	 * Reads the contents of the current node as a string
	 * @link http://php.net/manual/en/xmlreader.readstring.php
	 * @return string the content of the current node as a string. Empty string on
	 * failure.
	 */
	public function readString () {}

	/**
	 * (PHP 5 &gt;= 5.2.0)<br/>
	 * Validate document against XSD
	 * @link http://php.net/manual/en/xmlreader.setschema.php
	 * @param string $filename <p>
	 * The filename of the XSD schema.
	 * </p>
	 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
	 */
	public function setSchema ($filename) {}

	/**
	 * (PHP 5 &gt;= 5.1.2)<br/>
	 * Set parser options
	 * @link http://php.net/manual/en/xmlreader.setparserproperty.php
	 * @param int $property <p>
	 * One of the parser option
	 * constants.
	 * </p>
	 * @param bool $value <p>
	 * If set to <b>TRUE</b> the option will be enabled otherwise will
	 * be disabled.
	 * </p>
	 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
	 */
	public function setParserProperty ($property, $value) {}

	/**
	 * (PHP 5 &gt;= 5.2.0)<br/>
	 * Set the filename or URI for a RelaxNG Schema
	 * @link http://php.net/manual/en/xmlreader.setrelaxngschema.php
	 * @param string $filename <p>
	 * filename or URI pointing to a RelaxNG Schema.
	 * </p>
	 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
	 */
	public function setRelaxNGSchema ($filename) {}

	/**
	 * (PHP 5 &gt;= 5.1.2)<br/>
	 * Set the data containing a RelaxNG Schema
	 * @link http://php.net/manual/en/xmlreader.setrelaxngschemasource.php
	 * @param string $source <p>
	 * String containing the RelaxNG Schema.
	 * </p>
	 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
	 */
	public function setRelaxNGSchemaSource ($source) {}

	/**
	 * (PHP 5 &gt;= 5.1.2)<br/>
	 * Set the data containing the XML to parse
	 * @link http://php.net/manual/en/xmlreader.xml.php
	 * @param string $source <p>
	 * String containing the XML to be parsed.
	 * </p>
	 * @param string $encoding [optional] <p>
	 * The document encoding or <b>NULL</b>.
	 * </p>
	 * @param int $options [optional] <p>
	 * A bitmask of the LIBXML_*
	 * constants.
	 * </p>
	 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure. If called statically, returns an
	 * <b>XMLReader</b> or <b>FALSE</b> on failure.
	 */
	public function XML ($source, $encoding = null, $options = 0) {}

	/**
	 * (PHP 5 &gt;= 5.1.2)<br/>
	 * Returns a copy of the current node as a DOM object
	 * @link http://php.net/manual/en/xmlreader.expand.php
	 * @param DOMNode $basenode [optional]
	 * @return DOMNode The resulting <b>DOMNode</b> or <b>FALSE</b> on error.
	 */
	public function expand (DOMNode $basenode = null) {}

}
// End of xmlreader v.0.1
?>
