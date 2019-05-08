<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\Tests\Password;

use PasswordsEvolved\API\HIBPClient;
use PasswordsEvolved\Password\Generator;
use PasswordsEvolved\Tests\Traits\FunctionMockTrait;

class GeneratorTest extends \PHPUnit_Framework_TestCase
{
    use FunctionMockTrait;

    public function test_generate_password()
    {
        $api_client = $this->get_api_client_mock();
        $api_client->expects($this->once())
                   ->method('is_api_active')
                   ->willReturn(true);
        $api_client->expects($this->once())
                   ->method('is_password_compromised')
                   ->with($this->equalTo('aaaaaaaaaaaaaaaa'))
                   ->willReturn(false);

        $wp_rand = $this->getFunctionMock($this->getNamespace(Generator::class), 'wp_rand');
        $wp_rand->expects($this->exactly(16))
                ->with($this->identicalTo(0), $this->identicalTo(71))
                ->willReturn(0);

        $generator = new Generator($api_client);

        $this->assertEquals('aaaaaaaaaaaaaaaa', $generator->generate_password());
    }

    public function test_generate_password_all_characters()
    {
        $api_client = $this->get_api_client_mock();
        $api_client->expects($this->once())
                   ->method('is_api_active')
                   ->willReturn(true);
        $api_client->expects($this->once())
                   ->method('is_password_compromised')
                   ->with($this->equalTo('aaaaaaaaaaaaaaaa'))
                   ->willReturn(false);

        $wp_rand = $this->getFunctionMock($this->getNamespace(Generator::class), 'wp_rand');
        $wp_rand->expects($this->exactly(16))
                ->with($this->identicalTo(0), $this->identicalTo(91))
                ->willReturn(0);

        $generator = new Generator($api_client);

        $this->assertEquals('aaaaaaaaaaaaaaaa', $generator->generate_password(16, true, true));
    }

    public function test_generate_password_only_alphanumeric()
    {
        $api_client = $this->get_api_client_mock();
        $api_client->expects($this->once())
                   ->method('is_api_active')
                   ->willReturn(true);
        $api_client->expects($this->once())
                   ->method('is_password_compromised')
                   ->with($this->equalTo('aaaaaaaaaaaaaaaa'))
                   ->willReturn(false);

        $wp_rand = $this->getFunctionMock($this->getNamespace(Generator::class), 'wp_rand');
        $wp_rand->expects($this->exactly(16))
                ->with($this->identicalTo(0), $this->identicalTo(61))
                ->willReturn(0);

        $generator = new Generator($api_client);

        $this->assertEquals('aaaaaaaaaaaaaaaa', $generator->generate_password(16, false));
    }

    public function test_generate_password_api_is_inactive()
    {
        $api_client = $this->get_api_client_mock();
        $api_client->expects($this->once())
                   ->method('is_api_active')
                   ->willReturn(false);
        $api_client->expects($this->never())
                   ->method('is_password_compromised');

        $wp_rand = $this->getFunctionMock($this->getNamespace(Generator::class), 'wp_rand');
        $wp_rand->expects($this->exactly(16))
                ->with($this->identicalTo(0), $this->identicalTo(61))
                ->willReturn(0);

        $generator = new Generator($api_client);

        $this->assertEquals('aaaaaaaaaaaaaaaa', $generator->generate_password(16, false));
    }

    public function test_generate_password_length_too_short()
    {
        $api_client = $this->get_api_client_mock();
        $api_client->expects($this->never())
                   ->method('is_password_compromised');

        $wp_rand = $this->getFunctionMock($this->getNamespace(Generator::class), 'wp_rand');
        $wp_rand->expects($this->exactly(8))
                ->with($this->identicalTo(0), $this->identicalTo(71))
                ->willReturn(0);

        $generator = new Generator($api_client);

        $this->assertEquals('aaaaaaaa', $generator->generate_password(8));
    }

    /**
     * Creates a mock of the HIBP API client.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function get_api_client_mock()
    {
        return $this->getMockBuilder(HIBPClient::class)->disableOriginalConstructor()->getMock();
    }
}