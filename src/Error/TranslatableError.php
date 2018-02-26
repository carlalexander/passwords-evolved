<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\Error;

use PasswordsEvolved\Plugin;

/**
 * A WordPress error with translatable messages.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class TranslatableError extends \WP_Error
{
    /**
     * Constructor.
     *
     * @param string $message
     * @param array $data
     */
    public function __construct($message, array $data = array())
    {
        parent::__construct($message, $message, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function get_error_messages($code = '')
    {
        if (!empty($code) && isset($this->errors[$code])) {
            $data = $this->get_error_data($code);
            return $this->translate_messages($this->errors[$code], $data);
        }

        $translated_messages = array();

        foreach ($this->errors as $code => $messages) {
            $data = $this->get_error_data($code);
            $translated_messages = array_merge($translated_messages, $this->translate_messages($messages, $data));
        }

        return array_unique($translated_messages);
    }

    /**
     * Translate the given array of error messages.
     *
     * @param array $messages
     * @param mixed $data
     *
     * @return array
     */
    private function translate_messages(array $messages, $data)
    {
        if (empty($messages)) {
            return $messages;
        }

        $translated_messages = array();

        foreach ($messages as $message) {
            $translated_messages[] = $this->translate_message($message, $data);
        }

        return array_filter($translated_messages);
    }

    /**
     * Translate the given message and insert placeholder data values.
     *
     * @param string $message
     * @param mixed  $data
     *
     * @return string
     */
    private function translate_message($message, $data)
    {
        if (empty($message)) {
            return $message;
        }

        $message = __('error.' . $message, Plugin::DOMAIN);

        if (is_array($data)) {
            $message = vsprintf($message, $data);
        }

        return $message;
    }
}
