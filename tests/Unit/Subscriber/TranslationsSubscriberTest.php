<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\Tests\Unit\Subscriber;

use PasswordsEvolved\EventManagement\EventManager;
use PasswordsEvolved\Subscriber\TranslationsSubscriber;
use PasswordsEvolved\Tests\Mock\FunctionMockTrait;
use PHPUnit\Framework\TestCase;

class TranslationsSubscriberTest extends TestCase
{
    use FunctionMockTrait;

    /**
     * @var TranslationsSubscriber;
     */
    private $subscriber;

    protected function setUp(): void
    {
        $this->subscriber = new TranslationsSubscriber('passwords-evolved-test', '/path/to/translation');
    }

    protected function tearDown(): void
    {
        $this->subscriber = null;
    }

    public function test_get_subscribed_events()
    {
        $callbacks = TranslationsSubscriber::get_subscribed_events();

        foreach ($callbacks as $callback) {
            $this->assertTrue(method_exists(TranslationsSubscriber::class, is_array($callback) ? $callback[0] : $callback));
        }
    }

    public function test_ensure_default_translation_with_other_domain()
    {
        $this->assertEquals('/path/to/translation', $this->subscriber->ensure_default_translation('/path/to/translation', 'foo-domain'));
    }

    public function test_ensure_default_translation_with_different_path()
    {
        $this->assertEquals('/path/to/other/translation', $this->subscriber->ensure_default_translation('/path/to/other/translation', 'passwords-evolved-test'));
    }

    public function test_ensure_default_translation_with_readable_translation()
    {
        $is_readable = $this->getFunctionMock($this->getNamespace(TranslationsSubscriber::class), 'is_readable');
        $is_readable->expects($this->once())
                    ->with($this->identicalTo('/path/to/translation'))
                    ->willReturn(true);

        $this->assertEquals('/path/to/translation', $this->subscriber->ensure_default_translation('/path/to/translation', 'passwords-evolved-test'));
    }

    public function test_ensure_default_translation_with_unreadable_translation()
    {
        $is_readable = $this->getFunctionMock($this->getNamespace(TranslationsSubscriber::class), 'is_readable');
        $is_readable->expects($this->once())
                    ->with($this->identicalTo('/path/to/translation/passwords-evolved-test-es_ES'))
                    ->willReturn(false);

        $this->assertEquals('/path/to/translation/passwords-evolved-test-en_US', $this->subscriber->ensure_default_translation('/path/to/translation/passwords-evolved-test-es_ES', 'passwords-evolved-test'));
    }

    public function test_register_translations()
    {
        $determine_locale = $this->getFunctionMock($this->getNamespace(TranslationsSubscriber::class), 'determine_locale');
        $determine_locale->expects($this->once())
                         ->willReturn('en_US');

        $event_manager = $this->get_event_manager_mock();
        $event_manager->expects($this->once())
                      ->method('filter')
                      ->with($this->identicalTo('plugin_locale'), $this->identicalTo('en_US'), $this->identicalTo('passwords-evolved-test'))
                      ->willReturn('en_US');

        $load_textdomain = $this->getFunctionMock($this->getNamespace(TranslationsSubscriber::class), 'load_textdomain');
        $load_textdomain->expects($this->once())
                        ->with($this->equalTo('passwords-evolved-test'), $this->equalTo('/path/to/translation/passwords-evolved-test-en_US.mo'));


        $this->subscriber->set_event_manager($event_manager);

        $this->subscriber->register_translations();
    }

    /**
     * Creates a mock of an event manager object.
     */
    private function get_event_manager_mock()
    {
        return $this->getMockBuilder(EventManager::class)->disableOriginalConstructor()->getMock();
    }
}
