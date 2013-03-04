<?php

// Start of mcrypt v.

/**
 * (PHP 4, PHP 5)<br/>
 * Deprecated: Encrypts/decrypts data in ECB mode
 * @link http://php.net/manual/en/function.mcrypt-ecb.php
 * @param int $cipher
 * @param string $key
 * @param string $data
 * @param int $mode
 * @return string
 * @jms-builtin
 */
function mcrypt_ecb ($cipher, $key, $data, $mode) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Encrypts/decrypts data in CBC mode
 * @link http://php.net/manual/en/function.mcrypt-cbc.php
 * @param int $cipher
 * @param string $key
 * @param string $data
 * @param int $mode
 * @param string $iv [optional]
 * @return string
 * @jms-builtin
 */
function mcrypt_cbc ($cipher, $key, $data, $mode, $iv = null) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Encrypts/decrypts data in CFB mode
 * @link http://php.net/manual/en/function.mcrypt-cfb.php
 * @param int $cipher
 * @param string $key
 * @param string $data
 * @param int $mode
 * @param string $iv
 * @return string
 * @jms-builtin
 */
function mcrypt_cfb ($cipher, $key, $data, $mode, $iv) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Encrypts/decrypts data in OFB mode
 * @link http://php.net/manual/en/function.mcrypt-ofb.php
 * @param int $cipher
 * @param string $key
 * @param string $data
 * @param int $mode
 * @param string $iv
 * @return string
 * @jms-builtin
 */
function mcrypt_ofb ($cipher, $key, $data, $mode, $iv) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Gets the key size of the specified cipher
 * @link http://php.net/manual/en/function.mcrypt-get-key-size.php
 * @param int $cipher
 * @return int
 * @jms-builtin
 */
function mcrypt_get_key_size ($cipher) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Gets the block size of the specified cipher
 * @link http://php.net/manual/en/function.mcrypt-get-block-size.php
 * @param int $cipher <p>
 * One of the MCRYPT_ciphername constants or the name
 * of the algorithm as string.
 * </p>
 * @return int Gets the block size, as an integer.
 * @jms-builtin
 */
function mcrypt_get_block_size ($cipher) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Gets the name of the specified cipher
 * @link http://php.net/manual/en/function.mcrypt-get-cipher-name.php
 * @param int $cipher <p>
 * One of the MCRYPT_ciphername constants or the name
 * of the algorithm as string.
 * </p>
 * @return string This function returns the name of the cipher or <b>FALSE</b>, if the cipher does
 * not exist.
 * @jms-builtin
 */
function mcrypt_get_cipher_name ($cipher) {}

/**
 * (PHP 4, PHP 5)<br/>
 * Creates an initialization vector (IV) from a random source
 * @link http://php.net/manual/en/function.mcrypt-create-iv.php
 * @param int $size <p>
 * The size of the IV.
 * </p>
 * @param int $source [optional] <p>
 * The source of the IV. The source can be
 * <b>MCRYPT_RAND</b> (system random number generator),
 * <b>MCRYPT_DEV_RANDOM</b> (read data from
 * /dev/random) and
 * <b>MCRYPT_DEV_URANDOM</b> (read data from
 * /dev/urandom). Prior to 5.3.0,
 * <b>MCRYPT_RAND</b> was the only one supported on Windows.
 * </p>
 * @return string the initialization vector, or <b>FALSE</b> on error.
 * @jms-builtin
 */
function mcrypt_create_iv ($size, $source = 'MCRYPT_DEV_RANDOM') {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * Gets an array of all supported ciphers
 * @link http://php.net/manual/en/function.mcrypt-list-algorithms.php
 * @param string $lib_dir [optional] <p>
 * Specifies the directory where all algorithms are located. If not
 * specified, the value of the mcrypt.algorithms_dir
 * <i>php.ini</i> directive is used.
 * </p>
 * @return array an array with all the supported algorithms.
 * @jms-builtin
 */
function mcrypt_list_algorithms ($lib_dir = 'ini_get("mcrypt.algorithms_dir")') {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * Gets an array of all supported modes
 * @link http://php.net/manual/en/function.mcrypt-list-modes.php
 * @param string $lib_dir [optional] <p>
 * Specifies the directory where all modes are located. If not
 * specified, the value of the mcrypt.modes_dir
 * <i>php.ini</i> directive is used.
 * </p>
 * @return array an array with all the supported modes.
 * @jms-builtin
 */
function mcrypt_list_modes ($lib_dir = 'ini_get("mcrypt.modes_dir")') {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * Returns the size of the IV belonging to a specific cipher/mode combination
 * @link http://php.net/manual/en/function.mcrypt-get-iv-size.php
 * @param string $cipher <p>
 * One of the <b>MCRYPT_ciphername</b> constants, or the name
 * of the algorithm as string.
 * </p>
 * @param string $mode <p>
 * One of the <b>MCRYPT_MODE_modename</b> constants, or one of
 * the following strings: "ecb", "cbc", "cfb", "ofb", "nofb" or "stream".
 * The IV is ignored in ECB mode as this mode does not require it. You will
 * need to have the same IV (think: starting point) both at encryption and
 * decryption stages, otherwise your encryption will fail.
 * </p>
 * @return int the size of the Initialization Vector (IV) in bytes. On error the
 * function returns <b>FALSE</b>. If the IV is ignored in the specified cipher/mode
 * combination zero is returned.
 * @jms-builtin
 */
function mcrypt_get_iv_size ($cipher, $mode) {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * Encrypts plaintext with given parameters
 * @link http://php.net/manual/en/function.mcrypt-encrypt.php
 * @param string $cipher <p>
 * One of the <b>MCRYPT_ciphername</b>
 * constants, or the name of the algorithm as string.
 * </p>
 * @param string $key <p>
 * The key with which the data will be encrypted. If it's smaller than
 * the required keysize, it is padded with '\0'. It is
 * better not to use ASCII strings for keys.
 * </p>
 * <p>
 * It is recommended to use the mhash functions to create a key from a
 * string.
 * </p>
 * @param string $data <p>
 * The data that will be encrypted with the given <i>cipher</i>
 * and <i>mode</i>. If the size of the data is not n * blocksize,
 * the data will be padded with '\0'.
 * </p>
 * <p>
 * The returned crypttext can be larger than the size of the data that was
 * given by <i>data</i>.
 * </p>
 * @param string $mode <p>
 * One of the <b>MCRYPT_MODE_modename</b> constants,
 * or one of the following strings: "ecb", "cbc", "cfb", "ofb",
 * "nofb" or "stream".
 * </p>
 * @param string $iv [optional] <p>
 * Used for the initialization in CBC, CFB, OFB modes, and in some
 * algorithms in STREAM mode. If you do not supply an IV, while it is
 * needed for an algorithm, the function issues a warning and uses an
 * IV with all its bytes set to '\0'.
 * </p>
 * @return string the encrypted data, as a string.
 * @jms-builtin
 */
function mcrypt_encrypt ($cipher, $key, $data, $mode, $iv = null) {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * Decrypts crypttext with given parameters
 * @link http://php.net/manual/en/function.mcrypt-decrypt.php
 * @param string $cipher <p>
 * One of the <b>MCRYPT_ciphername</b> constants,
 * or the name of the algorithm as string.
 * </p>
 * @param string $key <p>
 * The key with which the data was encrypted. If it's smaller
 * than the required keysize, it is padded with
 * '\0'.
 * </p>
 * @param string $data <p>
 * The data that will be decrypted with the given <i>cipher</i>
 * and <i>mode</i>. If the size of the data is not n * blocksize,
 * the data will be padded with '\0'.
 * </p>
 * @param string $mode <p>
 * One of the <b>MCRYPT_MODE_modename</b> constants,
 * or one of the following strings: "ecb", "cbc", "cfb", "ofb",
 * "nofb" or "stream".
 * </p>
 * @param string $iv [optional] <p>
 * The <i>iv</i> parameter is used for the initialization
 * in CBC, CFB, OFB modes, and in some algorithms in STREAM mode. If you
 * do not supply an IV, while it is needed for an algorithm, the function
 * issues a warning and uses an IV with all its bytes set to
 * '\0'.
 * </p>
 * @return string the decrypted data as a string.
 * @jms-builtin
 */
function mcrypt_decrypt ($cipher, $key, $data, $mode, $iv = null) {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * Opens the module of the algorithm and the mode to be used
 * @link http://php.net/manual/en/function.mcrypt-module-open.php
 * @param string $algorithm <p>
 * The algorithm to be used.
 * </p>
 * @param string $algorithm_directory <p>
 * The <i>algorithm_directory</i> parameter is used to locate
 * the encryption module. When you supply a directory name, it is used. When
 * you set it to an empty string (""), the value set by the
 * mcrypt.algorithms_dir <i>php.ini</i> directive is used. When
 * it is not set, the default directory that is used is the one that was compiled
 * into libmcrypt (usually /usr/local/lib/libmcrypt).
 * </p>
 * @param string $mode <p>
 * The mode to be used.
 * </p>
 * @param string $mode_directory <p>
 * The <i>mode_directory</i> parameter is used to locate
 * the encryption module. When you supply a directory name, it is used. When
 * you set it to an empty string (""), the value set by the
 * mcrypt.modes_dir <i>php.ini</i> directive is used. When
 * it is not set, the default directory that is used is the one that was compiled-in
 * into libmcrypt (usually /usr/local/lib/libmcrypt).
 * </p>
 * @return resource Normally it returns an encryption descriptor, or <b>FALSE</b> on error.
 * @jms-builtin
 */
function mcrypt_module_open ($algorithm, $algorithm_directory, $mode, $mode_directory) {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * This function initializes all buffers needed for encryption
 * @link http://php.net/manual/en/function.mcrypt-generic-init.php
 * @param resource $td <p>
 * The encryption descriptor.
 * </p>
 * @param string $key <p>
 * The maximum length of the key should be the one obtained by calling
 * <b>mcrypt_enc_get_key_size</b> and every value smaller
 * than this is legal.
 * </p>
 * @param string $iv <p>
 * The IV should normally have the size of the algorithms block size, but
 * you must obtain the size by calling
 * <b>mcrypt_enc_get_iv_size</b>. IV is ignored in ECB. IV
 * MUST exist in CFB, CBC, STREAM, nOFB and OFB modes. It needs to be
 * random and unique (but not secret). The same IV must be used for
 * encryption/decryption. If you do not want to use it you should set it
 * to zeros, but this is not recommended.
 * </p>
 * @return int The function returns a negative value on error: -3 when the key length
 * was incorrect, -4 when there was a memory allocation problem and any
 * other return value is an unknown error. If an error occurs a warning will
 * be displayed accordingly. <b>FALSE</b> is returned if incorrect parameters
 * were passed.
 * @jms-builtin
 */
function mcrypt_generic_init ($td, $key, $iv) {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * This function encrypts data
 * @link http://php.net/manual/en/function.mcrypt-generic.php
 * @param resource $td <p>
 * The encryption descriptor.
 * </p>
 * <p>
 * The encryption handle should always be initialized with
 * <b>mcrypt_generic_init</b> with a key and an IV before
 * calling this function. Where the encryption is done, you should free the
 * encryption buffers by calling <b>mcrypt_generic_deinit</b>.
 * See <b>mcrypt_module_open</b> for an example.
 * </p>
 * @param string $data <p>
 * The data to encrypt.
 * </p>
 * @return string the encrypted data.
 * @jms-builtin
 */
function mcrypt_generic ($td, $data) {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * Decrypts data
 * @link http://php.net/manual/en/function.mdecrypt-generic.php
 * @param resource $td <p>
 * An encryption descriptor returned by
 * <b>mcrypt_module_open</b>
 * </p>
 * @param string $data <p>
 * Encrypted data.
 * </p>
 * @return string
 * @jms-builtin
 */
function mdecrypt_generic ($td, $data) {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5 &lt;= 5.1.6)<br/>
 * This function terminates encryption
 * @link http://php.net/manual/en/function.mcrypt-generic-end.php
 * @param resource $td
 * @return bool
 * @jms-builtin
 */
function mcrypt_generic_end ($td) {}

/**
 * (PHP 4 &gt;= 4.0.7, PHP 5)<br/>
 * This function deinitializes an encryption module
 * @link http://php.net/manual/en/function.mcrypt-generic-deinit.php
 * @param resource $td <p>
 * The encryption descriptor.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function mcrypt_generic_deinit ($td) {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * Runs a self test on the opened module
 * @link http://php.net/manual/en/function.mcrypt-enc-self-test.php
 * @param resource $td <p>
 * The encryption descriptor.
 * </p>
 * @return int If the self test succeeds it returns <b>FALSE</b>. In case of an error, it
 * returns <b>TRUE</b>.
 * @jms-builtin
 */
function mcrypt_enc_self_test ($td) {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * Checks whether the encryption of the opened mode works on blocks
 * @link http://php.net/manual/en/function.mcrypt-enc-is-block-algorithm-mode.php
 * @param resource $td <p>
 * The encryption descriptor.
 * </p>
 * @return bool <b>TRUE</b> if the mode is for use with block algorithms, otherwise it
 * returns <b>FALSE</b>.
 * @jms-builtin
 */
function mcrypt_enc_is_block_algorithm_mode ($td) {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * Checks whether the algorithm of the opened mode is a block algorithm
 * @link http://php.net/manual/en/function.mcrypt-enc-is-block-algorithm.php
 * @param resource $td <p>
 * The encryption descriptor.
 * </p>
 * @return bool <b>TRUE</b> if the algorithm is a block algorithm or <b>FALSE</b> if it is
 * a stream one.
 * @jms-builtin
 */
function mcrypt_enc_is_block_algorithm ($td) {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * Checks whether the opened mode outputs blocks
 * @link http://php.net/manual/en/function.mcrypt-enc-is-block-mode.php
 * @param resource $td <p>
 * The encryption descriptor.
 * </p>
 * @return bool <b>TRUE</b> if the mode outputs blocks of bytes,
 * or <b>FALSE</b> if it outputs just bytes.
 * @jms-builtin
 */
function mcrypt_enc_is_block_mode ($td) {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * Returns the blocksize of the opened algorithm
 * @link http://php.net/manual/en/function.mcrypt-enc-get-block-size.php
 * @param resource $td <p>
 * The encryption descriptor.
 * </p>
 * @return int the block size of the specified algorithm in bytes.
 * @jms-builtin
 */
function mcrypt_enc_get_block_size ($td) {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * Returns the maximum supported keysize of the opened mode
 * @link http://php.net/manual/en/function.mcrypt-enc-get-key-size.php
 * @param resource $td <p>
 * The encryption descriptor.
 * </p>
 * @return int the maximum supported key size of the algorithm in bytes.
 * @jms-builtin
 */
function mcrypt_enc_get_key_size ($td) {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * Returns an array with the supported keysizes of the opened algorithm
 * @link http://php.net/manual/en/function.mcrypt-enc-get-supported-key-sizes.php
 * @param resource $td <p>
 * The encryption descriptor.
 * </p>
 * @return array an array with the key sizes supported by the algorithm
 * specified by the encryption descriptor. If it returns an empty
 * array then all key sizes between 1 and
 * <b>mcrypt_enc_get_key_size</b> are supported by the
 * algorithm.
 * @jms-builtin
 */
function mcrypt_enc_get_supported_key_sizes ($td) {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * Returns the size of the IV of the opened algorithm
 * @link http://php.net/manual/en/function.mcrypt-enc-get-iv-size.php
 * @param resource $td <p>
 * The encryption descriptor.
 * </p>
 * @return int the size of the IV, or 0 if the IV is ignored by the algorithm.
 * @jms-builtin
 */
function mcrypt_enc_get_iv_size ($td) {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * Returns the name of the opened algorithm
 * @link http://php.net/manual/en/function.mcrypt-enc-get-algorithms-name.php
 * @param resource $td <p>
 * The encryption descriptor.
 * </p>
 * @return string the name of the opened algorithm as a string.
 * @jms-builtin
 */
function mcrypt_enc_get_algorithms_name ($td) {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * Returns the name of the opened mode
 * @link http://php.net/manual/en/function.mcrypt-enc-get-modes-name.php
 * @param resource $td <p>
 * The encryption descriptor.
 * </p>
 * @return string the name as a string.
 * @jms-builtin
 */
function mcrypt_enc_get_modes_name ($td) {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * This function runs a self test on the specified module
 * @link http://php.net/manual/en/function.mcrypt-module-self-test.php
 * @param string $algorithm <p>
 * The algorithm to test.
 * </p>
 * @param string $lib_dir [optional] <p>
 * The optional <i>lib_dir</i> parameter can contain the
 * location where the algorithm module is on the system.
 * </p>
 * @return bool The function returns <b>TRUE</b> if the self test succeeds, or <b>FALSE</b> when it
 * fails.
 * @jms-builtin
 */
function mcrypt_module_self_test ($algorithm, $lib_dir = null) {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * Returns if the specified module is a block algorithm or not
 * @link http://php.net/manual/en/function.mcrypt-module-is-block-algorithm-mode.php
 * @param string $mode <p>
 * The mode to check.
 * </p>
 * @param string $lib_dir [optional] <p>
 * The optional <i>lib_dir</i> parameter can contain the
 * location where the algorithm module is on the system.
 * </p>
 * @return bool This function returns <b>TRUE</b> if the mode is for use with block
 * algorithms, otherwise it returns <b>FALSE</b>. (e.g. <b>FALSE</b> for stream, and
 * <b>TRUE</b> for cbc, cfb, ofb).
 * @jms-builtin
 */
function mcrypt_module_is_block_algorithm_mode ($mode, $lib_dir = null) {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * This function checks whether the specified algorithm is a block algorithm
 * @link http://php.net/manual/en/function.mcrypt-module-is-block-algorithm.php
 * @param string $algorithm <p>
 * The algorithm to check.
 * </p>
 * @param string $lib_dir [optional] <p>
 * The optional <i>lib_dir</i> parameter can contain the
 * location where the algorithm module is on the system.
 * </p>
 * @return bool This function returns <b>TRUE</b> if the specified algorithm is a block
 * algorithm, or <b>FALSE</b> if it is a stream one.
 * @jms-builtin
 */
function mcrypt_module_is_block_algorithm ($algorithm, $lib_dir = null) {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * Returns if the specified mode outputs blocks or not
 * @link http://php.net/manual/en/function.mcrypt-module-is-block-mode.php
 * @param string $mode <p>
 * The mode to check.
 * </p>
 * @param string $lib_dir [optional] <p>
 * The optional <i>lib_dir</i> parameter can contain the
 * location where the algorithm module is on the system.
 * </p>
 * @return bool This function returns <b>TRUE</b> if the mode outputs blocks of bytes or
 * <b>FALSE</b> if it outputs just bytes. (e.g. <b>TRUE</b> for cbc and ecb, and
 * <b>FALSE</b> for cfb and stream).
 * @jms-builtin
 */
function mcrypt_module_is_block_mode ($mode, $lib_dir = null) {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * Returns the blocksize of the specified algorithm
 * @link http://php.net/manual/en/function.mcrypt-module-get-algo-block-size.php
 * @param string $algorithm <p>
 * The algorithm name.
 * </p>
 * @param string $lib_dir [optional] <p>
 * This optional parameter can contain the location where the mode module
 * is on the system.
 * </p>
 * @return int the block size of the algorithm specified in bytes.
 * @jms-builtin
 */
function mcrypt_module_get_algo_block_size ($algorithm, $lib_dir = null) {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * Returns the maximum supported keysize of the opened mode
 * @link http://php.net/manual/en/function.mcrypt-module-get-algo-key-size.php
 * @param string $algorithm <p>
 * The algorithm name.
 * </p>
 * @param string $lib_dir [optional] <p>
 * This optional parameter can contain the location where the mode module
 * is on the system.
 * </p>
 * @return int This function returns the maximum supported key size of the
 * algorithm specified in bytes.
 * @jms-builtin
 */
function mcrypt_module_get_algo_key_size ($algorithm, $lib_dir = null) {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * Returns an array with the supported keysizes of the opened algorithm
 * @link http://php.net/manual/en/function.mcrypt-module-get-supported-key-sizes.php
 * @param string $algorithm <p>
 * The algorithm to be used.
 * </p>
 * @param string $lib_dir [optional] <p>
 * The optional <i>lib_dir</i> parameter can contain the
 * location where the algorithm module is on the system.
 * </p>
 * @return array an array with the key sizes supported by the specified algorithm.
 * If it returns an empty array then all key sizes between 1 and
 * <b>mcrypt_module_get_algo_key_size</b> are supported by the
 * algorithm.
 * @jms-builtin
 */
function mcrypt_module_get_supported_key_sizes ($algorithm, $lib_dir = null) {}

/**
 * (PHP 4 &gt;= 4.0.2, PHP 5)<br/>
 * Closes the mcrypt module
 * @link http://php.net/manual/en/function.mcrypt-module-close.php
 * @param resource $td <p>
 * The encryption descriptor.
 * </p>
 * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
 * @jms-builtin
 */
function mcrypt_module_close ($td) {}

define ('MCRYPT_ENCRYPT', 0);
define ('MCRYPT_DECRYPT', 1);
define ('MCRYPT_DEV_RANDOM', 0);
define ('MCRYPT_DEV_URANDOM', 1);
define ('MCRYPT_RAND', 2);
define ('MCRYPT_3DES', "tripledes");
define ('MCRYPT_ARCFOUR_IV', "arcfour-iv");
define ('MCRYPT_ARCFOUR', "arcfour");
define ('MCRYPT_BLOWFISH', "blowfish");
define ('MCRYPT_BLOWFISH_COMPAT', "blowfish-compat");
define ('MCRYPT_CAST_128', "cast-128");
define ('MCRYPT_CAST_256', "cast-256");
define ('MCRYPT_CRYPT', "crypt");
define ('MCRYPT_DES', "des");
define ('MCRYPT_ENIGNA', "crypt");
define ('MCRYPT_GOST', "gost");
define ('MCRYPT_LOKI97', "loki97");
define ('MCRYPT_PANAMA', "panama");
define ('MCRYPT_RC2', "rc2");
define ('MCRYPT_RIJNDAEL_128', "rijndael-128");
define ('MCRYPT_RIJNDAEL_192', "rijndael-192");
define ('MCRYPT_RIJNDAEL_256', "rijndael-256");
define ('MCRYPT_SAFER64', "safer-sk64");
define ('MCRYPT_SAFER128', "safer-sk128");
define ('MCRYPT_SAFERPLUS', "saferplus");
define ('MCRYPT_SERPENT', "serpent");
define ('MCRYPT_THREEWAY', "threeway");
define ('MCRYPT_TRIPLEDES', "tripledes");
define ('MCRYPT_TWOFISH', "twofish");
define ('MCRYPT_WAKE', "wake");
define ('MCRYPT_XTEA', "xtea");
define ('MCRYPT_IDEA', "idea");
define ('MCRYPT_MARS', "mars");
define ('MCRYPT_RC6', "rc6");
define ('MCRYPT_SKIPJACK', "skipjack");
define ('MCRYPT_MODE_CBC', "cbc");
define ('MCRYPT_MODE_CFB', "cfb");
define ('MCRYPT_MODE_ECB', "ecb");
define ('MCRYPT_MODE_NOFB', "nofb");
define ('MCRYPT_MODE_OFB', "ofb");
define ('MCRYPT_MODE_STREAM', "stream");

// End of mcrypt v.
?>
