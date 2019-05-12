<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\Password\Generator;

/**
 * Password generator that uses the same algorithm as the `wp_generate_password` pluggable function.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class WordPressPasswordGenerator implements PasswordGeneratorInterface
{
    /**
     * {@inheritdoc}
     */
    public function generate_password($length = self::MIN_LENGTH, $special_chars = true, $extra_special_chars = false)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';

        if ($special_chars) {
            $chars .= '!@#$%^&*()';
        }

        if ($extra_special_chars) {
            $chars .= '-_ []{}<>~`+=,.;:/?|';
        }

        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $password .= substr($chars, wp_rand(0, strlen($chars) - 1), 1);
        }

        return $password;
    }
}
