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
use PasswordsEvolved\Options;

/**
 * Configures the dependency injection container with the plugin options service.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class OptionsConfiguration implements ContainerConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function modify(Container $container)
    {
        $container['options'] = $container->service(function (Container $container) {
            return new Options();
        });
    }
}
