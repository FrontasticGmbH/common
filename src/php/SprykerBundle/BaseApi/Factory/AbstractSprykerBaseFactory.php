<?php

namespace Frontastic\Common\SprykerBundle\BaseApi\Factory;

use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\SprykerBundle\Domain\Exception\ExceptionFactoryInterface;
use Frontastic\Common\SprykerBundle\Domain\MapperResolver;
use Frontastic\Common\SprykerBundle\Domain\SprykerClient;
use GuzzleHttp\ClientInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

abstract class AbstractSprykerBaseFactory
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;

    /**
     * SprykerCatalogSearchSuggestionsApiFactory constructor.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param \Frontastic\Common\ReplicatorBundle\Domain\Project $project
     *
     * @return mixed|\Frontastic\Common\SprykerBundle\BaseApi\SprykerApiBase
     */
    abstract public function factor(Project $project);

    /**
     * @param array $config
     *
     * @return \Frontastic\Common\SprykerBundle\Domain\SprykerClient
     */
    protected function createSprykerClient(array $config): SprykerClient
    {
        $sprykerConfig = $config['spryker'];
        $sprykerEndpoint = is_array($sprykerConfig)
            ? $sprykerConfig['endpoint']
            : $sprykerConfig->endpoint;

        return new SprykerClient(
            $this->getGuzzleClient(),
            $sprykerEndpoint,
            $this->getExceptionFactory()
        );
    }

    /**
     * @return \GuzzleHttp\ClientInterface
     */
    protected function getGuzzleClient(): ClientInterface
    {
        return $this->container->get('spryker.guzzle.client');
    }

    /**
     * @return \Frontastic\Common\SprykerBundle\Domain\MapperResolver
     */
    protected function getMapperResolver(): MapperResolver
    {
        return $this->container->get(MapperResolver::class);
    }

    /**
     * @return \Frontastic\Common\SprykerBundle\Domain\Exception\ExceptionFactoryInterface
     */
    private function getExceptionFactory(): ExceptionFactoryInterface
    {
        return $this->container->get('spryker.client.exception.factory');
    }
}
