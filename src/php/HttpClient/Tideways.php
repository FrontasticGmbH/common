<?php

namespace Frontastic\Common\HttpClient;

use Frontastic\Common\HttpClient;

class Tideways extends HttpClient
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

    public function request(
        string $method,
        string $url,
        string $body = '',
        array $headers = array(),
        Options $options = null
    ): Response {
        $span = null;
        if (class_exists(\Tideways\Profiler::class)) {
            $span = \Tideways\Profiler::createSpan('http');
        }

        try {
            $response = $this->aggregate->request($method, $url, $body, $headers, $options);
        } catch (\Throwable $e) {
            throw $e;
        } finally {
            if ($span) {
                $span->annotate([
                    'http.url' => $url,
                    'http.status' => $response->status,
                    'http.method' => $method,
                    // 'http.body' => $body,
                    // 'http.headers' => implode("\n", $headers),
                ]);
                $span->finish();
            }
        }

        return $response;
    }
}
