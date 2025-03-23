<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\Tests\Unit\Subscriber;

use PasswordsEvolved\Subscriber\PasswordHashingSubscriber;
use PHPUnit\Framework\TestCase;

class PasswordHashingSubscriberTest extends TestCase
{
    public function test_get_subscribed_events()
    {
        $callbacks = PasswordHashingSubscriber::get_subscribed_events();

        foreach ($callbacks as $callback) {
            $this->assertTrue(method_exists(PasswordHashingSubscriber::class, is_array($callback) ? $callback[0] : $callback));
        }
    }

    public function test_set_password_hashing_algorithm()
    {
        $expectedAlgorithm = 'algorithm';

        if (PHP_VERSION_ID >= 70300) {
            $expectedAlgorithm = PASSWORD_ARGON2ID;
        } elseif (PHP_VERSION_ID >= 70200) {
            $expectedAlgorithm = PASSWORD_ARGON2I;
        }

        $this->assertEquals($expectedAlgorithm, (new PasswordHashingSubscriber())->set_password_hashing_algorithm('algorithm'));
    }

    public function test_set_password_hashing_algorithm_prioritizes_argon2id_over_argon2i()
    {
        if (PHP_VERSION_ID < 70300) {
            $this->markTestSkipped('This test requires PHP 7.3 or higher where both PASSWORD_ARGON2ID and PASSWORD_ARGON2I are defined.');
        }

        $this->assertTrue(defined('PASSWORD_ARGON2ID'));
        $this->assertTrue(defined('PASSWORD_ARGON2I'));

        $result = (new PasswordHashingSubscriber())->set_password_hashing_algorithm('algorithm');
        $this->assertEquals(PASSWORD_ARGON2ID, $result);
        $this->assertNotEquals(PASSWORD_ARGON2I, $result);
    }
}
