<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\Tests\DependencyInjection;

use PasswordsEvolved\DependencyInjection\Container;
use PasswordsEvolved\DependencyInjection\ContainerConfigurationInterface;

class ContainerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Container
     */
    private $container;

    protected function setUp()
    {
        $this->container = new Container();
    }

    protected function tearDown()
    {
        $this->container = null;
    }

    public function test_constructor()
    {
        $arguments = array('foo' => 'bar');
        $container = new Container($arguments);

        $this->assertEquals($arguments['foo'], $container['foo']);
    }

    public function test_configure_array_cast()
    {
        $configuration = $this->get_configuration_mock();
        $configuration->expects($this->once())
                      ->method('modify')
                      ->with($this->identicalTo($this->container));

        $this->container->configure($configuration);
    }

    public function test_configure_with_array()
    {
        $foo_configuration = $this->get_configuration_mock();
        $foo_configuration->expects($this->once())
                          ->method('modify')
                          ->with($this->identicalTo($this->container));

        $bar_configuration = $this->get_configuration_mock();
        $bar_configuration->expects($this->once())
                          ->method('modify')
                          ->with($this->identicalTo($this->container));

        $this->container->configure(array($foo_configuration, $bar_configuration));
    }

    public function test_isset()
    {
        $this->container['null'] = null;
        $this->container['param'] = 'value';
        $this->container['service'] = function () {
            return new \stdClass();
        };

        $this->assertTrue(isset($this->container['null']));
        $this->assertTrue(isset($this->container['param']));
        $this->assertTrue(isset($this->container['service']));
        $this->assertFalse(isset($this->container['non_existent']));
    }

    public function test_lock()
    {
        $this->assertFalse($this->container->is_locked());

        $this->container->lock();

        $this->assertTrue($this->container->is_locked());
    }

    public function test_offset_get()
    {
        $this->container['null'] = null;
        $this->container['param'] = 'value';
        $this->container['service'] = function () {
            return new \stdClass();
        };

        $this->assertNull($this->container['null']);
        $this->assertEquals('value', $this->container['param']);
        $this->assertInstanceOf('stdClass', $this->container['service']);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Container doesn't have a value stored for the "foo" key.
     */
    public function test_offset_get_exception()
    {
        echo $this->container['foo'];
    }

    public function test_offset_get_locks_container()
    {
        $this->assertFalse($this->container->is_locked());

        $this->container['param'] = 'value';

        $this->assertEquals('value', $this->container['param']);
        $this->assertTrue($this->container->is_locked());
    }

    /**
     * @expectedException RuntimeException
     * @expectedExceptionMessage Container is locked and cannot be modified.
     */
    public function test_offset_set_when_locked()
    {
        $this->container->lock();

        $this->container['param'] = 'value';
    }

    public function test_unset()
    {
        $this->container['null'] = null;
        $this->container['param'] = 'value';

        unset($this->container['null'], $this->container['param']);

        $this->assertFalse(isset($this->container['null']));
        $this->assertFalse(isset($this->container['param']));
    }

    public function test_service()
    {
        $this->container['service'] = $this->container->service(function(Container $container) {
           return new \stdClass();
        });

        $foo_service = $this->container['service'];
        $this->assertInstanceOf('stdClass', $foo_service);

        $bar_service = $this->container['service'];
        $this->assertInstanceOf('stdClass', $bar_service);

        $this->assertSame($foo_service, $bar_service);
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Service definition is not a Closure or invokable object.
     */
    public function test_service_exception()
    {
        $this->container->service('foo');
    }

    /**
     * Creates a mock of a ContainerConfigurationInterface object.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function get_configuration_mock()
    {
        return $this->getMockBuilder(ContainerConfigurationInterface::class)->getMock();
    }
}
