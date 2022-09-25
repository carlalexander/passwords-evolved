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
 * The plugin's admin page.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class AdminPage extends AbstractAdminPage
{
    /**
     * {@inheritdoc}
     */
    public function get_capability()
    {
        return 'edit_users';
    }

    /**
     * {@inheritdoc}
     */
    public function get_parent_slug()
    {
        return 'options-general.php';
    }

    /**
     * {@inheritdoc}
     */
    protected function get_form_url()
    {
        return 'options.php';
    }
}
