<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\Password;

use PasswordsEvolved\API\HIBPClient;

/**
 * Password generator that uses the HIBP API client to ensure that a generated password hasn't been compromised.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class Generator
{
    /**
     * Minimum length of a password required for enforcement.
     *
     * @var int
     */
    const MIN_LENGTH = 16;

    /**
     * The HIBP API client.
     *
     * @var HIBPClient
     */
    private $api_client;

    /**
     * Constructor.
     *
     * @param HIBPClient $api_client
     */
    public function __construct(HIBPClient $api_client)
    {
        $this->api_client = $api_client;
    }

    /**
     * Generates a non-compromised password.
     *
     * @param int  $length
     * @param bool $special_chars
     * @param bool $extra_special_chars
     *
     * @return string
     */
    public function generate_password($length = self::MIN_LENGTH, $special_chars = true, $extra_special_chars = false)
    {
        $password = $this->generate_random_password($length, $special_chars, $extra_special_chars);

        if ($length < self::MIN_LENGTH || !$this->api_client->is_api_active()) {
            return $password;
        }

        while ($this->api_client->is_password_compromised($password) === true) {
            $password = $this->generate_random_password($length, $special_chars, $extra_special_chars);
        }

        return $password;
    }

    /**
     * Generate a random password using the same algorithm as the `wp_generate_password`
     * pluggable function.
     *
     * @param int  $length
     * @param bool $special_chars
     * @param bool $extra_special_chars
     *
     * @return string
     */
    private function generate_random_password($length, $special_chars, $extra_special_chars)
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
