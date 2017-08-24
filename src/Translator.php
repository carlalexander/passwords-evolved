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
 * Translator that translates strings using the WordPress translation API.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class Translator
{
    /**
     * Domain used by the translator.
     *
     * @var string
     */
    private $domain;

    /**
     * Constructor.
     *
     * @param string $domain
     */
    public function __construct($domain)
    {
        $this->domain = $domain;
    }

    /**
     * Translate the given string.
     *
     * @param $string
     *
     * @return string
     */
    public function translate($string)
    {
        return __($string, $this->domain);
    }
}
