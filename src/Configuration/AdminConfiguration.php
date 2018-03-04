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

use PasswordsEvolved\Admin\AdminPage;
use PasswordsEvolved\Admin\NetworkAdminPage;
use PasswordsEvolved\DependencyInjection\Container;
use PasswordsEvolved\DependencyInjection\ContainerConfigurationInterface;

/**
 * Configures the dependency injection container with the plugin's admin.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class AdminConfiguration implements ContainerConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function modify(Container $container)
    {
        $container['admin_page'] = $container->service(function (Container $container) {
            return $container['wordpress.is_multisite']
                 ? new NetworkAdminPage($container['api_client'], $container['options'], $container['plugin_path'] . 'resources/templates/admin/', $container['translator'])
                 : new AdminPage($container['api_client'], $container['options'], $container['plugin_path'] . 'resources/templates/admin/', $container['translator']);
        });
    }
}
