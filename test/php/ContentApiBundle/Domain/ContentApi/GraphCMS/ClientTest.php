<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi\GraphCMS;

use Doctrine\Common\Cache\ApcuCache;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi\Attribute;
use Frontastic\Common\HttpClient\Guzzle;
use GuzzleHttp\Promise;

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
        $this->client = new Client($projectId, $apiToken, $region, $stage, new Guzzle(), new ApcuCache());
    }

    public function testQuery()
    {
        $result = $this->client->query("query { ingredients(where: {price: 3}) { id, name, price } }")->wait();
        $expected = '{"data":{"ingredients":[{"id":"cjxac52hychgy0910j313jdyq","name":"Mehl","price":3},{"id":"cjxac5jtlcce70941sa2y768j","name":"Zucker","price":3}]}}';
        $this->assertEquals($expected, $result);
    }

    public function testQueryWithQuotes()
    {
        $result = $this->client->query("query { ingredients(where: {name: \"Mehl\"}) { id, name, price } }")->wait();
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
        $result = $this->client->get('Ingredient', 'cjxac52hychgy0910j313jdyq')->wait();
        $this->assertEquals(
            file_get_contents(__DIR__ . '/get.expected'),
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
        $result = $this->client->getAll('Step')->wait();
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
        $result = $this->client->search('er')->wait();

        $expectedResult = file_get_contents(__DIR__ . '/search-all.expected');
        $this->assertEquals(
            $expectedResult,
            $result->queryResultJson
        );
        $this->assertEquals(
            6,
            count($result->attributes)
        );
        $this->assertEquals(
            [
                new Attribute(['attributeId' => 'status', 'type' => 'Status']),
                new Attribute(['attributeId' => 'updatedAt', 'type' => 'DateTime']),
                new Attribute([ 'attributeId' => 'createdAt', 'type' => 'DateTime']),
                new Attribute([ 'attributeId' => 'id', 'type' => 'ID']),
                new Attribute(['attributeId' => 'title', 'type' => 'String']),
                new Attribute(['attributeId' => 'description', 'type' => 'String']),
                new Attribute(['attributeId' => 'ingredients', 'type' => 'LIST']),
                new Attribute(['attributeId' => 'prepTime', 'type' => 'Int']),
                new Attribute(['attributeId' => 'cookTime', 'type' => 'Int']),
                new Attribute(['attributeId' => 'steps', 'type' => 'LIST']),
                new Attribute(['attributeId' => 'categories', 'type' => 'LIST']),
                new Attribute(['attributeId' => 'cuisine', 'type' => 'LIST']),
                new Attribute(['attributeId' => 'images', 'type' => 'LIST']),
            ],
            $result->attributes['recipes']
        );
    }

    public function testSearchOneContentType()
    {
        $result = $this->client->search('er', ['Ingredient'])->wait();

        $expectedResult = file_get_contents(__DIR__ . '/search-one-contenttype.expected');

        $this->assertEquals(
            $expectedResult,
            $result->queryResultJson
        );
        $this->assertEquals(
            10,
            count($result->attributes['ingredients'])
        );
    }
}
