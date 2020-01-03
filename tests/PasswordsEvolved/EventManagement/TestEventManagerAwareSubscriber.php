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
use PasswordsEvolved\EventManagement\EventManagerAwareInterface;

class TestEventManagerAwareSubscriber extends TestSubscriber implements EventManagerAwareInterface
{
    protected $event_manager;

    public static function get_subscribed_events()
    {
        return array(
            'foo' => 'on_foo',
            'bar' => array('on_bar', 5),
            'foobar' => array('on_foobar', 5, 2)
        );
    }

    public function set_event_manager(EventManager $event_manager)
    {
        $this->event_manager = $event_manager;
    }
}
