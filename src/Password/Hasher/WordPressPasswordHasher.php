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
 * Password hasher that uses the WordPress password hashing library.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class WordPressPasswordHasher implements PasswordHasherInterface
{
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
     * {@inheritdoc}
     */
    public function hash_password($password)
    {
        return $this->wordpress_hasher->HashPassword($password);
    }

    /**
     * {@inheritdoc}
     */
    public function is_hash_supported($hash)
    {
        return 0 === strpos($hash, '$P$');
    }

    /**
     * {@inheritdoc}
     */
    public function is_hash_valid($hash)
    {
        // Never consider a WordPress hashed password valid even if it is. We should always try to rehash them.s
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function is_password_valid($password, $hash)
    {
        return $this->wordpress_hasher->CheckPassword($password, $hash);
    }
}
