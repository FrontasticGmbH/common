<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi;

use Frontastic\Common\ContentApiBundle\Domain\ContentApi\GraphCMS\Client;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi\GraphCMS\Inflector;

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
        return array_map(
            function ($e) {
                $c = new ContentType();
                $c->contentTypeId = $e;
                $c->name = $e;
                return $c;
            },
            $this->client->getContentTypes()
        );
    }

    public function getContent(string $contentId): Content
    {
        // query only by id does not work, GraphCMS always needs a contentType, too
        throw new \RuntimeException("getting content by ID is not supported by GraphCMS");
    }

    public function query(Query $query): Result
    {
        if ($query->contentType && $query->query) {
            // query by contentType and contentId
            $json = $this->client->get($query->contentType, $query->query);
            $name = lcfirst($query->contentType);

            $data = json_decode($json, true);

            $attributes = $data['data'][$name];
            if ($attributes === null) {
                $contents = [];
            } else {
                $content = new Content([
                    'contentId' => $attributes['id'],
                    'name' => array_keys($data['data'])[0],
                    'attributes' => $attributes,
                    'dangerousInnerContent' => $json
                ]);
                $contents = [$content];
            }
        } elseif ($query->contentType && ($query->query === null || trim($query->query) === '')) {
            // query by contentType and where filter (AttributeFilter)
            $json = $this->client->getAll($query->contentType);
            $name = lcfirst(Inflector::pluralize($query->contentType));
            $data = json_decode($json, true);
            $contents = array_map(
                function ($e) use ($name) {
                    return new Content([
                        'contentId' => $e['id'],
                        'name' => $name,
                        'attributes' => $e,
                        'dangerousInnerContent' => $e
                    ]);
                },
                $data['data'][$name]
            );
        } else {
            throw new \InvalidArgumentException('provide a ContentType or a ContentType and a ContentID (in the Text field)');
        }
        return new Result([
            'total' => count($contents),
            'count' => count($contents),
            'offset' => 0,
            'items' => $contents
        ]);
    }

    public function getDangerousInnerClient()
    {
        return $this->client;
    }
}
