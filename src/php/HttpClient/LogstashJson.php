<?php

namespace Frontastic\Common\HttpClient;

use Frontastic\Common\HttpClient;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * Logs request times to a JSON file for analysis in kibana.
 *
 * The catwalk needs to be configured to read this JSON file.
 */
class LogstashJson extends HttpClient
{
    /**
     * @var HttpClient
     */
    private $aggregate;

    public function __construct(HttpClient $aggregate)
    {
        $this->aggregate = $aggregate;
    }

    public function addDefaultHeaders(array $headers)
    {
        return $this->aggregate->addDefaultHeaders($headers);
    }

    public function requestAsync(
        string $method,
        string $url,
        string $body = '',
        array $headers = array(),
        Options $options = null
    ): PromiseInterface {
        $start = microtime(true);

        return $this->aggregate
            ->requestAsync($method, $url, $body, $headers, $options)
            ->then(function ($response) use ($start, $method, $url) {
                file_put_contents(
                    '/var/log/frontastic/responses_json.log',
                    json_encode(
                        [
                            'date' => date('r'),
                            'host' => parse_url($url, PHP_URL_HOST),
                            'path' => parse_url($url, PHP_URL_PATH),
                            'method' => $method,
                            'responsetime' => microtime(true) - $start,
                            'responsecode' => $response->status,
                        ]
                    ),
                    FILE_APPEND
                );

                return $response;
            });
    }
}
