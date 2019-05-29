<?php

namespace Frontastic\Common\HttpClient;

use \Psr\Log\LoggerInterface;
use Frontastic\Common\HttpClient;

class Tideways extends HttpClient
{
    /**
     * @var HttpClient
     */
    private $aggregate;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string|null
     */
    private $clientIdentifier;

    public function __construct(HttpClient $aggregate, LoggerInterface $logger, $clientIdentifier = null)
    {
        $this->aggregate = $aggregate;
        $this->logger = $logger;
        $this->clientIdentifier = $clientIdentifier;
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
        $traceId = null;
        if (class_exists(\Tideways\Profiler::class)) {
            $span = \Tideways\Profiler::createSpan('http');
            $span->startTimer();

            if ($traceId = \Tideways\Profiler::currentTraceId()) {
                $headers[] = 'X-Correlation-ID: ' . $traceId;
            }
        }

        try {
            $response = $this->aggregate->request($method, $url, $body, $headers, $options);
        } catch (\Exception $e) {
            $this->logger->error(sprintf(
                '[HTTP] Exception: %s %s (%s)',
                $method,
                $url,
                $body ?: '<null>'
            ));

            throw $e;
        } finally {
            if (isset($response) && $response->status >= 500) {
                $this->logger->error(
                    sprintf(
                        '[HTTP] Failed Request: %s %s (%s)',
                        $method,
                        $url,
                        $body ?: '<null>'
                    ),
                    ['status' => $response->status ?? 599, 'CorrelationId' => $traceId]
                );
            }

            if ($span) {
                $span->annotate([
                    'http.url' => $url,
                    'http.status' => $response->status,
                    'http.method' => $method,
                    'http.body' => $body,
                    'http.headers' => implode("\n", $headers),
                    'frontastic.http_client_identifier' => $this->clientIdentifier,
                ]);
                $span->finish();
            }
        }

        return $response;
    }
}
