<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi\GraphCMS;

use Frontastic\Common\HttpClient\Guzzle;

class ClientTest extends \PHPUnit\Framework\TestCase
{
    private $client;

    public function setup()
    {
        $apiToken = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ2ZXJzaW9uIjoxLCJ0b2tlbklkIjoiMDFmNDQyNDEtNTk1ZC00OWQ2LWJjOGQtM2E2NTRjZTQ3MjVjIn0.wdAXo_KwbFDqAah7B-OW_a5SaN7M7RxFHQx8pC675r8";
        $projectId = "cjxabf0100d2101eduvwa3in4";
        $this->client = new Client($projectId, $apiToken, new Guzzle());
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
            $result
        );
    }
}
