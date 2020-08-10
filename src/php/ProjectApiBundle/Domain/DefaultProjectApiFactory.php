<?php

namespace Frontastic\Common\ProjectApiBundle\Domain;

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
use Frontastic\Common\SprykerBundle\Domain\Locale\LocaleCreatorFactory as SprykerLocaleCreatorFactory;
use Frontastic\Common\SprykerBundle\Domain\Project\SprykerProjectApi;
use Frontastic\Common\SprykerBundle\Domain\MapperResolver as SprykerMapperResolver;
use Frontastic\Common\SprykerBundle\Domain\SprykerClientFactory;
use Psr\Container\ContainerInterface;
use OutOfBoundsException;

class DefaultProjectApiFactory implements ProjectApiFactory
{
    private const CONFIGURATION_TYPE_NAME = 'product';

    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function factor(Project $project): ProjectApi
    {
        $productConfig = $project->getConfigurationSection('product');

        switch ($productConfig->engine) {
            case 'commercetools':
                $clientFactory = $this->container->get(CommercetoolsClientFactory::class);
                $localeCreatorFactory = $this->container->get(CommercetoolsLocaleCreatorFactory::class);

                $client = $clientFactory->factorForProjectAndType($project, self::CONFIGURATION_TYPE_NAME);
                return new CommercetoolsProjcetApi(
                    $client,
                    $localeCreatorFactory->factor($project, $client),
                    $project->languages
                );
            case 'sap-commerce-cloud':
                $clientFactory = $this->container->get(SapClientFactory::class);
                $localeCreatorFactory = $this->container->get(SapLocaleCreatorFactory::class);

                $client = $clientFactory->factorForProjectAndType($project, self::CONFIGURATION_TYPE_NAME);
                return new SapProjectApi(
                    $client,
                    $localeCreatorFactory->factor($project, $client),
                    $project->languages
                );
            case 'shopware':
                $clientFactory = $this->container->get(ShopwareClientFactory::class);
                $dataMapper = $this->container->get(DataMapperResolver::class);
                $localeCreatorFactory = $this->container->get(ShopwareLocaleCreatorFactory::class);

                $client = $clientFactory->factorForProjectAndType($project, self::CONFIGURATION_TYPE_NAME);
                return new ShopwareProjectApi(
                    $client,
                    $dataMapper,
                    $localeCreatorFactory->factor($project, $client),
                    $project->languages
                );
            case 'spryker':
                $clientFactory = $this->serviceLocator->get(SprykerClientFactory::class);
                $dataMapper = $this->serviceLocator->get(SprykerMapperResolver::class);
                $localeCreatorFactory = $this->serviceLocator->get(SprykerLocaleCreatorFactory::class);

                $client = $clientFactory->factorForProjectAndType($project, self::CONFIGURATION_TYPE_NAME);
                return new SprykerProjectApi(
                    $client,
                    $dataMapper,
                    $localeCreatorFactory->factor($project, $client),
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
