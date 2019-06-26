<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi;

use Frontastic\Common\ContentApiBundle\Domain\ContentApi\GraphCMS\Client;

use Frontastic\Common\ContentApiBundle\Domain\ContentApi;
use Frontastic\Common\ContentApiBundle\Domain\ContentType;
use Frontastic\Common\ContentApiBundle\Domain\Category;
use Frontastic\Common\ContentApiBundle\Domain\Query;
use Frontastic\Common\ContentApiBundle\Domain\Result;

class GraphCMS implements ContentApi
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getContentTypes(): array
    {
        return [];
    }

    public function getContent(string $contentId): Content
    {
        $json = $this->client->query($contentId);
        $content = new Content(['contentId' => $contentId, 'name' => null, 'attributes' => [], 'dangerousInnerContent' => $json]);
        return $content;
    }

    public function query(Query $query): Result
    {
        /*
        return new Result([
            'total' => $result->getTotal(),
            'count' => $result->getLimit(),
            'offset' => $result->getSkip(),
            'items' => array_map(
                [$this, 'convertContent'],
                $result->getItems()
            ),
        ]);
        */
        return "not implemented";
    }

    public function getDangerousInnerClient()
    {
        return $this->client;
    }
}
