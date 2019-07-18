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

    /**
     * @var string
     */
    private $defaultLocale;

    public function __construct(Client $client, string $defaultLocale)
    {
        $this->client = $client;
        $this->defaultLocale = $defaultLocale;
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

    public function getContent(string $contentId, string $locale = null): Content
    {
        // query only by id does not work, GraphCMS always needs a contentType, too
        throw new \RuntimeException("getting content by ID is not supported by GraphCMS");
    }

    public function query(Query $query, string $locale = null): Result
    {
        $locale = $locale ?? $this->defaultLocale;

        if ($query->contentType && $query->query) {
            // query by contentType and contentId
            $json = $this->client->get($query->contentType, $query->query, $this->frontasticToGraphCmsLocale($locale));
            $name = lcfirst($query->contentType);

            $data = json_decode($json, true);

            $attributes = $data['data'][$name];
            if ($attributes === null) {
                $contents = [];
            } else {
                $content = new Content([
                    'contentId' => $attributes['id'],
                    'name' => isset($attributes['name']) ? $attributes['name'] : array_keys($data['data'])[0],
                    'attributes' => $attributes,
                    'dangerousInnerContent' => $json
                ]);
                $contents = [$content];
            }
        } elseif ($query->contentType && ($query->query === null || trim($query->query) === '')) {
            // query by contentType and where filter (AttributeFilter)
            $json = $this->client->getAll($query->contentType, $this->frontasticToGraphCmsLocale($locale));
            $name = lcfirst(Inflector::pluralize($query->contentType));
            $data = json_decode($json, true);
            $contents = array_map(
                function ($e) use ($name) {
                    return new Content([
                        'contentId' => $e['id'],
                        'name' => isset($e['name']) ? $e['name'] : $e['id'],
                        'attributes' => $e,
                        'dangerousInnerContent' => $e
                    ]);
                },
                $data['data'][$name]
            );
        } else {
            throw new \InvalidArgumentException(
                'provide a ContentType or a ContentType and a ContentID (in the Text field)'
            );
        }
        return new Result([
            'total' => count($contents),
            'count' => count($contents),
            'offset' => 0,
            'items' => $contents
        ]);
    }

    private function graphCmsToFrontasticLocale(string $graphCmsLocale): string
    {
        if (strpos($graphCmsLocale, '_') === false) {
            return $graphCmsLocale;
        }
        $parts = explode('_', $graphCmsLocale);
        if (count($parts) == 2) {
            $parts[1] = strtoupper($parts[1]);
            return implode('_', $parts);
        } else {
            throw new \InvalidArgumentException(
                'invalid formatted locale: '.$graphCmsLocale
            );
        }
    }

    private function frontasticToGraphCmsLocale(string $frontasticLocale): string
    {
        return strtoupper($frontasticLocale);
    }

    public function getDangerousInnerClient()
    {
        return $this->client;
    }
}
