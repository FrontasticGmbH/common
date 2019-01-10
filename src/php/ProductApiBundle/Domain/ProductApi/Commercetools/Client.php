<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools;

use Doctrine\Common\Cache\Cache;
use Frontastic\Common\HttpClient;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client\AccessTokenProvider;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client\ResultSet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException;
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

        $this->httpClient->addDefaultHeaders([sprintf('Authorization: Bearer %s', $this->getAccessToken())]);
    }

    /**
     * @param string $uri
     * @param array $parameters
     * @return \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client\ResultSet
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     */
    public function fetch(string $uri, array $parameters = []): ResultSet
    {
        return new ResultSet($this->request('GET', $uri, $parameters));
    }

    /**
     * @param string $uri
     * @param string $id
     * @param array $parameters
     * @return array
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     */
    public function fetchById(string $uri, string $id, array $parameters = []): array
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
        return $this->request('GET', $uri, $parameters, $headers);
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
        return $this->request('POST', $uri, $parameters, $headers, $body);
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
    private function request(
        string $method,
        string $uri,
        array $parameters = [],
        array $headers = [],
        string $body = ''
    ): array {
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

        $response = $this->httpClient->request(
            $method,
            sprintf('https://api.sphere.io/%s%s%s', $this->projectKey, $uri, $query),
            $body,
            $headers
        );

        if ($response->status >= 400) {
            $message = $response->body;
            if (($errorData = json_decode($message)) &&
                $errorData->message) {
                $message = $this->prepareErrorMessage($errorData);
            }
            throw new RequestException($message, $response->status);
        }

        $data = json_decode($response->body, true);
        if (JSON_ERROR_NONE === json_last_error()) {
            return $data;
        }
        // @todo Proper error handling or silent ignore
        return [];
    }

    private function prepareErrorMessage(\stdClass $errorData): string
    {
        $message = $errorData->message;

        if (isset($errorData->errors)) {
            $message .= "\n" . implode(
                "\n",
                array_map(
                    function ($error) {
                        return sprintf('%s (%s)', $error->message, $error->detailedErrorMessage ?? '');
                    },
                    $errorData->errors
                )
            );
        }

        return $message;
    }

    private function getAccessToken(): string
    {
        if ($this->accessToken) {
            return $this->accessToken;
        }

        $accessToken = $this->cache->fetch('accessToken');
        if ($accessToken && false === $accessToken->hasExpired()) {
            return ($this->accessToken = (string) $accessToken);
        }

        $accessToken = $this->obtainAccessToken();
        $this->cache->save('accessToken', $accessToken);

        return ($this->accessToken = (string) $accessToken);
    }

    private function obtainAccessToken(): AccessToken
    {
        // Scopes: "view_products" or "manage_project"

        $provider = new AccessTokenProvider([
            'clientId' => $this->clientId,
            'clientSecret' => $this->clientSecret,
            'scope' => sprintf('view_products:%s', $this->projectKey),
        ]);

        return $provider->getAccessToken(new ClientCredentials());
    }
}
