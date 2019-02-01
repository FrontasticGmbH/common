<?php

namespace Frontastic\Common\ProductApiBundle\Domain;

use Doctrine\Common\Cache\Cache;
use Frontastic\Common\HttpClient\Stream;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox;
use Frontastic\Common\ReplicatorBundle\Domain\Customer;

class DefaultProductApiFactory implements ProductApiFactory
{
    /**
     * @var
     */
    private $container;

    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    private $cache;

    /**
     * @var array
     */
    private $decorators;

    public function __construct($container, Cache $cache, iterable $decorators = [])
    {
        $this->container = $container;
        $this->cache = $cache;
    }

    public function factor(Customer $customer): ProductApi
    {
        try {
            return new LifecycleEventDecorator(
                $this->factorFromConfiguration($customer->configuration),
                $this->decorators
            );
        } catch (\OutOfBoundsException $e) {
            throw new \OutOfBoundsException(
                "No product API configured for customer {$customer->name}. " .
                "Check the provisioned customer configuration in app/config/customers/.",
                0,
                $e
            );
        }
    }

    public function factorFromConfiguration(array $config): ProductApi
    {
        switch (true) {
            case isset($config['commercetools']):
                // @todo These objects should come from the DI Container
                $httpClient = new Stream();

                return new Commercetools(
                    new Commercetools\Client(
                        $config['commercetools']->clientId,
                        $config['commercetools']->clientSecret,
                        $config['commercetools']->projectKey,
                        $httpClient,
                        $this->cache
                    ),
                    new Commercetools\Mapper(
                        $config['commercetools']->localeOverwrite ?? null
                    ),
                    $config['commercetools']->localeOverwrite ?? null
                );
            case isset($config['semknox']):
                // @todo These objects should come from the DI Container
                $httpClient = new Stream();

                $searchIndexClients = [];
                $dataStudioClients = [];
                foreach ($config['semknox']->languages as $language => $languageConfig) {
                    $searchIndexClients[$language] = new Semknox\SearchIndexClient(
                        $languageConfig['host'],
                        $languageConfig['customerId'],
                        $languageConfig['apiKey'],
                        $httpClient
                    );
                    $dataStudioClients[$language] = new Semknox\DataStudioClient(
                        $languageConfig['projectId'],
                        $languageConfig['accessToken'],
                        $httpClient
                    );
                }

                return new Semknox($searchIndexClients, $dataStudioClients);
            default:
                throw new \OutOfBoundsException('No valid API configuration found');
        }
    }
}
