<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\Tests\Unit\API;

use PasswordsEvolved\API\HIBPClient;
use PasswordsEvolved\Error\TranslatableError;
use PHPUnit\Framework\TestCase;

class HIBPClientTest extends TestCase
{
    public function test_is_api_active_with_response()
    {
        $http_transport = $this->get_http_transport_mock();
        $http_transport->expects($this->once())
                       ->method('get')
                       ->with($this->identicalTo('https://api.pwnedpasswords.com/range/A94A8'), $this->identicalTo(array('timeout' => 2,'user-agent' => 'PasswordsEvolvedPlugin/version_number')))
                       ->willReturn(array('response' => array('code' => 200), 'body' => "1E4C9B93F3F0682250B6CF8331B7EE68FD8:42\nFE5CCB19BA61C4C0873D391E987982FBBD3:230\n"));

        $client = new HIBPClient($http_transport, 'version_number');

        $this->assertTrue($client->is_api_active());
    }

    public function test_is_api_active_with_error()
    {
        $http_transport = $this->get_http_transport_mock();
        $http_transport->expects($this->once())
                       ->method('get')
                       ->with($this->identicalTo('https://api.pwnedpasswords.com/range/A94A8'), $this->identicalTo(array('timeout' => 2,'user-agent' => 'PasswordsEvolvedPlugin/version_number')))
                       ->willReturn($this->get_error_mock());

        $client = new HIBPClient($http_transport, 'version_number');

        $this->assertFalse($client->is_api_active());
    }

    public function test_is_password_compromised_with_compromised_password()
    {
        $http_transport = $this->get_http_transport_mock();
        $http_transport->expects($this->once())
                       ->method('get')
                       ->with($this->identicalTo('https://api.pwnedpasswords.com/range/5BAA6'), $this->identicalTo(array('timeout' => 2,'user-agent' => 'PasswordsEvolvedPlugin/version_number')))
                       ->willReturn(array('response' => array('code' => 200), 'body' => "1E4C9B93F3F0682250B6CF8331B7EE68FD8:42\nFE5CCB19BA61C4C0873D391E987982FBBD3:230\n"));

        $client = new HIBPClient($http_transport, 'version_number');

        $this->assertTrue($client->is_password_compromised('password'));
    }

    public function test_is_password_compromised_with_uncompromised_password()
    {
        $http_transport = $this->get_http_transport_mock();
        $http_transport->expects($this->once())
                       ->method('get')
                       ->with($this->identicalTo('https://api.pwnedpasswords.com/range/5BAA6'), $this->identicalTo(array('timeout' => 2,'user-agent' => 'PasswordsEvolvedPlugin/version_number')))
                       ->willReturn(array('response' => array('code' => 200), 'body' => "1E4C9B93F3F0682250B6CF3456B7EE68FD8:42\nFE5CCB19BA61C4C0873D391E987982FBBD3:230\n"));

        $client = new HIBPClient($http_transport, 'version_number');

        $this->assertFalse($client->is_password_compromised('password'));
    }

    public function test_is_password_compromised_with_non_string_password()
    {
        $http_transport = $this->get_http_transport_mock();

        $client = new HIBPClient($http_transport, 'version_number');

        $error = $client->is_password_compromised(new \stdClass());

        $this->assertInstanceOf(TranslatableError::class, $error);
        $this->assertEquals('password_not_string', $error->get_error_code());
    }

    public function test_is_password_compromised_with_transport_error()
    {
        $error = $this->get_error_mock();

        $http_transport = $this->get_http_transport_mock();
        $http_transport->expects($this->once())
                       ->method('get')
                       ->with($this->identicalTo('https://api.pwnedpasswords.com/range/5BAA6'), $this->identicalTo(array('timeout' => 2,'user-agent' => 'PasswordsEvolvedPlugin/version_number')))
                       ->willReturn($error);

        $client = new HIBPClient($http_transport, 'version_number');

        $this->assertSame($error, $client->is_password_compromised('password'));
    }

    public function test_is_password_compromised_with_no_status_code()
    {
        $http_transport = $this->get_http_transport_mock();
        $http_transport->expects($this->once())
                       ->method('get')
                       ->with($this->identicalTo('https://api.pwnedpasswords.com/range/5BAA6'), $this->identicalTo(array('timeout' => 2,'user-agent' => 'PasswordsEvolvedPlugin/version_number')))
                       ->willReturn(array());

        $client = new HIBPClient($http_transport, 'version_number');

        $error = $client->is_password_compromised('password');

        $this->assertInstanceOf(TranslatableError::class, $error);
        $this->assertEquals('response.no_status_code', $error->get_error_code());
    }

    public function test_is_password_compromised_with_invalid_response()
    {
        $http_transport = $this->get_http_transport_mock();
        $http_transport->expects($this->once())
                       ->method('get')
                       ->with($this->identicalTo('https://api.pwnedpasswords.com/range/5BAA6'), $this->identicalTo(array('timeout' => 2,'user-agent' => 'PasswordsEvolvedPlugin/version_number')))
                       ->willReturn(array('response' => array('code' => 400)));

        $client = new HIBPClient($http_transport, 'version_number');

        $error = $client->is_password_compromised('password');

        $this->assertInstanceOf(TranslatableError::class, $error);
        $this->assertEquals('response.invalid', $error->get_error_code());
        $this->assertEquals(array('status_code' => 400), $error->get_error_data());
    }

    public function test_is_password_compromised_with_empty_body()
    {
        $http_transport = $this->get_http_transport_mock();
        $http_transport->expects($this->once())
                       ->method('get')
                       ->with($this->identicalTo('https://api.pwnedpasswords.com/range/5BAA6'), $this->identicalTo(array('timeout' => 2,'user-agent' => 'PasswordsEvolvedPlugin/version_number')))
                       ->willReturn(array('response' => array('code' => 200)));

        $client = new HIBPClient($http_transport, 'version_number');

        $error = $client->is_password_compromised('password');

        $this->assertInstanceOf(TranslatableError::class, $error);
        $this->assertEquals('response.empty_body', $error->get_error_code());
    }

    /**
     * Creates a mock of WP_Error object.
     */
    private function get_error_mock()
    {
        return $this->getMockBuilder(\WP_Error::class)->getMock();
    }

    /**
     * Creates a mock of WP_Http object.
     */
    private function get_http_transport_mock()
    {
        return $this->getMockBuilder(\WP_Http::class)->getMock();
    }
}
