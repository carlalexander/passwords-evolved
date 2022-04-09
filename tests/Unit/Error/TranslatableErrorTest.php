<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\Tests\Unit\Error;

use PasswordsEvolved\Error\TranslatableError;
use PasswordsEvolved\Tests\Mock\FunctionMockTrait;
use PHPUnit\Framework\TestCase;

class TranslatableErrorTest extends TestCase
{
    use FunctionMockTrait;

    public function test_get_error_code()
    {
        $error = new TranslatableError('test');

        $this->assertEquals('test', $error->get_error_code());
    }

    public function test_get_error_codes()
    {
        $error = new TranslatableError('test');

        $this->assertEquals(array('test'), $error->get_error_codes());
    }

    public function test_get_error_messages()
    {
        $error = new TranslatableError('test', array('foo' => 'bar'));

        $__ = $this->getFunctionMock($this->getNamespace(TranslatableError::class), '__');
        $__->expects($this->once())
            ->with($this->equalTo('error.test'), $this->equalTo('passwords-evolved'))
            ->willReturn('foobar');

        $this->assertEquals(array('foobar'), $error->get_error_messages());
    }

    public function test_get_error_messages_with_placeholder()
    {
        $error = new TranslatableError('test', array('foo' => 'bar'));

        $__ = $this->getFunctionMock($this->getNamespace(TranslatableError::class), '__');
        $__->expects($this->once())
            ->with($this->equalTo('error.test'), $this->equalTo('passwords-evolved'))
            ->willReturn('foo "%s"');

        $this->assertEquals(array('foo "bar"'), $error->get_error_messages());
    }
}
