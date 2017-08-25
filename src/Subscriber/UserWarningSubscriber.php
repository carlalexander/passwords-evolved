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
use PasswordsEvolved\Translator;

/**
 * Subscriber that warns a user if they're using a compromised password.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class UserWarningSubscriber implements SubscriberInterface
{
    /**
     * The current WordPress user.
     *
     * @var \WP_User
     */
    private $current_user;

    /**
     * The plugin translator.
     *
     * @var Translator
     */
    private $translator;

    /**
     * Constructor.
     *
     * @param \WP_User   $current_user
     * @param Translator $translator
     */
    public function __construct(\WP_User $current_user, Translator $translator)
    {
        $this->current_user = $current_user;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public static function get_subscribed_events()
    {
        return array(
            'all_admin_notices' => 'display_warning',
        );
    }

    /**
     * Displays a warning to the user if they've just logged in with a compromised password.
     */
    public function display_warning()
    {
        $display_warning = get_user_meta($this->current_user->ID, 'passwords_evolved_warn_user', true);

        if (!$display_warning) {
            return;
        }

        echo sprintf($this->translate('message'), get_edit_profile_url($this->current_user->ID) . '#password');

        update_user_meta($this->current_user->ID, 'passwords_evolved_warn_user', false);
    }

    /**
     * Translate a string within the user warning context.
     *
     * @param string $string
     *
     * @return string
     */
    private function translate($string)
    {
        return $this->translator->translate('user_warning.' . $string);
    }
}
