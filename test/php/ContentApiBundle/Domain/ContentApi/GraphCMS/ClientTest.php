<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi\GraphCMS;

use Frontastic\Common\ContentApiBundle\Domain\ContentApi\Attribute;
use Frontastic\Common\HttpClient\Guzzle;

/**
 * @group integration
 */
class ClientTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Client
     */
    private $client;

    public function setup()
    {
        $apiToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ2ZXJzaW9uIjoxLCJ0b2tlbklkIjoiMDFmNDQyNDEtNTk1ZC00OWQ2LWJjOGQtM2E2NTRjZTQ3MjVjIn0.wdAXo_KwbFDqAah7B-OW_a5SaN7M7RxFHQx8pC675r8";
        $projectId = "cjxabf0100d2101eduvwa3in4";
        $region = "euwest";
        $stage = "master";
        $this->client = new Client($projectId, $apiToken, $region, $stage, new Guzzle());
    }

    public function testQuery()
    {
        $result = $this->client->query("query { ingredients(where: {price: 3}) { id, name, price } }");
        $expected = '{"data":{"ingredients":[{"id":"cjxac52hychgy0910j313jdyq","name":"Mehl","price":3},{"id":"cjxac5jtlcce70941sa2y768j","name":"Zucker","price":3}]}}';
        $this->assertEquals($expected, $result);
    }

    public function testQueryWithQuotes()
    {
        $result = $this->client->query("query { ingredients(where: {name: \"Mehl\"}) { id, name, price } }");
        $expected = '{"data":{"ingredients":[{"id":"cjxac52hychgy0910j313jdyq","name":"Mehl","price":3}]}}';
        $this->assertEquals($expected, $result);
    }

    public function testGetAttributes()
    {
        $result = $this->client->getAttributes('Ingredient');

        $this->assertEquals(
            ['status', 'updatedAt', 'createdAt', 'id', 'recipes', 'name', 'description', 'season', 'price', 'image'],
            array_map(
                function ($e) {
                    return $e['name'];
                },
                $result
            )
        );
    }

    public function testGet()
    {
        $result = $this->client->get('Ingredient', 'cjxac52hychgy0910j313jdyq');
        $this->assertEquals(
            '{"data":{"ingredient":{"status":"PUBLISHED","updatedAt":"2019-07-09T10:01:19.803Z","createdAt":"2019-06-24T12:06:28.582Z","id":"cjxac52hychgy0910j313jdyq","name":"Mehl","description":null,"season":["Winter","Fall"],"price":3,"recipes":[{"id":"cjxac6bziccie0941viedjs7x"}],"image":{"handle":"BA4Ao48KQHOiNukP58n1"}}}}',
            $result->queryResultJson
        );
        $this->assertEquals(
            [
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
            $result->attributes
        );
    }

    public function testGetContentTypes()
    {
        $result = $this->client->getContentTypes();
        $this->assertEquals(
            [
                'Asset',
                'Step',
                'Recipe',
                'Ingredient',
                'Category',
                'Cuisine'
            ],
            $result
        );
    }

    public function testGetAll()
    {
        $result = $this->client->getAll('Step');
        $this->assertEquals(
            '{"data":{"steps":[]}}',
            $result->queryResultJson
        );
        $this->assertEquals(
            [
                new Attribute(['attributeId' => 'status', 'type' => 'Status']),
                new Attribute(['attributeId' => 'updatedAt', 'type' => 'DateTime']),
                new Attribute(['attributeId' => 'createdAt', 'type' => 'DateTime']),
                new Attribute(['attributeId' => 'id', 'type' => 'ID']),
                new Attribute(['attributeId' => 'recipe', 'type' => 'Recipe']),
                new Attribute(['attributeId' => 'description', 'type' => 'String']),
                new Attribute(['attributeId' => 'images', 'type' => 'LIST']),
            ],
            $result->attributes
        );
    }

    public function testSearchAll()
    {
        $result = $this->client->search('er');

        $expectedResult = '{"data":{"assets":[{"status":"PUBLISHED","updatedAt":"2019-07-09T10:00:58.397Z","createdAt":"2019-07-09T10:00:58.397Z","id":"cjxvn9g65add70941xz1y9jg2","handle":"BA4Ao48KQHOiNukP58n1","fileName":"Sciurus_niger_on_fence.jpg","height":337,"width":300,"size":28855,"mimeType":"image/jpeg","url":"https://media.graphcms.com/BA4Ao48KQHOiNukP58n1","imagesStep":[],"imagesRecipe":[],"imageIngredient":[{"id":"cjxac52hychgy0910j313jdyq"}]},{"status":"PUBLISHED","updatedAt":"2019-07-09T10:00:58.496Z","createdAt":"2019-07-09T10:00:58.496Z","id":"cjxvn9g8w2bkx0d53mir8lgoo","handle":"smUKZA7QryOjtD4JiZlm","fileName":"OtospermophilusVariegatusGrandCanyon.jpg","height":974,"width":1220,"size":133558,"mimeType":"image/jpeg","url":"https://media.graphcms.com/smUKZA7QryOjtD4JiZlm","imagesStep":[],"imagesRecipe":[],"imageIngredient":[]},{"status":"PUBLISHED","updatedAt":"2019-07-09T10:00:58.591Z","createdAt":"2019-07-09T10:00:58.591Z","id":"cjxvn9gbj2bl20d53d98m5skx","handle":"rkUJ8LTSU61CeQpooOBo","fileName":"Sciurus-vulgaris_hernandeangelis_stockholm_2008-06-04.jpg","height":1004,"width":1500,"size":249959,"mimeType":"image/jpeg","url":"https://media.graphcms.com/rkUJ8LTSU61CeQpooOBo","imagesStep":[],"imagesRecipe":[],"imageIngredient":[]}],"recipes":[],"ingredients":[{"status":"PUBLISHED","updatedAt":"2019-07-09T07:01:58.640Z","createdAt":"2019-06-24T12:06:44.389Z","id":"cjxac5ep1fppw0d53nbrhqvlm","name":"Butter","description":null,"season":["Winter"],"price":2,"recipes":[{"id":"cjxac6bziccie0941viedjs7x"}],"image":null},{"status":"PUBLISHED","updatedAt":"2019-07-09T07:02:03.591Z","createdAt":"2019-06-24T12:06:51.033Z","id":"cjxac5jtlcce70941sa2y768j","name":"Zucker","description":null,"season":[],"price":3,"recipes":[],"image":null}],"categories":[{"status":"PUBLISHED","updatedAt":"2019-07-29T10:52:28.754Z","createdAt":"2019-07-29T10:52:24.202Z","id":"cjyo9wmiy7gqz09410ga238r8","title":"Cracker","description":null,"recipes":[]}],"cuisines":[]}}';
        $this->assertEquals(
            $expectedResult,
            $result->queryResultJson
        );
        $this->assertEquals(
            29,
            count($result->attributes)
        );
    }

    public function testSearchOneContentType()
    {
        $result = $this->client->search('er', ['Ingredient']);

        $expectedResult = '{"data":{"ingredients":[{"status":"PUBLISHED","updatedAt":"2019-07-09T07:01:58.640Z","createdAt":"2019-06-24T12:06:44.389Z","id":"cjxac5ep1fppw0d53nbrhqvlm","name":"Butter","description":null,"season":["Winter"],"price":2,"recipes":[{"id":"cjxac6bziccie0941viedjs7x"}],"image":null},{"status":"PUBLISHED","updatedAt":"2019-07-09T07:02:03.591Z","createdAt":"2019-06-24T12:06:51.033Z","id":"cjxac5jtlcce70941sa2y768j","name":"Zucker","description":null,"season":[],"price":3,"recipes":[],"image":null}]}}';

        $this->assertEquals(
            $expectedResult,
            $result->queryResultJson
        );
        $this->assertEquals(
            10,
            count($result->attributes)
        );
    }
}
