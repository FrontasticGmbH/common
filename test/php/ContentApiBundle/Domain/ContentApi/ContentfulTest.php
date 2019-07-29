<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi;

use Frontastic\Common\ContentApiBundle\Domain;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * @group integration
 */
class ContentfulTest extends TestCase
{
    /**
     * @var Contentful
     */
    private $api;

    public function setUp()
    {
        $testAccessToken = 'lf7mvelTkEFNMXTzSKRev4NmdsOzeRVN8xXR6ayyJOk';
        $testSpaceId = 'cho9or523rqg';

        $client = new \Contentful\Delivery\Client($testAccessToken, $testSpaceId);
        $renderer = new \Contentful\RichText\Renderer();

        $this->api = new Contentful($client, $renderer, 'en_US');
    }

    public function testSimpleQueryAll()
    {
        // simply querying all blogPost types. There should be 3.
        $query = new Domain\Query([
            'contentType' => 'blogPost',
        ]);

        $result = $this->api->query($query);

        // checking if there are really 3 blog posts
        $this->assertSame(3, $result->total);
    }

    public function testSimpleQuerySome()
    {
        // simply querying some blogPost's fitting the given criteria
        $query = new Domain\Query([
            'contentType' => 'blogPost',
            'query' => 'Hello World',
        ]);

        $result = $this->api->query($query);

        $this->assertSame(1, $result->total);
    }

    public function testQueryByDepartmentAttribute()
    {
        $query = new Domain\Query([
            'contentType' => 'blogPostWithAttributes',
            'attributes' => [
                new Domain\AttributeFilter([
                    'name' => 'department',
                    'value' => 'software-engineering',
                ]),
            ],
        ]);

        $result = $this->api->query($query);

        $this->assertSame(1, $result->total);
    }

    public function testQueryByTagAttribute()
    {
        $query = new Domain\Query([
            'contentType' => 'blogPostWithAttributes',
            'attributes' => [
                new Domain\AttributeFilter([
                    'name' => 'tags',
                    'value' => 'general',
                ]),
            ]
        ]);

        $result = $this->api->query($query);

        $this->assertSame(2, $result->total);
    }

    public function testQueryByMultipleAttributes()
    {
        $query = new Domain\Query([
            'contentType' => 'blogPostWithAttributes',
            'attributes' => [
                new Domain\AttributeFilter([
                    'name' => 'department',
                    'value' => 'software-engineering',
                ]),
                new Domain\AttributeFilter([
                    'name' => 'tags',
                    'value' => 'general',
                ]),
            ]
        ]);

        $result = $this->api->query($query);

        $this->assertSame(1, $result->total);
    }
}
