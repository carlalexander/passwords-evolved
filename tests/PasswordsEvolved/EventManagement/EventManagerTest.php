<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\Tests\EventManagement;

use PasswordsEvolved\EventManagement\EventManager;
use PasswordsEvolved\Tests\Traits\FunctionMockTrait;

class EventManagerTest extends \PHPUnit_Framework_TestCase
{
    use FunctionMockTrait;

    /**
     * @var EventManager
     */
    private $manager;

    protected function setUp()
    {
        $this->manager = new EventManager();
    }

    protected function tearDown()
    {
        $this->manager = null;
    }

    public function test_add_callback()
    {
        $add_filter = $this->getFunctionMock($this->getNamespace(EventManager::class), 'add_filter');
        $add_filter->expects($this->once())
                   ->with($this->equalTo('foo'), $this->equalTo('on_foo'), $this->equalTo(5), $this->equalTo(2));

        $this->manager->add_callback('foo', 'on_foo', 5, 2);
    }

    public function test_add_subscriber()
    {
        $subscriber = new TestSubscriber();

        $add_filter = $this->getFunctionMock($this->getNamespace(EventManager::class), 'add_filter');
        $add_filter->expects($this->exactly(3))
                   ->withConsecutive(
                       array($this->equalTo('foo'), $this->identicalTo(array($subscriber, 'on_foo')), $this->equalTo(10), $this->equalTo(1)),
                       array($this->equalTo('bar'), $this->identicalTo(array($subscriber, 'on_bar')), $this->equalTo(5), $this->equalTo(1)),
                       array($this->equalTo('foobar'), $this->identicalTo(array($subscriber, 'on_foobar')), $this->equalTo(5), $this->equalTo(2))
                   );

        $this->manager->add_subscriber($subscriber);
    }

    public function test_add_event_manager_aware_subscriber()
    {
        $subscriber = new TestEventManagerAwareSubscriber();

        $add_filter = $this->getFunctionMock($this->getNamespace(EventManager::class), 'add_filter');
        $add_filter->expects($this->exactly(3))
                   ->withConsecutive(
                       array($this->equalTo('foo'), $this->identicalTo(array($subscriber, 'on_foo')), $this->equalTo(10), $this->equalTo(1)),
                       array($this->equalTo('bar'), $this->identicalTo(array($subscriber, 'on_bar')), $this->equalTo(5), $this->equalTo(1)),
                       array($this->equalTo('foobar'), $this->identicalTo(array($subscriber, 'on_foobar')), $this->equalTo(5), $this->equalTo(2))
                   );

        $this->manager->add_subscriber($subscriber);

        $reflection = new \ReflectionObject($subscriber);
        $eventManagerProperty = $reflection->getProperty('event_manager');
        $eventManagerProperty->setAccessible(true);

        $this->assertSame($this->manager, $eventManagerProperty->getValue($subscriber));
    }

    public function test_execute()
    {
        $do_action_ref_array = $this->getFunctionMock($this->getNamespace(EventManager::class), 'do_action_ref_array');
        $do_action_ref_array->expects($this->once())
                            ->with($this->equalTo('foo'), $this->equalTo(array('bar')));

        $this->manager->execute('foo', 'bar');
    }

    public function test_filter()
    {
        $apply_filters_ref_array = $this->getFunctionMock($this->getNamespace(EventManager::class), 'apply_filters_ref_array');
        $apply_filters_ref_array->expects($this->once())
                                ->with($this->equalTo('foo'), $this->equalTo(array('bar')))
                                ->willReturn('foobar');

        $this->assertEquals('foobar', $this->manager->filter('foo', 'bar'));
    }

    public function test_get_current_hook()
    {
        $current_filter = $this->getFunctionMock($this->getNamespace(EventManager::class), 'current_filter');
        $current_filter->expects($this->once())
                       ->willReturn('foo');

        $this->assertEquals('foo', $this->manager->get_current_hook());
    }

    public function test_has_callback()
    {
        $has_filter = $this->getFunctionMock($this->getNamespace(EventManager::class), 'has_filter');
        $has_filter->expects($this->once())
                   ->with($this->equalTo('foo'), $this->equalTo('on_foo'))
                   ->willReturn(10);

        $this->assertEquals(10, $this->manager->has_callback('foo', 'on_foo'));
    }

    public function test_remove_callback()
    {
        $remove_filter = $this->getFunctionMock($this->getNamespace(EventManager::class), 'remove_filter');
        $remove_filter->expects($this->once())
                      ->with($this->equalTo('foo'), $this->equalTo('on_foo'), $this->equalTo(2))
                      ->willReturn(true);

        $this->assertTrue($this->manager->remove_callback('foo', 'on_foo', 2));
    }

    public function test_remove_subscriber()
    {
        $subscriber = new TestSubscriber();

        $remove_filter = $this->getFunctionMock($this->getNamespace(EventManager::class), 'remove_filter');
        $remove_filter->expects($this->exactly(3))
                      ->withConsecutive(
                          array($this->equalTo('foo'), $this->identicalTo(array($subscriber, 'on_foo')), $this->equalTo(10)),
                          array($this->equalTo('bar'), $this->identicalTo(array($subscriber, 'on_bar')), $this->equalTo(5)),
                          array($this->equalTo('foobar'), $this->identicalTo(array($subscriber, 'on_foobar')), $this->equalTo(5))
                      );

        $this->manager->remove_subscriber($subscriber);
    }
}
