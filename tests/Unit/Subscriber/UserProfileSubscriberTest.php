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

use PasswordsEvolved\API\HIBPClient;
use PasswordsEvolved\Error\TranslatableError;
use PasswordsEvolved\Subscriber\UserProfileSubscriber;
use PHPUnit\Framework\TestCase;

class UserProfileSubscriberTest extends TestCase
{
    public function test_get_subscribed_events()
    {
        $callbacks = UserProfileSubscriber::get_subscribed_events();

        foreach ($callbacks as $callback) {
            $this->assertTrue(method_exists(UserProfileSubscriber::class, is_array($callback) ? $callback[0] : $callback));
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

        $user = new \stdClass();
        $user->user_pass = 'password';

        $subscriber = new UserProfileSubscriber($api_client);

        $subscriber->validate_password($error, true, $user);

        $this->assertInstanceOf(TranslatableError::class, $error);
        $this->assertEquals('user_profile', $error->get_error_code());
    }

    public function test_validate_password_reset_with_empty_password()
    {
        $api_client = $this->get_api_client_mock();
        $api_client->expects($this->never())
                   ->method('is_password_compromised');

        $error = $this->get_error_mock();
        $error->expects($this->once())
              ->method('get_error_code')
              ->willReturn('');

        $user = new \stdClass();

        $subscriber = new UserProfileSubscriber($api_client);

        $subscriber->validate_password($error, null, $user);
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

        $subscriber = new UserProfileSubscriber($api_client);

        $subscriber->validate_password($error, null, null);
    }

    public function test_validate_password_reset_with_no_user()
    {
        $api_client = $this->get_api_client_mock();
        $api_client->expects($this->never())
                   ->method('is_password_compromised');

        $error = $this->get_error_mock();
        $error->expects($this->once())
              ->method('get_error_code')
              ->willReturn('');

        $subscriber = new UserProfileSubscriber($api_client);

        $subscriber->validate_password($error, null, null);
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

        $user = new \stdClass();
        $user->user_pass = 'password';

        $subscriber = new UserProfileSubscriber($api_client);

        $subscriber->validate_password($error, true, $user);

        $this->assertNotInstanceOf(TranslatableError::class, $error);
    }

    /**
     * Creates a mock of the HIBP API client.
     */
    private function get_api_client_mock()
    {
        return $this->getMockBuilder(HIBPClient::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * Creates a mock of the WordPress error object.
     */
    public function get_error_mock()
    {
        return $this->getMockBuilder(\WP_Error::class)->disableOriginalConstructor()->getMock();
    }
}
