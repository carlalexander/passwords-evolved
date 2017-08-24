<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\API;

use PasswordsEvolved\Error\TranslatableError;

/**
 * The client for the "Have I been pwned?" (HIBP) API.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class HIBPClient
{
    /**
     * Base URL for all HIBP API endpoints.
     *
     * @var string
     */
    const ENDPOINT_BASE = 'https://haveibeenpwned.com/api/v2';

    /**
     * The plugin version.
     *
     * @var string
     */
    private $version;

    /**
     * Constructor.
     *
     * @param string $version
     */
    public function __construct($version)
    {
        $this->version = $version;
    }

    /**
     * Check if the API is working or not.
     *
     * @return bool
     */
    public function is_api_active()
    {
        return true === $this->is_password_compromised('test');
    }

    /**
     * Checks with the HIBP API if the given password is a compromised password.
     *
     * @param string $password
     * @param bool   $use_hash
     *
     * @return bool|TranslatableError
     */
    public function is_password_compromised($password, $use_hash = true)
    {
        if (!is_string($password)) {
            return new TranslatableError('password_not_string');
        }

        if ($use_hash) {
            $password = sha1($password);
        }

        $status_code = $this->get_status_code(self::ENDPOINT_BASE . '/pwnedpassword/' . $password);

        // 429 means we hit the rate limit. Wait 2 seconds and try again.
        // https://haveibeenpwned.com/API/v2#RateLimiting
        if (429 === $status_code) {
            sleep(2);

            $status_code = $this->get_status_code(self::ENDPOINT_BASE . '/pwnedpassword/' . $password);
        }

        if (200 === $status_code) {
            return true;
        } elseif (404 === $status_code) {
            return false;
        }

        return new TranslatableError('response.invalid', array('status_code' => $status_code));
    }

    /**
     * Performs a GET request using curl to get the HTTP status code of the given URL.
     *
     * @param string $url
     *
     * @return int
     */
    private function get_status_code($url)
    {
        $handle = curl_init($url);

        curl_setopt($handle, CURLOPT_TIMEOUT, 2);
        curl_setopt($handle, CURLOPT_HEADER, false);
        curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($handle, CURLOPT_USERAGENT, 'PasswordsEvolvedPlugin/' . $this->version);

        curl_exec($handle);

        $status_code = (int) curl_getinfo($handle, CURLINFO_HTTP_CODE);

        curl_close($handle);

        return $status_code;
    }
}
