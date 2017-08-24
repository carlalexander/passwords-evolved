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
use PasswordsEvolved\Translator;

/**
 * Configures the dependency injection container with the plugin translator service.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class TranslatorConfiguration implements ContainerConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function modify(Container $container)
    {
        $container['translator'] = $container->service(function (Container $container) {
            return new Translator($container['plugin_domain']);
        });
    }
}
