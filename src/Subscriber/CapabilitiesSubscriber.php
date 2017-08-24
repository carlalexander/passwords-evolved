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
 * Subscriber that ensures that every WordPress role has the password enforcement
 * capability either active or inactive.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class CapabilitiesSubscriber implements SubscriberInterface
{
    /**
     * Array of roles that need their passwords enforced.
     *
     * @var array
     */
    private $enforced_roles;

    /**
     * WordPress roles object.
     *
     * @var \WP_Roles
     */
    private $roles;

    /**
     * Constructor.
     *
     * @param array     $enforced_roles
     * @param \WP_Roles $roles
     */
    public function __construct(array $enforced_roles, \WP_Roles $roles)
    {
        $this->enforced_roles = $enforced_roles;
        $this->roles = $roles;
    }

    /**
     * {@inheritdoc}
     */
    public static function get_subscribed_events()
    {
        return array(
            'init' => 'ensure_roles_have_capability',
        );
    }

    /**
     * Ensure that every WordPress role has the password enforcement
     * capability either active or inactive.
     */
    public function ensure_roles_have_capability()
    {
        foreach ($this->roles->role_objects as $role => $role_object) {
            $role_object->add_cap('passwords_evolved_enforce_password', in_array($role, $this->enforced_roles));
        }
    }
}
