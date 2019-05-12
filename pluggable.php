<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PasswordsEvolved\Password\Generator\PasswordGeneratorInterface;

/**
 * Pluggable functions used by the Passwords Evolved plugin.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */

if (!function_exists('wp_check_password')) {
    /**
     * Checks the given plaintext password against the given encrypted password hash.
     *
     * @param string $password
     * @param string $hash
     * @param int    $user_id
     *
     * @return bool
     */
    function wp_check_password($password, $hash, $user_id = null)
    {
        global $passwords_evolved;
        $hasher = $passwords_evolved->get_password_hasher();

        $check = $hasher->is_password_valid($password, $hash);

        if ($user_id && $check && !$hasher->is_hash_valid($hash)) {
            $hash = wp_set_password($password, $user_id);
        }

        return apply_filters('check_password', $check, $password, $hash, $user_id);
    }
}

if (!function_exists('wp_generate_password')) {
    /**
     * Generates a random password that hasn't been compromised.
     *
     * @param int  $length
     * @param bool $special_chars
     * @param bool $extra_special_chars
     *
     * @return string
     */
    function wp_generate_password($length = PasswordGeneratorInterface::MIN_LENGTH, $special_chars = true, $extra_special_chars = false)
    {
        global $passwords_evolved;

        $password = $passwords_evolved->get_password_generator()->generate_password($length, $special_chars, $extra_special_chars);

        return apply_filters('random_password', $password);
    }
}

if (!function_exists('wp_hash_password')) {
    /**
     * Create a hash of the given plain text password.
     *
     * @param string $password
     *
     * @return string
     */
    function wp_hash_password($password)
    {
        global $passwords_evolved;

        return $passwords_evolved->get_password_hasher()->hash_password(trim($password));
    }
}

if (!function_exists('wp_set_password')) {
    /**
     * Set a new encrypted password for the user with the given user ID
     * using the given plain text password.
     *
     * @param string $password
     * @param int    $user_id
     *
     * @return string
     */
    function wp_set_password($password, $user_id)
    {
        global $wpdb;

        $hash = wp_hash_password($password);
        $wpdb->update($wpdb->users, array('user_pass' => $hash, 'user_activation_key' => ''), array('ID' => $user_id));

        wp_cache_delete($user_id, 'users');

        return $hash;
    }
}
