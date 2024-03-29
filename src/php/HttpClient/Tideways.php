<?php

namespace Frontastic\Common\HttpClient;

use Frontastic\Common\CoreBundle\Domain\Tracing;
use Frontastic\Common\HttpClient;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Log\LoggerInterface;

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
     * @var ?string
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

    public function requestAsync(
        string $method,
        string $url,
        string $body = '',
        array $headers = array(),
        Options $options = null
    ): PromiseInterface {
        $span = null;
        $traceId = Tracing::getCurrentTraceId();

        if (class_exists(\Tideways\Profiler::class)) {
            $span = \Tideways\Profiler::createSpan('http');
            $span->startTimer();

            \Tideways\Profiler::setCustomVariable(Tracing::CORRELATION_ID_HEADER_KEY, $traceId);
        }

        $headers[] = Tracing::CORRELATION_ID_HEADER_KEY. ': ' . $traceId;

        return $this->aggregate
            ->requestAsync($method, $url, $body, $headers, $options)
            ->then(
                function ($response) use ($method, $url, $body, $headers, $span, $traceId) {
                    $status = $response->status ?? 599;

                    if ($status >= 500) {
                        $this->logger->error(
                            sprintf(
                                '[HTTP] Failed Request: %s %s (%s)',
                                $method,
                                $url,
                                substr($body, 0, 1000) ?: '<null>'
                            ),
                            ['status' => $status, 'CorrelationId' => $traceId]
                        );
                    }

                    $this->finishSpan($span, $url, $status, $method, $body, $headers);

                    return $response;
                },
                function (\Exception $reason) use ($method, $url, $body, $headers, $span) {
                    $this->logger->error(sprintf(
                        '[HTTP] Exception: %s %s (%s)',
                        $method,
                        $url,
                        substr($body, 0, 1000) ?: '<null>'
                    ));

                    $this->finishSpan($span, $url, 599, $method, $body, $headers);

                    throw $reason;
                }
            );
    }

    private function finishSpan($span, $url, $status, $method, $body, $headers)
    {
        if ($span) {
            $span->annotate([
                'http.url' => $url,
                'http.status' => $status,
                'http.method' => $method,
                'http.body' => $body,
                'http.headers' => implode("\n", $headers),
                'frontastic.http_client_identifier' => $this->clientIdentifier,
            ]);
            $span->finish();
        }
    }
}
