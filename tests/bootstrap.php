<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * PHPUnit bootstrap file for Passwords Evolved plugin.
 */

$_tests_dir = getenv('WP_TESTS_DIR');
if ( !$_tests_dir ) $_tests_dir = '/tmp/wordpress-tests-lib';

$_core_dir = getenv('WP_CORE_DIR');

if ( ! $_core_dir ) {
    $_core_dir = '/tmp/wordpress';
}

require_once dirname(__DIR__) . '/vendor/autoload.php';

if (version_compare(PHP_VERSION, '5.5', '<')) {
    require_once '../lib/password.php';
}

require_once $_core_dir . '/wp-includes/class-phpass.php';
require_once $_core_dir . '/wp-includes/class-wp-error.php';
require_once $_core_dir . '/wp-includes/class-wp-role.php';
require_once $_core_dir . '/wp-includes/class-wp-roles.php';
require_once $_core_dir . '/wp-includes/class-wp-user.php';
