<?php

namespace Frontastic\Common\HttpClient;

use Frontastic\Common\HttpClient;
use Symfony\Component\DependencyInjection\ContainerInterface;

class Factory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function create($clientIdentifier, Configuration $configuration = null): HttpClient
    {
        if ($configuration === null) {
            $configuration = new Configuration();
        }
        if ($configuration->options === null) {
            $configuration->options = new Options();
        }

        $httpClient = new Stream();
        if ($configuration->defaultHeaders !== null) {
            $httpClient->addDefaultHeaders($configuration->defaultHeaders);
        }

        if ($configuration->signatureSecret !== null) {
            $httpClient = new Signing($httpClient, $configuration->signatureSecret);
        }

        if ($configuration->collectProfiling) {
            $httpClient = new Tideways(
                $httpClient,
                $this->container->get('logger'),
                $clientIdentifier
            );
        }

        if ($configuration->collectStats) {
            $httpClient = new StatsD(
                $clientIdentifier,
                $this->container->get('Domnikl\Statsd\Client'),
                $httpClient
            );
        }

        return $httpClient;
    }
}
