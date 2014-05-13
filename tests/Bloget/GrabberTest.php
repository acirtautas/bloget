<?php

namespace tests\blogger;

use Bloget\Grabber;

class GrabberTest extends \PHPUnit_Framework_TestCase
{
    private $grabber;

    public function setUp()
    {
        $client = new \Goutte\Client;
        $this->grabber = new Grabber($client);
    }

    public function testGetClient()
    {
        $this->assertInstanceOf('\Goutte\Client', $this->grabber->getClient());
    }
}
