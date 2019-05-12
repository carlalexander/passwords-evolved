<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\Tests\Subscriber;

use PasswordsEvolved\Admin\NetworkAdminPage;
use PasswordsEvolved\Options;
use PasswordsEvolved\Subscriber\NetworkAdminPageSubscriber;
use PasswordsEvolved\Tests\Traits\FunctionMockTrait;

class NetworkAdminPageSubscriberTest extends \PHPUnit_Framework_TestCase
{
    use FunctionMockTrait;

    public function test_get_subscribed_events()
    {
        $callbacks = NetworkAdminPageSubscriber::get_subscribed_events();

        foreach ($callbacks as $callback) {
            $this->assertTrue(method_exists(NetworkAdminPageSubscriber::class, is_array($callback) ? $callback[0] : $callback));
        }
    }

    public function test_add_admin_page()
    {
        $admin_page = $this->get_network_admin_page_mock();
        $admin_page->expects($this->once())
                   ->method('get_parent_slug')
                   ->willReturn('parent_slug');
        $admin_page->expects($this->once())
                   ->method('get_page_title')
                   ->willReturn('page_title');
        $admin_page->expects($this->once())
                   ->method('get_menu_title')
                   ->willReturn('menu_title');
        $admin_page->expects($this->once())
                   ->method('get_capability')
                   ->willReturn('capability');
        $admin_page->expects($this->once())
                   ->method('get_slug')
                   ->willReturn('slug');

        $add_submenu_page = $this->getFunctionMock($this->getNamespace(NetworkAdminPageSubscriber::class), 'add_submenu_page');
        $add_submenu_page->expects($this->once())
                         ->with($this->equalTo('parent_slug'), $this->equalTo('page_title'), $this->equalTo('menu_title'), $this->equalTo('capability'), $this->equalTo('slug'), $this->identicalTo(array($admin_page, 'render_page')));

        $subscriber = new NetworkAdminPageSubscriber($this->get_options_mock(), $admin_page, 'basename');

        $subscriber->add_admin_page();
    }

    public function test_add_plugin_page_link()
    {
        $admin_page = $this->get_network_admin_page_mock();
        $admin_page->expects($this->once())
                   ->method('get_page_url')
                   ->willReturn('page_url');
        $admin_page->expects($this->once())
                   ->method('get_plugins_page_title')
                   ->willReturn('plugins_page_title');

        $subscriber = new NetworkAdminPageSubscriber($this->get_options_mock(), $admin_page, 'basename');

        $this->assertSame(array(), $subscriber->add_plugin_page_link(array(), 'foo'));
        $this->assertSame(array('<a href="page_url">plugins_page_title</a>'), $subscriber->add_plugin_page_link(array(), 'basename'));
    }

    public function test_configure()
    {
        $admin_page = $this->get_network_admin_page_mock();
        $admin_page->expects($this->once())
                   ->method('configure');

        $subscriber = new NetworkAdminPageSubscriber($this->get_options_mock(), $admin_page, 'basename');

        $subscriber->configure();
    }

    public function test_save_network_options_with_empty_options()
    {
        $admin_page = $this->get_network_admin_page_mock();
        $admin_page->expects($this->once())
                   ->method('get_page_url')
                   ->willReturn('page_url');
        $admin_page->expects($this->once())
                   ->method('get_slug')
                   ->willReturn('slug');

        $options = $this->get_options_mock();
        $options->expects($this->once())
                ->method('get_option_name')
                ->with($this->equalTo('enforced_roles'))
                ->willReturn('enforced_roles');
        $options->expects($this->once())
                ->method('set')
                ->with($this->equalTo('enforced_roles'), $this->identicalTo(array()));

        $check_admin_referer = $this->getFunctionMock($this->getNamespace(NetworkAdminPageSubscriber::class), 'check_admin_referer');
        $check_admin_referer->expects($this->once())
                            ->with($this->equalTo('slug-options'));

        $wp_redirect = $this->getFunctionMock($this->getNamespace(NetworkAdminPageSubscriber::class), 'wp_redirect');
        $wp_redirect->expects($this->once())
                    ->with($this->equalTo('page_url&updated=true'));

        $subscriber = new NetworkAdminPageSubscriber($options, $admin_page, 'basename');

        $subscriber->save_network_options(true);
    }

    public function test_save_network_options_with_options()
    {
        $admin_page = $this->get_network_admin_page_mock();
        $admin_page->expects($this->once())
                   ->method('get_page_url')
                   ->willReturn('page_url');
        $admin_page->expects($this->once())
                   ->method('get_slug')
                   ->willReturn('slug');

        $options = $this->get_options_mock();
        $options->expects($this->exactly(2))
                ->method('get_option_name')
                ->with($this->equalTo('enforced_roles'))
                ->willReturn('enforced_roles');
        $options->expects($this->once())
                ->method('set')
                ->with($this->equalTo('enforced_roles'), $this->identicalTo(array('administrator', 'editor')));

        $check_admin_referer = $this->getFunctionMock($this->getNamespace(NetworkAdminPageSubscriber::class), 'check_admin_referer');
        $check_admin_referer->expects($this->once())
                            ->with($this->equalTo('slug-options'));

        $wp_redirect = $this->getFunctionMock($this->getNamespace(NetworkAdminPageSubscriber::class), 'wp_redirect');
        $wp_redirect->expects($this->once())
                    ->with($this->equalTo('page_url&updated=true'));

        $_POST['enforced_roles'] = array('administrator', 'editor');

        $subscriber = new NetworkAdminPageSubscriber($options, $admin_page, 'basename');

        $subscriber->save_network_options(true);

        unset($_POST['enforced_roles']);
    }

    /**
     * Creates a mock of network admin page object.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function get_network_admin_page_mock()
    {
        return $this->getMockBuilder(NetworkAdminPage::class)->disableOriginalConstructor()->getMock();
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
}
