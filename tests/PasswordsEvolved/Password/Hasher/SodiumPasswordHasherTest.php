<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\Tests\Password\Hasher;

use PasswordsEvolved\Password\Hasher\SodiumPasswordHasher;
use PasswordsEvolved\Tests\Traits\FunctionMockTrait;

class SodiumPasswordHasherTest extends \PHPUnit_Framework_TestCase
{
    use FunctionMockTrait;

    public function test_hash_password_without_libsodium()
    {
        if (SodiumPasswordHasher::is_libsodium_available()) {
            $this->markTestSkipped('Libsodium is available.');
        }

        $hasher = new SodiumPasswordHasher();

        $this->assertNull($hasher->hash_password('password'));
    }

    public function test_is_hash_supported_without_libsodium()
    {
        if (SodiumPasswordHasher::is_libsodium_available()) {
            $this->markTestSkipped('Libsodium is available.');
        }

        $hasher = new SodiumPasswordHasher();

        $this->assertFalse($hasher->is_hash_supported('$P$hashed_foobar'));
        $this->assertFalse($hasher->is_hash_supported('$2y$hashed_foobar'));
        $this->assertFalse($hasher->is_hash_supported('$argon2i$hashed_foobar'));
        $this->assertFalse($hasher->is_hash_supported('$argon2id$hashed_foobar'));
    }

    public function test_is_hash_supported_with_libsodium()
    {
        if (!SodiumPasswordHasher::is_libsodium_available()) {
            $this->markTestSkipped('Libsodium isn\'t available.');
        }

        $hasher = new SodiumPasswordHasher();

        $this->assertFalse($hasher->is_hash_supported('$P$hashed_foobar'));
        $this->assertFalse($hasher->is_hash_supported('$2y$hashed_foobar'));
        $this->assertTrue($hasher->is_hash_supported('$argon2i$hashed_foobar'));
        $this->assertFalse($hasher->is_hash_supported('$argon2id$hashed_foobar'));
    }

    public function test_is_hash_valid_without_libsodium()
    {
        if (SodiumPasswordHasher::is_libsodium_available()) {
            $this->markTestSkipped('Libsodium is available.');
        }

        $hasher = new SodiumPasswordHasher();

        $this->assertFalse($hasher->is_hash_valid('$argon2i$hashed_foobar'));
    }

    public function test_is_hash_valid_with_libsodium()
    {
        if (!SodiumPasswordHasher::is_libsodium_available()) {
            $this->markTestSkipped('Libsodium isn\'t available.');
        }

        $hasher = new SodiumPasswordHasher();

        $valid_hash = $hasher->hash_password('password');
        $invalid_hash = sodium_crypto_pwhash_str('password', 6, 64 * 1024 * 1024);

        $this->assertTrue($hasher->is_hash_valid($valid_hash));
        $this->assertFalse($hasher->is_hash_valid($invalid_hash));
    }

    public function test_password_validation()
    {
        if (!SodiumPasswordHasher::is_libsodium_available()) {
            $this->markTestSkipped('Libsodium isn\'t available.');
        }

        $hasher = new SodiumPasswordHasher();

        $hash = $hasher->hash_password('password');

        $this->assertTrue($hasher->is_password_valid('password', $hash));
        $this->assertFalse($hasher->is_password_valid('another_password', $hash));
    }
}
