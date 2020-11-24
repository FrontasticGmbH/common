<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi;

use Frontastic\Common\ContentApiBundle\Domain;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi\Contentful\LocaleMapper;
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

    public function setUp(): void
    {
        $testAccessToken = 'lf7mvelTkEFNMXTzSKRev4NmdsOzeRVN8xXR6ayyJOk';
        $testSpaceId = 'cho9or523rqg';
        $environmentId = 'master';

        $client = new \Contentful\Delivery\Client($testAccessToken, $testSpaceId, $environmentId);

        $renderer = new \Contentful\RichText\Renderer();

        $localeMapper = $this->createMock(LocaleMapper::class);

        $this->api = new Contentful($client, $renderer, $localeMapper, 'en_US');
    }

    public function testSimpleQueryAll()
    {
        // simply querying all blogPost types. There should be 3.
        $query = new Domain\Query([
            'contentType' => 'blogPost',
        ]);

        $result = $this->api->query($query);
        $asyncResult = $this->api->query($query, null, Domain\ContentApi::QUERY_ASYNC)->wait();

        // checking if there are really 3 blog posts
        $this->assertSame(3, $result->total);
        $this->assertSame(3, $asyncResult->total);
    }

    public function testSimpleQuerySome()
    {
        // simply querying some blogPost's fitting the given criteria
        $query = new Domain\Query([
            'contentType' => 'blogPost',
            'query' => 'Hello World',
        ]);

        $result = $this->api->query($query);
        $asyncResult = $this->api->query($query, null, Domain\ContentApi::QUERY_ASYNC)->wait();

        $this->assertSame(1, $result->total);
        $this->assertSame(1, $asyncResult->total);
    }

    // TODO: This test fails. Find out how to fetch by multiple ids
    public function testSimpleQueryMultipleIds()
    {
        $query = new Domain\Query([
            'contentType' => 'blogPost',
            'contentIds' => [
                '31TNnjHlfaGUoMOwU0M2og',
                '2PtC9h1YqIA6kaUaIsWEQ0', // Static sites are great
                'not there',
            ],
        ]);

        $result = $this->api->query($query);
        $this->assertSame(2, $result->total);
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
        $asyncResult = $this->api->query($query, null, Domain\ContentApi::QUERY_ASYNC)->wait();

        $this->assertSame(1, $result->total);
        $this->assertSame(1, $asyncResult->total);
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
            ],
        ]);

        $result = $this->api->query($query);
        $asyncResult = $this->api->query($query, null, Domain\ContentApi::QUERY_ASYNC)->wait();

        $this->assertSame(2, $result->total);
        $this->assertSame(2, $asyncResult->total);
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
            ],
        ]);

        $result = $this->api->query($query);
        $asyncResult = $this->api->query($query, null, Domain\ContentApi::QUERY_ASYNC)->wait();

        $this->assertSame(1, $result->total);
        $this->assertSame(1, $asyncResult->total);
    }
}
