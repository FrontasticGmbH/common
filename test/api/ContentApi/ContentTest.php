<?php

namespace Frontastic\Common\ApiTests\ContentApi;

use Frontastic\Common\ApiTests\FrontasticApiTestCase;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi\Attribute;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi\Content;
use Frontastic\Common\ContentApiBundle\Domain\ContentType;
use Frontastic\Common\ContentApiBundle\Domain\Query;
use Frontastic\Common\ContentApiBundle\Domain\Result;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use GuzzleHttp\Promise\PromiseInterface;

class ContentTest extends FrontasticApiTestCase
{
    /**
     * @dataProvider projectAndLanguageForContentApi
     */
    public function testGetContentTypes(Project $project, string $language)
    {
        $contentApi = $this->getContentApiForProject($project);

        $contentTypes = $contentApi->getContentTypes();

        $this->assertNotEmpty($contentTypes);
        $this->assertContainsOnlyInstancesOf(ContentType::class, $contentTypes);

        foreach ($contentTypes as $contentType) {
            $query = new Query(['contentType' => $contentType->contentTypeId]);
            $result = $contentApi->query($query, $language);

            $this->assertInstanceOf(Result::class, $result);
            $this->assertAreContentResultWellFormed($result, $query->contentType);
        }
    }

    /**
     * @dataProvider projectAndLanguageForContentApi
     */
    public function testGetContent(Project $project, string $language)
    {
        $contentApi = $this->getContentApiForProject($project);

        $query = new Query(['query' => 'a']);
        $result = $contentApi->query($query, $language, ContentApi::QUERY_SYNC);

        /** @var Content $contentItem */
        $contentItem = $result->items[0];

        $result = $contentApi->getContent($contentItem->contentId, $language);

        $this->assertInstanceOf(Content::class, $result);
        $this->assertSame($contentItem->contentId, $result->contentId);
    }

    /**
     * @dataProvider projectAndLanguageForContentApi
     */
    public function testQuerySyncReturnsResult(Project $project, string $language)
    {
        $contentApi = $this->getContentApiForProject($project);

        $query = new Query(['query' => 'a']);
        $result = $contentApi->query($query, $language, ContentApi::QUERY_SYNC);

        $this->assertInstanceOf(Result::class, $result);
        $this->assertContainsOnlyInstancesOf(Content::class, $result->items);
        $this->assertCount($result->count, $result->items);
    }

    /**
     * @dataProvider projectAndLanguageForContentApi
     */
    public function testQueryAsyncReturnsPromiseToResult(Project $project, string $language): void
    {
        $query = new Query(['query' => 'a']);
        $promise = $this
            ->getContentApiForProject($project)
            ->query($query, $language, ContentApi::QUERY_ASYNC);

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $result = $promise->wait();

        $this->assertInstanceOf(Result::class, $result);
        $this->assertAreContentResultWellFormed($result, null, $query->query);
    }

    /**
     * @dataProvider projectAndLanguageForContentApi
     */
    public function testQueryByContentTypeReturnsResult(Project $project, string $language)
    {
        $contentApi = $this->getContentApiForProject($project);

        $contentTypes = $contentApi->getContentTypes();
        $contentType = $contentTypes[0];

        $query = new Query(['contentType' => $contentType->contentTypeId]);
        $result = $contentApi->query($query, $language);

        $this->assertInstanceOf(Result::class, $result);
        $this->assertAreContentResultWellFormed($result, $query->contentType);
    }

    /**
     * @dataProvider projectAndLanguageForContentApi
     */
    public function testQueryByStringAndContentTypeReturnsResult(Project $project, string $language)
    {
        $contentApi = $this->getContentApiForProject($project);

        $contentTypes = $contentApi->getContentTypes();
        $contentType = $contentTypes[0];

        $query = new Query([
            'query' => 'a',
            'contentType' => $contentType->contentTypeId,
        ]);
        $result = $contentApi->query($query, $language);

        $this->assertInstanceOf(Result::class, $result);
        $this->assertAreContentResultWellFormed($result, $query->contentType, $query->query);
    }

    private function assertAreContentResultWellFormed(
        Result $result,
        string $contentTypeId = null,
        string $query = null
    ): void {
        $this->assertContainsOnlyInstancesOf(Content::class, $result->items);
        $this->assertCount($result->count, $result->items);

        /** @var Content $content */
        foreach ($result->items as $content) {
            $this->assertNotNull($content->contentId);
            $this->assertNotNull($content->contentTypeId);
            $this->assertNotNull($content->name);
            $this->assertContainsOnlyInstancesOf(Attribute::class, $content->attributes);

            if ($contentTypeId) {
                $this->assertSame($contentTypeId, $content->contentTypeId);
            }
            if ($query) {
                $this->assertThat($content->name, $this->stringContains($query));
            }
        }
    }
}
