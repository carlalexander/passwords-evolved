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

use PasswordsEvolved\Password\Hasher\WordPressPasswordHasher;

class WordPressPasswordHasherTest extends \PHPUnit_Framework_TestCase
{
    public function test_hash_password()
    {
        $wp_hasher = $this->get_wp_hasher_mock();
        $wp_hasher->expects($this->once())
                  ->method('HashPassword')
                  ->with($this->equalTo('password'))
                  ->willReturn('$P$password');

        $hasher = new WordPressPasswordHasher($wp_hasher);

        $this->assertEquals('$P$password', $hasher->hash_password('password'));
    }

    public function test_is_hash_supported()
    {
        $hasher = new WordPressPasswordHasher($this->get_wp_hasher_mock());

        $this->assertTrue($hasher->is_hash_supported('$P$password'));
        $this->assertFalse($hasher->is_hash_supported('$2y$password'));
    }

    public function test_is_hash_valid()
    {
        $hasher = new WordPressPasswordHasher($this->get_wp_hasher_mock());

        $this->assertFalse($hasher->is_hash_valid('$P$password'));
    }

    public function test_is_password_valid()
    {
        $wp_hasher = $this->get_wp_hasher_mock();
        $wp_hasher->expects($this->once())
                  ->method('CheckPassword')
                  ->with($this->equalTo('password'), $this->equalTo('$P$password'))
                  ->willReturn(true);

        $hasher = new WordPressPasswordHasher($wp_hasher);

        $this->assertTrue($hasher->is_password_valid('password', '$P$password'));
    }

    /**
     * Creates a PasswordHash mock object.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function get_wp_hasher_mock()
    {
        return $this->getMockBuilder(\PasswordHash::class)->disableOriginalConstructor()->getMock();
    }
}
