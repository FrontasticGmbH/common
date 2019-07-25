<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi\GraphCMS;

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

    public function __construct(
        string $projectId,
        string $apiToken,
        string $region,
        string $stage,
        HttpClient $httpClient
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
                           }
                       }
                    }
               }
            }
        ";
        $json = json_decode($this->query($query), true);
        return $json['data']['__type']['fields'];
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
        return $attribute['type']['kind'] == 'LIST' || $attribute['type']['kind'] == 'OBJECT';
    }

    protected function isNoReference(array $attribute): bool
    {
        return !$this->isReference($attribute);
    }

    // contentType must be capitalized and singular
    protected function attributeQueryPart(array $attributes): string
    {
        $simpleAttributes = array_filter(
            $attributes,
            [$this , 'isNoReference']
        );
        $references = array_filter(
            $attributes,
            [$this , 'isReference']
        );
        return implode(
            array_merge(
                $this->getAttributeNames($simpleAttributes),
                array_map(
                    function ($e) {
                        if ($e['type']['name'] == 'Asset') {
                            return "{$e['name']} { handle }";
                        } elseif ($e['type']['name'] == 'RichText') {
                            return "{$e['name']} { markdown }";
                        } else {
                            return "{$e['name']} { id }";
                        }
                    },
                    $references
                )
            ),
            ','
        );
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
                $type = $attribute['type']['name']
                    ?? $attribute['type']['kind'];

                // sometimes the "first" type here is of Type "NON_NULL" and the real one is in "ofType" field
                if ($type === 'NON_NULL') {
                    $type = $attribute['type']['ofType']['name']
                        ?? $attribute['type']['ofType']['kind'];
                }

                // map type for frontastic
                switch ($type) {
                    case 'RichText':
                        $type = 'Text';
                        break;
                }

                return new Attribute([
                    'attributeId' => $attribute['name'],
                    'content' => null, // will be added later when it is fetched
                    'type' => $type,
                ]);
            },
            $attributes
        );
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
}
