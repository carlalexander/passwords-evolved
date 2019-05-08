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

use PasswordsEvolved\Admin\AdminPage;
use PasswordsEvolved\Subscriber\AdminPageSubscriber;
use PasswordsEvolved\Tests\Traits\FunctionMockTrait;

class AdminPageSubscriberTest extends \PHPUnit_Framework_TestCase
{
    use FunctionMockTrait;

    public function test_get_subscribed_events()
    {
        $callbacks = AdminPageSubscriber::get_subscribed_events();

        foreach ($callbacks as $callback) {
            $this->assertTrue(method_exists(AdminPageSubscriber::class, is_array($callback) ? $callback[0] : $callback));
        }
    }

    public function test_add_admin_page()
    {
        $admin_page = $this->get_admin_page_mock();
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

        $add_submenu_page = $this->getFunctionMock($this->getNamespace(AdminPageSubscriber::class), 'add_submenu_page');
        $add_submenu_page->expects($this->once())
                         ->with($this->equalTo('parent_slug'), $this->equalTo('page_title'), $this->equalTo('menu_title'), $this->equalTo('capability'), $this->equalTo('slug'), $this->identicalTo(array($admin_page, 'render_page')));

        $subscriber = new AdminPageSubscriber($admin_page, 'basename');

        $subscriber->add_admin_page();
    }

    public function test_add_plugin_page_link()
    {
        $admin_page = $this->get_admin_page_mock();
        $admin_page->expects($this->once())
                   ->method('get_page_url')
                   ->willReturn('page_url');
        $admin_page->expects($this->once())
                   ->method('get_plugins_page_title')
                   ->willReturn('plugins_page_title');

        $subscriber = new AdminPageSubscriber($admin_page, 'basename');

        $this->assertSame(array(), $subscriber->add_plugin_page_link(array(), 'foo'));
        $this->assertSame(array('<a href="page_url">plugins_page_title</a>'), $subscriber->add_plugin_page_link(array(), 'basename'));
    }

    public function test_configure()
    {
        $admin_page = $this->get_admin_page_mock();
        $admin_page->expects($this->once())
                   ->method('configure');

        $subscriber = new AdminPageSubscriber($admin_page, 'basename');

        $subscriber->configure();
    }

    /**
     * Creates a mock of an admin page object.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function get_admin_page_mock()
    {
        return $this->getMockBuilder(AdminPage::class)->disableOriginalConstructor()->getMock();
    }
}