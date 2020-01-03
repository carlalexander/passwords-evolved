<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\Password\Hasher;

/**
 * Password hasher that uses libsodium.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class SodiumPasswordHasher implements PasswordHasherInterface
{
    /**
     * Maximum amount of RAM that the function will use in bytes.
     *
     * @var int
     */
    private $mem_limit;

    /**
     * Maximum amount of computations to perform.
     *
     * @var int
     */
    private $ops_limit;

    /**
     * Constructor.
     */
    public function __construct()
    {
        // Value for SODIUM_CRYPTO_PWHASH_MEMLIMIT_INTERACTIVE
        $this->mem_limit = 32 * 1024 * 1024;
        // Value for SODIUM_CRYPTO_PWHASH_OPSLIMIT_INTERACTIVE
        $this->ops_limit = 4;
    }

    /**
     * Check if libsodium is available for hashing passwords.
     *
     * @return bool
     */
    public static function is_libsodium_available()
    {
        if (class_exists('ParagonIE_Sodium_Compat') && method_exists('ParagonIE_Sodium_Compat', 'crypto_pwhash_is_available')) {
            return \ParagonIE_Sodium_Compat::crypto_pwhash_is_available();
        }

        // WordPress won't load the libsodium compatibility layer on PHP 7 because the extension comes with
        // "sodium_crypto_box" function. So we check for the "sodium" extension and PHP version.
        return extension_loaded('sodium') && PHP_VERSION_ID < 70200;
    }

    /**
     * {@inheritdoc}
     */
    public function hash_password($password)
    {
        if (!self::is_libsodium_available()) {
            return;
        }

        return sodium_crypto_pwhash_str($password, $this->ops_limit, $this->mem_limit);
    }

    /**
     * {@inheritdoc}
     */
    public function is_hash_supported($hash)
    {
        if (0 !== strpos($hash, '$argon2i$')) {
            return false;
        }

        return self::is_libsodium_available();
    }

    /**
     * {@inheritdoc}
     */
    public function is_hash_valid($hash)
    {
        // Ugly check because PHP doesn't have an implementation for sodium_crypto_pwhash_str_needs_rehash
        // despite documentation for it.
        return 0 !== preg_match('/^\$argon2id?\$v=19\$m=32768,t=4,p=1\$/', $hash);
    }

    /**
     * {@inheritdoc}
     */
    public function is_password_valid($password, $hash)
    {
        if (!self::is_libsodium_available()) {
            return false;
        }

        return sodium_crypto_pwhash_str_verify($hash, $password);
    }
}
