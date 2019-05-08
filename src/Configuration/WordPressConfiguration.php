<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\Configuration;

use PasswordsEvolved\DependencyInjection\Container;
use PasswordsEvolved\DependencyInjection\ContainerConfigurationInterface;

/**
 * Configures the dependency injection container with WordPress parameters and services.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class WordPressConfiguration implements ContainerConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function modify(Container $container)
    {
        $container['wordpress.current_user'] = wp_get_current_user();

        $container['wordpress.hasher'] = $container->service(function (Container $container) {
            global $wp_hasher;

            if (!$wp_hasher instanceof \PasswordHash) {
                require_once ABSPATH . WPINC . '/class-phpass.php';
                $wp_hasher = new \PasswordHash(8, true);
            }

            return $wp_hasher;
        });

        $container['wordpress.http_transport'] = _wp_http_get_object();

        $container['wordpress.is_multisite'] = is_multisite();

        $container['wordpress.roles'] = wp_roles();
    }
}
