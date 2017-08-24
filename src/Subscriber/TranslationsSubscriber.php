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

/**
 * Subscriber that manages the plugin's translations.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class TranslationsSubscriber implements SubscriberInterface
{
    /**
     * The domain of the plugin translations.
     *
     * @var string
     */
    private $domain;

    /**
     * Relative path to the plugin translation files.
     *
     * @var string
     */
    private $translations_path;

    /**
     * Constructor.
     *
     * @param string $domain
     * @param string $translations_path
     */
    public function __construct($domain, $translations_path)
    {
        $this->domain = $domain;
        $this->translations_path = $translations_path;
    }

    /**
     * {@inheritdoc}
     */
    public static function get_subscribed_events()
    {
        return array(
            'init' => 'register_translations',
            'plugin_locale' => array('enforce_locale', 10, 2),
        );
    }

    /**
     * Enforces the use of the "en_US" locale for translations.
     *
     * This is necessary since we're using placeholder values for text instead of english text.
     *
     * @param string $locale
     * @param string $domain
     *
     * @return string
     */
    public function enforce_locale($locale, $domain)
    {
        if ($domain == $this->domain) {
            $locale = 'en_US';
        }

        return $locale;
    }

    /**
     * Register the plugin's translations files with WordPress.
     */
    public function register_translations()
    {
        load_plugin_textdomain($this->domain, false, $this->translations_path);
    }
}
