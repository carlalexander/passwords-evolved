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

use PasswordsEvolved\Admin\AbstractAdminPage;
use PasswordsEvolved\EventManagement\SubscriberInterface;

/**
 * Subscriber that registers the plugin's admin page with WordPress.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
class AdminPageSubscriber implements SubscriberInterface
{
    /**
     * The admin page.
     *
     * @var AbstractAdminPage
     */
    protected $page;

    /**
     * The basename of the plugin.
     *
     * @var string
     */
    protected $plugin_basename;

    /**
     * Constructor.
     *
     * @param AbstractAdminPage $page
     * @param string            $plugin_basename
     */
    public function __construct(AbstractAdminPage $page, $plugin_basename)
    {
        $this->page = $page;
        $this->plugin_basename = $plugin_basename;
    }

    /**
     * {@inheritdoc}
     */
    public static function get_subscribed_events()
    {
        return array(
            'admin_init' => 'configure',
            'admin_menu' => 'add_admin_page',
            'plugin_action_links' => array('add_plugin_page_link', 10, 2),
        );
    }

    /**
     * Adds the plugin's admin page to the options menu.
     */
    public function add_admin_page()
    {
        add_submenu_page($this->page->get_parent_slug(), $this->page->get_page_title(), $this->page->get_menu_title(), $this->page->get_capability(), $this->page->get_slug(), array($this->page, 'render_page'));
    }

    /**
     * Adds link from plugins page to Passwords Evolved admin page.
     *
     * @param array  $links
     * @param string $file
     *
     * @return array
     */
    public function add_plugin_page_link(array $links, $file)
    {
        if ($file != $this->plugin_basename) {
            return $links;
        }

        array_unshift($links, sprintf('<a href="%s">%s</a>', $this->page->get_page_url(), $this->page->get_plugins_page_title()));

        return $links;
    }

    /**
     * Configure the admin page using the Settings API.
     */
    public function configure()
    {
        $this->page->configure();
    }
}
