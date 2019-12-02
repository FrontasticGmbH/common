<?php

namespace Frontastic\Common\ProductApiBundle\Domain;

use Frontastic\Catwalk\ApiCoreBundle\Domain\Context;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\ClientFactory;
use Frontastic\Common\ReplicatorBundle\Domain\Project;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DefaultProductApiFactory implements ProductApiFactory
{
    /**
     * @var ClientFactory
     */
    private $commercetoolsClientFactory;

    /**
     * @var array
     */
    private $decorators;

    public function __construct(ClientFactory $commercetoolsClientFactory, iterable $decorators = [])
    {
        $this->commercetoolsClientFactory = $commercetoolsClientFactory;
        $this->decorators = $decorators;
    }

    public function factor(Project $project): ProductApi
    {
        $productConfig = $project->getConfigurationSection('product');

        switch ($productConfig->engine) {
            case 'commercetools':
                $commercetoolsConfig = $project->getConfigurationSection('commercetools');
                $productApi = new Commercetools(
                    $this->commercetoolsClientFactory->factorForProjectAndType($project, 'product'),
                    new Commercetools\Mapper(
                        $commercetoolsConfig->localeOverwrite ?? null
                    ),
                    $project->defaultLanguage,
                    $commercetoolsConfig->localeOverwrite ?? null
                );
                break;

            default:
                throw new \OutOfBoundsException(
                    "No product API configured for project {$project->name}. " .
                    "Check the provisioned customer configuration in app/config/customers/."
                );
        }

        return new ProductApi\LifecycleEventDecorator($productApi, $this->decorators);
    }
}
