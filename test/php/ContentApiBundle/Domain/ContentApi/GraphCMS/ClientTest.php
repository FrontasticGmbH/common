<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi\GraphCMS;

use Frontastic\Common\HttpClient\Stream;

class ClientTest extends \PHPUnit\Framework\TestCase
{
    private $client;

    public function setup()
    {
        $apiToken = "";
        $this->client = new Client($apiToken, new Stream());
    }

    public function testFoo()
    {
        $result = $this->client->query("foo");
        $this->assertEquals($result, "bar");
    }
}
