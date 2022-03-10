<?php

namespace Frontastic\Common\ShopifyBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\RequestProvider;
use Frontastic\Common\HttpClient;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException;
use Frontastic\Common\ShopifyBundle\Domain\Exception\QueryException;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\SimpleCache\CacheInterface;
use Frontastic\Common\CoreBundle\Domain\Json\Json;

class ShopifyClient
{
    const SHOPIFY_API_VERSION = "2022-01";

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var string
     */
    private $hostUrl;

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * @var RequestProvider
     */
    private $requestProvider;

    public function __construct(
        HttpClient $httpClient,
        CacheInterface $cache,
        RequestProvider $requestProvider,
        string $hostUrl,
        string $accessToken
    ) {
        $this->httpClient = $httpClient;
        $this->cache = $cache;
        $this->requestProvider = $requestProvider;
        $this->accessToken = $accessToken;
        $this->httpClient->addDefaultHeaders([
            'content-type: application/json',
            'X-Shopify-Storefront-Access-Token: ' . $this->accessToken
        ]);

        // Build host url using fixed API version
        $this->hostUrl = $this->buildUrl($hostUrl);
    }

    private function buildUrl(string $hostUrl): string
    {
        $url = parse_url($hostUrl);
        $scheme = $url['scheme'] ?? 'https';
        $host = $url['host'] ?? $url['path'];

        return $scheme .'://' . $host . '/api/'. self::SHOPIFY_API_VERSION . '/graphql.json';
    }

    /**
     * takes GraphQL query, returns JSON result as string
     */
    public function request(string $query, string $locale = null): PromiseInterface
    {
        $body = Json::encode(['query' => $query], JSON_HEX_QUOT);
        $headers = [];

        $request = $this->requestProvider->getCurrentRequest();

        if ($request !== null) {
            $headers[] = sprintf('X-Forwarded-For: %s', $request->getClientIp());
        }

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

        $body = new ResponseAccess([
            'container' => Json::decode($response->body, true)
        ]);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new RequestException(
                'JSON error: ' . json_last_error_msg(),
                $response->status
            );
        }

        if ($body->hasErrors()) {
            throw QueryException::createFromErrors($body->getErrors());
        }

        return [
            'response' => $response,
            'status' => $response->status,
            'body' => $body->container,
        ];
    }

    protected function prepareException(HttpClient\Response $response): \Exception
    {
        $errorData = Json::decode($response->body);
        $exception = new RequestException(
            ($errorData->message ?? $response->body) ?: 'Internal Server Error',
            $response->status ?? 503
        );

        if (isset($errorData->errors)) {
            $errorData->errors = array_reverse($errorData->errors);
            foreach ($errorData->errors as $error) {
                $exception = new RequestException(
                    $error->message ?? 'Unknown error',
                    $response->status ?? 503,
                    $exception
                );

                $exception->setTranslationData(
                    $error->code ?? 'Unknown',
                    array_diff_key(
                        (array)$error,
                        ['action' => true, 'message' => true, 'code' => true]
                    )
                );
            }
        }

        return $exception;
    }
}
