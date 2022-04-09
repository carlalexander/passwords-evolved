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

use PasswordsEvolved\Subscriber\UserWarningSubscriber;
use PasswordsEvolved\Tests\Mock\FunctionMockTrait;
use PasswordsEvolved\Translator;
use PHPUnit\Framework\TestCase;

class UserWarningSubscriberTest extends TestCase
{
    use FunctionMockTrait;

    public function test_get_subscribed_events()
    {
        $callbacks = UserWarningSubscriber::get_subscribed_events();

        foreach ($callbacks as $callback) {
            $this->assertTrue(method_exists(UserWarningSubscriber::class, is_array($callback) ? $callback[0] : $callback));
        }
    }

    public function test_display_warning_not_shown()
    {
        $translator = $this->get_translator_mock();
        $translator->expects($this->never())
                   ->method('translate');

        $user = $this->get_user_mock();
        $user->ID = 42;

        $get_user_meta = $this->getFunctionMock($this->getNamespace(UserWarningSubscriber::class), 'get_user_meta');
        $get_user_meta->expects($this->once())
                      ->with($this->equalTo(42), $this->equalTo('passwords_evolved_warn_user'), $this->identicalTo(true))
                      ->willReturn(false);

        $update_user_meta = $this->getFunctionMock($this->getNamespace(UserWarningSubscriber::class), 'update_user_meta');
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

        $get_user_meta = $this->getFunctionMock($this->getNamespace(UserWarningSubscriber::class), 'get_user_meta');
        $get_user_meta->expects($this->once())
                      ->with($this->equalTo(42), $this->equalTo('passwords_evolved_warn_user'), $this->identicalTo(true))
                      ->willReturn(true);

        $get_edit_profile_url = $this->getFunctionMock($this->getNamespace(UserWarningSubscriber::class), 'get_edit_profile_url');
        $get_edit_profile_url->expects($this->once())
                             ->with($this->equalTo(42))
                             ->willReturn('profile_url');

        $sprintf = $this->getFunctionMock($this->getNamespace(UserWarningSubscriber::class), 'sprintf');
        $sprintf->expects($this->once())
                ->with($this->equalTo('user_warning'), $this->equalTo('profile_url#password'));

        $update_user_meta = $this->getFunctionMock($this->getNamespace(UserWarningSubscriber::class), 'update_user_meta');
        $update_user_meta->expects($this->once())
                         ->with($this->equalTo(42), $this->equalTo('passwords_evolved_warn_user'), $this->identicalTo(false));

        $subscriber = new UserWarningSubscriber($user, $translator);

        $subscriber->display_warning();
    }

    /**
     * Creates a mock of the translator object.
     */
    private function get_translator_mock()
    {
        return $this->getMockBuilder(Translator::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * Creates a mock of the WordPress user object.
     */
    private function get_user_mock()
    {
        return $this->getMockBuilder(\WP_User::class)->disableOriginalConstructor()->getMock();
    }
}
