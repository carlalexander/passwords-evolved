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

use PasswordsEvolved\API\HIBPClient;
use PasswordsEvolved\Subscriber\ResetPasswordSubscriber;
use PasswordsEvolved\Translator;

class ResetPasswordSubscriberTest extends \PHPUnit_Framework_TestCase
{
    public function test_get_subscribed_events()
    {
        $callbacks = ResetPasswordSubscriber::get_subscribed_events();

        foreach ($callbacks as $callback) {
            $this->assertTrue(method_exists(ResetPasswordSubscriber::class, is_array($callback) ? $callback[0] : $callback));
        }
    }

    public function test_validate_password_reset_with_compromised_password()
    {
        $api_client = $this->get_api_client_mock();
        $api_client->expects($this->once())
                   ->method('is_password_compromised')
                   ->with($this->equalTo('password'))
                   ->willReturn(true);

        $error = $this->get_error_mock();
        $error->expects($this->once())
              ->method('get_error_code')
              ->willReturn('');
        $error->expects($this->once())
              ->method('add')
              ->with($this->equalTo('error.reset_password'), $this->equalTo('error.reset_password.translation'));

        $translator = $this->get_translator_mock();
        $translator->expects($this->once())
                   ->method('translate')
                   ->with($this->equalTo('error.reset_password'))
                   ->willReturn('error.reset_password.translation');

        $_POST['pass1'] = 'password';

        $subscriber = new ResetPasswordSubscriber($api_client, $translator);

        $subscriber->validate_password($error, $this->get_user_mock());

        unset($_POST['pass1']);
    }

    public function test_validate_password_reset_with_empty_password()
    {
        $api_client = $this->get_api_client_mock();
        $api_client->expects($this->never())
                   ->method('is_password_compromised');

        $error = $this->get_error_mock();
        $error->expects($this->never())
              ->method('get_error_code');

        $translator = $this->get_translator_mock();
        $translator->expects($this->never())
                   ->method('translate');

        $subscriber = new ResetPasswordSubscriber($api_client, $translator);

        $subscriber->validate_password($error, null);
    }

    public function test_validate_password_reset_with_existing_error()
    {
        $api_client = $this->get_api_client_mock();
        $api_client->expects($this->never())
                   ->method('is_password_compromised');

        $error = $this->get_error_mock();
        $error->expects($this->once())
              ->method('get_error_code')
              ->willReturn('error_code');

        $translator = $this->get_translator_mock();
        $translator->expects($this->never())
                   ->method('translate');

        $_POST['pass1'] = 'password';

        $subscriber = new ResetPasswordSubscriber($api_client, $translator);

        $subscriber->validate_password($error, $this->get_user_mock());

        unset($_POST['pass1']);
    }

    public function test_validate_password_reset_with_no_user()
    {
        $api_client = $this->get_api_client_mock();
        $api_client->expects($this->never())
                   ->method('is_password_compromised');

        $error = $this->get_error_mock();
        $error->expects($this->never())
              ->method('get_error_code');

        $translator = $this->get_translator_mock();
        $translator->expects($this->never())
                   ->method('translate');

        $_POST['pass1'] = 'password';

        $subscriber = new ResetPasswordSubscriber($api_client, $translator);

        $subscriber->validate_password($error, null);

        unset($_POST['pass1']);
    }

    public function test_validate_password_reset_with_uncompromised_password()
    {
        $api_client = $this->get_api_client_mock();
        $api_client->expects($this->once())
                   ->method('is_password_compromised')
                   ->with($this->equalTo('password'))
                   ->willReturn(false);

        $error = $this->get_error_mock();
        $error->expects($this->once())
              ->method('get_error_code')
              ->willReturn('');
        $error->expects($this->never())
              ->method('add');

        $translator = $this->get_translator_mock();
        $translator->expects($this->never())
                   ->method('translate');

        $_POST['pass1'] = 'password';

        $subscriber = new ResetPasswordSubscriber($api_client, $translator);

        $subscriber->validate_password($error, $this->get_user_mock());

        unset($_POST['pass1']);
    }

    /**
     * Creates a mock of the HIBP API client.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function get_api_client_mock()
    {
        return $this->getMockBuilder(HIBPClient::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * Creates a mock of the WordPress error object.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    public function get_error_mock()
    {
        return $this->getMockBuilder(\WP_Error::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * Creates a mock of the translator object.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function get_translator_mock()
    {
        return $this->getMockBuilder(Translator::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * Creates a mock of the WordPress user object.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function get_user_mock()
    {
        return $this->getMockBuilder(\WP_User::class)->disableOriginalConstructor()->getMock();
    }
}
