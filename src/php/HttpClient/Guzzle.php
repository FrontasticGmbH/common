<?php

namespace Frontastic\Common\HttpClient;

use Frontastic\Common\HttpClient;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * HTTP client implementation
 */
class Guzzle extends HttpClient
{
    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $guzzleClient;

    /**
     * Optional default headers for each request.
     *
     * @var array
     */
    private $headers = array();

    /**
     * @var Options
     */
    private $defaultOptions;

    public function __construct(Options $defaultOptions = null)
    {
        $this->defaultOptions = $defaultOptions ?: new Options();
        $this->guzzleClient = new \GuzzleHttp\Client();
    }

    public function addDefaultHeaders(array $headers)
    {
        $this->headers = array_merge(
            $this->headers,
            $headers
        );
    }

    public function getDefaultHeaders(): array
    {
        return $this->headers;
    }

    public function setDefaultHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    public function requestAsync(
        string $method,
        string $url,
        string $body = '',
        array $headers = array(),
        Options $options = null
    ): PromiseInterface {
        $options = $options ?: $this->defaultOptions;

        $sensibleHeaders = [];
        foreach (array_merge($this->headers, $headers) as $header) {
            list($key, $value) = explode(':', $header, 2);
            $key = trim($key);
            $value = trim($value);
            $sensibleHeaders[$key] = $value;
        }

        return $this->guzzleClient
            ->requestAsync(
                $method,
                $url,
                [
                    'body' => $body ?: null,
                    'headers' => $sensibleHeaders,
                    'connect_timeout' => $options->timeout,
                    'timeout' => $options->timeout,
                    'http_errors' => false,
                    'allow_redirects' => false,
                ]
            )
            ->then(
                function (ResponseInterface $guzzleResponse) {
                    $headers = [];
                    foreach ($guzzleResponse->getHeaders() as $key => $values) {
                        $key = strtolower($key);
                        $values = array_map('trim', $values);
                        $value = implode(',', $values);

                        $headers[$key] = $value;
                    }

                    return new HttpClient\Response([
                        'status' => $guzzleResponse->getStatusCode(),
                        'headers' => $headers,
                        'body' => $guzzleResponse->getBody()->getContents(),
                        'rawApiOutput' => $guzzleResponse
                    ]);
                },
                function (RequestException $exception) use ($url) {
                    return new HttpClient\Response([
                        'status' => 599,
                        'body' => "Could not connect to server {$url}: " . $exception->getMessage(),
                    ]);
                }
            );
    }
}
