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
class TranslationsSubscriber extends AbstractEventManagerAwareSubscriber
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
        $this->translations_path = rtrim($translations_path, '/');
    }

    /**
     * {@inheritdoc}
     */
    public static function get_subscribed_events()
    {
        return array(
            'init' => 'register_translations',
            'load_textdomain_mofile' => array('ensure_default_translation', 10, 2),
        );
    }

    /**
     * Ensure that we load the "en_US" translation if there's no readable translation file.
     *
     * This is necessary since we're using placeholder values for text instead of english text.
     *
     * @param string $mofile_path
     * @param string $domain
     *
     * @return string
     */
    public function ensure_default_translation($mofile_path, $domain)
    {
        if ($domain !== $this->domain || false === stripos($mofile_path, $this->translations_path)) {
            return $mofile_path;
        }

        if (!is_readable($mofile_path)) {
            $mofile_path = preg_replace('/'.$this->domain.'-[a-z]{2}_[A-Z]{2}/', $this->domain.'-en_US', $mofile_path);
        }

        return $mofile_path;
    }

    /**
     * Register the plugin's translations files with WordPress.
     */
    public function register_translations()
    {
        load_textdomain($this->domain, $this->translations_path.'/'.$this->domain.'-'.$this->event_manager->filter('plugin_locale', determine_locale(), $this->domain).'.mo');
    }
}
