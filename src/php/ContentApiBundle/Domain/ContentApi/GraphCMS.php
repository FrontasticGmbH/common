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
        list($contentId, $contentType) = explode(':', $contentId);
        if ($contentId === null || $contentType === null) {
            // query only by id does not work, GraphCMS always needs a contentType, too
            throw new \RuntimeException("getting content by ID is not supported by GraphCMS, use '<contentId>:<contentType>' instead");
        }

        $clientResult = $this->client->get($contentType, $contentId, $this->frontasticToGraphCmsLocale($locale));

        $name = lcfirst($contentType);

        $data = json_decode($clientResult->queryResultJson, true);

        $attributes = $data['data'][$name];

        return new Content([
            'contentId' => $attributes['id'],
            'name' => $this->extractName($attributes),
            'attributes' => $this->fillAttributesWithData($clientResult->attributes, $attributes),
            'dangerousInnerContent' => $clientResult->queryResultJson
        ]);
    }

    public function query(Query $query, string $locale = null): Result
    {
        $locale = $locale ?? $this->defaultLocale;

        if ($query->contentType && $query->query) {
            // query by contentType and contentId
            $clientResult = $this->client->get($query->contentType, $query->query, $this->frontasticToGraphCmsLocale($locale));

            $name = lcfirst($query->contentType);

            $data = json_decode($clientResult->queryResultJson, true);

            $attributes = $data['data'][$name];
            if ($attributes === null) {
                $contents = [];
            } else {
                $content = new Content([
                    'contentId' => $attributes['id'],
                    'name' => $this->extractName($attributes),
                    'attributes' => $this->fillAttributesWithData($clientResult->attributes, $attributes),
                    'dangerousInnerContent' => $clientResult->queryResultJson
                ]);
                $contents = [$content];
            }
        } elseif ($query->contentType && ($query->query === null || trim($query->query) === '')) {
            // query by contentType and where filter (AttributeFilter)
            $clientResult = $this->client->getAll($query->contentType, $this->frontasticToGraphCmsLocale($locale));

            $name = lcfirst(Inflector::pluralize($query->contentType));
            $data = json_decode($clientResult->queryResultJson, true);
            $contents = array_map(
                function ($e) use ($name, $clientResult) {
                    return new Content([
                        'contentId' => $e['id'],
                        'name' => $this->extractName($e),
                        'attributes' => $this->fillAttributesWithData(
                            $clientResult->attributes,
                            $e
                        ),
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

    /**
     * @param Attribute[] $attributes
     * @param array $fields
     * @return Attribute[]
     */
    private function fillAttributesWithData(array $attributes, array $fields): array
    {
        return array_map(
            function (Attribute $attribute) use ($fields): Attribute {
                $attributeContent = $fields[(string) $attribute->attributeId];
                if ($attribute->type === 'Text') {
                    $attributeContent = $attributeContent['markdown'];
                }

                $attribute->content = $attributeContent;

                return $attribute;
            },
            $attributes
        );
    }

    private function extractName(array $attributes): string
    {
        if (isset($attributes['name'])) {
            return $attributes['name'];
        }

        if (isset($attributes['title'])) {
            return $attributes['title'];
        }

        return $attributes['id'];
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
