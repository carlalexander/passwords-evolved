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
 * A password hasher hashes a password using a hashing algorithm.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
interface PasswordHasherInterface
{
    /**
     * Hashes the given password. Returns null if unable to hash password.
     *
     * @param string $password
     *
     * @return string|null
     */
    public function hash_password($password);

    /**
     * Checks if the password hasher supports the given hash for verification.
     *
     * @param string $hash
     *
     * @return bool
     */
    public function is_hash_supported($hash);

    /**
     * Checks if the given hash is valid. If a hash is invalid, we need to rehash it.
     *
     * @param string $hash
     *
     * @return bool
     */
    public function is_hash_valid($hash);

    /**
     * Validates that the given password matches the given hash.
     *
     * @param string $password
     * @param string $hash
     *
     * @return bool
     */
    public function is_password_valid($password, $hash);
}
