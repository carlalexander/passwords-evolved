<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\Tests\Password;

use PasswordsEvolved\Password\Hasher;
use phpmock\phpunit\PHPMock;

class HasherTest extends \PHPUnit_Framework_TestCase
{
    use PHPMock;

    public function test_hash_password()
    {
        $password_hash = $this->getFunctionMock('PasswordsEvolved\Password', 'password_hash');
        $password_hash->expects($this->once())
                      ->with($this->equalTo('foobar'), $this->identicalTo(PASSWORD_BCRYPT))
                      ->willReturn('hashed_foobar');

        $hasher = new Hasher($this->get_wp_hasher_mock());

        $this->assertEquals('hashed_foobar', $hasher->hash_password('foobar'));
    }

    public function test_is_hash_valid()
    {
        $password_needs_rehash = $this->getFunctionMock('PasswordsEvolved\Password', 'password_needs_rehash');
        $password_needs_rehash->expects($this->once())
                              ->with($this->equalTo('hashed_foobar'), $this->identicalTo(PASSWORD_BCRYPT))
                              ->willReturn(true);

        $hasher = new Hasher($this->get_wp_hasher_mock());

        $this->assertTrue($hasher->is_hash_valid('hashed_foobar'));
    }

    public function test_verify_password_with_php()
    {
        $password_verify = $this->getFunctionMock('PasswordsEvolved\Password', 'password_verify');
        $password_verify->expects($this->once())
                        ->with($this->equalTo('foobar'), $this->equalTo('$2y$hashed_foobar'))
                        ->willReturn(true);

        $wp_hasher = $this->get_wp_hasher_mock();
        $wp_hasher->expects($this->never())
                  ->method('CheckPassword');

        $hasher = new Hasher($wp_hasher);

        $this->assertTrue($hasher->verify_password('foobar', '$2y$hashed_foobar'));
    }

    public function test_verify_password_with_wordpress()
    {
        $password_verify = $this->getFunctionMock('PasswordsEvolved\Password', 'password_verify');
        $password_verify->expects($this->never());

        $wp_hasher = $this->get_wp_hasher_mock();
        $wp_hasher->expects($this->once())
                  ->method('CheckPassword')
                  ->with($this->equalTo('foobar'), $this->equalTo('$P$hashed_foobar'))
                  ->willReturn(true);

        $hasher = new Hasher($wp_hasher);

        $this->assertTrue($hasher->verify_password('foobar', '$P$hashed_foobar'));
    }

    /**
     * Creates a mock of the password hash.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function get_wp_hasher_mock()
    {
        return $this->getMockBuilder('PasswordHash')
            ->disableOriginalConstructor()
            ->getMock();
    }
}