<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\EventManagement;

/**
 * Used by classes that want to access the event manager via setter injection.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
interface EventManagerAwareInterface
{
    /**
     * Set the event manager.
     *
     * @param EventManager $event_manager
     */
    public function set_event_manager(EventManager $event_manager);
}
