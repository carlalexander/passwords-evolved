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
use PasswordsEvolved\Subscriber\AuthenticationSubscriber;
use PasswordsEvolved\Tests\Mock\FunctionMockTrait;
use PHPUnit\Framework\TestCase;

class AuthenticationSubscriberTest extends TestCase
{
    use FunctionMockTrait;

    public function test_get_subscribed_events()
    {
        $callbacks = AuthenticationSubscriber::get_subscribed_events();

        foreach ($callbacks as $callback) {
            $this->assertTrue(method_exists(AuthenticationSubscriber::class, is_array($callback) ? $callback[0] : $callback));
        }
    }

    public function test_add_error_code()
    {
        $subscriber = new AuthenticationSubscriber($this->get_api_client_mock());

        $this->assertEquals(array('error.authentication'), $subscriber->add_error_code(array()));
    }

    public function test_validate_password_with_enforced_password()
    {
        $api_client = $this->get_api_client_mock();
        $api_client->expects($this->once())
            ->method('is_password_compromised')
            ->with($this->equalTo('password'))
            ->willReturn(true);

        $user = $this->get_user_mock();
        $user->expects($this->once())
            ->method('get_role_caps')
            ->willReturn(array('passwords_evolved_enforce_password' => true));

        $wp_lostpassword_url = $this->getFunctionMock($this->getNamespace(AuthenticationSubscriber::class), 'wp_lostpassword_url');
        $wp_lostpassword_url->expects($this->once())
                            ->willReturn('url');

        $subscriber = new AuthenticationSubscriber($api_client);

        $error = $subscriber->validate_password($user, 'username', 'password');

        $this->assertInstanceOf(TranslatableError::class, $error);
        $this->assertEquals('authentication', $error->get_error_code());
        $this->assertEquals(array('reset_password_url' => 'url'), $error->get_error_data());
    }

    public function test_validate_password_with_unenforced_password()
    {
        $api_client = $this->get_api_client_mock();
        $api_client->expects($this->once())
                   ->method('is_password_compromised')
                   ->with($this->equalTo('password'))
                   ->willReturn(true);

        $user = $this->get_user_mock();
        $user->expects($this->once())
             ->method('get_role_caps')
             ->willReturn(array('passwords_evolved_enforce_password' => false));
        $user->ID = 42;

        $update_user_meta = $this->getFunctionMock($this->getNamespace(AuthenticationSubscriber::class), 'update_user_meta');
        $update_user_meta->expects($this->once())
                         ->with($this->equalTo(42), $this->equalTo('passwords_evolved_warn_user'), $this->identicalTo(true));

        $subscriber = new AuthenticationSubscriber($api_client);

        $this->assertSame($user, $subscriber->validate_password($user, 'username', 'password'));
    }

    public function test_validate_password_with_no_user()
    {
        $subscriber = new AuthenticationSubscriber($this->get_api_client_mock());

        $this->assertNull($subscriber->validate_password(null, 'username', 'password'));
    }

    public function test_validate_password_with_uncompromised_password()
    {
        $api_client = $this->get_api_client_mock();
        $api_client->expects($this->once())
                   ->method('is_password_compromised')
                   ->with($this->equalTo('password'))
                   ->willReturn(false);

        $user = $this->get_user_mock();
        $user->expects($this->once())
             ->method('get_role_caps')
             ->willReturn(array());

        $subscriber = new AuthenticationSubscriber($api_client);

        $this->assertSame($user, $subscriber->validate_password($user, 'username', 'password'));
    }

    /**
     * Creates a mock of the HIBP API client.
     */
    private function get_api_client_mock()
    {
        return $this->getMockBuilder(HIBPClient::class)->disableOriginalConstructor()->getMock();
    }

    /**
     * Creates a mock of the WordPress user object.
     */
    private function get_user_mock()
    {
        return $this->getMockBuilder(\WP_User::class)->disableOriginalConstructor()->getMock();
    }
}
