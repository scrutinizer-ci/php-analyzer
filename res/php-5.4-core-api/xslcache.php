<?php

// Start of xslcache v.0.7.2

/** @jms-builtin */
class XSLTCache  {

	/**
	 * @param path
	 * @param cachesheet[optional]
	 */
	public function importStylesheet ($path, $cachesheet) {}

	/**
	 * @param doc
	 */
	public function transformToDoc ($doc) {}

	/**
	 * @param doc
	 * @param uri
	 */
	public function transformToUri ($doc, $uri) {}

	/**
	 * @param doc
	 */
	public function transformToXml ($doc) {}

	/**
	 * @param namespace
	 * @param name
	 * @param value[optional]
	 */
	public function setParameter ($namespace, $name, $value) {}

	/**
	 * @param namespace
	 * @param name
	 */
	public function getParameter ($namespace, $name) {}

	/**
	 * @param namespace
	 * @param name
	 */
	public function removeParameter ($namespace, $name) {}

	public function hasExsltSupport () {}

	/**
	 * @param restrict[optional]
	 */
	public function registerPHPFunctions ($restrict) {}

}
// End of xslcache v.0.7.2
?>
