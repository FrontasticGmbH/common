<?php

namespace Frontastic\Common\FindologicBundle\Domain;

use Frontastic\Common\HttpClient;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\SimpleCache\CacheInterface;

class FindologicClient
{
    /**
     * @var string
     */
    private $shopkey;

    /**
     * @var string
     */
    private $hostUrl;

    /**
     * @var HttpClient
     */
    private $httpClient;

    public function __construct(
        HttpClient $httpClient,
        string $hostUrl,
        string $shopkey
    ) {
        $this->httpClient = $httpClient;
        $this->hostUrl = $hostUrl;
        $this->shopkey = $shopkey;
    }

    public function request(string $query, string $locale = null): PromiseInterface
    {
        $body = json_encode([]);
        $headers = [];

        return $this->httpClient
            ->requestAsync('POST', $this->hostUrl, $body, $headers)
            ->then(function (HttpClient\Response $response) {
                if ($response->status >= 400) {
                    throw $this->prepareException($response);
                }

                return $this->parseResponse($response);
            });
    }

    private function parseResponse(HttpClient\Response $response): ?array
    {
        if ($response->body === '') {
            return null;
        }

        $body = json_decode($response->body, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RequestException(
                'JSON error: ' . json_last_error_msg(),
                $response->status
            );
        }

        return [
            'response' => $response,
            'status' => $response->status,
            'body' => $body->container,
        ];
    }

    protected function prepareException(HttpClient\Response $response): \Exception
    {
        $errorData = json_decode($response->body);
        $exception = new RequestException(
            ($errorData->message ?? $response->body) ?: 'Internal Server Error',
            $response->status ?? 503
        );

        return $exception;
    }
}
