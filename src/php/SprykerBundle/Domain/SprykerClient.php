<?php

namespace Frontastic\Common\SprykerBundle\Domain;

use Frontastic\Common\HttpClient;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException;
use Frontastic\Common\SprykerBundle\Domain\Exception\ExceptionFactoryInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Promise\PromiseInterface;
use WoohooLabs\Yang\JsonApi\Response\JsonApiResponse;

class SprykerClient implements SprykerClientInterface
{
    private const SPRYKER_LANGUAGE_HEADER = 'Accept-Language';

    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $projectKey;

    /**
     * @var array
     */
    private $defaultHeaders = [
        'Accept' => 'application/json'
    ];

    /**
     * @var \Frontastic\Common\SprykerBundle\Domain\Exception\ExceptionFactoryInterface
     */
    private $exceptionFactory;

    /**
     * @param HttpClient $client
     * @param string $url
     * @param string $projectKey
     * @param \Frontastic\Common\SprykerBundle\Domain\Exception\ExceptionFactoryInterface $exceptionFactory
     */
    public function __construct(
        HttpClient $client,
        string $url,
        string $projectKey,
        ExceptionFactoryInterface $exceptionFactory
    ) {
        $this->httpClient = $client;
        $this->url = $url;
        $this->projectKey = $projectKey;
        $this->exceptionFactory = $exceptionFactory;
    }

    public function getProjectKey(): string
    {
        return $this->projectKey;
    }

    public function forLanguage(string $language): SprykerClientInterface
    {
        $this->defaultHeaders[self::SPRYKER_LANGUAGE_HEADER] = $language;
        return $this;
    }

    /**
     * @param string $endpoint
     * @param array $headers
     * @param string $mode
     * @return JsonApiResponse|PromiseInterface<JsonApiResponse>
     */
    public function get(string $endpoint, array $headers = [], string $mode = self::MODE_SYNC)
    {
        if ($mode === self::MODE_ASYNC) {
            return $this->sendAsyncRequest(SprykerClientInterface::METHOD_GET, $endpoint, $headers);
        }

        return $this->sendRequest(SprykerClientInterface::METHOD_GET, $endpoint, $headers);
    }

    /**
     * @inheritDoc
     */
    public function head(string $endpoint, array $headers = [])
    {
        return $this->sendRequest(SprykerClientInterface::METHOD_HEAD, $endpoint, $headers);
    }

    /**
     * @param string $endpoint
     * @param array $headers
     * @param string $body
     * @param string $mode
     * @return JsonApiResponse|PromiseInterface<JsonApiResponse>
     */
    public function post(
        string $endpoint,
        array $headers = [],
        string $body = '',
        string $mode = self::MODE_SYNC
    ) {
        if ($mode === self::MODE_ASYNC) {
            return $this->sendAsyncRequest(SprykerClientInterface::METHOD_POST, $endpoint, $headers, $body);
        }

        return $this->sendRequest(SprykerClientInterface::METHOD_POST, $endpoint, $headers, $body);
    }

    /**
     * @param string $endpoint
     * @param array $headers
     * @param string $body
     * @return JsonApiResponse
     */
    public function patch(string $endpoint, array $headers = [], string $body = ''): JsonApiResponse
    {
        return $this->sendRequest(SprykerClientInterface::METHOD_PATCH, $endpoint, $headers, $body);
    }

    /**
     * @param string $endpoint
     * @param array $headers
     * @return JsonApiResponse
     */
    public function delete(string $endpoint, array $headers = []): JsonApiResponse
    {
        return $this->sendRequest(SprykerClientInterface::METHOD_DELETE, $endpoint, $headers);
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $headers
     * @param string $body
     * @return JsonApiResponse
     */
    private function sendRequest(
        string $method,
        string $endpoint,
        array $headers = [],
        string $body = ''
    ): JsonApiResponse {
        return $this->sendAsyncRequest($method, $endpoint, $headers, $body)->wait();
    }

    /**
     * @param string $method
     * @param string $endpoint
     * @param array $headers
     * @param string $body
     *
     * @return PromiseInterface<JsonApiResponse>
     */
    private function sendAsyncRequest(
        string $method,
        string $endpoint,
        array $headers = [],
        string $body = ''
    ): PromiseInterface {
        $fullUrl = $this->getFullAddress($endpoint);

        return $this->httpClient
            ->requestAsync($method, $fullUrl, $body, $this->buildRequestHeaders($headers))
            ->then(
                function (HttpClient\Response $response) {
                    if ($response->status >= 400) {
                        throw $this->prepareException($response);
                    }
                    return new JsonApiResponse($response->rawApiOutput);
                },
                function (RequestException $exception) use ($endpoint) {
                    if ($exception instanceof ServerException) {
                        throw $this->exceptionFactory->createFromGuzzleServerException($exception, $endpoint);
                    }

                    /** @type ClientException $exception */
                    throw $this->exceptionFactory->createFromGuzzleClientException($exception, $endpoint);
                }
            );
    }

    /**
     * @param string $endpoint
     * @return string
     */
    private function getFullAddress(string $endpoint): string
    {
        return sprintf(
            '%s/%s',
            rtrim($this->url, '/'),
            ltrim($endpoint, '/')
        );
    }

    /**
     * @param string[] $additionalHeaders
     *
     * @return string[]
     */
    private function buildRequestHeaders(array $additionalHeaders): array
    {
        $nonFormattedHeaders = array_merge($this->defaultHeaders, $additionalHeaders);

        $headers = [];
        foreach ($nonFormattedHeaders as $header => $value) {
            if (is_array($value) && is_callable([$this, $value[0]])) {
                $value = $this->{$value[0]}();
            }
            $headers[] = sprintf('%s: %s', $header, $value);
        }

        return $headers;
    }

    protected function prepareException(HttpClient\Response $response): \Exception
    {
        $errorData = json_decode($response->body);
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
