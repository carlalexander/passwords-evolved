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

use PasswordsEvolved\EventManagement\SubscriberInterface;

/**
 * Subscriber that handles WordPress password hashing.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class PasswordHashingSubscriber implements SubscriberInterface
{
    /**
     * {@inheritdoc}
     */
    public static function get_subscribed_events()
    {
        return array(
            'wp_hash_password_algorithm' => 'set_password_hashing_algorithm',
        );
    }

    /**
     * Set the password hashing algorithm.
     *
     * @param mixed $algorithm
     */
    public function set_password_hashing_algorithm($algorithm)
    {
        if (defined('PASSWORD_ARGON2ID')) {
            $algorithm = PASSWORD_ARGON2ID;
        } elseif (defined('PASSWORD_ARGON2I')) {
            $algorithm = PASSWORD_ARGON2I;
        }

        return $algorithm;
    }
}
