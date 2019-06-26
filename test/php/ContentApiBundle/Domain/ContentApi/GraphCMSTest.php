<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi;

use Frontastic\Common\ContentApiBundle\Domain\ContentApi\GraphCMS\Client;

class GraphCMSTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Client|MockObject
     */
    private $clientMock;

    /**
     * @var GraphCMS
     */
    private $api;

    public function setup()
    {
        $this->clientMock = $this->createMock(Client::class);
        $this->api = new GraphCMS($this->clientMock);
    }

    public function testGetContent()
    {
        $contentId = "foobar";
        $this->clientMock->expects($this->once())->method('query')->with($contentId)->will($this->returnValue('{}'));
        $result = $this->api->getContent($contentId);
        $this->assertEquals(new Content(['contentId' => 'foobar']), $result);
    }
}
