<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi\GraphCMS;

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
    public function query(string $query): string
    {
        $url = "https://api-{$this->region}.graphcms.com/v1/{$this->projectId}/{$this->stage}";
        $body = json_encode(['query' => $query], JSON_HEX_QUOT);
        $result = $this->httpClient->requestAsync('GET', $url, $body)->wait();
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

    protected function getAttributeNames($attributes): array
    {
        return array_map(
            function ($e) {
                return $e['name'];
            },
            $attributes
        );
    }

    protected function isReference($attribute): bool
    {
        return $attribute['type']['kind'] == 'LIST' || $attribute['type']['kind'] == 'OBJECT';
    }

    protected function isNoReference($attribute): bool
    {
        return !$this->isReference($attribute);
    }

    // contentType must be capitalized and singular
    protected function attributeQueryPart($contentType): string
    {
        $attributes = $this->getAttributes($contentType);
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
    public function get($contentType, $contentId): string
    {
        $attributeString = $this->attributeQueryPart($contentType);
        $name = lcfirst($contentType);
        return $this->query("
          query {
            $name(where: { id: \"$contentId\" }) {
              $attributeString
            }
          }
        ");
    }

    // contentType mus be capitalized and singular
    public function getAll($contentType): string
    {
        $attributeString = $this->attributeQueryPart($contentType);
        $name = lcfirst(Inflector::pluralize($contentType));
        return $this->query("
          query {
            $name {
              $attributeString
            }
          }
        ");
    }

    private function startsWith($haystack, $needle)
    {
        return substr_compare($haystack, $needle, 0, strlen($needle)) === 0;
    }

    private function endsWith($haystack, $needle)
    {
        return substr_compare($haystack, $needle, -strlen($needle)) === 0;
    }

    /** * @SuppressWarnings(PHPMD.CyclomaticComplexity) * */
    protected function hasNameOfSupplementalObject($name)
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
