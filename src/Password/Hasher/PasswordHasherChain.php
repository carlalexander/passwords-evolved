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
 * Manages a chain of password hashers.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class PasswordHasherChain implements PasswordHasherInterface
{
    /**
     * The password hashers that the chain handles.
     *
     * @var PasswordHasherInterface[]
     */
    private $password_hashers;

    /**
     * Constructor.
     *
     * @param PasswordHasherInterface[] $password_hashers
     */
    public function __construct(array $password_hashers = [])
    {
        $this->password_hashers = array_filter($password_hashers, function ($password_hasher) {
            return $password_hasher instanceof PasswordHasherInterface;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function hash_password($password)
    {
        $hash = array_reduce($this->password_hashers, function ($hash, PasswordHasherInterface $password_hasher) use ($password) {
            if (empty($hash)) {
                $hash = $password_hasher->hash_password($password);
            }

            return $hash;
        });

        if (empty($hash)) {
            throw new \RuntimeException('Could not create a hash for the given password.');
        }

        return $hash;
    }

    /**
     * {@inheritdoc}
     */
    public function is_hash_supported($hash)
    {
        return $this->get_password_hasher($hash) instanceof PasswordHasherInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function is_hash_valid($hash)
    {
        $password_hasher = $this->get_password_hasher($hash);

        if (!$password_hasher instanceof PasswordHasherInterface) {
            return false;
        }

        return $password_hasher->is_hash_valid($hash);
    }

    /**
     * {@inheritdoc}
     */
    public function is_password_valid($password, $hash)
    {
        return array_reduce($this->password_hashers, function ($check, PasswordHasherInterface $password_hasher) use ($password, $hash) {
            if (true !== $check) {
                $check = $password_hasher->is_password_valid($password, $hash);
            }

            return $check;
        }, false);
    }

    /**
     * Get the password hasher that supports the given hash.
     *
     * @param string $hash
     *
     * @return PasswordHasherInterface|null
     */
    private function get_password_hasher($hash)
    {
        return array_reduce($this->password_hashers, function ($found, PasswordHasherInterface $password_hasher) use ($hash) {
            if (!$found instanceof PasswordHasherInterface && $password_hasher->is_hash_supported($hash)) {
                $found = $password_hasher;
            }

            return $found;
        });
    }
}
