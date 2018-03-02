<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\Subscriber;

use PasswordsEvolved\Admin\NetworkAdminPage;
use PasswordsEvolved\Options;

/**
 * Subscriber that registers the plugin's admin page with WordPress as a network admin page.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class NetworkAdminPageSubscriber extends AdminPageSubscriber
{
    /**
     * The plugin options.
     *
     * @var Options
     */
    private $options;

    /**
     * Constructor.
     *
     * @param Options          $options
     * @param NetworkAdminPage $page
     * @param string           $plugin_basename
     */
    public function __construct(Options $options, NetworkAdminPage $page, $plugin_basename)
    {
        parent::__construct($page, $plugin_basename);

        $this->options = $options;
    }

    /**
     * {@inheritdoc}
     */
    public static function get_subscribed_events()
    {
        return array(
            'admin_init' => 'configure',
            'network_admin_menu' => 'add_admin_page',
            'network_admin_plugin_action_links' => array('add_plugin_page_link', 10, 2),
            'network_admin_edit_passwords_evolved' => 'save_network_options'
        );
    }

    /**
     * Save the plugin network options when the admin page for is submitted.
     *
     * @SuppressWarnings(PHPMD.ExitExpression)
     *
     * @param bool $return
     */
    public function save_network_options($return = false)
    {
        check_admin_referer($this->page->get_slug() . '-options');

        $enforced_roles = array();

        if (!empty($_POST[$this->options->get_option_name('enforced_roles')])) {
            $enforced_roles = array_map(function ($role) {
                return filter_var($role, FILTER_SANITIZE_STRING);
            }, $_POST[$this->options->get_option_name('enforced_roles')]);
        }

        $this->options->set('enforced_roles', $enforced_roles);

        wp_redirect($this->page->get_page_url() . '&updated=true');

        // Used for unit tests to skip exit statement
        if ($return) {
            return;
        }

        exit;
    }
}
