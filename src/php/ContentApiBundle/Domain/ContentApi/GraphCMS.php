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
        // query only by id does not work, GraphCMS always needs a contentType, too
        throw new Exception("not supported");
    }

    public function query(Query $query): Result
    {
        // TODO convert query object to graphQL query, use contentType as entity type, query as contentId (if set) and attributes as where attributes, return all fields of entity type
        if ($query->contentType && $query->query) {
            // query by contentType and contentId
            $json = $this->client->get($query->contentType, $query->query);
        } elseif ($query->contentType && ($query->query === null || trim($query->query) === '')) {
            // query by contentType and where filter (AttributeFilter)
        }
        $data = json_decode($json, true);

        $content = new Content([
            'contentId' => $query->query,
            'name' => array_keys($data['data'])[0],
            'attributes' => $data['data'][lcfirst($query->contentType)],
            'dangerousInnerContent' => $json
        ]);
        return new Result([
            'total' => 1,
            'count' => 1,
            'offset' => 0,
            'items' => [$content]
        ]);
    }

    public function getDangerousInnerClient()
    {
        return $this->client;
    }
}
