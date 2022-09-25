<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\Admin;

use PasswordsEvolved\API\HIBPClient;
use PasswordsEvolved\Options;
use PasswordsEvolved\Translator;

/**
 * The plugin's network admin page.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class NetworkAdminPage extends AbstractAdminPage
{
    /**
     * {@inheritdoc}
     */
    public function get_capability()
    {
        return 'manage_network_users';
    }

    /**
     * {@inheritdoc}
     */
    public function get_parent_slug()
    {
        return 'settings.php';
    }

    /**
     * {@inheritdoc}
     */
    protected function get_form_url()
    {
        return 'edit.php?action=passwords_evolved';
    }
}
