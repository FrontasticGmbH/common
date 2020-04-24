<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain;

use Exception;
use Frontastic\Common\HttpClient;
use Frontastic\Common\HttpClient\Response;
use Frontastic\Common\ShopwareBundle\Domain\Exception\RequestException;
use Frontastic\Common\ShopwareBundle\Domain\Exception\ResourceNotFoundException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

class Client implements ClientInterface
{
    private const SHOPWARE_LANGUAGE_HEADER = 'sw-language-id';
    private const SHOPWARE_CURRENCY_HEADER = 'sw-currency-id';
    private const SHOPWARE_CONTEXT_TOKEN_HEADER = 'sw-context-token';

    /**
     * @var \Frontastic\Common\HttpClient
     */
    private $httpClient;

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $baseUri;

    private $defaultHeaders = [
        'Accept' => '*/*',
        'Content-Type' => 'application/json',
        'sw-access-key' => ['getApiKey'],
    ];

    public function __construct(HttpClient $httpClient, string $apiKey, string $baseUri)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
        $this->baseUri = $baseUri;
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
            $body = json_encode($body);
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

    protected function getApiKey(): string
    {
        return $this->apiKey;
    }

    protected function prepareException(Response $response): Exception
    {
        $errorData = json_decode($response->body);
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
}
