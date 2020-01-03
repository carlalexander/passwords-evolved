<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\Subscriber;

use PasswordsEvolved\EventManagement\EventManager;
use PasswordsEvolved\EventManagement\EventManagerAwareInterface;
use PasswordsEvolved\EventManagement\SubscriberInterface;

/**
 * AbstractEventManagerAwareSubscriber is used by subscribers that want access to the event manager.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
abstract class AbstractEventManagerAwareSubscriber implements EventManagerAwareInterface, SubscriberInterface
{
    /**
     * WordPress Plugin API manager.
     *
     * @var EventManager
     */
    protected $event_manager;

    /**
     * {@inheritdoc}
     */
    public function set_event_manager(EventManager $event_manager)
    {
        $this->event_manager = $event_manager;
    }
}