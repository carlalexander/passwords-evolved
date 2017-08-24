<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\Tests\Subscriber;

use PasswordsEvolved\Subscriber\UserWarningSubscriber;
use phpmock\phpunit\PHPMock;

class UserWarningSubscriberTest extends \PHPUnit_Framework_TestCase
{
    use PHPMock;

    public function test_get_subscribed_events()
    {
        $callbacks = UserWarningSubscriber::get_subscribed_events();

        foreach ($callbacks as $callback) {
            $this->assertTrue(method_exists('PasswordsEvolved\Subscriber\UserWarningSubscriber', is_array($callback) ? $callback[0] : $callback));
        }
    }

    public function test_display_warning_not_shown()
    {
        $translator = $this->get_translator_mock();
        $translator->expects($this->never())
                   ->method('translate');

        $user = $this->get_user_mock();
        $user->ID = 42;

        $get_user_meta = $this->getFunctionMock('PasswordsEvolved\Subscriber', 'get_user_meta');
        $get_user_meta->expects($this->once())
                      ->with($this->equalTo(42), $this->equalTo('passwords_evolved_warn_user'), $this->identicalTo(true))
                      ->willReturn(false);

        $update_user_meta = $this->getFunctionMock('PasswordsEvolved\Subscriber', 'update_user_meta');
        $update_user_meta->expects($this->never());

        $subscriber = new UserWarningSubscriber($user, $translator);

        $subscriber->display_warning();
    }

    public function test_display_warning_shown()
    {
        $translator = $this->get_translator_mock();
        $translator->expects($this->once())
                   ->method('translate')
                   ->with($this->equalTo('user_warning.message'))
                   ->willReturn('user_warning');

        $user = $this->get_user_mock();
        $user->ID = 42;

        $get_user_meta = $this->getFunctionMock('PasswordsEvolved\Subscriber', 'get_user_meta');
        $get_user_meta->expects($this->once())
                      ->with($this->equalTo(42), $this->equalTo('passwords_evolved_warn_user'), $this->identicalTo(true))
                      ->willReturn(true);

        $update_user_meta = $this->getFunctionMock('PasswordsEvolved\Subscriber', 'update_user_meta');
        $update_user_meta->expects($this->once())
                         ->with($this->equalTo(42), $this->equalTo('passwords_evolved_warn_user'), $this->identicalTo(false));

        $subscriber = new UserWarningSubscriber($user, $translator);

        $this->setOutputCallback(function() {});

        $subscriber->display_warning();
    }

    /**
     * Creates a mock of the translator object.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function get_translator_mock()
    {
        return $this->getMockBuilder('PasswordsEvolved\Translator')
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Creates a mock of the WordPress user object.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function get_user_mock()
    {
        return $this->getMockBuilder('WP_User')
            ->disableOriginalConstructor()
            ->getMock();
    }
}