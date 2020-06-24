<?php

namespace Frontastic\Common\SprykerBundle\Domain\Product;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApiFactory;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\SprykerBundle\BaseApi\Factory\AbstractSprykerBaseFactory;
use Frontastic\Common\SprykerBundle\Domain\Product\Decorator\ProductApiLifecycleEventDecorator;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SprykerProductApiFactory extends AbstractSprykerBaseFactory implements ProductApiFactory
{
    /**
     * @var iterable
     */
    private $decorators;

    /**
     * SprykerProductApiFactory constructor.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param iterable $decorators
     */
    public function __construct(ContainerInterface $container, iterable $decorators = [])
    {
        parent::__construct($container);
        $this->decorators = $decorators;
    }

    /**
     * @param Project $project
     *
     * @return ProductApi
     */
    public function factor(Project $project): ProductApi
    {
        return new ProductApiLifecycleEventDecorator(
            $this->factorFromConfiguration($project->configuration),
            $this->decorators
        );
    }

    /**
     * @param array $config
     *
     * @return ProductApi
     */
    public function factorFromConfiguration(array $config): ProductApi
    {
        return new SprykerProductApi(
            $this->createSprykerJsonClient($config),
            $this->getMapperResolver()
        );
    }
}
