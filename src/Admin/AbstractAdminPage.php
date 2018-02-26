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
 * A WordPress admin page.
 *
 * @author Carl Alexander <contact@carlalexander.ca>
 */
abstract class AbstractAdminPage
{
    /**
     * The HIBP API client.
     *
     * @var HIBPClient
     */
    protected $api_client;

    /**
     * The plugin options.
     *
     * @var Options
     */
    protected $options;

    /**
     * Slug used by the admin page.
     *
     * @var string
     */
    protected $slug = 'passwords-evolved';

    /**
     * Path to the admin page templates.
     *
     * @var string
     */
    protected $template_path;

    /**
     * The plugin translator.
     *
     * @var Translator
     */
    protected $translator;

    /**
     * Constructor.
     *
     * @param HIBPClient $api_client
     * @param Options    $options
     * @param string     $template_path
     * @param Translator $translator
     */
    public function __construct(HIBPClient $api_client, Options $options, $template_path, Translator $translator)
    {
        $this->api_client = $api_client;
        $this->options = $options;
        $this->template_path = $template_path;
        $this->translator = $translator;
    }

    /**
     * Configure the admin page using the Settings API.
     */
    public function configure()
    {
        // Register settings
        register_setting($this->get_slug(), $this->options->get_option_name('enforced_roles'));

        // Register section
        add_settings_section($this->get_slug() . '-section', $this->translate('section.title'), array($this, 'render_section'), $this->get_slug());
        add_settings_field($this->get_slug() . '-api-status', $this->translate('api_status.title'), array($this, 'render_api_status_field'), $this->get_slug(), $this->get_slug() . '-section');
        add_settings_field($this->get_slug() . '-enforced-role', $this->translate('enforced_roles.title'), array($this, 'render_enforced_roles_field'), $this->get_slug(), $this->get_slug() . '-section');
    }

    /**
     * Get the title of the admin page in the WordPress admin menu.
     *
     * @return string
     */
    public function get_menu_title()
    {
        return $this->translate('menu_title');
    }

    /**
     * Get the title of the admin page.
     *
     * @return string
     */
    public function get_page_title()
    {
        return $this->translate('page_title');
    }

    /**
     * Get the URL of the admin page.
     *
     * @return string
     */
    public function get_page_url()
    {
        return network_admin_url($this->get_parent_slug()) . '?page=' . $this->get_slug();
    }

    /**
     * Get the title used for the admin page link on the plugins page.
     *
     * @return string
     */
    public function get_plugins_page_title()
    {
        return $this->translate('plugins_page.title');
    }

    /**
     * Get the slug used by the admin page.
     *
     * @return string
     */
    public function get_slug()
    {
        return $this->slug;
    }

    /**
     * Render the API status field.
     */
    public function render_api_status_field()
    {
        $this->render_template('api_status_field');
    }

    /**
     * Renders the field for the enforced roles.
     */
    public function render_enforced_roles_field()
    {
        $this->render_template('enforced_roles_field');
    }

    /**
     * Render the plugin's admin page.
     */
    public function render_page()
    {
        $this->render_template('page');
    }

    /**
     * Render the top section of the plugin's admin page.
     */
    public function render_section()
    {
        $this->render_template('section');
    }

    /**
     * Get the capability required to view the admin page.
     *
     * @return string
     */
    abstract public function get_capability();

    /**
     * Get the parent slug of the admin page.
     *
     * @return string
     */
    abstract public function get_parent_slug();

    /**
     * Renders the given template if it's readable.
     *
     * @param string $template
     */
    protected function render_template($template)
    {
        $template_path = $this->template_path . $template . '.php';

        if (!is_readable($template_path)) {
            return;
        }

        include $template_path;
    }

    /**
     * Translate a string within the admin page context.
     *
     * @param string $string
     *
     * @return string
     */
    protected function translate($string)
    {
        return $this->translator->translate('admin_page.' . $string);
    }

    /**
     * Get the URL used to submit the admin page form.
     *
     * @return string
     */
    abstract protected function get_form_url();
}
