<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\Tests\Unit\Password\Hasher;

use PasswordsEvolved\Password\Hasher\NativePasswordHasher;
use PHPUnit\Framework\TestCase;

class NativePasswordHasherTest extends TestCase
{
    public function test_constructor()
    {
        $algorithm = PASSWORD_DEFAULT;

        if (defined('PASSWORD_ARGON2ID')) {
            $algorithm = PASSWORD_ARGON2ID;
        } elseif (defined('PASSWORD_ARGON2I')) {
            $algorithm = PASSWORD_ARGON2I;
        }

        $hasher = new NativePasswordHasher();

        $reflection = new \ReflectionObject($hasher);
        $algorithmProperty = $reflection->getProperty('algorithm');
        $algorithmProperty->setAccessible(true);

        $this->assertSame($algorithm, $algorithmProperty->getValue($hasher));
    }

    public function is_hash_supported()
    {
        $argon2i_supported = PHP_VERSION_ID >= 70200;
        $argon2id_supported = PHP_VERSION_ID >= 70300;

        $hasher = new NativePasswordHasher();

        $this->assertFalse($hasher->is_hash_supported('$P$hashed_foobar'));
        $this->assertTrue($hasher->is_hash_supported('$2y$hashed_foobar'));
        $this->assertSame($argon2i_supported, $hasher->is_hash_supported('$argon2i$hashed_foobar'));
        $this->assertSame($argon2id_supported, $hasher->is_hash_supported('$argon2id$hashed_foobar'));
    }

    public function test_is_hash_valid_bcrypt()
    {
        $hasher = new NativePasswordHasher();
        $hash = password_hash('password', PASSWORD_BCRYPT);
        $valid = PHP_VERSION_ID < 70200;

        $this->assertSame($valid, $hasher->is_hash_valid($hash));
    }

    public function test_is_hash_valid_argon2i()
    {
        if (PHP_VERSION_ID < 70200) {
            $this->markTestSkipped('Argon2i hashing algorithm is only available on PHP 7.2 or higher');
        }

        $hasher = new NativePasswordHasher();
        $hash = password_hash('password', PASSWORD_ARGON2I);
        $valid = PHP_VERSION_ID < 70300;

        $this->assertSame($valid, $hasher->is_hash_valid($hash));
    }

    public function test_is_hash_valid_argon2id()
    {
        if (PHP_VERSION_ID < 70300) {
            $this->markTestSkipped('Argon2id hashing algorithm is only available on PHP 7.3 or higher');
        }

        $hasher = new NativePasswordHasher();
        $hash = password_hash('password', PASSWORD_ARGON2ID);

        $this->assertTrue($hasher->is_hash_valid($hash));
    }

    public function test_password_validation()
    {
        $hasher = new NativePasswordHasher();

        $hash = $hasher->hash_password('password');

        $this->assertTrue($hasher->is_password_valid('password', $hash));
        $this->assertFalse($hasher->is_password_valid('another_password', $hash));
    }
}
