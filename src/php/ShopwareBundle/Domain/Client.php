<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain;

use Doctrine\Common\Cache\Cache;
use Exception;
use Frontastic\Common\HttpClient;
use Frontastic\Common\HttpClient\Response;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client\AccessTokenProvider;
use Frontastic\Common\ShopwareBundle\Domain\Exception\RequestException;
use Frontastic\Common\ShopwareBundle\Domain\Exception\ResourceNotFoundException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Uri;
use League\OAuth2\Client\Grant\ClientCredentials;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Psr\Http\Message\UriInterface;
use Frontastic\Common\CoreBundle\Domain\Json\Json;

class Client implements ClientInterface
{
    private const SHOPWARE_LANGUAGE_HEADER = 'sw-language-id';
    private const SHOPWARE_CURRENCY_HEADER = 'sw-currency-id';
    private const SHOPWARE_CONTEXT_TOKEN_HEADER = 'sw-context-token';
    private const SHOPWARE_ACCESS_TOKEN_HEADER = 'Authorization';

    /**
     * @var \Frontastic\Common\HttpClient
     */
    private $httpClient;

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $baseUri;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    private $defaultHeaders = [
        'Accept' => '*/*',
        'Content-Type' => 'application/json',
        'sw-access-key' => ['getApiKey'],
    ];

    public function __construct(
        HttpClient $httpClient,
        Cache $cache,
        string $apiKey,
        string $baseUri,
        string $clientId,
        string $clientSecret
    ) {
        $this->httpClient = $httpClient;
        $this->cache = $cache;
        $this->apiKey = $apiKey;
        $this->baseUri = $baseUri;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
    }

    public function forLanguage(string $languageId): ClientInterface
    {
        $this->defaultHeaders[self::SHOPWARE_LANGUAGE_HEADER] = $languageId;
        return $this;
    }

    public function forCurrency(string $currencyId): ClientInterface
    {
        $this->defaultHeaders[self::SHOPWARE_CURRENCY_HEADER] = $currencyId;
        return $this;
    }

    public function withContextToken(string $token): ClientInterface
    {
        $this->defaultHeaders[self::SHOPWARE_CONTEXT_TOKEN_HEADER] = $token;
        return $this;
    }

    public function withAccessToken(): ClientInterface
    {
        $this->defaultHeaders[self::SHOPWARE_ACCESS_TOKEN_HEADER] = sprintf('Bearer %s', $this->getAccessToken());
        return $this;
    }

    public function get(string $uri, array $parameters = [], array $headers = []): PromiseInterface
    {
        return $this->request(self::METHOD_GET, $uri, $parameters, $headers);
    }

    public function patch(string $uri, array $headers = [], $body = null): PromiseInterface
    {
        return $this->request(self::METHOD_PATCH, $uri, [], $headers, $body);
    }

    public function post(string $uri, array $headers = [], $body = null): PromiseInterface
    {
        return $this->request(self::METHOD_POST, $uri, [], $headers, $body);
    }

    public function put(string $uri, array $headers = [], $body = null): PromiseInterface
    {
        return $this->request(self::METHOD_PUT, $uri, [], $headers, $body);
    }

    public function delete(string $uri, array $headers = []): PromiseInterface
    {
        return $this->request(self::METHOD_DELETE, $uri, [], $headers);
    }

    private function request(
        string $method,
        string $uriComponent,
        array $parameters,
        array $headers,
        $body = null
    ): PromiseInterface {
        $uri = $this->buildWithQueryString(
            new Uri($this->baseUri . $uriComponent),
            $parameters
        );

        $defaultTimeout = (int)getenv('http_client_timeout');

        if ($body !== null && is_array($body)) {
            $body = Json::encode($body);
        }

        return $this->httpClient
            ->requestAsync(
                $method,
                (string)$uri,
                (string)$body,
                $this->buildRequestHeaders($headers),
                new HttpClient\Options([
                    'timeout' => ($method === self::METHOD_POST ? max(10, $defaultTimeout) : max(5, $defaultTimeout)),
                ])
            )
            ->then(function (Response $response) {
                if ($response->status === 404) {
                    throw new ResourceNotFoundException('Resource not found');
                }

                if ($response->status >= 400) {
                    throw $this->prepareException($response);
                }

                if ($response->status === 204) {
                    return $response;
                }

                $data = Json::decode($response->body, true);
                if (JSON_ERROR_NONE === json_last_error()) {
                    $data['headers'] = $response->headers;

                    return $data;
                }

                throw new RequestException(
                    'JSON error: ' . json_last_error_msg(),
                    $response->status
                );
            });
    }

    protected function getApiKey(): string
    {
        return $this->apiKey;
    }

    protected function prepareException(Response $response): Exception
    {
        $errorData = Json::decode($response->body);
        $exception = new RequestException(
            $errorData->message ?? $response->body ?? 'Internal Server Error',
            (int)($response->status ?? 503)
        );

        if (isset($errorData->errors)) {
            foreach ($errorData->errors as $error) {
                $exception = new RequestException(
                    $error->detail ?? $error->title ?? 'Unknown error',
                    (int)($error->status ?? 503),
                    $exception
                );
            }
        }

        return $exception;
    }

    /**
     * @param string[] $additionalHeaders
     *
     * @return string[]
     */
    private function buildRequestHeaders(array $additionalHeaders): array
    {
        $headers = [];
        foreach ($this->defaultHeaders as $header => $value) {
            if (is_array($value) && is_callable([$this, $value[0]])) {
                $value = $this->{$value[0]}();
            }
            $headers[] = sprintf('%s: %s', $header, $value);
        }

        return array_merge($headers, $additionalHeaders);
    }

    private function buildWithQueryString(UriInterface $uri, array $parameters): UriInterface
    {
        return Uri::withQueryValues($uri, $parameters);
    }

    private function getAccessToken(): string
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        $cacheId = sprintf('shopware:accessToken:%s', md5($this->apiKey));

        $accessToken = $this->cache->fetch($cacheId);
        if ($accessToken && false === $accessToken->hasExpired()) {
            return ($this->accessToken = (string)$accessToken);
        }

        try {
            $accessToken = $this->obtainAccessToken();
        } catch (\Exception $e) {
            throw new \RuntimeException(
                'Cannot connect to Shopware to obtain an access token',
                0,
                $e
            );
        }

        $this->cache->save($cacheId, $accessToken);
        return ($this->accessToken = (string)$accessToken);
    }

    private function obtainAccessToken(): AccessTokenInterface
    {
        if (empty($this->clientId) || empty($this->clientSecret)) {
            throw new \RuntimeException(
                'The client credentials are not been set',
            );
        }

        $authUrl = $this->buildWithQueryString(
            new Uri($this->baseUri . '/api/oauth/token'),
            []
        );

        $provider = new AccessTokenProvider(
            (string)$authUrl,
            [
                'clientId' => $this->clientId,
                'clientSecret' => $this->clientSecret,
                'grant_type'=> 'client_credentials',
            ]
        );

        return $provider->getAccessToken(new ClientCredentials());
    }
}
