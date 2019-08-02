<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools;

use Doctrine\Common\Cache\Cache;
use Frontastic\Common\HttpClient;
use Frontastic\Common\HttpClient\Response;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client\AccessTokenProvider;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client\ResultSet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use League\OAuth2\Client\Grant\ClientCredentials;
use League\OAuth2\Client\Token\AccessToken;

class Client
{
    private $clientId;

    private $clientSecret;

    private $projectKey;

    private $httpClient;

    private $cache;

    private $accessToken;

    public function __construct(
        string $clientId,
        string $sclientSecret,
        string $projectKey,
        HttpClient $httpClient,
        Cache $cache
    ) {
        $this->clientId = $clientId;
        $this->clientSecret = $sclientSecret;
        $this->projectKey = $projectKey;
        $this->httpClient = $httpClient;
        $this->cache = $cache;
    }

    /**
     * @param string $uri
     * @param array $parameters
     * @return \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client\ResultSet
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     * @deprecated Use `fetchAsync()->wait()` instead
     */
    public function fetch(string $uri, array $parameters = []): ResultSet
    {
        return $this->fetchAsync($uri, $parameters)->wait();
    }

    /**
     * @param string $uri
     * @param array $parameters
     * @return PromiseInterface Containing a {@link ResultSet}
     */
    public function fetchAsync(string $uri, array $parameters = []): PromiseInterface
    {
        return $this->request('GET', $uri, $parameters)
            ->then(function (array $response) {
                return new ResultSet($response);
            });
    }

    /**
     * @param string $uri
     * @param string $id
     * @param array $parameters
     * @return array
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     * @deprecated Use `fetchAsyncById()->wait()` instead
     */
    public function fetchById(string $uri, string $id, array $parameters = []): array
    {
        return $this->fetchAsyncById($uri, $id, $parameters)->wait();
    }

    public function fetchAsyncById(string $uri, string $id, array $parameters = []): PromiseInterface
    {
        return $this->request('GET', sprintf('%s/%s', $uri, $id), $parameters);
    }

    /**
     * @param string $uri
     * @param array $parameters
     * @param array $headers
     * @return array
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     */
    public function get(string $uri, array $parameters = [], array $headers = []): array
    {
        return $this->request('GET', $uri, $parameters, $headers)->wait();
    }

    /**
     * @param string $uri
     * @param array $parameters
     * @param array $headers
     * @param string $body
     * @return array
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     */
    public function post(string $uri, array $parameters = [], array $headers = [], string $body = ''): array
    {
        return $this->request('POST', $uri, $parameters, $headers, $body)->wait();
    }

    /**
     * @param string $uri
     * @param array $parameters
     * @param array $headers
     * @param string $body
     * @return array
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     */
    public function delete(string $uri, array $parameters = [], array $headers = [], string $body = ''): array
    {
        return $this->request('DELETE', $uri, $parameters, $headers, $body)->wait();
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $parameters
     * @param array $headers
     * @param string $body
     * @return array
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     */
    public function request(
        string $method,
        string $uri,
        array $parameters = [],
        array $headers = [],
        string $body = ''
    ): PromiseInterface {
        $query = '?';
        foreach ($parameters as $name => $parameter) {
            if (false === is_array($parameter)) {
                continue;
            }
            foreach ($parameter as $value) {
                $query .= sprintf('%s=%s&', $name, rawurlencode($value));
            }
            unset($parameters[$name]);
        }

        $query .= $parameters ? http_build_query($parameters) : '';

        $headers[] = sprintf('Authorization: Bearer %s', $this->getAccessToken());

        $defaultTimeout = (int)getenv('http_client_timeout');

        return $this->httpClient
            ->requestAsync(
                $method,
                sprintf('https://api.sphere.io/%s%s%s', $this->projectKey, $uri, $query),
                $body,
                $headers,
                new HttpClient\Options([
                    'timeout' => ($method === 'POST' ? max(10, $defaultTimeout) : max(2, $defaultTimeout)),
                ])
            )
            ->then(function (Response $response) {
                if ($response->status >= 400) {
                    throw $this->prepareException($response);
                }

                $data = json_decode($response->body, true);
                if (JSON_ERROR_NONE === json_last_error()) {
                    return $data;
                }

                throw new RequestException(
                    'JSON error: ' . json_last_error_msg(),
                    $response->status
                );
            });
    }

    protected function prepareException(Response $response): \Exception
    {
        $errorData = json_decode($response->body);
        $exception = new RequestException(($errorData->message ?? $response->body) ?: 'Internal Server Error', 503);

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

    private function getAccessToken(): string
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        $cacheId = sprintf(
            'commercetools:accessToken:%s',
            md5($this->clientId . $this->clientSecret . $this->projectKey)
        );

        $accessToken = $this->cache->fetch($cacheId);
        if ($accessToken && false === $accessToken->hasExpired()) {
            return ($this->accessToken = (string)$accessToken);
        }

        try {
            $accessToken = $this->obtainAccessToken();
        } catch (\Exception $e) {
            throw new \RuntimeException(
                'Cannot connect to Commercetools to obtain an access token',
                0,
                $e
            );
        }

        $this->cache->save($cacheId, $accessToken);
        return ($this->accessToken = (string)$accessToken);
    }

    private function obtainAccessToken(): AccessToken
    {
        // Scopes: "view_products" or "manage_project"

        $provider = new AccessTokenProvider([
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret,
            'scope' => sprintf('manage_project:%s', $this->projectKey),
        ]);

        return $provider->getAccessToken(new ClientCredentials());
    }
}
