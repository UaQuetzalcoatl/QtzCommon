<?php

namespace QtzCommonTest;

use PHPUnit_Framework_TestCase;
use Zend\Http;
use QtzCommon\Validator\RemoteContentExists;

/**
 * Description of RemoteContentExistsTest
 *
 * @author alex
 */
class RemoteContentExistsTest extends PHPUnit_Framework_TestCase
{
    public function testCanUseDefaultHttpClient()
    {
        $validator = new RemoteContentExists;
        $this->assertInstanceOf('\Zend\Http\Client', $validator->getHttpClient());
    }

    public function testCanAcceptHttpClientInstance()
    {
        $client = new Http\Client;
        $validator = new RemoteContentExists($client);
        $this->assertEquals($client, $validator->getHttpClient());
    }

    public function testCanAcceptArrayOptions()
    {
        $client = new Http\Client;
        $validator = new RemoteContentExists(array('client' => $client));
        $this->assertEquals($client, $validator->getHttpClient());
    }

    public function testInvalidUrl()
    {
        $client = new Http\Client;
        $adapter = new Http\Client\Adapter\Test;
        $client->setAdapter($adapter);
        $validator = new RemoteContentExists($client);

        $adapter->setResponse(
            "HTTP/1.1 404 Not Found"  . "\r\n" .
            "Content-type: text/html" . "\r\n" .
            "\r\n"
        );

        $this->assertFalse($validator->isValid('someuri.com'));
    }

    public function testInvalidUrlWithException()
    {
        $client = new Http\Client;
        $adapter = new Http\Client\Adapter\Test;
        $adapter->setNextRequestWillFail(true);
        $client->setAdapter($adapter);
        $validator = new RemoteContentExists($client);
        $result = $validator->isValid('someuri.com');
        $errors = $validator->getMessages();
        $templates = $validator->getMessageTemplates();

        $this->assertFalse($result);
        $this->assertEquals($templates[RemoteContentExists::ERROR_INVALID_URL], array_pop($errors));
    }

    public function testValidUrl()
    {
        $client = new Http\Client;
        $adapter = new Http\Client\Adapter\Test;
        $client->setAdapter($adapter);
        $validator = new RemoteContentExists($client);

        $adapter->setResponse(
            "HTTP/1.1 200 OK"        . "\r\n" .
            "Content-type: text/html" . "\r\n" .
            "\r\n"
        );

        $this->assertTrue($validator->isValid('somehost.com'));
    }

    public function testRealUrl()
    {
        $validator = new RemoteContentExists;
        $this->assertTrue($validator->isValid('http://google.com'));
    }
}
