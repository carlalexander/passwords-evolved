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

use PasswordsEvolved\API\HIBPClient;
use PasswordsEvolved\Error\TranslatableError;
use PasswordsEvolved\EventManagement\SubscriberInterface;

/**
 * Subscriber that handles WordPress authentication.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class AuthenticationSubscriber implements SubscriberInterface
{
    /**
     * The HIBP API client.
     *
     * @var HIBPClient
     */
    private $api_client;

    /**
     * Constructor.
     *
     * @param HIBPClient $api_client
     */
    public function __construct(HIBPClient $api_client)
    {
        $this->api_client = $api_client;
    }

    /**
     * {@inheritdoc}
     */
    public static function get_subscribed_events()
    {
        return array(
            'authenticate' => array('validate_password', 99, 3),
            'shake_error_codes' => 'add_error_code',
        );
    }

    /**
     * Add our error code to the handled login error codes.
     *
     * @param array $error_codes
     *
     * @return array
     */
    public function add_error_code(array $error_codes)
    {
        $error_codes[] = 'error.authentication';

        return $error_codes;
    }

    /**
     * Validate the password that someone is using to authenticate.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param mixed  $user
     * @param string $username
     * @param string $password
     *
     * @return mixed
     */
    public function validate_password($user, $username, $password)
    {
        if (!$user instanceof \WP_User) {
            return $user;
        }

        $password_compromised = $this->api_client->is_password_compromised($password);
        $capabilities = $user->get_role_caps();
        $enforce_password = empty($capabilities['passwords_evolved_enforce_password']) ? false : $capabilities['passwords_evolved_enforce_password'];

        if (true === $password_compromised && $enforce_password) {
            return new TranslatableError('authentication', array('reset_password_url' => wp_lostpassword_url()));
        } elseif (true === $password_compromised && !$enforce_password) {
            update_user_meta($user->ID, 'passwords_evolved_warn_user', true);
        }

        return $user;
    }
}
