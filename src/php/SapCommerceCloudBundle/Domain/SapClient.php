<?php

namespace Frontastic\Common\SapCommerceCloudBundle\Domain;

use Frontastic\Common\HttpClient;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use League\OAuth2\Client\Grant\ClientCredentials;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessTokenInterface;
use Psr\SimpleCache\CacheInterface;

class SapClient
{
    /** @var HttpClient */
    private $httpClient;

    /** @var CacheInterface */
    private $cache;

    /** @var string */
    private $hostUrl;

    /** @var string */
    private $siteId;

    /** @var string */
    private $clientId;

    /** @var string */
    private $clientSecret;

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

    /** @var string|null */
    private $accessToken = null;

    public function __construct(
        HttpClient $httpClient,
        CacheInterface $cache,
        string $hostUrl,
        string $siteId,
        string $clientId,
        string $clientSecret,
        string $catalogId,
        string $catalogVersionId
    ) {
        $this->httpClient = $httpClient;
        $this->cache = $cache;
        $this->hostUrl = $hostUrl;
        $this->siteId = $siteId;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
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
        return $this->request('GET', $urlTemplate, null, $parameters);
    }

    public function delete(string $urlTemplate, array $parameters = []): PromiseInterface
    {
        return $this->request('DELETE', $urlTemplate, null, $parameters);
    }

    public function post(string $urlTemplate, array $payload, array $parameters = []): PromiseInterface
    {
        return $this->request('POST', $urlTemplate, $payload, $parameters);
    }

    public function put(string $urlTemplate, array $payload, array $parameters = []): PromiseInterface
    {
        return $this->request('PUT', $urlTemplate, $payload, $parameters);
    }

    private function request(string $method, string $urlTemplate, ?array $payload, array $parameters): PromiseInterface
    {
        $body = '';
        $headers = [];

        if ($payload !== null) {
            $body = json_encode($payload);
            if ($body === false) {
                throw new \RuntimeException('Invalid JSON payload');
            }
            $headers[] = 'Content-Type: application/json';
        }

        $options = $method === 'GET' ? $this->readClientOptions : $this->writeClientOptions;
        $url = $this->buildUrl($urlTemplate, $parameters);

        return $this->httpClient
            ->requestAsync($method, $url, $body, array_merge($headers, [$this->getAuthorizationHeader()]), $options)
            ->then(function (HttpClient\Response $response) use ($method, $url, $body, $headers, $options) {
                if ($response->status === 401) {
                    $this->invalidateAccessToken();
                    return $this->httpClient->requestAsync(
                        $method,
                        $url,
                        $body,
                        array_merge($headers, [$this->getAuthorizationHeader()]),
                        $options
                    );
                }
                return $response;
            })
            ->then(function (HttpClient\Response $response): ?array {
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

    private function parseResponse(HttpClient\Response $response): ?array
    {
        $status = $response->status ?? 503;
        if ($status < 200 || $status >= 300) {
            throw SapRequestException::fromResponse($response);
        }

        if ($response->body === '') {
            return null;
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

    private function getAuthorizationHeader(): string
    {
        return 'Authorization: Bearer ' . $this->getAccessToken();
    }

    private function getAccessToken(): string
    {
        if ($this->accessToken !== null) {
            return $this->accessToken;
        }

        $accessTokenCacheKey = $this->getAccessTokenCacheKey();

        /** @var AccessTokenInterface|null $accessToken */
        $accessToken = $this->cache->get($accessTokenCacheKey);
        if ($accessToken !== null && !$accessToken->hasExpired()) {
            $this->accessToken = $accessToken->getToken();
            return $this->accessToken;
        }

        $accessToken = $this->obtainAccessToken();
        $this->cache->set($accessTokenCacheKey, $accessToken);

        $this->accessToken = $accessToken->getToken();
        return $this->accessToken;
    }

    private function invalidateAccessToken(): void
    {
        $this->accessToken = null;
        $this->cache->delete($this->getAccessTokenCacheKey());
    }

    private function getAccessTokenCacheKey(): string
    {
        return sprintf(
            'frontastic.sapCommerceCloud.%s.accessToken.%s',
            $this->getInstanceId(),
            md5(sprintf('%s:%s', $this->clientId, $this->clientSecret))
        );
    }

    private function obtainAccessToken(): AccessTokenInterface
    {
        try {
            $tokenUrl = $this->hostUrl . '/authorizationserver/oauth/token';
            $provider = new GenericProvider([
                'urlAuthorize' => $tokenUrl,
                'urlAccessToken' => $tokenUrl,
                'urlResourceOwnerDetails' => null,
                'clientId' => $this->clientId,
                'clientSecret' => $this->clientSecret,
            ]);

            return $provider->getAccessToken(new ClientCredentials());
        } catch (\Exception $exception) {
            throw new \RuntimeException(
                'Error obtaining SAP Commerce Cloud addess token',
                0,
                $exception
            );
        }
    }
}
