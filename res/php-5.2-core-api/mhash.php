<?php

// Start of mhash v.

/**
 * Get the block size of the specified hash
 * @link http://www.php.net/manual/en/function.mhash-get-block-size.php
 * @param hash int <p>
 * The hash id. One of the MHASH_XXX constants.
 * </p>
 * @return int the size in bytes or false, if the hash
 * does not exist.
 *
 * @jms-builtin
 */
function mhash_get_block_size ($hash) {}

/**
 * Get the name of the specified hash
 * @link http://www.php.net/manual/en/function.mhash-get-hash-name.php
 * @param hash int <p>
 * The hash id. One of the MHASH_XXX constants.
 * </p>
 * @return string the name of the hash or false, if the hash does not exist.
 *
 * @jms-builtin
 */
function mhash_get_hash_name ($hash) {}

/**
 * Generates a key
 * @link http://www.php.net/manual/en/function.mhash-keygen-s2k.php
 * @param hash int <p>
 * The hash id used to create the key.
 * One of the MHASH_XXX constants.
 * </p>
 * @param password string <p>
 * User supplied password.
 * </p>
 * @param salt string <p>
 * Must be different and random enough for every key you generate in
 * order to create different keys. That salt must be known when you check
 * the keys, thus it is a good idea to append the key to it. Salt has a
 * fixed length of 8 bytes and will be padded with zeros if you supply
 * less bytes.
 * </p>
 * @param bytes int <p>
 * The key length, in bytes.
 * </p>
 * @return string the generated key as a string, or false on error.
 *
 * @jms-builtin
 */
function mhash_keygen_s2k ($hash, $password, $salt, $bytes) {}

/**
 * Get the highest available hash id
 * @link http://www.php.net/manual/en/function.mhash-count.php
 * @return int the highest available hash id. Hashes are numbered from 0 to this
 * hash id.
 *
 * @jms-builtin
 */
function mhash_count () {}

/**
 * Compute hash
 * @link http://www.php.net/manual/en/function.mhash.php
 * @param hash int <p>
 * The hash id. One of the MHASH_XXX constants.
 * </p>
 * @param data string <p>
 * The user input, as a string.
 * </p>
 * @param key string[optional] <p>
 * If specified, the function will return the resulting HMAC instead.
 * HMAC is keyed hashing for message authentication, or simply a message
 * digest that depends on the specified key. Not all algorithms 
 * supported in mhash can be used in HMAC mode.
 * </p>
 * @return string the resulting hash (also called digest) or HMAC as a string, or
 * false on errors.
 *
 * @jms-builtin
 */
function mhash ($hash, $data, $key = null) {}

define ('MHASH_CRC32', 0);
define ('MHASH_MD5', 1);
define ('MHASH_SHA1', 2);
define ('MHASH_HAVAL256', 3);
define ('MHASH_RIPEMD160', 5);
define ('MHASH_TIGER', 7);
define ('MHASH_GOST', 8);
define ('MHASH_CRC32B', 9);
define ('MHASH_HAVAL224', 10);
define ('MHASH_HAVAL192', 11);
define ('MHASH_HAVAL160', 12);
define ('MHASH_HAVAL128', 13);
define ('MHASH_TIGER128', 14);
define ('MHASH_TIGER160', 15);
define ('MHASH_MD4', 16);
define ('MHASH_ADLER32', 18);
define ('MHASH_RIPEMD128', 23);
define ('MHASH_RIPEMD256', 24);
define ('MHASH_RIPEMD320', 25);

// End of mhash v.
?>
