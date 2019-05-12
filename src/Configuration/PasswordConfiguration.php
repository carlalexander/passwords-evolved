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
use PasswordsEvolved\Password\Generator\NonCompromisedPasswordGenerator;
use PasswordsEvolved\Password\Generator\WordPressPasswordGenerator;
use PasswordsEvolved\Password\Hasher\NativePasswordHasher;
use PasswordsEvolved\Password\Hasher\PasswordHasherChain;
use PasswordsEvolved\Password\Hasher\SodiumPasswordHasher;
use PasswordsEvolved\Password\Hasher\WordPressPasswordHasher;


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
            return new NonCompromisedPasswordGenerator($container['api_client'], $container['password.generator.wordpress']);
        });

        $container['password.generator.wordpress'] = $container->service(function (Container $container) {
            return new WordPressPasswordGenerator();
        });

        $container['password.hasher'] = $container->service(function (Container $container) {
            return new PasswordHasherChain([
                $container['password.hasher.sodium'],
                $container['password.hasher.native'],
                $container['password.hasher.wordpress'],
            ]);
        });

        $container['password.hasher.native'] = $container->service(function (Container $container) {
            return new NativePasswordHasher();
        });

        $container['password.hasher.sodium'] = $container->service(function (Container $container) {
            return new SodiumPasswordHasher();
        });

        $container['password.hasher.wordpress'] = $container->service(function (Container $container) {
            return new WordPressPasswordHasher($container['wordpress.hasher']);
        });
    }
}
