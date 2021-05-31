<?php

namespace Frontastic\Common\HttpClient;

use Domnikl\Statsd\Client;
use Frontastic\Common\HttpClient;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Factory
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \Redis
     */
    private $redis;

    /**
     * @var Options
     */
    private $defaultOptions;

    public function __construct(LoggerInterface $httpClientLogger, \Redis $redis = null, Options $defaultOptions = null)
    {
        $this->logger = $httpClientLogger;
        $this->redis = $redis;

        if ($defaultOptions === null) {
            $defaultOptions = new Options();
        }
        $this->defaultOptions = $defaultOptions;
    }

    public function create($clientIdentifier, Configuration $configuration = null): HttpClient
    {
        if ($configuration === null) {
            $configuration = new Configuration();
        }
        if ($configuration->options === null) {
            $configuration->options = clone $this->defaultOptions;
        }

        $httpClient = new Guzzle($configuration->options);
        if ($configuration->defaultHeaders !== null) {
            $httpClient->addDefaultHeaders($configuration->defaultHeaders);
        }

        if ($this->redis) {
            // Always use a circuit breaker
            $circuitBreaker = \Ackintosh\Ganesha\Builder::withRateStrategy()
                // Use redis to share circuit breaker state across applicaion
                // servers
                ->adapter(new \Ackintosh\Ganesha\Storage\Adapter\Redis($this->redis))
                // Use custom storage keys so they are customer specific if
                // multiple customers use the same redis backend:
                ->storageKeys(new CircuitBreaker\StorageKeys())
                // The interval in time (seconds) that evaluate the thresholds
                ->timeWindow(60)
                // The failure rate threshold in percentage that changes
                // CircuitBreaker's state to `OPEN`
                ->failureRateThreshold(50)
                // The interval (seconds) to change CircuitBreaker's state from
                // `OPEN` to `HALF_OPEN`
                ->intervalToHalfOpen(5)
                // The minimum number of requests to detect failures
                ->minimumRequests(10)
                ->build();

            $circuitBreaker->subscribe(function ($event, $service, $message) {
                switch ($event) {
                    case \Ackintosh\Ganesha::EVENT_TRIPPED:
                        $this->logger->error(
                            "Service $service disabled for now by circuit breaker."
                        );
                        break;
                    case \Ackintosh\Ganesha::EVENT_CALMED_DOWN:
                        $this->logger->error(
                            "Service $service back up."
                        );
                        break;
                    default:
                        break;
                }
            });
            $httpClient = new CircuitBreaker($httpClient, $circuitBreaker);
        }

        if ($configuration->signatureSecret !== null) {
            $httpClient = new Signing($httpClient, $configuration->signatureSecret);
        }

        if ($configuration->collectStats) {
            $httpClient = new Logger($httpClient, $this->logger);
        }

        return $httpClient;
    }
}
