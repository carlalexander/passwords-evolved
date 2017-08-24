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
use PasswordsEvolved\Translator;

/**
 * Subscriber that handles WordPress password resets.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class ResetPasswordSubscriber implements SubscriberInterface
{
    /**
     * The HIBP API client.
     *
     * @var HIBPClient
     */
    private $api_client;

    /**
     * The plugin translator.
     *
     * @var Translator
     */
    private $translator;

    /**
     * Constructor.
     *
     * @param HIBPClient $api_client
     * @param Translator $translator
     */
    public function __construct(HIBPClient $api_client, Translator $translator)
    {
        $this->api_client = $api_client;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public static function get_subscribed_events()
    {
        return array(
            'validate_password_reset' => array('validate_password', 10, 2),
        );
    }

    /**
     * Validate the new password submitted in the password reset form.
     *
     * @param \WP_Error $error
     * @param mixed    $user
     */
    public function validate_password(\WP_Error $error, $user)
    {
        if (empty($_POST['pass1']) || !$user instanceof \WP_User || $error->get_error_code()) {
            return;
        }

        $translation_string = 'error.reset_password';

        if ($this->api_client->is_password_compromised($_POST['pass1']) === true) {
            $error->add($translation_string, $this->translator->translate($translation_string));
        }
    }
}
