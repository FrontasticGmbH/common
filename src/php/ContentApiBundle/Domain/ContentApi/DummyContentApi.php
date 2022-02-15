<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi;

use Frontastic\Common\ContentApiBundle\Domain\ContentApi\Content;
use GuzzleHttp\Promise\PromiseInterface;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi;
use Frontastic\Common\ContentApiBundle\Domain\Query;

class DummyContentApi implements ContentApi
{
    public function getContentTypes(): array
    {
        throw $this->exception();
    }

    public function getContent(string $contentId, string $locale = null, string $mode = self::QUERY_SYNC): ?object
    {
        throw $this->exception();
    }

    public function query(Query $query, string $locale = null, string $mode = self::QUERY_SYNC): ?object
    {
        throw $this->exception();
    }

    public function getDangerousInnerClient()
    {
        throw $this->exception();
    }

    private function exception(): \Throwable
    {
        return new \Exception("ContentApi is not available for Nextjs projects.");
    }
}
