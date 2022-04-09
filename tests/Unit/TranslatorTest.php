<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\Tests\Unit;

use PasswordsEvolved\Tests\Mock\FunctionMockTrait;
use PasswordsEvolved\Translator;
use PHPUnit\Framework\TestCase;

class TranslatorTest extends TestCase
{
    use FunctionMockTrait;

    /**
     * @var Translator
     */
    protected $translator;

    protected function setUp(): void
    {
        $this->translator = new Translator('passwords-evolved-test');
    }

    protected function tearDown(): void
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
