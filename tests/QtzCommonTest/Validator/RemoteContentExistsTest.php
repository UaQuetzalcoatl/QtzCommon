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
}
