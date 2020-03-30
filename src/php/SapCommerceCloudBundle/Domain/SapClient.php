<?php

namespace Frontastic\Common\SapCommerceCloudBundle\Domain;

use Frontastic\Common\HttpClient;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;

class SapClient
{
    /** @var HttpClient */
    private $httpClient;

    /** @var string */
    private $hostUrl;

    /** @var string */
    private $siteId;

    /** @var string */
    private $catalogId;

    /** @var string */
    private $catalogVersionId;

    /** @var array<string, string> */
    private $urlReplacements;

    /** @var HttpClient\Options */
    private $readClientOptions;

    /** @var HttpClient\Options */
    private $writeClientOptions;

    public function __construct(
        HttpClient $httpClient,
        string $hostUrl,
        string $siteId,
        string $catalogId,
        string $catalogVersionId
    ) {
        $this->httpClient = $httpClient;
        $this->hostUrl = $hostUrl;
        $this->siteId = $siteId;
        $this->catalogId = $catalogId;
        $this->catalogVersionId = $catalogVersionId;

        $this->urlReplacements = [
            '{siteId}' => $siteId,
            '{catalogId}' => $catalogId,
            '{catalogVersionId}' => $catalogVersionId,
        ];

        $defaultTimeout = (int)getenv('http_client_timeout');
        $this->readClientOptions = new HttpClient\Options([
            'timeout' => max(2, $defaultTimeout),
        ]);
        $this->writeClientOptions = new HttpClient\Options([
            'timeout' => max(10, $defaultTimeout),
        ]);
    }

    /**
     * @return string A ID unique for this host and site which can be used as a cache key
     */
    public function getInstanceId(): string
    {
        return md5(implode('/', [$this->hostUrl, $this->siteId, $this->catalogId, $this->catalogVersionId]));
    }

    public function getHostUrl(): string
    {
        return $this->hostUrl;
    }

    public function get(string $urlTemplate, array $parameters = []): PromiseInterface
    {
        return $this->httpClient
            ->getAsync($this->buildUrl($urlTemplate, $parameters), '', [], $this->readClientOptions)
            ->then(function (HttpClient\Response $response): array {
                return $this->parseResponse($response);
            });
    }

    public function post(string $urlTemplate, array $payload, array $parameters = []): PromiseInterface
    {
        $body = json_encode($payload);
        if ($body === false) {
            throw new \RuntimeException('Invalid JSON payload');
        }

        return $this->httpClient
            ->postAsync(
                $this->buildUrl($urlTemplate, $parameters),
                $body,
                [
                    'Content-Type: application/json',
                ],
                $this->writeClientOptions
            )
            ->then(function (HttpClient\Response $response): array {
                return $this->parseResponse($response);
            });
    }

    private function buildUrl(string $urlTemplate, array $parameters)
    {
        $url = $urlTemplate;
        foreach ($this->urlReplacements as $search => $replacement) {
            $url = str_replace($search, $replacement, $url);
        }

        if (count($parameters) > 0) {
            $url .= strstr($url, '?') === false ? '?' : '&';
            $url .= http_build_query($parameters);
        }

        $url = $this->hostUrl . $url;
        return $url;
    }

    private function parseResponse(HttpClient\Response $response): array
    {
        $status = $response->status ?? 503;
        if ($status < 200 || $status >= 300) {
            $errorData = json_decode($response->body, true);
            throw new RequestException(
                $errorData['errors'][0]['message'] ?? 'Internal Server Error',
                $status
            );
        }

        $data = json_decode($response->body, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RequestException(
                'JSON error: ' . json_last_error_msg(),
                $status
            );
        }

        return $data;
    }
}
