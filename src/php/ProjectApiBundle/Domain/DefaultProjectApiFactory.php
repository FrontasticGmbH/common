<?php

namespace Frontastic\Common\ProjectApiBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\Api\FactoryServiceLocator;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\ClientFactory as CommercetoolsClientFactory;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale\CommercetoolsLocaleCreatorFactory;
use Frontastic\Common\ProjectApiBundle\Domain\ProjectApi\Commercetools as CommercetoolsProjcetApi;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\SapCommerceCloudBundle\Domain\Locale\SapLocaleCreatorFactory;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapClientFactory;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapProjectApi;
use Frontastic\Common\ShopwareBundle\Domain\ClientFactory as ShopwareClientFactory;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperResolver;
use Frontastic\Common\ShopwareBundle\Domain\Locale\LocaleCreatorFactory as ShopwareLocaleCreatorFactory;
use Frontastic\Common\ShopwareBundle\Domain\ProjectApi\ShopwareProjectApi;
use OutOfBoundsException;

class DefaultProjectApiFactory implements ProjectApiFactory
{
    private const CONFIGURATION_TYPE_NAME = 'product';

    /**
     * @var \Frontastic\Common\CoreBundle\Domain\Api\FactoryServiceLocator
     */
    private $serviceLocator;

    public function __construct(FactoryServiceLocator $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }

    public function factor(Project $project): ProjectApi
    {
        $productConfig = $project->getConfigurationSection('product');

        switch ($productConfig->engine) {
            case 'commercetools':
                $clientFactory = $this->serviceLocator->get(CommercetoolsClientFactory::class);
                $localeCreatorFactory = $this->serviceLocator->get(CommercetoolsLocaleCreatorFactory::class);

                $client = $clientFactory->factorForProjectAndType($project, self::CONFIGURATION_TYPE_NAME);
                return new CommercetoolsProjcetApi(
                    $client,
                    $localeCreatorFactory->factor($project, $client),
                    $project->languages
                );
            case 'sap-commerce-cloud':
                $clientFactory = $this->serviceLocator->get(SapClientFactory::class);
                $localeCreatorFactory = $this->serviceLocator->get(SapLocaleCreatorFactory::class);

                $client = $clientFactory->factorForProjectAndType($project, self::CONFIGURATION_TYPE_NAME);
                return new SapProjectApi(
                    $client,
                    $localeCreatorFactory->factor($project, $client),
                    $project->languages
                );
            case 'shopware':
                $clientFactory = $this->serviceLocator->get(ShopwareClientFactory::class);
                $dataMapper = $this->serviceLocator->get(DataMapperResolver::class);
                $localeCreatorFactory = $this->serviceLocator->get(ShopwareLocaleCreatorFactory::class);

                $client = $clientFactory->factorForProjectAndType($project, self::CONFIGURATION_TYPE_NAME);
                return new ShopwareProjectApi(
                    $client,
                    $localeCreatorFactory->factor($project, $client),
                    $dataMapper,
                    $project->languages
                );
            default:
                throw new OutOfBoundsException(
                    "No product API configured for project {$project->name}. " .
                    "Check the provisioned customer configuration in app/config/customers/."
                );
        }
    }
}
