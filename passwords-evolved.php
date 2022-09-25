<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/*
Plugin Name: Passwords Evolved
Description: WordPress password authentication for the modern era.
Author: Carl Alexander
Author URI: https://carlalexander.ca
Version: 1.3.3
Text Domain: passwords-evolved
Domain Path: /resources/translations
License: GPL3
*/

// PHP version check to check if we have to use the password_compat library.
if (version_compare(PHP_VERSION, '5.6', '<')) {
    exit(sprintf('Passwords Evolved requires PHP 5.6 or higher. Your WordPress site is using PHP %s.', PHP_VERSION));
}

// Setup class autoloader
require_once dirname(__FILE__) . '/src/Autoloader.php';
\PasswordsEvolved\Autoloader::register();

// Load Passwords Evolved plugin
global $passwords_evolved;
$passwords_evolved = new \PasswordsEvolved\Plugin(__FILE__);
add_action('after_setup_theme', array($passwords_evolved, 'load'));

// Load Passwords Evolved pluggable functions
require_once dirname(__FILE__) . '/pluggable.php';
