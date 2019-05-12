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

use PasswordsEvolved\Password\Hasher\PasswordHasherChain;
use PasswordsEvolved\Password\Hasher\PasswordHasherInterface;

class PasswordHasherChainTest extends \PHPUnit_Framework_TestCase
{
    public function test_hash_password_first_try()
    {
        $hasher1 = $this->get_password_hasher_mock();
        $hasher1->expects($this->once())
                ->method('hash_password')
                ->with($this->equalTo('password'))
                ->willReturn('$2y$password');

        $hasher2 = $this->get_password_hasher_mock();
        $hasher2->expects($this->never())
                ->method('hash_password');

        $chain = new PasswordHasherChain([$hasher1, $hasher2]);

        $this->assertEquals('$2y$password', $chain->hash_password('password'));
    }

    public function test_hash_password_second_try()
    {
        $hasher1 = $this->get_password_hasher_mock();
        $hasher1->expects($this->once())
                ->method('hash_password')
                ->with($this->equalTo('password'))
                ->willReturn(null);

        $hasher2 = $this->get_password_hasher_mock();
        $hasher2->expects($this->once())
                ->method('hash_password')
                ->with($this->equalTo('password'))
                ->willReturn('$P$password');

        $chain = new PasswordHasherChain([$hasher1, $hasher2]);

        $this->assertEquals('$P$password', $chain->hash_password('password'));
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Could not create a hash for the given password.
     */
    public function test_hash_password_exception()
    {
        $chain = new PasswordHasherChain();

        $this->assertEquals('$P$password', $chain->hash_password('password'));
    }

    public function test_is_hash_supported()
    {
        $hasher1 = $this->get_password_hasher_mock();
        $hasher1->expects($this->exactly(2))
                ->method('is_hash_supported')
                ->withConsecutive(
                    array($this->equalTo('$2y$password')),
                    array($this->equalTo('$P$password'))
                )
                ->willReturnOnConsecutiveCalls(true, false);

        $hasher2 = $this->get_password_hasher_mock();
        $hasher2->expects($this->once())
                ->method('is_hash_supported')
                ->with($this->equalTo('$P$password'))
                ->willReturn(false);

        $chain = new PasswordHasherChain([$hasher1, $hasher2]);

        $this->assertTrue($chain->is_hash_supported('$2y$password'));
        $this->assertFalse($chain->is_hash_supported('$P$password'));
    }

    public function test_is_hash_valid()
    {
        $hasher1 = $this->get_password_hasher_mock();
        $hasher1->expects($this->exactly(2))
                ->method('is_hash_supported')
                ->withConsecutive(
                    array($this->equalTo('$2y$password')),
                    array($this->equalTo('$P$password'))
                )
                ->willReturnOnConsecutiveCalls(true, false);
        $hasher1->expects($this->once())
                ->method('is_hash_valid')
                ->with($this->equalTo('$2y$password'))
                ->willReturn(true);

        $hasher2 = $this->get_password_hasher_mock();
        $hasher2->expects($this->once())
                ->method('is_hash_supported')
                ->with($this->equalTo('$P$password'))
                ->willReturn(false);

        $chain = new PasswordHasherChain([$hasher1, $hasher2]);

        $this->assertTrue($chain->is_hash_valid('$2y$password'));
        $this->assertFalse($chain->is_hash_valid('$P$password'));
    }

    public function test_is_password_valid()
    {
        $hasher1 = $this->get_password_hasher_mock();
        $hasher1->expects($this->exactly(2))
                ->method('is_password_valid')
                ->withConsecutive(
                    array($this->equalTo('password'), $this->equalTo('$2y$password')),
                    array($this->equalTo('password'), $this->equalTo('$P$password'))
                )
                ->willReturnOnConsecutiveCalls(true, false);

        $hasher2 = $this->get_password_hasher_mock();
        $hasher2->expects($this->once())
                ->method('is_password_valid')
                ->with($this->equalTo('password'), $this->equalTo('$P$password'))
                ->willReturn(false);

        $chain = new PasswordHasherChain([$hasher1, $hasher2]);

        $this->assertTrue($chain->is_password_valid('password', '$2y$password'));
        $this->assertFalse($chain->is_password_valid('password', '$P$password'));
    }

    /**
     * Creates a PasswordHasherInterface mock object.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function get_password_hasher_mock()
    {
        return $this->getMockBuilder(PasswordHasherInterface::class)->getMock();
    }
}
