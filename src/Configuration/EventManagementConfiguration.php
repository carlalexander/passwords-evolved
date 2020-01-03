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
use PasswordsEvolved\EventManagement\EventManager;
use PasswordsEvolved\Subscriber\AdminPageSubscriber;
use PasswordsEvolved\Subscriber\AuthenticationSubscriber;
use PasswordsEvolved\Subscriber\CapabilitiesSubscriber;
use PasswordsEvolved\Subscriber\NetworkAdminPageSubscriber;
use PasswordsEvolved\Subscriber\ResetPasswordSubscriber;
use PasswordsEvolved\Subscriber\TranslationsSubscriber;
use PasswordsEvolved\Subscriber\UserProfileSubscriber;
use PasswordsEvolved\Subscriber\UserWarningSubscriber;

/**
 * Configures the dependency injection container with the plugin's event management service.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class EventManagementConfiguration implements ContainerConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function modify(Container $container)
    {
        $container['event_manager'] = $container->service(function (Container $container) {
            return new EventManager();
        });

        $container['subscribers'] = $container->service(function (Container $container) {
            $subscribers = array(
                new AuthenticationSubscriber($container['api_client']),
                new CapabilitiesSubscriber($container['options']->get('enforced_roles', array('administrator')), $container['wordpress.roles']),
                new ResetPasswordSubscriber($container['api_client'], $container['translator']),
                new TranslationsSubscriber($container['plugin_domain'], $container['plugin_path'] . '/resources/translations'),
                new UserProfileSubscriber($container['api_client']),
                new UserWarningSubscriber($container['wordpress.current_user'], $container['translator']),
            );

            $subscribers[] = $container['wordpress.is_multisite']
                           ? new NetworkAdminPageSubscriber($container['options'], $container['admin_page'], $container['plugin_basename'])
                           : new AdminPageSubscriber($container['admin_page'], $container['plugin_basename']);

            return $subscribers;
        });
    }
}
