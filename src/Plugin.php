<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved;

use PasswordsEvolved\Configuration;
use PasswordsEvolved\DependencyInjection\Container;
use PasswordsEvolved\Password\Generator\PasswordGeneratorInterface;
use PasswordsEvolved\Password\Hasher\PasswordHasherInterface;

/**
 * Passwords Evolved Plugin.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class Plugin
{
    /**
     * Domain used for translating plugin strings.
     *
     * @var string
     */
    const DOMAIN = 'passwords-evolved';

    /**
     * Passwords Evolved plugin version.
     *
     * @var string
     */
    const VERSION = '1.3.3';

    /**
     * The plugin's dependency injection container.
     *
     * @var Container
     */
    private $container;

    /**
     * Flag to track if the plugin is loaded.
     *
     * @var bool
     */
    private $loaded;

    /**
     * Constructor.
     *
     * @param string $file
     */
    public function __construct($file)
    {
        $this->container = new Container(array(
            'plugin_basename' => plugin_basename($file),
            'plugin_domain' => self::DOMAIN,
            'plugin_path' => plugin_dir_path($file),
            'plugin_relative_path' => basename(plugin_dir_path($file)),
            'plugin_url' => plugin_dir_url($file),
            'plugin_version' => self::VERSION,
        ));
        $this->loaded = false;
    }

    /**
     * Get the plugin password generator.
     *
     * @return PasswordGeneratorInterface
     */
    public function get_password_generator()
    {
        return $this->container['password.generator'];
    }

    /**
     * Get the plugin password hasher.
     *
     * @return PasswordHasherInterface
     */
    public function get_password_hasher()
    {
        return $this->container['password.hasher'];
    }

    /**
     * Checks if the plugin is loaded.
     *
     * @return bool
     */
    public function is_loaded()
    {
        return $this->loaded;
    }

    /**
     * Loads the plugin into WordPress.
     */
    public function load()
    {
        if ($this->is_loaded()) {
            return;
        }

        $this->container->configure(array(
            Configuration\AdminConfiguration::class,
            Configuration\APIClientConfiguration::class,
            Configuration\EventManagementConfiguration::class,
            Configuration\OptionsConfiguration::class,
            Configuration\PasswordConfiguration::class,
            Configuration\TranslatorConfiguration::class,
            Configuration\WordPressConfiguration::class,
        ));

        foreach ($this->container['subscribers'] as $subscriber) {
            $this->container['event_manager']->add_subscriber($subscriber);
        }

        $this->loaded = true;
    }
}
