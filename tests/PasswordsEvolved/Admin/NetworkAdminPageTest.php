<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\Tests\Admin;

use PasswordsEvolved\Admin\NetworkAdminPage;
use PasswordsEvolved\Options;
use PasswordsEvolved\Tests\Traits\FunctionMockTrait;
use PasswordsEvolved\Translator;

class NetworkAdminPageTest extends \PHPUnit_Framework_TestCase
{
    use FunctionMockTrait;

    public function test_configure()
    {
        $options = $this->get_options_mock();
        $options->expects($this->once())
            ->method('get_option_name')
            ->with($this->equalTo('enforced_roles'))
            ->willReturn('enforced_roles');

        $translator = $this->get_translator_mock();
        $translator->expects($this->exactly(3))
            ->method('translate')
            ->withConsecutive(
                array($this->equalTo('admin_page.section.title')),
                array($this->equalTo('admin_page.api_status.title')),
                array($this->equalTo('admin_page.enforced_roles.title'))
            )
            ->willReturnOnConsecutiveCalls('admin_page.section.title', 'admin_page.api_status.title', 'admin_page.enforced_roles.title');

        $page = new NetworkAdminPage($options, '/template/path', $translator);

        $register_setting = $this->getFunctionMock($this->getNamespace(NetworkAdminPage::class), 'register_setting');
        $register_setting->expects($this->once())
            ->with($this->equalTo('passwords-evolved'), $this->equalTo('enforced_roles'));

        $add_settings_section = $this->getFunctionMock($this->getNamespace(NetworkAdminPage::class), 'add_settings_section');
        $add_settings_section->expects($this->once())
            ->with($this->equalTo('passwords-evolved-section'), $this->equalTo('admin_page.section.title'), $this->identicalTo(array($page, 'render_section')), $this->equalTo('passwords-evolved'));

        $add_settings_field = $this->getFunctionMock($this->getNamespace(NetworkAdminPage::class), 'add_settings_field');
        $add_settings_field->expects($this->at(0))
            ->with($this->equalTo('passwords-evolved-api-status'), $this->equalTo('admin_page.api_status.title'), $this->identicalTo(array($page, 'render_api_status_field')), $this->equalTo('passwords-evolved'), $this->equalTo('passwords-evolved-section'));
        $add_settings_field->expects($this->at(1))
            ->with($this->equalTo('passwords-evolved-enforced-role'), $this->equalTo('admin_page.enforced_roles.title'), $this->identicalTo(array($page, 'render_enforced_roles_field')), $this->equalTo('passwords-evolved'), $this->equalTo('passwords-evolved-section'));

        $page->configure();
    }

    public function test_get_capability()
    {
        $page = new NetworkAdminPage($this->get_options_mock(), '/template/path', $this->get_translator_mock());

        $this->assertEquals('manage_network_plugins', $page->get_capability());
    }

    public function test_get_menu_title()
    {
        $translator = $this->get_translator_mock();

        $translator->expects($this->once())
            ->method('translate')
            ->with($this->equalTo('admin_page.menu_title'))
            ->willReturn('menu_title');

        $page = new NetworkAdminPage($this->get_options_mock(), '/template/path', $translator);

        $this->assertEquals('menu_title', $page->get_menu_title());
    }

    public function test_get_page_title()
    {
        $translator = $this->get_translator_mock();

        $translator->expects($this->once())
            ->method('translate')
            ->with($this->equalTo('admin_page.page_title'))
            ->willReturn('page_title');

        $page = new NetworkAdminPage($this->get_options_mock(), '/template/path', $translator);

        $this->assertEquals('page_title', $page->get_page_title());
    }

    public function test_get_page_url()
    {
        $admin_url = $this->getFunctionMock($this->getNamespace(NetworkAdminPage::class), 'network_admin_url');
        $admin_url->expects($this->once())
            ->with($this->equalTo('settings.php'))
            ->willReturn('settings.php');

        $page = new NetworkAdminPage($this->get_options_mock(), '/template/path', $this->get_translator_mock());

        $this->assertEquals('settings.php?page=passwords-evolved', $page->get_page_url());
    }

    public function test_get_parent_slug()
    {
        $page = new NetworkAdminPage($this->get_options_mock(), '/template/path', $this->get_translator_mock());

        $this->assertEquals('settings.php', $page->get_parent_slug());
    }

    public function test_get_plugins_page_title()
    {
        $translator = $this->get_translator_mock();

        $translator->expects($this->once())
            ->method('translate')
            ->with($this->equalTo('admin_page.plugins_page.title'))
            ->willReturn('plugins_page.title');

        $page = new NetworkAdminPage($this->get_options_mock(), '/template/path', $translator);

        $this->assertEquals('plugins_page.title', $page->get_plugins_page_title());
    }

    public function test_get_slug()
    {
        $page = new NetworkAdminPage($this->get_options_mock(), '/template/path', $this->get_translator_mock());

        $this->assertEquals('passwords-evolved', $page->get_slug());
    }

    /**
     * Creates a mock of the plugin options class.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function get_options_mock()
    {
        return $this->getMockBuilder(Options::class)->getMock();
    }

    /**
     * Creates a mock of the plugin translator class.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function get_translator_mock()
    {
        return $this->getMockBuilder(Translator::class)->disableOriginalConstructor()->getMock();
    }
}
