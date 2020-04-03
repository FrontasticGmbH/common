<?php

namespace Frontastic\Common\HttpClient;

use Frontastic\Common\HttpClient;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Log\LoggerInterface;

/**
 * Logs HTTP request to our logger together with some metadata.
 */
class Logger extends HttpClient
{
    /**
     * @var HttpClient
     */
    private $aggregate;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(HttpClient $aggregate, LoggerInterface $httpClientLogger)
    {
        $this->aggregate = $aggregate;
        $this->logger = $httpClientLogger;
    }

    public function addDefaultHeaders(array $headers)
    {
        return $this->aggregate->addDefaultHeaders($headers);
    }

    public function requestAsync(
        string $method,
        string $url,
        string $body = '',
        array $headers = [],
        Options $options = null
    ): PromiseInterface
    {
        $start = microtime(true);

        return $this->aggregate
            ->requestAsync($method, $url, $body, $headers, $options)
            ->then(function ($response) use ($start, $method, $url) {
                $time = microtime(true) - $start;

                $host = parse_url($url, PHP_URL_HOST);
                $this->logger->info(
                    sprintf(
                        'Request against %s took %dms',
                        $host,
                        $time * 1000
                    ),
                    [
                        'outgoingRequest' => [
                            'host' => $host,
                            'path' => parse_url($url, PHP_URL_PATH),
                            'method' => $method,
                            'responseTime' => $time,
                            'statusCode' => $response->status,
                        ],
                    ]
                );

                return $response;
            });
    }
}
