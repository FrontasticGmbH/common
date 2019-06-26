<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi\GraphCMS;

use Frontastic\Common\HttpClient;

class Client
{
    /**
     * @var string
     */
    private $apiToken;

    private $httpClient;

    public function __construct(string $apiToken, HttpClient $httpClient)
    {
        $this->apiToken = $apiToken;
        $this->httpClient = $httpClient;
    }

    // takes graphQL query, returns JSON result as string
    public function query(string $query): string
    {
        return "bar";
    }
}
