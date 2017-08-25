<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\Password;

/**
 * Password hasher that supports the current WordPress hashing algorithm as well
 * as the new PHP password hashing functions.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class Hasher
{
    /**
     * Algorithm used when hashing a password.
     *
     * @var int
     */
    private $algorithm = \PASSWORD_BCRYPT;

    /**
     * WordPress password hasher.
     *
     * @var \PasswordHash
     */
    private $wordpress_hasher;

    /**
     * Constructor.
     *
     * @param \PasswordHash $wordpress_hasher
     */
    public function __construct(\PasswordHash $wordpress_hasher)
    {
        $this->wordpress_hasher = $wordpress_hasher;
    }

    /**
     * Hashes the given password using
     *
     * @param string $password
     *
     * @return string
     */
    public function hash_password($password)
    {
        return password_hash($password, $this->algorithm);
    }

    /**
     * Checks if the given hash is valid.
     *
     * @param string $hash
     *
     * @return bool
     */
    public function is_hash_valid($hash)
    {
        return password_needs_rehash($hash, $this->algorithm);
    }

    /**
     * Verifies that the given password matches the given hash.
     *
     * @param string $password
     * @param string $hash
     *
     * @return bool
     */
    public function verify_password($password, $hash)
    {
        return strpos($hash, '$P$') !== 0 ? password_verify($password, $hash) : $this->verify_wordpress_password($password, $hash);
    }

    /**
     * Verifies that the given password matches the given hash using the
     * WordPress password hasher.
     *
     * @param string $password
     * @param string $hash
     *
     * @return bool
     */
    private function verify_wordpress_password($password, $hash)
    {
        return $this->wordpress_hasher->CheckPassword($password, $hash);
    }
}
