<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi;

use Frontastic\Common\ContentApiBundle\Domain\ContentApi\GraphCMS\Client;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi\GraphCMS\ClientResult;
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
        $locale = 'de_DE';
        $this->clientMock = $this->createMock(Client::class);
        $this->api = new GraphCMS($this->clientMock, $locale);
    }

    public function testQueryWithContentId()
    {
        $contentType =  'Ingredient';
        $contentId = 'cjxac52hychgy0910j313jdyq';
        $query = new Query(['contentType' => $contentType, 'query' => $contentId]);
        $jsonContent = '{"data":{"ingredient":{"status":"PUBLISHED","updatedAt":"2019-07-09T10:01:19.803Z","createdAt":"2019-06-24T12:06:28.582Z","id":"cjxac52hychgy0910j313jdyq","name":"Mehl","description":null,"season":["Winter","Fall"],"price":3,"recipes":[{"id":"cjxac6bziccie0941viedjs7x"}],"image":{"handle":"BA4Ao48KQHOiNukP58n1"}}}}';
        $clientResult = new ClientResult([
            'queryResultJson' => $jsonContent,
            'attributes' => [
                new Attribute(['attributeId' => 'status', 'type' => 'Status']),
                new Attribute(['attributeId' => 'updatedAt', 'type' => 'DateTime']),
                new Attribute(['attributeId' => 'createdAt', 'type' => 'DateTime']),
                new Attribute(['attributeId' => 'id', 'type' => 'ID']),
                new Attribute(['attributeId' => 'recipes', 'type' => 'LIST']),
                new Attribute(['attributeId' => 'name', 'type' => 'String']),
                new Attribute(['attributeId' => 'description', 'type' => 'String']),
                new Attribute(['attributeId' => 'season', 'type' => 'LIST']),
                new Attribute(['attributeId' => 'price', 'type' => 'Int']),
                new Attribute(['attributeId' => 'image', 'type' => 'Asset']),
            ],
        ]);
        $this->clientMock->expects($this->once())->method('get')->with($contentType, $contentId)->will($this->returnValue($clientResult));

        $result = $this->api->query($query);
        $this->assertEquals([new Content([
            'contentId' => $contentId . ':' . $contentType,
            'name' => 'Mehl',
            'attributes' => [
                new Attribute(['attributeId' => 'status', 'type' => 'Status', 'content' => 'PUBLISHED']),
                new Attribute(['attributeId' => 'updatedAt', 'type' => 'DateTime', 'content' => '2019-07-09T10:01:19.803Z']),
                new Attribute(['attributeId' => 'createdAt', 'type' => 'DateTime', 'content' => '2019-06-24T12:06:28.582Z']),
                new Attribute(['attributeId' => 'id', 'type' => 'ID', 'content' => 'cjxac52hychgy0910j313jdyq']),
                new Attribute(['attributeId' => 'recipes', 'type' => 'LIST', 'content' => [['id' => 'cjxac6bziccie0941viedjs7x']]]),
                new Attribute(['attributeId' => 'name', 'type' => 'String', 'content' => 'Mehl']),
                new Attribute(['attributeId' => 'description', 'type' => 'String', 'content' => null]),
                new Attribute(['attributeId' => 'season', 'type' => 'LIST', 'content' => ['Winter', 'Fall']]),
                new Attribute(['attributeId' => 'price', 'type' => 'Int', 'content' => 3]),
                new Attribute(['attributeId' => 'image', 'type' => 'Asset', 'content' => ['handle' => 'BA4Ao48KQHOiNukP58n1']]),
            ],
            'dangerousInnerContent' => $jsonContent
        ])], $result->items);
    }


    public function testQueryAllContentsEmpty()
    {
        $contentType =  'Steps';
        $query = new Query(['contentType' => $contentType]);
        $jsonContent = '{"data":{"steps":[]}}';
        $clientResult = new ClientResult([
            'queryResultJson' => $jsonContent,
            'attributes' => [
                new Attribute(['attributeId' => 'status', 'type' => 'Status']),
                new Attribute(['attributeId' => 'updatedAt', 'type' => 'DateTime']),
                new Attribute(['attributeId' => 'createdAt', 'type' => 'DateTime']),
                new Attribute(['attributeId' => 'id', 'type' => 'ID']),
                new Attribute(['attributeId' => 'recipes', 'type' => 'Recipe']),
                new Attribute(['attributeId' => 'description', 'type' => 'String']),
                new Attribute(['attributeId' => 'images', 'type' => 'LIST']),
            ],
        ]);
        $this->clientMock->expects($this->once())->method('getAll')->with($contentType)->will($this->returnValue($clientResult));

        $result = $this->api->query($query);
        $this->assertEquals([], $result->items);
    }

    public function testEmptyQuery()
    {
        $query = new Query();
        $this->expectException(\InvalidArgumentException::class);
        $result = $this->api->query($query);
    }

    public function testSearchQuery()
    {
        $query = new Query(['query' => 'er']);
        // should find category "Cracker", ingredients "Butter", "Zucker" ...

        $jsonContent = '{"data":{"assets":[{"status":"PUBLISHED","updatedAt":"2019-07-09T10:00:58.397Z","createdAt":"2019-07-09T10:00:58.397Z","id":"cjxvn9g65add70941xz1y9jg2","handle":"BA4Ao48KQHOiNukP58n1","fileName":"Sciurus_niger_on_fence.jpg","height":337,"width":300,"size":28855,"mimeType":"image/jpeg","url":"https://media.graphcms.com/BA4Ao48KQHOiNukP58n1","imagesStep":[],"imagesRecipe":[],"imageIngredient":[{"id":"cjxac52hychgy0910j313jdyq"}]},{"status":"PUBLISHED","updatedAt":"2019-07-09T10:00:58.496Z","createdAt":"2019-07-09T10:00:58.496Z","id":"cjxvn9g8w2bkx0d53mir8lgoo","handle":"smUKZA7QryOjtD4JiZlm","fileName":"OtospermophilusVariegatusGrandCanyon.jpg","height":974,"width":1220,"size":133558,"mimeType":"image/jpeg","url":"https://media.graphcms.com/smUKZA7QryOjtD4JiZlm","imagesStep":[],"imagesRecipe":[],"imageIngredient":[]},{"status":"PUBLISHED","updatedAt":"2019-07-09T10:00:58.591Z","createdAt":"2019-07-09T10:00:58.591Z","id":"cjxvn9gbj2bl20d53d98m5skx","handle":"rkUJ8LTSU61CeQpooOBo","fileName":"Sciurus-vulgaris_hernandeangelis_stockholm_2008-06-04.jpg","height":1004,"width":1500,"size":249959,"mimeType":"image/jpeg","url":"https://media.graphcms.com/rkUJ8LTSU61CeQpooOBo","imagesStep":[],"imagesRecipe":[],"imageIngredient":[]}],"recipes":[],"ingredients":[{"status":"PUBLISHED","updatedAt":"2019-07-09T07:01:58.640Z","createdAt":"2019-06-24T12:06:44.389Z","id":"cjxac5ep1fppw0d53nbrhqvlm","name":"Butter","description":null,"season":["Winter"],"price":2,"recipes":[{"id":"cjxac6bziccie0941viedjs7x"}],"image":null},{"status":"PUBLISHED","updatedAt":"2019-07-09T07:02:03.591Z","createdAt":"2019-06-24T12:06:51.033Z","id":"cjxac5jtlcce70941sa2y768j","name":"Zucker","description":null,"season":[],"price":3,"recipes":[],"image":null}],"categories":[{"status":"PUBLISHED","updatedAt":"2019-07-29T10:52:28.754Z","createdAt":"2019-07-29T10:52:24.202Z","id":"cjyo9wmiy7gqz09410ga238r8","title":"Cracker","description":null,"recipes":[]}],"cuisines":[]}}';

        $clientResult = new ClientResult([
            'queryResultJson' => $jsonContent,
            'attributes' => [
                /* TODO
                new Attribute(['attributeId' => 'images', 'type' => 'LIST']),
                */
            ],
        ]);
        $this->clientMock->expects($this->once())->method('search')->with('er')->will($this->returnValue($clientResult));

        $result = $this->api->query($query);
        var_dump($result->items);
        $this->assertEquals([], $result->items);
    }
}
