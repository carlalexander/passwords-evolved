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

use PasswordsEvolved\API\HIBPClient;
use PasswordsEvolved\DependencyInjection\Container;
use PasswordsEvolved\DependencyInjection\ContainerConfigurationInterface;

/**
 * Configures the dependency injection container with the HIBP API client service.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class APIClientConfiguration implements ContainerConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function modify(Container $container)
    {
        $container['api_client'] = $container->service(function (Container $container) {
            return new HIBPClient($container['wordpress.http_transport'], $container['plugin_version']);
        });
    }
}
