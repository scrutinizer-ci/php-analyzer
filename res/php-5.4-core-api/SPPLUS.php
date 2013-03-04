<?php

// Start of SPPLUS v.3.0

/**
 * (PECL spplus &gt;= 1.0.0)<br/>
 * Obtain a hmac key (needs 8 arguments)
 * @link http://php.net/manual/en/function.calcul-hmac.php
 * @param string $clent 
 * @param string $siretcode 
 * @param string $price 
 * @param string $reference 
 * @param string $validity 
 * @param string $taxation 
 * @param string $devise 
 * @param string $language 
 * @return string Returns true on success or false on failure.
 * @jms-builtin
 */
function calcul_hmac ($clent, $siretcode, $price, $reference, $validity, $taxation, $devise, $language) {}

/**
 * (PECL spplus &gt;= 1.0.0)<br/>
 * Obtain a nthmac key (needs 2 arguments)
 * @link http://php.net/manual/en/function.nthmac.php
 * @param string $clent 
 * @param string $data 
 * @return string Returns true on success or false on failure.
 * @jms-builtin
 */
function nthmac ($clent, $data) {}

/**
 * (PECL spplus &gt;= 1.0.0)<br/>
 * Obtain the payment url (needs 2 arguments)
 * @link http://php.net/manual/en/function.signeurlpaiement.php
 * @param string $clent 
 * @param string $data 
 * @return string Returns true on success or false on failure.
 * @jms-builtin
 */
function signeurlpaiement ($clent, $data) {}

/**
 * (PECL spplus &gt;= 1.0.0)<br/>
 * Obtain a hmac key (needs 2 arguments)
 * @link http://php.net/manual/en/function.calculhmac.php
 * @param string $clent 
 * @param string $data 
 * @return string Returns true on success or false on failure.
 * @jms-builtin
 */
function calculhmac ($clent, $data) {}

// End of SPPLUS v.3.0
?>
