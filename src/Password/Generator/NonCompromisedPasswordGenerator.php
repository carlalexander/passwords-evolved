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

use PasswordsEvolved\API\HIBPClient;

/**
 * Password generator that uses the HIBP API client to ensure that a generated password hasn't been compromised.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class NonCompromisedPasswordGenerator implements PasswordGeneratorInterface
{
    /**
     * The HIBP API client.
     *
     * @var HIBPClient
     */
    private $api_client;

    /**
     * The password generator used to generate the passwords that we're validating.
     *
     * @var PasswordGeneratorInterface
     */
    private $password_generator;

    /**
     * Constructor.
     *
     * @param HIBPClient                 $api_client
     * @param PasswordGeneratorInterface $password_generator
     */
    public function __construct(HIBPClient $api_client, PasswordGeneratorInterface $password_generator)
    {
        $this->api_client = $api_client;
        $this->password_generator = $password_generator;
    }

    /**
     * {@inheritdoc}
     */
    public function generate_password($length = self::MIN_LENGTH, $special_chars = true, $extra_special_chars = false)
    {
        $password = $this->password_generator->generate_password($length, $special_chars, $extra_special_chars);

        if ($length < self::MIN_LENGTH || !$this->api_client->is_api_active()) {
            return $password;
        }

        while (true === $this->api_client->is_password_compromised($password)) {
            $password = $this->password_generator->generate_password($length, $special_chars, $extra_special_chars);
        }

        return $password;
    }
}
