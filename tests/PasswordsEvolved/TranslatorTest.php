<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\Tests;

use PasswordsEvolved\Tests\Traits\FunctionMockTrait;
use PasswordsEvolved\Translator;

class TranslatorTest extends \PHPUnit_Framework_TestCase
{
    use FunctionMockTrait;

    /**
     * @var Translator
     */
    protected $translator;

    protected function setUp()
    {
        $this->translator = new Translator('passwords-evolved-test');
    }

    protected function tearDown()
    {
        $this->translator = null;
    }

    public function test_translate()
    {
        $__ = $this->getFunctionMock($this->getNamespace(Translator::class), '__');
        $__->expects($this->once())
           ->with($this->equalTo('foo'), $this->equalTo('passwords-evolved-test'))
           ->willReturn('bar');

        $this->assertEquals('bar', $this->translator->translate('foo'));
    }
}
