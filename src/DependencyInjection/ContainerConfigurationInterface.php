<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\DependencyInjection;

/**
 * A container configuration object configures a dependency injection container during the build process.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
interface ContainerConfigurationInterface
{
    /**
     * Modifies the given dependency injection container.
     *
     * @param Container $container
     */
    public function modify(Container $container);
}
