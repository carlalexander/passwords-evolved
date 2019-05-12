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
 * A password generator generates a random passwords from a defined set of characters.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
interface PasswordGeneratorInterface
{
    /**
     * Minimum password length required for a strong password.
     *
     * @var int
     */
    const MIN_LENGTH = 18;

    /**
     * Generate a random password.
     *
     * @param int $length
     * @param bool $special_chars
     * @param bool $extra_special_chars
     *
     * @return string
     */
    public function generate_password($length = self::MIN_LENGTH, $special_chars = true, $extra_special_chars = false);
}
