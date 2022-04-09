<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\Tests\Unit;

use PasswordsEvolved\Options;
use PasswordsEvolved\Tests\Mock\FunctionMockTrait;
use PHPUnit\Framework\TestCase;

class OptionsTest extends TestCase
{
    use FunctionMockTrait;

    /**
     * @var Options
     */
    protected $options;

    protected function setUp(): void
    {
        $this->options = new Options();
    }

    protected function tearDown(): void
    {
        $this->options = null;
    }

    public function test_get_option_name()
    {
        $this->assertEquals('passwords_evolved_test', $this->options->get_option_name('test'));
    }

    public function test_get()
    {
        $get_option = $this->getFunctionMock($this->getNamespace(Options::class), 'get_network_option');
        $get_option->expects($this->exactly(3))
                   ->withConsecutive(
                       array($this->identicalTo(null), $this->equalTo('passwords_evolved_foo'), $this->identicalTo(null)),
                       array($this->identicalTo(null), $this->equalTo('passwords_evolved_foo'), $this->identicalTo(array())),
                       array($this->identicalTo(null), $this->equalTo('passwords_evolved_foo'), $this->identicalTo('bar'))
                   )
                   ->willReturn('bar');

        $this->assertSame('bar', $this->options->get('foo'));
        $this->assertSame(array('bar'), $this->options->get('foo', array()));
        $this->assertSame('bar', $this->options->get('foo', 'bar'));
    }

    public function test_has()
    {
        $get_option = $this->getFunctionMock($this->getNamespace(Options::class), 'get_network_option');
        $get_option->expects($this->exactly(2))
                   ->withConsecutive(
                       [$this->identicalTo(null), $this->equalTo('passwords_evolved_foo'), $this->equalTo(null)],
                       [$this->identicalTo(null), $this->equalTo('passwords_evolved_bar'), $this->equalTo(null)]
                   )
                   ->willReturnOnConsecutiveCalls('foobar', null);

        $this->assertTrue($this->options->has('foo'));
        $this->assertFalse($this->options->has('bar'));
    }

    public function test_remove()
    {
        $delete_option = $this->getFunctionMock($this->getNamespace(Options::class), 'delete_network_option');
        $delete_option->expects($this->once())
                      ->with($this->identicalTo(null), $this->equalTo('passwords_evolved_foo'));

        $this->options->remove('foo');
    }

    public function test_set()
    {
        $delete_option = $this->getFunctionMock($this->getNamespace(Options::class), 'update_network_option');
        $delete_option->expects($this->once())
                      ->with($this->identicalTo(null), $this->equalTo('passwords_evolved_foo'), $this->equalTo('bar'));

        $this->options->set('foo', 'bar');
    }
}
