<?php

namespace Frontastic\Common\ProductApiBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\Api\FactoryServiceLocator;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\SapCommerceCloudBundle\Domain\Locale\SapLocaleCreatorFactory;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapClientFactory;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapDataMapper;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapProductApi;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\ShopwareProductApi;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DefaultProductApiFactory implements ProductApiFactory
{
    /**
     * @var \Frontastic\Common\CoreBundle\Domain\Api\FactoryServiceLocator
     */
    private $serviceLocator;
    private $commercetoolsClientFactory;

    /**
     * @var Commercetools\Locale\CommercetoolsLocaleCreatorFactory
     */
    private $commercetoolsLocaleCreatorFactory;

    /**
     * @var SapClientFactory
     */
    private $sapClientFactory;

    /**
     * @var SapLocaleCreatorFactory
     */
    private $sapLocaleCreatorFactory;

    /**
     * @var array
     */
    private $decorators;

    public function __construct(
        FactoryServiceLocator $serviceLocator,
        iterable $decorators = []
    ) {
        $this->decorators = $decorators;
        $this->serviceLocator = $serviceLocator;
    }

    public function factor(Project $project): ProductApi
    {
        $productConfig = $project->getConfigurationSection('product');

        switch ($productConfig->engine) {
            case 'commercetools':
                /**
                 * @var \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\ClientFactory $clientFactory
                 * @var \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale\DefaultCommercetoolsLocaleCreatorFactory $localeCreatorFactory
                 * @var \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Mapper $dataMapper
                 */
                $clientFactory = $this->serviceLocator->resolveClientFactory($productConfig->engine);
                $dataMapper = $this->serviceLocator->resolveDataMapper($productConfig->engine);
                $localeCreatorFactory = $this->serviceLocator->resolveLocaleCreatorFactory($productConfig->engine);

                $client = $clientFactory->factorForProjectAndType($project, 'product');

                $productApi = new Commercetools(
                    $client,
                    $dataMapper,
                    $localeCreatorFactory->factor($project, $client),
                    $project->defaultLanguage
                );
                break;
            case 'sap-commerce-cloud':
                $client = $this->sapClientFactory->factorForProjectAndType($project, 'product');
                $productApi = new SapProductApi(
                    $client,
                    $this->sapLocaleCreatorFactory->factor($project, $client),
                    new SapDataMapper($client)
                );
                break;
            case 'shopware':
                /**
                 * @var \Frontastic\Common\ShopwareBundle\Domain\ClientFactory $clientFactory
                 * @var \Frontastic\Common\ShopwareBundle\Domain\DataMapperResolver $dataMapper
                 * @var \Frontastic\Common\ShopwareBundle\Domain\Locale\LocaleCreatorFactory $localeCreatorFactory
                 */
                $clientFactory = $this->serviceLocator->resolveClientFactory($productConfig->engine);
                $dataMapper = $this->serviceLocator->resolveDataMapper($productConfig->engine);
                $localeCreatorFactory = $this->serviceLocator->resolveLocaleCreatorFactory($productConfig->engine);

                $client = $clientFactory->factor($project);

                $productApi = new ShopwareProductApi(
                    $client,
                    $dataMapper,
                    $localeCreatorFactory->factor($project, $client)
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
