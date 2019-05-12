<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\Tests\Password\Generator;

use PasswordsEvolved\API\HIBPClient;
use PasswordsEvolved\Password\Generator\NonCompromisedPasswordGenerator;
use PasswordsEvolved\Password\Generator\PasswordGeneratorInterface;
use PasswordsEvolved\Tests\Traits\FunctionMockTrait;

class NonCompromisedPasswordGeneratorTest extends \PHPUnit_Framework_TestCase
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
                   ->with($this->equalTo('aaaaaaaaaaaaaaaaaa'))
                   ->willReturn(false);

        $password_generator = $this->get_password_generator_mock();
        $password_generator->expects($this->once())
                           ->method('generate_password')
                           ->with($this->equalTo(18), $this->equalTo(true), $this->equalTo(false))
                           ->willReturn('aaaaaaaaaaaaaaaaaa');

        $generator = new NonCompromisedPasswordGenerator($api_client, $password_generator);

        $this->assertEquals('aaaaaaaaaaaaaaaaaa', $generator->generate_password());
    }

    public function test_generate_password_all_characters()
    {
        $api_client = $this->get_api_client_mock();
        $api_client->expects($this->once())
                   ->method('is_api_active')
                   ->willReturn(true);
        $api_client->expects($this->once())
                   ->method('is_password_compromised')
                   ->with($this->equalTo('aaaaaaaaaaaaaaaaaa'))
                   ->willReturn(false);

        $password_generator = $this->get_password_generator_mock();
        $password_generator->expects($this->once())
                           ->method('generate_password')
                           ->with($this->equalTo(18), $this->equalTo(true), $this->equalTo(true))
                           ->willReturn('aaaaaaaaaaaaaaaaaa');

        $generator = new NonCompromisedPasswordGenerator($api_client, $password_generator);

        $this->assertEquals('aaaaaaaaaaaaaaaaaa', $generator->generate_password(18, true, true));
    }

    public function test_generate_password_only_alphanumeric()
    {
        $api_client = $this->get_api_client_mock();
        $api_client->expects($this->once())
                   ->method('is_api_active')
                   ->willReturn(true);
        $api_client->expects($this->once())
                   ->method('is_password_compromised')
                   ->with($this->equalTo('aaaaaaaaaaaaaaaaaa'))
                   ->willReturn(false);

        $password_generator = $this->get_password_generator_mock();
        $password_generator->expects($this->once())
                           ->method('generate_password')
                           ->with($this->equalTo(18), $this->equalTo(false), $this->equalTo(false))
                           ->willReturn('aaaaaaaaaaaaaaaaaa');

        $generator = new NonCompromisedPasswordGenerator($api_client, $password_generator);

        $this->assertEquals('aaaaaaaaaaaaaaaaaa', $generator->generate_password(18, false));
    }

    public function test_generate_password_api_is_inactive()
    {
        $api_client = $this->get_api_client_mock();
        $api_client->expects($this->once())
                   ->method('is_api_active')
                   ->willReturn(false);
        $api_client->expects($this->never())
                   ->method('is_password_compromised');

        $password_generator = $this->get_password_generator_mock();
        $password_generator->expects($this->once())
                           ->method('generate_password')
                           ->with($this->equalTo(18), $this->equalTo(true), $this->equalTo(false))
                           ->willReturn('aaaaaaaaaaaaaaaaaa');

        $generator = new NonCompromisedPasswordGenerator($api_client, $password_generator);

        $this->assertEquals('aaaaaaaaaaaaaaaaaa', $generator->generate_password());
    }

    public function test_generate_password_length_too_short()
    {
        $api_client = $this->get_api_client_mock();

        $password_generator = $this->get_password_generator_mock();
        $password_generator->expects($this->once())
                           ->method('generate_password')
                           ->with($this->equalTo(8), $this->equalTo(true), $this->equalTo(false))
                           ->willReturn('aaaaaaaa');

        $generator = new NonCompromisedPasswordGenerator($api_client, $password_generator);

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

    /**
     * Creates a PasswordGeneratorInterface mock.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function get_password_generator_mock()
    {
        return $this->getMockBuilder(PasswordGeneratorInterface::class)->getMock();
    }
}
