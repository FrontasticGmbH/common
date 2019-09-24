<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi;

use Frontastic\Common\ContentApiBundle\Domain\ContentApi;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi\GraphCMS\Client;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi\GraphCMS\ClientResult;
use Frontastic\Common\ContentApiBundle\Domain\Query;
use GuzzleHttp\Promise;
use PHPUnit\Framework\MockObject\MockObject;

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

    /**
     * @var string
     */
    private $locale;

    public function setup()
    {
        $this->locale = 'de_DE';
        $this->clientMock = $this->createMock(Client::class);
        $this->api = new GraphCMS($this->clientMock, $this->locale);
    }

    public function testQueryWithContentId()
    {
        $contentType =  'Ingredient';
        $contentId = 'cjxac52hychgy0910j313jdyq';
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

        $promise = Promise\promise_for($clientResult);

        $this->clientMock->expects($this->exactly(2))->method('get')->with($contentType, $contentId)->will($this->returnValue($promise));

        $combinedContentId = $contentId . ':' . $contentType;
        $expectedContent = new Content([
            'contentId' => $combinedContentId,
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
        ]);

        $result = $this->api->getContent($combinedContentId);
        $this->assertEquals($expectedContent, $result);

        $resultAsync = $this->api->getContent($combinedContentId, $this->locale, ContentApi::QUERY_ASYNC)->wait();
        $this->assertEquals($expectedContent, $resultAsync);
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

        $promise = Promise\promise_for($clientResult);

        $this->clientMock->expects($this->exactly(2))->method('getAll')->with($contentType)->will($this->returnValue($promise));

        $result = $this->api->query($query);
        $this->assertEquals([], $result->items);

        $resultAsync = $this->api->query($query, $this->locale, ContentApi::QUERY_ASYNC)->wait();
        $this->assertEquals([], $resultAsync->items);
    }

    public function testEmptyQuery()
    {
        $query = new Query();

        $this->expectException(\InvalidArgumentException::class);
        $result = $this->api->query($query);
    }

    public function testEmptyQueryAsync()
    {
        $query = new Query();

        $resultAsync = $this->api->query($query, $this->locale, ContentApi::QUERY_ASYNC);
        $this->assertInstanceOf(Promise\RejectedPromise::class, $resultAsync);
    }

    public function testSearchQuery()
    {
        $query = new Query(['query' => 'er']);
        // should find category "Cracker", ingredients "Butter", "Zucker" ...

        $jsonContent = '{"data":{"assets":[{"status":"PUBLISHED","updatedAt":"2019-07-09T10:00:58.397Z","createdAt":"2019-07-09T10:00:58.397Z","id":"cjxvn9g65add70941xz1y9jg2","handle":"BA4Ao48KQHOiNukP58n1","fileName":"Sciurus_niger_on_fence.jpg","height":337,"width":300,"size":28855,"mimeType":"image/jpeg","url":"https://media.graphcms.com/BA4Ao48KQHOiNukP58n1","imagesStep":[],"imagesRecipe":[],"imageIngredient":[{"id":"cjxac52hychgy0910j313jdyq"}]},{"status":"PUBLISHED","updatedAt":"2019-07-09T10:00:58.496Z","createdAt":"2019-07-09T10:00:58.496Z","id":"cjxvn9g8w2bkx0d53mir8lgoo","handle":"smUKZA7QryOjtD4JiZlm","fileName":"OtospermophilusVariegatusGrandCanyon.jpg","height":974,"width":1220,"size":133558,"mimeType":"image/jpeg","url":"https://media.graphcms.com/smUKZA7QryOjtD4JiZlm","imagesStep":[],"imagesRecipe":[],"imageIngredient":[]},{"status":"PUBLISHED","updatedAt":"2019-07-09T10:00:58.591Z","createdAt":"2019-07-09T10:00:58.591Z","id":"cjxvn9gbj2bl20d53d98m5skx","handle":"rkUJ8LTSU61CeQpooOBo","fileName":"Sciurus-vulgaris_hernandeangelis_stockholm_2008-06-04.jpg","height":1004,"width":1500,"size":249959,"mimeType":"image/jpeg","url":"https://media.graphcms.com/rkUJ8LTSU61CeQpooOBo","imagesStep":[],"imagesRecipe":[],"imageIngredient":[]}],"recipes":[],"ingredients":[{"status":"PUBLISHED","updatedAt":"2019-07-09T07:01:58.640Z","createdAt":"2019-06-24T12:06:44.389Z","id":"cjxac5ep1fppw0d53nbrhqvlm","name":"Butter","description":null,"season":["Winter"],"price":2,"recipes":[{"id":"cjxac6bziccie0941viedjs7x"}],"image":null},{"status":"PUBLISHED","updatedAt":"2019-07-09T07:02:03.591Z","createdAt":"2019-06-24T12:06:51.033Z","id":"cjxac5jtlcce70941sa2y768j","name":"Zucker","description":null,"season":[],"price":3,"recipes":[],"image":null}],"categories":[{"status":"PUBLISHED","updatedAt":"2019-07-29T10:52:28.754Z","createdAt":"2019-07-29T10:52:24.202Z","id":"cjyo9wmiy7gqz09410ga238r8","title":"Cracker","description":null,"recipes":[]}],"cuisines":[]}}';

        $clientResult = new ClientResult([
            'queryResultJson' => $jsonContent,
            'attributes' => [
                /* not tested here, see testAttributesOnSearchQuery */
                'assets' => [],
                'ingredients' => [],
                'categories' => []
            ],
        ]);

        $promise = Promise\promise_for($clientResult);

        $this->clientMock->expects($this->exactly(2))
            ->method('search')
            ->with('er', [], strtoupper($this->locale))
            ->will($this->returnValue($promise));

        $result = $this->api->query($query);
        $this->assertEquals(6, count($result->items));

        $resultAsync = $this->api->query($query, $this->locale, ContentApi::QUERY_ASYNC)->wait();
        $this->assertEquals(6, count($resultAsync->items));
    }

    public function testAttributesOnSearchQuery()
    {
        $query = new Query(['query' => 'er']);
        // should find category "Cracker", ingredients "Butter", "Zucker" ...

        // reduced content here
        $jsonContent = '{"data": {"assets": [], "recipes": [], "ingredients": [{"status": "PUBLISHED", "updatedAt": "2019-07-09T07:01:58.640Z", "createdAt": "2019-06-24T12:06:44.389Z", "id": "cjxac5ep1fppw0d53nbrhqvlm", "name": "Butter", "description": null, "season": ["Winter"], "price": 2, "recipes": [{"id": "cjxac6bziccie0941viedjs7x"}], "image": null}], "categories": [], "cuisines": []}}';

        $clientResult = new ClientResult([
            'queryResultJson' => $jsonContent,
            'attributes' => [
                'ingredients' => [
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
                ]
            ]
        ]);
        $promise = Promise\promise_for($clientResult);

        $this->clientMock->expects($this->exactly(2))->method('search')->with('er')->will($this->returnValue($promise));

        $expectedAttributes = [
            new Attribute(['attributeId' => 'status', 'type' => 'Status', 'content' => 'PUBLISHED']),
            new Attribute(['attributeId' => 'updatedAt', 'type' => 'DateTime', 'content' => "2019-07-09T07:01:58.640Z"]),
            new Attribute(['attributeId' => 'createdAt', 'type' => 'DateTime', 'content' => "2019-06-24T12:06:44.389Z"]),
            new Attribute(['attributeId' => 'id', 'type' => 'ID', 'content' => "cjxac5ep1fppw0d53nbrhqvlm"]),
            new Attribute(['attributeId' => 'recipes', 'type' => 'LIST', 'content' => [['id' => "cjxac6bziccie0941viedjs7x"]]]),
            new Attribute(['attributeId' => 'name', 'type' => 'String', 'content' => "Butter"]),
            new Attribute(['attributeId' => 'description', 'type' => 'String', 'content' => null]),
            new Attribute(['attributeId' => 'season', 'type' => 'LIST', 'content' => ["Winter"]]),
            new Attribute(['attributeId' => 'price', 'type' => 'Int', 'content' => 2]),
            new Attribute(['attributeId' => 'image', 'type' => 'Asset', 'content' => null]),
        ];

        $result = $this->api->query($query);
        $this->assertEquals(1, count($result->items));
        $this->assertEquals($expectedAttributes, $result->items[0]->attributes);

        $resultAsync = $this->api->query($query, $this->locale, ContentApi::QUERY_ASYNC)->wait();
        $this->assertEquals(1, count($resultAsync->items));
        $this->assertEquals($expectedAttributes, $resultAsync->items[0]->attributes);
    }
}
