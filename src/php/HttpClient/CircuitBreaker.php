<?php

namespace Frontastic\Common\HttpClient;

use Frontastic\Common\HttpClient;
use GuzzleHttp\Promise\PromiseInterface;

/**
 * We are not using the Guzzle middleware here, because we want to consider
 * every GET request exceeding a certain threshold a failure.
 */
class CircuitBreaker extends HttpClient
{
    /**
     * @var HttpClient
     */
    private $aggregate;

    /**
     * @var Ackintosh\Ganesha
     */
    private $circuitBreaker;

    public function __construct(
        HttpClient $aggregate,
        \Ackintosh\Ganesha $circuitBreaker
    ) {
        $this->aggregate = $aggregate;
        $this->circuitBreaker = $circuitBreaker;
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
        $service = parse_url($url, PHP_URL_HOST);

        // Everything but GET requests are always passed through, in the hope
        // that some cart updates, for example, will stiull work while we take
        // the load of the backend.
        //
        // it might be that exactly those POST requests overload the backend
        // but we consider the likelyhood of this low
        if ($method === 'GET' &&
            !$this->circuitBreaker->isAvailable($service)) {
            return new \GuzzleHttp\Promise\RejectedPromise(
                "Service $service temporarily disabled by circuit breaker because " .
                "of high error rates or slow responses."
            );
        }

        return $this->aggregate
            ->requestAsync($method, $url, $body, $headers, $options)
            ->then(
                // All GET requests over 500ms are considered a failure (too
                // slow for a sensible site). On top of this all requests
                // responding with a status code >= 400 are considered a
                // failure.
                function ($response) use ($method, $service, $start) {
                    $elapsedTime = microtime(true) - $start;
                    if ($method === 'GET' && $elapsedTime > 0.5) {
                        $this->circuitBreaker->failure($service);
                    } elseif ($response->status >= 400) {
                        $this->circuitBreaker->failure($service);
                    } else {
                        $this->circuitBreaker->success($service);
                    }

                    return $response;
                },
                function (\Exception $reason) use ($service) {
                    $this->circuitBreaker->failure($service);
                    throw $reason;
                }
            );
    }
}
