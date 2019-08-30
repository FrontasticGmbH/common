<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi\GraphCMS;

use Doctrine\Common\Cache\Cache;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi\Attribute;
use Frontastic\Common\HttpClient;

class Client
{
    /**
     * @var string
     */
    private $apiToken;

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
        string $region,
        string $stage,
        HttpClient $httpClient,
        Cache $cache
    ) {
        $this->projectId = $projectId;
        $this->apiToken = $apiToken;
        $this->region = $region;
        $this->stage = $stage;
        $this->httpClient = $httpClient;
        $this->httpClient->setDefaultHeaders([
            'content-type: application/json',
            'Authorization: Bearer '.$this->apiToken
        ]);
        $this->cache = $cache;
    }

    /**
     * takes GraphQL query, returns JSON result as string
     */
    public function query(string $query, string $locale = null): string
    {
        $url = "https://api-{$this->region}.graphcms.com/v1/{$this->projectId}/{$this->stage}";
        $body = json_encode(['query' => $query], JSON_HEX_QUOT);
        $headers = [];
        if ($locale !== null) {
            $headers[] = 'gcms-locale:'.$locale;
        }
        $result = $this->httpClient->requestAsync('GET', $url, $body, $headers)->wait();
        return $result->body;
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

        $json = json_decode($this->query($query), true);

        $attributes = $json['data']['__type']['fields'] ?? [];

        $this->cache->save($cacheId, $cachedAttributes, 60 * 60);

        return $attributes;
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
        if ($this->isListOrObject($attribute['type'])
            || ($attribute['type']['kind'] == 'NON_NULL' && $this->isListOrObject($attribute['type']['ofType']))
        ) {
            if ($attribute['type']['ofType']['ofType']['kind'] === 'SCALAR'
                || ($attribute['type']['ofType']['ofType']['kind'] === 'NON_NULL'
                    && $attribute['type']['ofType']['ofType']['ofType']['kind'] === 'SCALAR')
            ) {
                // special handling for a list of scalar values
                return false;
            }

            return true;
        }

        return false;
    }

    private function isListOrObject(array $type): bool
    {
        return $type['kind'] == 'LIST' || $type['kind'] == 'OBJECT';
    }

    protected function isNoReference(array $attribute): bool
    {
        return !$this->isReference($attribute);
    }

    // contentType must be capitalized and singular
    protected function attributeQueryPart(array $attributes): string
    {
        return implode(
            $this->fetchAttributeFields($attributes, 2),
            ','
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
                    if ($e['type']['name'] == 'Asset') {
                        $queryAttributesString = $this->getAdditionalAttributes(
                            $e,
                            $maxDepth,
                            $currentDepth,
                            'id, handle, mimeType',
                            ['id', 'handle', 'mimeType', 'altText']
                        );

                        return "{$e['name']} { $queryAttributesString }";
                    } elseif ($e['type']['name'] == 'RichText' || $e['type']['ofType']['name'] == 'RichText') {
                        return "{$e['name']} { html }";
                    } else {
                        $queryAttributesString = $this->getAdditionalAttributes($e, $maxDepth, $currentDepth, 'id');

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
        $contentType = $this->determineAttributeType($referenceField);

        if ($contentType === 'LIST'
            && $referenceField['type']['ofType']['ofType']['kind'] === 'OBJECT'
        ) {
            $contentType = $referenceField['type']['ofType']['ofType']['name'];
        }

        $attributes = $this->getAttributes($contentType);

        if (!empty($attributes)) {
            return implode(
                array_filter(
                    $this->fetchAttributeFields($attributes, $maxDepth, $currentDepth + 1),
                    function ($attributeName) use ($whitelistFields) {
                        if (empty($whitelistFields)) {
                            return true;
                        }

                        return in_array($attributeName, $whitelistFields);
                    }
                ),
                ','
            );
        }

        return $defaultAttributes;
    }

    // contentType must be capitalized and singular
    public function get(string $contentType, string $contentId, string $locale = null): ClientResult
    {
        $attributes = $this->getAttributes($contentType);
        $attributeString = $this->attributeQueryPart($attributes);
        $name = lcfirst($contentType);
        $queryResultJson = $this->query(
            "query {
                $name(where: { id: \"$contentId\" }) {
                  $attributeString
                }
              }
            ",
            $locale
        );

        return new ClientResult([
            'queryResultJson' => $queryResultJson,
            'attributes' => $this->convertAttributes($attributes)
        ]);
    }

    // contentType mus be capitalized and singular
    public function getAll(string $contentType, string $locale = null): ClientResult
    {
        $attributes = $this->getAttributes($contentType);
        $attributeString = $this->attributeQueryPart($attributes);
        $name = lcfirst(Inflector::pluralize($contentType));

        $queryResultJson = $this->query(
            "query {
                $name {
                  $attributeString
                }
              }
            ",
            $locale
        );

        return new ClientResult([
            'queryResultJson' => $queryResultJson,
            'attributes' => $this->convertAttributes($attributes)
        ]);
    }

    private function convertAttributes(array $attributes): array
    {
        return array_map(
            function ($attribute): Attribute {
                return new Attribute([
                    'attributeId' => $attribute['name'],
                    'content' => null, // will be added later when it is fetched
                    'type' => $this->determineAttributeType($attribute),
                ]);
            },
            $attributes
        );
    }

    /**
     * Determines the type of an attribute and returns it
     *
     * @param array $attribute
     * @return string
     */
    private function determineAttributeType(array $attribute)
    {
        $type = $attribute['type']['name']
            ?? $attribute['type']['kind'];

        // sometimes the "first" type here is of Type "NON_NULL" and the real one is in "ofType" field
        if ($type === 'NON_NULL') {
            $type = $attribute['type']['ofType']['name']
                ?? $attribute['type']['ofType']['kind'];
        }

        if ($type === 'LIST') {
            if ($attribute['type']['ofType']['ofType']['kind'] === 'SCALAR') {
                $type = $attribute['type']['ofType']['ofType']['name'];
            } elseif ($attribute['type']['ofType']['ofType']['kind'] === 'NON_NULL'
                && $attribute['type']['ofType']['ofType']['ofType']['kind'] === 'SCALAR') {
                $type = $attribute['type']['ofType']['ofType']['ofType']['name'];
            }
        }

        // map type for frontastic
        switch ($type) {
            case 'RichText':
                $type = 'Text';
                break;
        }
        return $type;
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
        return $name === 'Query' ||
            $name === 'Mutation' ||
            $name == 'PageInfo' ||
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
        $allTypes = json_decode(
            $this->query(
                "{
                     __schema {
                        types {
                            name
                            kind
                        }
                    }
                 }"
            ),
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

    public function search(string $searchString, array $contentTypes = [], string $locale = null): ClientResult
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
        $queryResultJson = $this->query(
            "query data {
                $queryParts
              }
            ",
            $locale
        );
        return new ClientResult([
            'queryResultJson' => $queryResultJson,
            'attributes' => array_map(
                function ($value) {
                    return $this->convertAttributes(array_values($value));
                },
                $attributesByContentType
            )
        ]);
    }
}
