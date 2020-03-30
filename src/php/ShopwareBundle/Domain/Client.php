<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain;

use Exception;
use Frontastic\Common\HttpClient;
use Frontastic\Common\HttpClient\Response;
use Frontastic\Common\ShopwareBundle\Domain\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

class Client implements ClientInterface
{
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

    public function __construct(HttpClient $httpClient, string $apiKey, string $baseUri)
    {
        $this->httpClient = $httpClient;
        $this->apiKey = $apiKey;
        $this->baseUri = $baseUri;
    }

    public function get(string $uri, array $parameters = [], array $headers = []): PromiseInterface
    {
        return $this->request(self::METHOD_GET, $uri, $parameters, $headers);
    }

    public function patch(string $uri, array $parameters = [], array $headers = [], $body = null): PromiseInterface
    {
        return $this->request(self::METHOD_PATCH, $uri, $parameters, $headers, $body);
    }

    public function post(string $uri, array $parameters = [], array $headers = [], $body = null): PromiseInterface
    {
        return $this->request(self::METHOD_POST, $uri, $parameters, $headers, $body);
    }

    public function put(string $uri, array $parameters = [], array $headers = [], $body = null): PromiseInterface
    {
        return $this->request(self::METHOD_PUT, $uri, $parameters, $headers, $body);
    }

    public function delete(string $uri, array $parameters = [], array $headers = []): PromiseInterface
    {
        return $this->request(self::METHOD_DELETE, $uri, $parameters, $headers);
    }

    public function request(
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

        $headers[] = 'Accept: */*';
        $headers[] = 'Content-Type: application/json';
        $headers[] = sprintf('sw-access-key: %s', $this->apiKey);

        $defaultTimeout = (int)getenv('http_client_timeout');

        if ($body !== null && is_array($body)) {
            $body = json_encode($body);
        }

        return $this->httpClient
            ->requestAsync(
                $method,
                (string)$uri,
                (string)$body,
                $headers,
                new HttpClient\Options([
                    'timeout' => ($method === self::METHOD_POST ? max(10, $defaultTimeout) : max(5, $defaultTimeout)),
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

    protected function prepareException(Response $response): Exception
    {
        $errorData = json_decode($response->body);
        $exception = new RequestException(
            ($errorData->message ?? $response->body) ?: 'Internal Server Error',
            $response->status ?? 503
        );

        if (isset($errorData->errors)) {
            foreach ($errorData->errors as $error) {
                $exception = new RequestException(
                    $error->title ?? 'Unknown error',
                    $error->status ?? 503,
                    $exception
                );
            }
        }

        return $exception;
    }

    private function buildWithQueryString(UriInterface $uri, array $parameters): UriInterface
    {
        return Uri::withQueryValues($uri, $parameters);
    }
}
