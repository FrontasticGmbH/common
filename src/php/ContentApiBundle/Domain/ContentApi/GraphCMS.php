<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi;

use Frontastic\Common\ContentApiBundle\Domain\Category;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi\GraphCMS\Client;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi\GraphCMS\Inflector;
use Frontastic\Common\ContentApiBundle\Domain\ContentType;
use Frontastic\Common\ContentApiBundle\Domain\Query;
use Frontastic\Common\ContentApiBundle\Domain\Result;
use GuzzleHttp\Promise;
use GuzzleHttp\Promise\PromiseInterface;

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

    public function getContent(string $contentId, string $locale = null, string $mode = self::QUERY_SYNC): ?object
    {
        $parts = explode(':', $contentId);
        if (count($parts) != 2) {
            // query only by id does not work, GraphCMS always needs a contentType, too
            $exception = new \RuntimeException(
                "getting content by ID is not supported by GraphCMS, use '<contentId>:<contentType>' instead"
            );

            if ($mode === self::QUERY_SYNC) {
                throw $exception;
            }

            return Promise\rejection_for($exception);
        }
        list($contentId, $contentType) = $parts;

        $locale = $locale ?? $this->defaultLocale;
        $promise = $this->client
            ->get($contentType, $contentId, $this->frontasticToGraphCmsLocale($locale))
            ->then(function ($clientResult) use ($contentType, $contentId, $mode) {

                if (!$this->hasContent($clientResult, $contentType)) {
                    $exception = new \RuntimeException(
                        sprintf(
                            'No content found for id: "%s" and contentType: "%s"',
                            $contentId,
                            $contentType
                        )
                    );

                    if ($mode === self::QUERY_SYNC) {
                        throw $exception;
                    }
                    return Promise\rejection_for($exception);
                }

                $attributes = $this->getDataFromResult($clientResult, $contentType);

                if (empty($attributes)) {
                    $exception = new \RuntimeException(
                        sprintf(
                            'No content found for id: "%s" and contentType: "%s"',
                            $contentId,
                            $contentType
                        )
                    );

                    if ($mode === self::QUERY_SYNC) {
                        throw $exception;
                    }
                    return Promise\rejection_for($exception);
                }

                return new Content([
                    'contentId' => $this->generateContentId($attributes['id'], $contentType),
                    'name' => $this->extractName($attributes),
                    'attributes' => $this->fillAttributesWithData($clientResult->attributes, $attributes),
                    'dangerousInnerContent' => $clientResult->queryResultJson
                ]);
            });

        if ($mode === self::QUERY_SYNC) {
            return $promise->wait();
        }

        return $promise;
    }

    public function query(Query $query, string $locale = null, string $mode = self::QUERY_SYNC): ?object
    {
        $locale = $locale ?? $this->defaultLocale;

        $contentTypeGiven = $query->contentType !== null && trim($query->contentType) !== '';
        $queryGiven = $query->query !== null && trim($query->query) !== '';

        if ($queryGiven && !$contentTypeGiven) {
            $promise = $this->queryBySearchString($query, $locale);
        } elseif ($queryGiven && $contentTypeGiven) {
            $promise = $this->queryByContentTypeAndSearchString($query, $locale);
        } elseif (!$queryGiven && $contentTypeGiven) {
            $promise = $this->queryByContentType($query, $locale, $mode);
        } else {
            $exception = new \InvalidArgumentException('provide a ContentType and/or a search text');

            if ($mode === self::QUERY_SYNC) {
                throw $exception;
            }

            return Promise\rejection_for($exception);
        }

        if ($mode === self::QUERY_SYNC) {
            return $promise->wait();
        }

        return $promise;
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
                $newAttribute = clone $attribute;

                $attributeContent = $fields[(string)$attribute->attributeId];
                if ($attribute->type === 'Text') {
                    $attributeContent = $attributeContent['html'];
                }

                $newAttribute->content = $attributeContent;

                return $newAttribute;
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
                'invalid formatted locale: ' . $graphCmsLocale
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

        return $data['data'][$name] ?? [];
    }

    private function hasContent(ContentApi\GraphCMS\ClientResult $clientResult, $contentType): bool
    {
        return $this->getDataFromResult($clientResult, $contentType) !== null;
    }

    private function queryBySearchString(Query $query, string $locale): PromiseInterface
    {
        return $this->client->search($query->query, [], $this->frontasticToGraphCmsLocale($locale))
            ->then(function ($clientSearchResult) use ($query) {
                return $this->getContentFromClientSearchResult($clientSearchResult, $query);
            })
            ->then(function ($contents) {
                return new Result([
                    'total' => count($contents),
                    'count' => count($contents),
                    'offset' => 0,
                    'items' => $contents
                ]);
            });
    }

    private function queryByContentTypeAndSearchString(Query $query, string $locale): PromiseInterface
    {
        return $this->client->search(
            $query->query,
            [$query->contentType],
            $this->frontasticToGraphCmsLocale($locale)
        )
            ->then(function ($clientSearchResult) use ($query) {
                return $this->getContentFromClientSearchResult($clientSearchResult, $query);
            })
            ->then(function ($contents) {
                return new Result([
                    'total' => count($contents),
                    'count' => count($contents),
                    'offset' => 0,
                    'items' => $contents
                ]);
            });
    }

    private function queryByContentType(Query $query, string $locale, string $mode): PromiseInterface
    {
        return $this->client->getAll($query->contentType, $this->frontasticToGraphCmsLocale($locale))
            ->then(function ($clientResult) use ($query, $mode) {
                $name = lcfirst(Inflector::pluralize($query->contentType));
                $data = json_decode($clientResult->queryResultJson, true);
                if (!isset($data['data'])) {
                    $exception = new \InvalidArgumentException('invalid search parameters');

                    if ($mode === self::QUERY_SYNC) {
                        throw $exception;
                    }
                    return Promise\rejection_for($exception);
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
                return $contents;
            })
            ->then(function ($contents) {
                return new Result([
                    'total' => count($contents),
                    'count' => count($contents),
                    'offset' => 0,
                    'items' => $contents
                ]);
            });
    }

    /**
     * @param GraphCMS\ClientResult $clientResult
     * @param Query $query
     * @return array
     */
    private function getContentFromClientSearchResult(
        ContentApi\GraphCMS\ClientResult $clientResult,
        Query $query
    ): array {
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
        return $contents;
    }
}
