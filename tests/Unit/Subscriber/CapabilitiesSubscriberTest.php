<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\Tests\Unit\Subscriber;

use PasswordsEvolved\Subscriber\CapabilitiesSubscriber;
use PasswordsEvolved\Tests\Mock\FunctionMockTrait;
use PHPUnit\Framework\TestCase;

class CapabilitiesSubscriberTest extends TestCase
{
    use FunctionMockTrait;

    public function test_get_subscribed_events()
    {
        $callbacks = CapabilitiesSubscriber::get_subscribed_events();

        foreach ($callbacks as $callback) {
            $this->assertTrue(method_exists(CapabilitiesSubscriber::class, is_array($callback) ? $callback[0] : $callback));
        }
    }

    public function test_ensure_roles_have_capability()
    {
        $admistrator_role = $this->get_role_mock();
        $admistrator_role->expects($this->once())
                         ->method('add_cap')
                         ->with($this->equalTo('passwords_evolved_enforce_password'), $this->identicalTo(true));

        $editor_role = $this->get_role_mock();
        $editor_role->expects($this->once())
                    ->method('add_cap')
                    ->with($this->equalTo('passwords_evolved_enforce_password'), $this->identicalTo(false));

        $roles = $this->get_roles_mock();
        $roles->role_objects = array('administrator' => $admistrator_role, 'editor' => $editor_role);

        $subscriber = new CapabilitiesSubscriber(array('administrator'), $roles);

        $subscriber->ensure_roles_have_capability();
    }

    /**
     * Creates a mock of the WordPress role object.
     */
    private function get_role_mock()
    {
        return $this->getMockBuilder(\WP_Role::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * Creates a mock of the WordPress roles object.
     */
    private function get_roles_mock()
    {
        return $this->getMockBuilder(\WP_Roles::class)->disableOriginalConstructor()->getMock();
    }
}
