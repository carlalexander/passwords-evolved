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
use PasswordsEvolved\EventManagement\SubscriberInterface;
use PasswordsEvolved\Error\TranslatableError;

/**
 * Subscriber that handles WordPress password changes.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class UserProfileSubscriber implements SubscriberInterface
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
            'user_profile_update_errors' => array('validate_password', 1, 3),
        );
    }

    /**
     * Validate the new password submitted when adding or modifying a user.
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     *
     * @param \WP_Error $error
     * @param bool      $update
     * @param mixed     $user
     */
    public function validate_password(\WP_Error &$error, $update, $user)
    {
        if ($error->get_error_code() || !$user instanceof \stdClass || empty($user->user_pass)) {
            return;
        }

        if ($this->api_client->is_password_compromised($user->user_pass) === true) {
            $error = new TranslatableError('user_profile');
        }
    }
}
