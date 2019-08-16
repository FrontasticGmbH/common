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
     * @var Options
     */
    private $defaultOptions;

    public function __construct(LoggerInterface $logger, Options $defaultOptions = null)
    {
        $this->logger = $logger;

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

        if ($configuration->signatureSecret !== null) {
            $httpClient = new Signing($httpClient, $configuration->signatureSecret);
        }

        if ($configuration->collectStats) {
            $httpClient = new Logstash($httpClient);
        }

        return $httpClient;
    }
}
