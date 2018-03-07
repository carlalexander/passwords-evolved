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
    const ENDPOINT_BASE = 'https://api.pwnedpasswords.com';

    /**
     * The WordPress HTTP transport.
     *
     * @var \WP_Http
     */
    private $http_transport;

    /**
     * Flag whether the API is active or not.
     *
     * @var bool
     */
    private $is_active;

    /**
     * The plugin version.
     *
     * @var string
     */
    private $version;

    /**
     * Constructor.
     *
     * @param \WP_Http $http_transport
     * @param string   $version
     */
    public function __construct(\WP_Http $http_transport, $version)
    {
        $this->http_transport = $http_transport;
        $this->version = $version;
    }

    /**
     * Check if the API is working or not.
     *
     * @return bool
     */
    public function is_api_active()
    {
        if (null === $this->is_active) {
            $this->is_active = true === $this->is_password_compromised('test');
        }

        return $this->is_active;
    }

    /**
     * Checks with the HIBP API if the given password is a compromised password.
     *
     * @param string $password
     *
     * @return bool|\WP_Error
     */
    public function is_password_compromised($password)
    {
        if (!is_string($password)) {
            return new TranslatableError('password_not_string');
        } elseif (!preg_match('/^[0-9a-f]{40}$/i', $password)) {
            $password = sha1($password);
        }

        // Ensure that SHA1 string is in uppercase since the API uses uppercase SHA1 strings.
        $password = strtoupper($password);
        $suffixes = $this->get_suffixes(substr($password, 0, 5));

        if ($suffixes instanceof \WP_Error) {
            return $suffixes;
        }

        return in_array(substr($password, 5), $suffixes);
    }

    /**
     * Extracts the status code from the given response.
     *
     * @param array $response
     *
     * @return int|null
     */
    private function get_response_status_code(array $response)
    {
        if (!isset($response['response']) || !is_array($response['response']) || !isset($response['response']['code'])) {
            return;
        }

        return (int) $response['response']['code'];
    }

    /**
     * Get all the password suffixes for the given password prefix.
     *
     * @param string $prefix
     *
     * @return array|\WP_Error
     */
    private function get_suffixes($prefix)
    {
        $response = $this->http_transport->get(self::ENDPOINT_BASE . '/range/' . $prefix, array(
            'timeout' => 2,
            'user-agent' => 'PasswordsEvolvedPlugin/' . $this->version,
        ));

        if ($response instanceof \WP_Error) {
            return $response;
        }

        $response_status_code = $this->get_response_status_code($response);

        if (null === $response_status_code) {
            return new TranslatableError('response.no_status_code');
        } elseif (200 != $response_status_code) {
            return new TranslatableError('response.invalid', array('status_code' => $response_status_code));
        } elseif (empty($response['body'])) {
            return new TranslatableError('response.empty_body');
        }

        // array_map needed to fix a weird bug where strings returned by preg_replace were
        // longer than 35 characters.
        return array_map(function ($suffix) {
            return substr($suffix, 0, 35);
        }, preg_replace('/([0-9a-f]{35}):\d+/i', '$1', explode("\n", $response['body'])));
    }
}
