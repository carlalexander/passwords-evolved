<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\Tests\Unit\Password;

use PasswordsEvolved\Password\Generator\WordPressPasswordGenerator;
use PasswordsEvolved\Tests\Mock\FunctionMockTrait;
use PHPUnit\Framework\TestCase;

class WordPressPasswordGeneratorTest extends TestCase
{
    use FunctionMockTrait;

    public function test_generate_password()
    {
        $wp_rand = $this->getFunctionMock($this->getNamespace(WordPressPasswordGenerator::class), 'wp_rand');
        $wp_rand->expects($this->exactly(18))
                ->with($this->identicalTo(0), $this->identicalTo(71))
                ->willReturn(0);

        $generator = new WordPressPasswordGenerator();

        $this->assertEquals('aaaaaaaaaaaaaaaaaa', $generator->generate_password());
    }

    public function test_generate_password_all_characters()
    {
        $wp_rand = $this->getFunctionMock($this->getNamespace(WordPressPasswordGenerator::class), 'wp_rand');
        $wp_rand->expects($this->exactly(18))
                ->with($this->identicalTo(0), $this->identicalTo(91))
                ->willReturn(0);

        $generator = new WordPressPasswordGenerator();

        $this->assertEquals('aaaaaaaaaaaaaaaaaa', $generator->generate_password(18, true, true));
    }

    public function test_generate_password_only_alphanumeric()
    {
        $wp_rand = $this->getFunctionMock($this->getNamespace(WordPressPasswordGenerator::class), 'wp_rand');
        $wp_rand->expects($this->exactly(18))
                ->with($this->identicalTo(0), $this->identicalTo(61))
                ->willReturn(0);

        $generator = new WordPressPasswordGenerator();

        $this->assertEquals('aaaaaaaaaaaaaaaaaa', $generator->generate_password(18, false));
    }
}
