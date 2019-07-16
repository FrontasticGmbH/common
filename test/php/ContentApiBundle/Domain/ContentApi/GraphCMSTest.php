<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi;

use Frontastic\Common\ContentApiBundle\Domain\ContentApi\GraphCMS\Client;
use Frontastic\Common\ContentApiBundle\Domain\Query;

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

    public function testQueryWithContentId()
    {
        $contentType =  'Ingredient';
        $contentId = 'cjxac52hychgy0910j313jdyq';
        $query = new Query(['contentType' => $contentType, 'query' => $contentId]);
        $jsonContent = '{"data":{"ingredient":{"status":"PUBLISHED","updatedAt":"2019-07-09T10:01:19.803Z","createdAt":"2019-06-24T12:06:28.582Z","id":"cjxac52hychgy0910j313jdyq","name":"Mehl","description":null,"season":["Winter","Fall"],"price":3,"recipes":[{"id":"cjxac6bziccie0941viedjs7x"}],"image":{"handle":"BA4Ao48KQHOiNukP58n1"}}}}';
        $this->clientMock->expects($this->once())->method('get')->with($contentType, $contentId)->will($this->returnValue($jsonContent));

        $result = $this->api->query($query);
        $this->assertEquals([new Content([
            'contentId' => $contentId,
            'name' => 'Mehl',
            'attributes' => [
                'status' => 'PUBLISHED',
                'updatedAt' => '2019-07-09T10:01:19.803Z',
                'createdAt' => '2019-06-24T12:06:28.582Z',
                'id' => 'cjxac52hychgy0910j313jdyq',
                'name' => 'Mehl',
                'description' => null,
                'season' => [
                    'Winter',
                    'Fall'
                ],
                'price' => 3,
                'recipes' => [[
                    'id' => 'cjxac6bziccie0941viedjs7x'
                ]],
                'image' => [
                    'handle' => 'BA4Ao48KQHOiNukP58n1'
                ]
            ],
            'dangerousInnerContent' => $jsonContent
        ])], $result->items);
    }


    public function testQueryAllContentsEmpty()
    {
        $contentType =  'Steps';
        $query = new Query(['contentType' => $contentType]);
        $jsonContent = '{"data":{"steps":[]}}';
        $this->clientMock->expects($this->once())->method('getAll')->with($contentType)->will($this->returnValue($jsonContent));

        $result = $this->api->query($query);
        $this->assertEquals([], $result->items);
    }

    public function testEmptyQuery()
    {
        $query = new Query();
        $this->expectException(\InvalidArgumentException::class);
        $result = $this->api->query($query);
    }
}
