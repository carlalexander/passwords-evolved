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

/**
 * Autoloads Passwords Evolved plugin classes using PSR-4.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class Autoloader
{
    /**
     * Handles autoloading of Password Evolved plugin classes.
     *
     * @param string $class
     */
    public static function autoload($class)
    {
        if (0 !== strpos($class, __NAMESPACE__)) {
            return;
        }

        $class = substr($class, strlen(__NAMESPACE__));
        $file = dirname(__FILE__) . str_replace(array('\\', "\0"), array('/', ''), $class) . '.php';

        if (is_file($file)) {
            require $file;
        }
    }

    /**
     * Registers PasswordsEvolved_Autoloader as an SPL autoloader.
     *
     * @param bool $prepend
     */
    public static function register($prepend = false)
    {
        spl_autoload_register(array(new self(), 'autoload'), true, $prepend);
    }
}
