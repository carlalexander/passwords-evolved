<?php

/*
 * This file is part of the Passwords Evolved WordPress plugin.
 *
 * (c) Carl Alexander <contact@carlalexander.ca>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PasswordsEvolved\Tests\API;

use PasswordsEvolved\API\HIBPClient;
use phpmock\phpunit\PHPMock;

class HIBPClientTest extends \PHPUnit_Framework_TestCase
{
    use PHPMock;

    /**
     * @var HIBPClient
     */
    private $client;

    protected function setUp()
    {
        $this->client = new HIBPClient('version_number');
    }

    protected function tearDown()
    {
        $this->client = null;
    }

    public function test_is_api_active()
    {
        $handle = new \stdClass();

        $curl_init = $this->getFunctionMock('PasswordsEvolved\API', 'curl_init');
        $curl_init->expects($this->exactly(2))
            ->with($this->equalTo('https://haveibeenpwned.com/api/v2/pwnedpassword/a94a8fe5ccb19ba61c4c0873d391e987982fbbd3'))
            ->willReturn($handle);

        $curl_setopt = $this->getFunctionMock('PasswordsEvolved\API', 'curl_setopt');
        $curl_setopt->expects($this->exactly(8))
            ->withConsecutive(
                array($this->identicalTo($handle), $this->equalTo(CURLOPT_TIMEOUT), $this->equalTo(2)),
                array($this->identicalTo($handle), $this->equalTo(CURLOPT_HEADER), $this->identicalTo(false)),
                array($this->identicalTo($handle), $this->equalTo(CURLOPT_RETURNTRANSFER), $this->identicalTo(true)),
                array($this->identicalTo($handle), $this->equalTo(CURLOPT_USERAGENT), $this->equalTo('PasswordsEvolvedPlugin/version_number'))
            );

        $curl_exec = $this->getFunctionMock('PasswordsEvolved\API', 'curl_exec');
        $curl_exec->expects($this->exactly(2))
            ->with($this->identicalTo($handle));

        $curl_getinfo = $this->getFunctionMock('PasswordsEvolved\API', 'curl_getinfo');
        $curl_getinfo->expects($this->exactly(2))
            ->with($this->identicalTo($handle), $this->equalTo(CURLINFO_HTTP_CODE))
            ->willReturnOnConsecutiveCalls(200, 503);

        $curl_close = $this->getFunctionMock('PasswordsEvolved\API', 'curl_close');
        $curl_close->expects($this->exactly(2))
            ->with($this->identicalTo($handle));

        $this->assertTrue($this->client->is_api_active());
        $this->assertFalse($this->client->is_api_active());
    }

    public function test_is_password_compromised()
    {
        $handle = new \stdClass();

        $curl_init = $this->getFunctionMock('PasswordsEvolved\API', 'curl_init');
        $curl_init->expects($this->exactly(4))
                  ->withConsecutive(
                      array($this->equalTo('https://haveibeenpwned.com/api/v2/pwnedpassword/0beec7b5ea3f0fdbc95d0dd47f3c5bc275da8a33')),
                      array($this->equalTo('https://haveibeenpwned.com/api/v2/pwnedpassword/foo')),
                      array($this->equalTo('https://haveibeenpwned.com/api/v2/pwnedpassword/62cdb7020ff920e5aa642c3d4066950dd1f01f4d')),
                      array($this->equalTo('https://haveibeenpwned.com/api/v2/pwnedpassword/bar'))
                  )
                  ->willReturn($handle);

        $curl_setopt = $this->getFunctionMock('PasswordsEvolved\API', 'curl_setopt');
        $curl_setopt->expects($this->exactly(16))
                    ->withConsecutive(
                        array($this->identicalTo($handle), $this->equalTo(CURLOPT_TIMEOUT), $this->equalTo(2)),
                        array($this->identicalTo($handle), $this->equalTo(CURLOPT_HEADER), $this->identicalTo(false)),
                        array($this->identicalTo($handle), $this->equalTo(CURLOPT_RETURNTRANSFER), $this->identicalTo(true)),
                        array($this->identicalTo($handle), $this->equalTo(CURLOPT_USERAGENT), $this->equalTo('PasswordsEvolvedPlugin/version_number'))
                    );

        $curl_exec = $this->getFunctionMock('PasswordsEvolved\API', 'curl_exec');
        $curl_exec->expects($this->exactly(4))
                  ->with($this->identicalTo($handle));

        $curl_getinfo = $this->getFunctionMock('PasswordsEvolved\API', 'curl_getinfo');
        $curl_getinfo->expects($this->exactly(4))
                     ->with($this->identicalTo($handle), $this->equalTo(CURLINFO_HTTP_CODE))
                     ->willReturnOnConsecutiveCalls(404, 404, 200, 200);

        $curl_close = $this->getFunctionMock('PasswordsEvolved\API', 'curl_close');
        $curl_close->expects($this->exactly(4))
                   ->with($this->identicalTo($handle));

        $this->assertFalse($this->client->is_password_compromised('foo'));
        $this->assertFalse($this->client->is_password_compromised('foo', false));
        $this->assertTrue($this->client->is_password_compromised('bar'));
        $this->assertTrue($this->client->is_password_compromised('bar', false));
    }

    public function test_is_password_compromised_error()
    {
        $handle = new \stdClass();

        $curl_init = $this->getFunctionMock('PasswordsEvolved\API', 'curl_init');
        $curl_init->expects($this->once())
            ->with($this->equalTo('https://haveibeenpwned.com/api/v2/pwnedpassword/8843d7f92416211de9ebb963ff4ce28125932878'))
            ->willReturn($handle);

        $curl_setopt = $this->getFunctionMock('PasswordsEvolved\API', 'curl_setopt');
        $curl_setopt->expects($this->exactly(4))
            ->withConsecutive(
                array($this->identicalTo($handle), $this->equalTo(CURLOPT_TIMEOUT), $this->equalTo(2)),
                array($this->identicalTo($handle), $this->equalTo(CURLOPT_HEADER), $this->identicalTo(false)),
                array($this->identicalTo($handle), $this->equalTo(CURLOPT_RETURNTRANSFER), $this->identicalTo(true)),
                array($this->identicalTo($handle), $this->equalTo(CURLOPT_USERAGENT), $this->equalTo('PasswordsEvolvedPlugin/version_number'))
            );

        $curl_exec = $this->getFunctionMock('PasswordsEvolved\API', 'curl_exec');
        $curl_exec->expects($this->once())
            ->with($this->identicalTo($handle));

        $curl_getinfo = $this->getFunctionMock('PasswordsEvolved\API', 'curl_getinfo');
        $curl_getinfo->expects($this->once())
            ->with($this->identicalTo($handle), $this->equalTo(CURLINFO_HTTP_CODE))
            ->willReturn(400);

        $curl_close = $this->getFunctionMock('PasswordsEvolved\API', 'curl_close');
        $curl_close->expects($this->once())
            ->with($this->identicalTo($handle));

        $error = $this->client->is_password_compromised('foobar');

        $this->assertInstanceOf('PasswordsEvolved\Error\TranslatableError', $error);
        $this->assertEquals('response.invalid', $error->get_error_code());
        $this->assertEquals(array('status_code' => 400), $error->get_error_data());
    }

    public function test_is_password_compromised_rate_limit()
    {
        $handle = new \stdClass();

        $curl_init = $this->getFunctionMock('PasswordsEvolved\API', 'curl_init');
        $curl_init->expects($this->exactly(2))
                  ->with($this->equalTo('https://haveibeenpwned.com/api/v2/pwnedpassword/8843d7f92416211de9ebb963ff4ce28125932878'))
                  ->willReturn($handle);

        $curl_setopt = $this->getFunctionMock('PasswordsEvolved\API', 'curl_setopt');
        $curl_setopt->expects($this->exactly(8))
                    ->withConsecutive(
                        array($this->identicalTo($handle), $this->equalTo(CURLOPT_TIMEOUT), $this->equalTo(2)),
                        array($this->identicalTo($handle), $this->equalTo(CURLOPT_HEADER), $this->identicalTo(false)),
                        array($this->identicalTo($handle), $this->equalTo(CURLOPT_RETURNTRANSFER), $this->identicalTo(true)),
                        array($this->identicalTo($handle), $this->equalTo(CURLOPT_USERAGENT), $this->equalTo('PasswordsEvolvedPlugin/version_number'))
                    );

        $curl_exec = $this->getFunctionMock('PasswordsEvolved\API', 'curl_exec');
        $curl_exec->expects($this->exactly(2))
            ->with($this->identicalTo($handle));

        $curl_getinfo = $this->getFunctionMock('PasswordsEvolved\API', 'curl_getinfo');
        $curl_getinfo->expects($this->exactly(2))
                     ->with($this->identicalTo($handle), $this->equalTo(CURLINFO_HTTP_CODE))
                     ->willReturnOnConsecutiveCalls(429, 200);

        $curl_close = $this->getFunctionMock('PasswordsEvolved\API', 'curl_close');
        $curl_close->expects($this->exactly(2))
                    ->with($this->identicalTo($handle));

        $sleep = $this->getFunctionMock('PasswordsEvolved\API', 'sleep');
        $sleep->expects($this->once())
              ->with($this->identicalTo(2));

        $this->assertTrue($this->client->is_password_compromised('foobar'));
    }
}