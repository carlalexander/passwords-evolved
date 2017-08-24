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

use PasswordsEvolved\Subscriber\TranslationsSubscriber;
use phpmock\phpunit\PHPMock;

class TranslationsSubscriberTest extends \PHPUnit_Framework_TestCase
{
    use PHPMock;

    /**
     * @var TranslationsSubscriber;
     */
    private $subscriber;

    protected function setUp()
    {
        $this->subscriber = new TranslationsSubscriber('passwords-evolved-test', '/path/to/translation');
    }

    protected function tearDown()
    {
        $this->subscriber = null;
    }

    public function test_get_subscribed_events()
    {
        $callbacks = TranslationsSubscriber::get_subscribed_events();

        foreach ($callbacks as $callback) {
            $this->assertTrue(method_exists('PasswordsEvolved\Subscriber\TranslationsSubscriber', is_array($callback) ? $callback[0] : $callback));
        }
    }

    public function test_enforce_locale_forces_english_locale_for_plugin()
    {
        $this->assertEquals('en_US', $this->subscriber->enforce_locale('es_ES', 'passwords-evolved-test'));
    }

    public function test_enforce_locale_respects_other_domain()
    {
        $this->assertEquals('es_ES', $this->subscriber->enforce_locale('es_ES', 'foo-domain'));
    }

    public function test_register_translations()
    {
        $load_plugin_textdomain = $this->getFunctionMock('PasswordsEvolved\Subscriber', 'load_plugin_textdomain');
        $load_plugin_textdomain->expects($this->once())
                               ->with($this->equalTo('passwords-evolved-test'), $this->identicalTo(false), $this->equalTo('/path/to/translation'));

        $this->subscriber->register_translations();
    }
}