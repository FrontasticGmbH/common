<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi\LifecycleEventDecorator;

use Frontastic\Common\ContentApiBundle\Domain\ContentApi;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi\Content;
use Frontastic\Common\ContentApiBundle\Domain\ContentType;
use Frontastic\Common\ContentApiBundle\Domain\Query;
use Frontastic\Common\ContentApiBundle\Domain\Result;

class BaseImplementationAdapterV2 extends BaseImplementationV2
{
    /**
     * @var BaseImplementation
     */
    private $baseImplementation;

    public function __construct(BaseImplementation $baseImplementation)
    {
        $this->baseImplementation = $baseImplementation;
    }

    public function beforeGetContentTypes(ContentApi $contentApi): ?array
    {
        $this->baseImplementation->beforeGetContentTypes($contentApi);
        return null;
    }

    /**
     * @param ContentApi $contentApi
     * @param ContentType[] $contentTypes
     * @return ContentType[]|null
     */
    public function afterGetContentTypes(ContentApi $contentApi, array $contentTypes): ?array
    {
        return $this->baseImplementation->afterGetContentTypes($contentApi, $contentTypes);
    }

    public function beforeGetContent(
        ContentApi $contentApi,
        string $contentId,
        string $locale = null,
        string $mode = ContentApi::QUERY_SYNC
    ): ?array {
        $this->baseImplementation->beforeGetContent($contentApi, $contentId, $locale, $mode);
        return [$contentId, $locale, $mode];
    }

    public function afterGetContent(ContentApi $contentApi, ?Content $content): ?Content
    {
        return $this->baseImplementation->afterGetContent($contentApi, $content);
    }

    public function beforeQuery(
        ContentApi $contentApi,
        Query $query,
        string $locale = null,
        string $mode = ContentApi::QUERY_SYNC
    ): ?array {
        $this->baseImplementation->beforeQuery($contentApi, $query, $locale, $mode);
        return [$query, $locale, $mode];
    }

    public function afterQuery(ContentApi $contentApi, ?Result $result): ?Result
    {
        return $this->baseImplementation->afterQuery($contentApi, $result);
    }
}
