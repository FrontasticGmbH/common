<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi;

use Frontastic\Common\ContentApiBundle\Domain\ContentApi;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi\GraphCMS\Client;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi\GraphCMS\Inflector;
use Frontastic\Common\ContentApiBundle\Domain\ContentType;
use Frontastic\Common\ContentApiBundle\Domain\Query;
use Frontastic\Common\ContentApiBundle\Domain\Result;
use GuzzleHttp\Promise;
use GuzzleHttp\Promise\PromiseInterface;
use Frontastic\Common\CoreBundle\Domain\Json\Json;

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

                $name = $this->extractName($attributes);

                return new Content([
                    'contentId' => $this->generateContentId($attributes['id'], $contentType),
                    'contentTypeId' => $contentType,
                    'name' => $name,
                    'slug' => $attributes['slug'] ?? Inflector::slug($name),
                    'attributes' => $this->fillAttributesWithData($clientResult->attributes, $attributes),
                    'dangerousInnerContent' => $clientResult->queryResultJson,
                ]);
            });

        return $this->returnPromiseOrResult($promise, $mode);
    }

    public function query(Query $query, string $locale = null, string $mode = self::QUERY_SYNC): ?object
    {
        $locale = $locale ?? $this->defaultLocale;

        $contentTypeGiven = $this->stringIsNonEmpty($query->contentType);
        $contentIdsGiven = $this->arrayIsNonEmpty($query->contentIds);
        $searchStringGiven = $this->stringIsNonEmpty($query->query);

        if ($searchStringGiven && !$contentTypeGiven) {
            $promise = $this->queryBySearchString($query, $locale);
        } elseif ($searchStringGiven && $contentTypeGiven) {
            $promise = $this->queryByContentTypeAndSearchString($query, $locale);
        } elseif (!$searchStringGiven && $contentTypeGiven) {
            $promise = $this->queryByContentType($query, $locale, $mode);
        } elseif ($contentIdsGiven) {
            // Mock query method to prevent exception if only contentIds provided
            $promise = $this->queryByContentIds($query, $locale);
        } else {
            $promise = Promise\rejection_for(
                new \InvalidArgumentException('provide a ContentType and/or a search text')
            );
        }

        return $this->returnPromiseOrResult($promise, $mode);
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

                $attributeContent = isset($fields[(string)$attribute->attributeId]) ?
                    $fields[(string)$attribute->attributeId] : null;
                if ($attribute->type === 'Text') {
                    $attributeContent = $attributeContent['html'] ?? null;
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
        $name = lcfirst(Inflector::pluralize($contentType));

        $data = Json::decode($clientResult->queryResultJson, true);

        return $data['data'][$name][0] ?? [];
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
                    'items' => $contents,
                ]);
            })
            ->otherwise(function (\Exception $exception) {
                throw $exception;
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
                    'items' => $contents,
                ]);
            });
    }

    private function queryByContentType(Query $query, string $locale, string $mode): PromiseInterface
    {
        return $this->client->getAll($query->contentType, $this->frontasticToGraphCmsLocale($locale))
            ->then(function ($clientResult) use ($query, $mode) {
                $name = lcfirst(Inflector::pluralize($query->contentType));
                $data = Json::decode($clientResult->queryResultJson, true);
                if (!isset($data['data'])) {
                    $exception = new \InvalidArgumentException('invalid search parameters');

                    if ($mode === self::QUERY_SYNC) {
                        throw $exception;
                    }
                    return Promise\rejection_for($exception);
                }
                return array_map(
                    function ($e) use ($clientResult, $query) {
                        $name = $this->extractName($e);

                        return new Content([
                            'contentId' => $this->generateContentId($e['id'], $query->contentType),
                            'contentTypeId' => $query->contentType,
                            'name' => $name,
                            'slug' => $e['slug'] ?? Inflector::slug($name),
                            'attributes' => $this->fillAttributesWithData(
                                $clientResult->attributes,
                                $e
                            ),
                            'dangerousInnerContent' => $e,
                        ]);
                    },
                    $data['data'][$name]
                );
            })
            ->then(function ($contents) {
                return new Result([
                    'total' => count($contents),
                    'count' => count($contents),
                    'offset' => 0,
                    'items' => $contents,
                ]);
            });
    }

    public function queryByContentIds(Query $query, string $locale): PromiseInterface
    {
        $promise = new Promise\Promise();

        $promise->resolve(new Result());

        return $promise;
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
        $data = Json::decode($clientResult->queryResultJson, true);
        $attributes = $clientResult->attributes;
        $contents = [];
        foreach ($data['data'] as $contentType => $items) {
            // contentType is in plural lowercase version here
            $contentsForContentType = array_map(
                function ($e) use ($contentType, $attributes) {
                    $contentTypeSingularized = ucfirst(Inflector::singularize($contentType));
                    $contentId = $this->generateContentId($e['id'], $contentTypeSingularized);
                    $name = $this->extractName($e);
                    return new Content([
                        'contentId' => $contentId,
                        'contentTypeId' => $contentTypeSingularized,
                        'name' => $name,
                        'slug' => $e['slug'] ?? Inflector::slug($name),
                        'attributes' => $this->fillAttributesWithData(
                            $attributes[$contentType],
                            $e
                        ),
                        'dangerousInnerContent' => $e,
                    ]);
                },
                $items
            );
            $contents = array_merge($contents, $contentsForContentType);
        }
        return $contents;
    }

    private function stringIsNonEmpty(?string $value): bool
    {
        return $value !== null && trim($value) !== '';
    }

    private function arrayIsNonEmpty(?array $value): bool
    {
        return $value !== null && !empty($value);
    }

    private function returnPromiseOrResult(PromiseInterface $promise, string $mode)
    {
        if ($mode === self::QUERY_SYNC) {
            return $promise->wait();
        }

        return $promise;
    }
}
