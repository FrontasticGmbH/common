<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi\GraphCMS;

use Doctrine\Common\Cache\Cache;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi\Attribute;
use Frontastic\Common\HttpClient;
use GuzzleHttp\Promise\PromiseInterface;
use Frontastic\Common\CoreBundle\Domain\Json\Json;

class Client
{
    /**
     * @var string
     */
    private $apiToken;

    /**
     * @var string
     */
    private $apiVersion;

    /**
     * @var string
     */
    private $projectId;

    /**
     * @var string
     */
    private $region;

    /**
     * @var string
     */
    private $stage;

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @const string[] names of attributes in which should be searched
     */
    private const SEARCH_ATTRIBUTES = ['name', 'fileName', 'title', 'label'];

    /**
     * @var Cache
     */
    private $cache;

    public function __construct(
        string $projectId,
        string $apiToken,
        string $apiVersion,
        string $region,
        string $stage,
        HttpClient $httpClient,
        Cache $cache
    ) {
        $this->projectId = $projectId;
        $this->apiToken = $apiToken;
        $this->apiVersion = $apiVersion;
        $this->region = $region;
        $this->stage = $stage;
        $this->httpClient = $httpClient;
        $this->httpClient->addDefaultHeaders([
            'content-type: application/json',
            'Authorization: Bearer '.$this->apiToken
        ]);
        $this->cache = $cache;
    }

    /**
     * takes GraphQL query, returns JSON result as string
     */
    public function query(string $query, string $locale = null): PromiseInterface
    {
        $url = "https://api-{$this->region}.graphcms.com/{$this->apiVersion}/{$this->projectId}/{$this->stage}";
        $body = Json::encode(['query' => $query], JSON_HEX_QUOT);
        $headers = [];
        if ($locale !== null) {
            $headers[] = 'gcms-locale:'.$locale;
        }

        // span needs to be defined in order to be able to pass it to the promise-function below
        $span = null;
        // logging graphcms queries to tideways in order to analyze slow queries
        if (class_exists(\Tideways\Profiler::class)) {
            $span = \Tideways\Profiler::createSpan('graphcms');
            $span->annotate(
                [
                    'title' => sprintf('graphcms(stage: %s, region: %s)', $this->stage, $this->region),
                    'query' => $body,
                ]
            );
        }

        return $this->httpClient
            ->requestAsync('POST', $url, $body, $headers)
            ->then(function (HttpClient\Response $result) use ($span) {
                if (class_exists(\Tideways\Profiler::class)) {
                    $span->annotate(
                        [
                            'response.status' => $result->status,
                            'response.graphCmsRequestId' => $result->headers['x-request-id'] ?? null,
                            'response.graphCmsCacheHeader' => $result->headers['x-cdn-cache-status'] ?? null,
                            'response.body' => $result->status !== 200 ? $result->body : null,
                        ]
                    );
                    $span->finish();
                }

                if ($result->status >= 400) {
                    throw $this->prepareException($result);
                }

                return $result->body;
            });
    }

    protected function prepareException(HttpClient\Response $response): \Exception
    {
        $errorData = Json::decode($response->body);
        $exception = new \Exception(
            ($errorData->message ?? $response->body) ?: 'Internal Server Error',
            $response->status ?? 503
        );

        if (isset($errorData->errors)) {
            $errorData->errors = array_reverse($errorData->errors);
            foreach ($errorData->errors as $error) {
                $exception = new \Exception(
                    $error->message ?? 'Unknown error',
                    $response->status ?? 503,
                    $exception
                );
            }
        }

        return $exception;
    }

    /**
     * Returns array of all attributes of a entity a.k.a. contentType. Contenttype must be capitalized and singular.
     */
    public function getAttributes(string $contentType): array
    {
        $cacheId = sprintf(
            'graphCMS:contentModel:%s',
            md5($this->projectId . $this->stage . $contentType)
        );

        $cachedAttributes = $this->cache->fetch($cacheId);
        if ($cachedAttributes !== false) {
            return $cachedAttributes;
        }

        $query = "
            query {
                __type(name: \"$contentType\") {
                    fields {
                       name
                       type {
                           name
                           kind
                           ofType {
                               name
                               kind
                               ofType {
                                   name
                                   kind
                                   ofType {
                                       name
                                       kind
                                   }
                               }
                           }
                       }
                    }
               }
            }
        ";

        return $this->query($query)->then(function (string $result) use ($cacheId) {
            $json = Json::decode($result, true);

            $attributes = $json['data']['__type']['fields'] ?? [];

            $this->cache->save($cacheId, $attributes, 24 * 60 * 60);

            return $attributes;
        })->wait();
    }

    protected function getAttributeNames(array $attributes): array
    {
        return array_map(
            function ($e) {
                return $e['name'];
            },
            $attributes
        );
    }

    protected function isReference(array $attribute): bool
    {
        return $this->determineAttributeType($attribute)['kind'] === 'OBJECT' ||
            $this->determineAttributeType($attribute)['kind'] === 'UNION';
    }

    protected function isNoReference(array $attribute): bool
    {
        return !$this->isReference($attribute);
    }

    // contentType must be capitalized and singular
    protected function attributeQueryPart(array $attributes): string
    {
        return implode(
            ',',
            $this->fetchAttributeFields($attributes, 2)
        );
    }

    private function fetchAttributeFields(array $attributes, int $maxDepth, int $currentDepth = 0)
    {
        $simpleAttributes = array_filter(
            $attributes,
            [$this, 'isNoReference']
        );

        if ($maxDepth > $currentDepth) {
            $references = array_filter(
                $attributes,
                [$this, 'isReference']
            );
        } else {
            $references = [];
        }

        return array_merge(
            $this->getAttributeNames($simpleAttributes),
            array_map(
                function ($e) use ($maxDepth, $currentDepth) {
                    if (isset($e['type']['name']) && $e['type']['name'] == 'Asset') {
                        $queryAttributesString = $this->getAdditionalAttributes(
                            $e,
                            $maxDepth,
                            $currentDepth,
                            'id, handle, mimeType',
                            ['id', 'handle', 'mimeType', 'altText']
                        );

                        return "{$e['name']} { $queryAttributesString }";
                    } elseif (isset($e['type']['name']) && $e['type']['name'] == 'RichText' ||
                        isset($e['type']['ofType']['name']) && $e['type']['ofType']['name'] == 'RichText'
                    ) {
                        return "{$e['name']} { html }";
                    } else {
                        $queryAttributesString = $this->getAdditionalAttributes($e, $maxDepth, $currentDepth, 'id');

                        if (empty($queryAttributesString)) {
                            return '';
                        }

                        return "{$e['name']} { $queryAttributesString }";
                    }
                },
                $references
            )
        );
    }

    private function getAdditionalAttributes(
        array $referenceField,
        int $maxDepth,
        int $currentDepth,
        string $defaultAttributes = '',
        array $whitelistFields = []
    ): string {
        $attributeType = $this->determineAttributeType($referenceField);
        $contentType = $attributeType['type'];
        $contentKind = $attributeType['kind'];

        if ($contentKind == 'UNION') {
            // TODO: When the attribute kind is an union, we need first get the objects that form this union
            // and then get the attributes for each of those objects.
            // https://grandstack.io/docs/graphql-interface-union-types/#union-types

            // Skipping the UNIONs kind
            return '';
        }

        $attributes = $this->getAttributes($contentType);

        if (!empty($attributes)) {
            return implode(
                ',',
                array_filter(
                    $this->fetchAttributeFields($attributes, $maxDepth, $currentDepth + 1),
                    function ($attributeName) use ($whitelistFields) {
                        if (empty($whitelistFields)) {
                            return true;
                        }

                        return in_array($attributeName, $whitelistFields);
                    }
                )
            );
        }

        return $defaultAttributes;
    }

    // contentType must be capitalized and singular
    public function get(string $contentType, string $contentId, string $locale = null): PromiseInterface
    {
        return $this->getMultiple($contentType, [$contentId], $locale);
    }

    // contentType must be capitalized and singular
    public function getMultiple(string $contentType, array $contentIds, string $locale = null): PromiseInterface
    {
        $attributes = $this->getAttributes($contentType);
        $attributeString = $this->attributeQueryPart($attributes);
        $name = lcfirst(Inflector::pluralize($contentType));
        $contentIdsString = "[".implode(
            ',',
            array_map(
                function ($id) {
                    return "\"$id\"";
                },
                $contentIds
            )
        )."]";
        $queryString = "query {
                $name(where: { id_in: $contentIdsString }) {
                  $attributeString
                }
              }
            ";

        return $this->query($queryString, $locale)
            ->then(function (string $queryResultJson) use ($attributes): ClientResult {
                return new ClientResult([
                    'queryResultJson' => $queryResultJson,
                    'attributes' => $this->convertAttributes($attributes)
                ]);
            });
    }

    // contentType must be capitalized and singular
    public function getAll(string $contentType, string $locale = null): PromiseInterface
    {
        $attributes = $this->getAttributes($contentType);
        $attributeString = $this->attributeQueryPart($attributes);
        $name = lcfirst(Inflector::pluralize($contentType));
        $queryString = "query {
                $name {
                  $attributeString
                }
              }
            ";

        return $this->query($queryString, $locale)
            ->then(function (string $queryResultJson) use ($attributes): ClientResult {
                return new ClientResult([
                    'queryResultJson' => $queryResultJson,
                    'attributes' => $this->convertAttributes($attributes)
                ]);
            });
    }

    private function convertAttributes(array $attributes): array
    {
        return array_map(
            function ($attribute): Attribute {
                $type = $this->determineAttributeType($attribute);
                return new Attribute([
                    'attributeId' => $attribute['name'],
                    'content' => null, // will be added later when it is fetched
                    'type' => $type['list'] ? 'LIST' : $type['type'],
                ]);
            },
            $attributes
        );
    }

    /**
     * Determines the type of an attribute and returns it
     *
     * @param array $attribute
     * @return array with keys 'kind' and 'name' and 'list'
     */
    private function determineAttributeType(array $attribute)
    {
        $isList = false;
        $deepestAttributes = $attribute['type'];
        while (isset($deepestAttributes['ofType']) && $deepestAttributes['ofType'] !== null) {
            if ($deepestAttributes['kind'] === 'LIST') {
                $isList = true;
            }
            $deepestAttributes = $deepestAttributes['ofType'];
        }
        $type = $deepestAttributes['name'];
        $kind = $deepestAttributes['kind'];

        // map type for frontastic
        switch ($type) {
            case 'RichText':
                $type = 'Text';
                break;
        }
        $result = ['type' => $type, 'kind' => $kind, 'list' => $isList];
        return $result;
    }

    private function startsWith(string $haystack, string $needle): bool
    {
        return substr_compare($haystack, $needle, 0, strlen($needle)) === 0;
    }

    private function endsWith(string $haystack, string $needle): bool
    {
        return substr_compare($haystack, $needle, -strlen($needle)) === 0;
    }

    /** * @SuppressWarnings(PHPMD.CyclomaticComplexity) * */
    protected function hasNameOfSupplementalObject(string $name): bool
    {
        return $name === 'Color' ||
            $name === 'DocumentVersion' ||
            $name === 'Query' ||
            $name === 'Location' ||
            $name === 'Mutation' ||
            $name === 'PageInfo' ||
            $name === 'RichText' ||
            $name === 'RGBA' ||
            $name === 'Version' ||
            $this->startsWith($name, '__') ||
            $this->startsWith($name, 'Aggregate') ||
            $this->endsWith($name, 'Edge') ||
            $this->endsWith($name, 'Connection') ||
            $this->endsWith($name, 'Payload') ||
            $this->endsWith($name, 'PreviousValues') ||
            ($this->endsWith($name, 'Asset') && $name !== 'Asset');
    }

    public function getContentTypes(): array
    {
        $allTypes = Json::decode(
            $this->query(
                "{
                     __schema {
                        types {
                            name
                            kind
                        }
                    }
                 }"
            )->wait(),
            true
        )['data']['__schema']['types'];
        $relevantTypes = array_filter(
            $allTypes,
            function ($e) {
                return $e['kind'] === 'OBJECT' &&
                                  !$this->hasNameOfSupplementalObject($e['name']);
            }
        );
        return array_values(
            array_map(
                function ($e) {
                    return $e['name'];
                },
                $relevantTypes
            )
        );
    }

    public function search(string $searchString, array $contentTypes = [], string $locale = null): PromiseInterface
    {
        if (empty($contentTypes)) {
            $contentTypes = $this->getContentTypes();
        }
        $attributesByContentType = [];
        $queryParts = implode(",\n", array_filter(array_map(
            function ($contentType) use ($searchString, &$attributesByContentType): string {
                $name = lcfirst(Inflector::pluralize($contentType));
                $attributes = $this->getAttributes($contentType);

                $attributesByContentType[$name] = [];
                foreach ($attributes as $attribute) {
                    $attributesByContentType[$name][$attribute['name']] = $attribute;
                }

                $possibleSearchAttributes = array_filter(
                    $attributes,
                    function ($attribute) {
                        return in_array(
                            $attribute['name'],
                            self::SEARCH_ATTRIBUTES
                        );
                    }
                );

                if (count($possibleSearchAttributes) === 0) {
                    return '';
                }
                $searchAttribute = reset($possibleSearchAttributes); // get first entry of array, regardless of it's key
                $attributeString = $this->attributeQueryPart($attributes);
                $queryPart =
                           $name . "(where: { ${searchAttribute['name']}_contains: \"$searchString\" }){ " .
                           $attributeString .  " }";
                return $queryPart;
            },
            $contentTypes
        )));

        $queryString = "query data {
                $queryParts
              }
            ";

        return $this->query($queryString, $locale)
            ->then(function ($queryResultJson) use ($attributesByContentType) {
                return new ClientResult([
                    'queryResultJson' => $queryResultJson,
                    'attributes' => array_map(
                        function ($value) {
                            return $this->convertAttributes(array_values($value));
                        },
                        $attributesByContentType
                    )
                ]);
            });
    }
}
