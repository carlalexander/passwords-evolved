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
use PasswordsEvolved\Password\Generator;
use PasswordsEvolved\Password\Hasher;

/**
 * Configures the dependency injection container with the plugin password services.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class PasswordConfiguration implements ContainerConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function modify(Container $container)
    {
        $container['password.generator'] = $container->service(function (Container $container) {
            return new Generator($container['api_client']);
        });

        $container['password.hasher'] = $container->service(function (Container $container) {
            return new Hasher($container['wordpress.hasher']);
        });
    }
}
