<?php

namespace Frontastic\Common\ProductApiBundle\Domain;

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
     * @var Commercetools\Locale\CommercetoolsLocaleCreatorFactory
     */
    private $localeCreatorFactory;

    /**
     * @var array
     */
    private $decorators;

    public function __construct(
        ClientFactory $commercetoolsClientFactory,
        Commercetools\Locale\CommercetoolsLocaleCreatorFactory $localeCreatorFactory,
        iterable $decorators = []
    ) {
        $this->commercetoolsClientFactory = $commercetoolsClientFactory;
        $this->localeCreatorFactory = $localeCreatorFactory;
        $this->decorators = $decorators;
    }

    public function factor(Project $project): ProductApi
    {
        $productConfig = $project->getConfigurationSection('product');

        switch ($productConfig->engine) {
            case 'commercetools':
                $client = $this->commercetoolsClientFactory->factorForProjectAndType($project, 'product');
                $productApi = new Commercetools(
                    $client,
                    new Commercetools\Mapper(),
                    $this->localeCreatorFactory->factor($project, $client),
                    $project->defaultLanguage
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
