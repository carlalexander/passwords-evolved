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

use PasswordsEvolved\EventManagement\SubscriberInterface;

class TestSubscriber implements SubscriberInterface
{
    public static function get_subscribed_events()
    {
        return array(
            'foo' => 'on_foo',
            'bar' => array('on_bar', 5),
            'foobar' => array('on_foobar', 5, 2)
        );
    }
}
