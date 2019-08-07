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
        $parts = explode(':', $contentId);
        if (count($parts) != 2) {
            // query only by id does not work, GraphCMS always needs a contentType, too
            throw new \RuntimeException(
                "getting content by ID is not supported by GraphCMS, use '<contentId>:<contentType>' instead"
            );
        }
        list($contentId, $contentType) = $parts;

        $locale = $locale ?? $this->defaultLocale;
        $clientResult = $this->client->get($contentType, $contentId, $this->frontasticToGraphCmsLocale($locale));

        if (!$this->hasContent($clientResult, $contentType)) {
            throw new \RuntimeException(
                sprintf(
                    'No content found for id: %s and contentType: %s',
                    $contentId,
                    $contentType
                )
            );
        }

        $attributes = $this->getDataFromResult($clientResult, $contentType);

        return new Content([
            'contentId' => $this->generateContentId($attributes['id'], $contentType),
            'name' => $this->extractName($attributes),
            'attributes' => $this->fillAttributesWithData($clientResult->attributes, $attributes),
            'dangerousInnerContent' => $clientResult->queryResultJson
        ]);
    }

    public function query(Query $query, string $locale = null): Result
    {
        $locale = $locale ?? $this->defaultLocale;

        $contentTypeGiven = $query->contentType !== null && trim($query->contentType) !== '' ;
        $queryGiven = $query->query !== null && trim($query->query) !== '';

        if ($queryGiven && !$contentTypeGiven) {
            // query by search string
            $clientResult = $this->client->search(
                $query->query,
                [],
                $this->frontasticToGraphCmsLocale($locale)
            );
            $data = json_decode($clientResult->queryResultJson, true);
            $attributes = $clientResult->attributes;
            $contents = [];
            foreach ($data['data'] as $contentType => $items) {
                // contentType is in plural lowercase version here
                $contentsForContentType = array_map(
                    function ($e) use ($contentType, $clientResult, $query, $attributes) {
                        $contentId = $this->generateContentId(
                            $e['id'],
                            ucfirst(Inflector::singularize($contentType))
                        );
                        return new Content([
                            'contentId' => $contentId,
                            'name' => $this->extractName($e),
                            'attributes' => $this->fillAttributesWithData(
                                $attributes[$contentType],
                                $e
                            ),
                            'dangerousInnerContent' => $e
                        ]);
                    },
                    $items
                );
                $contents = array_merge($contents, $contentsForContentType);
            }
        } elseif ($queryGiven && $contentTypeGiven) {
            // query by contentType and search string
            $clientResult = $this->client->search(
                $query->query,
                [$query->contentType],
                $this->frontasticToGraphCmsLocale($locale)
            );

            $data = json_decode($clientResult->queryResultJson, true);
            $attributes = $clientResult->attributes;
            $contents = [];
            foreach ($data['data'] as $contentType => $items) {
                // contentType is in plural lowercase version here
                $contentsForContentType = array_map(
                    function ($e) use ($contentType, $clientResult, $query, $attributes) {
                        $contentId = $this->generateContentId(
                            $e['id'],
                            ucfirst(Inflector::singularize($contentType))
                        );
                        return new Content([
                            'contentId' => $contentId,
                            'name' => $this->extractName($e),
                            'attributes' => $this->fillAttributesWithData(
                                $attributes[$contentType],
                                $e
                            ),
                            'dangerousInnerContent' => $e
                        ]);
                    },
                    $items
                );
                $contents = array_merge($contents, $contentsForContentType);
            }
        } elseif (!$queryGiven && $contentTypeGiven) {
            // query by contentType
            $clientResult = $this->client->getAll($query->contentType, $this->frontasticToGraphCmsLocale($locale));

            $name = lcfirst(Inflector::pluralize($query->contentType));
            $data = json_decode($clientResult->queryResultJson, true);
            if (!isset($data['data'])) {
                throw new \InvalidArgumentException(
                    'invalid search parameters'
                );
            }
            $contents = array_map(
                function ($e) use ($clientResult, $query) {
                    return new Content([
                        'contentId' => $this->generateContentId($e['id'], $query->contentType),
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
                'provide a ContentType and/or a search text'
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
                    $attributeContent = $attributeContent['html'];
                }

                $attribute->content = $attributeContent;

                return $attribute;
            },
            $attributes
        );
    }

    private function generateContentId($id, $contentType): string
    {
        return $id . ':' . $contentType;
    }

    private function extractName(array $attributes): string
    {
        if (isset($attributes['name'])) {
            return $attributes['name'];
        }

        if (isset($attributes['title'])) {
            return $attributes['title'];
        }

        if (isset($attributes['fileName'])) {
            return $attributes['fileName'];
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

    private function getDataFromResult(ContentApi\GraphCMS\ClientResult $clientResult, $contentType): array
    {
        $name = lcfirst($contentType);

        $data = json_decode($clientResult->queryResultJson, true);

        if ($data === false) {
            return [];
        }

        return $data['data'][$name];
    }

    private function hasContent(ContentApi\GraphCMS\ClientResult $clientResult, $contentType): bool
    {
        return $this->getDataFromResult($clientResult, $contentType) !== null;
    }
}
