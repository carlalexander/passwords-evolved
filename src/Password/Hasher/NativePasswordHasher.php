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
 * Password hasher that uses the native PHP password hashing functions.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class NativePasswordHasher implements PasswordHasherInterface
{
    /**
     * Algorithm used when hashing a password.
     *
     * @var int
     */
    private $algorithm;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->algorithm = PASSWORD_DEFAULT;

        if (defined('PASSWORD_ARGON2ID')) {
            $this->algorithm = PASSWORD_ARGON2ID;
        } elseif (defined('PASSWORD_ARGON2I')) {
            $this->algorithm = PASSWORD_ARGON2I;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hash_password($password)
    {
        return password_hash($password, $this->algorithm);
    }

    /**
     * {@inheritdoc}
     */
    public function is_hash_supported($hash)
    {
        if (0 === strpos($hash, '$argon2id$')) {
            return defined('PASSWORD_ARGON2ID');
        } elseif (0 === strpos($hash, '$argon2i$')) {
            return defined('PASSWORD_ARGON2I');
        }

        return 0 === strpos($hash, '$2');
    }

    /**
     * {@inheritdoc}
     */
    public function is_hash_valid($hash)
    {
        return !password_needs_rehash($hash, $this->algorithm);
    }

    /**
     * {@inheritdoc}
     */
    public function is_password_valid($password, $hash)
    {
        return password_verify($password, $hash);
    }
}
