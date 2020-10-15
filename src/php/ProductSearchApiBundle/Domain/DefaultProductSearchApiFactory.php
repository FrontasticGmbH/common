<?php

namespace Frontastic\Common\ProductSearchApiBundle\Domain;

use Frontastic\Common\FindologicBundle\Domain\FindologicClientFactory;
use Frontastic\Common\FindologicBundle\Domain\ProductSearchApi\FindologicProductSearchApi;
use Frontastic\Common\FindologicBundle\Domain\ProductSearchApi\QueryValidator;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\ClientFactory as CommercetoolsClientFactory;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale\CommercetoolsLocaleCreatorFactory;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Mapper as CommercetoolsDataMapper;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\EnabledFacetService;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApi\Commercetools as CommercetoolsProductSearchApi;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\SapCommerceCloudBundle\Domain\Locale\SapLocaleCreatorFactory;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapClientFactory;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapDataMapper;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapProductSearchApi;
use Frontastic\Common\ShopifyBundle\Domain\Mapper\ShopifyProductMapper;
use Frontastic\Common\ShopifyBundle\Domain\ProductSearchApi\ShopifyProductSearchApi;
use Frontastic\Common\ShopifyBundle\Domain\ShopifyClientFactory;
use Frontastic\Common\ShopwareBundle\Domain\ClientFactory as ShopwareClientFactory;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperResolver as ShopwareDataMapperResolver;
use Frontastic\Common\ShopwareBundle\Domain\Locale\LocaleCreatorFactory as ShopwareLocaleCreatorFactory;
use Frontastic\Common\ShopwareBundle\Domain\ProductSearchApi\ShopwareProductSearchApi;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareProjectConfigApiFactory;
use Frontastic\Common\SprykerBundle\Domain\Locale\LocaleCreatorFactory as SprykerLocaleCreatorFactory;
use Frontastic\Common\SprykerBundle\Domain\MapperResolver as SprykerMapperResolver;
use Frontastic\Common\SprykerBundle\Domain\ProductSearch\SprykerProductSearchApi;
use Frontastic\Common\SprykerBundle\Domain\SprykerClientFactory;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Frontastic\Common\FindologicBundle\Domain\FindologicMapperFactory;

class DefaultProductSearchApiFactory implements ProductSearchApiFactory
{
    private const CONFIGURATION_TYPE_NAME_PRODUCT = 'product';
    private const CONFIGURATION_TYPE_NAME_PRODUCT_SEARCH = 'productSearch';

    /** @var ContainerInterface */
    private $container;

    /** @var EnabledFacetService */
    private $enabledFacetService;

    /** @var array */
    private $decorators;

    /** @var array */
    private $productDecorators;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        ContainerInterface $container,
        EnabledFacetService $enabledFacetService,
        LoggerInterface $logger,
        iterable $decorators = [],
        iterable $productDecorators = []
    ) {
        $this->container = $container;
        $this->enabledFacetService = $enabledFacetService;
        $this->decorators = $decorators;
        $this->productDecorators = $productDecorators;
        $this->logger = $logger;
    }

    public function factor(Project $project): ProductSearchApi
    {
        $productSearchConfig = $this->getProductSearchConfigForProject($project);
        $vendorConfig = $project->getConfigurationSection($productSearchConfig->engine);

        $productSearchApi = $this->factorFromConfiguration($project, $productSearchConfig, $vendorConfig);
        $productSearchApi = new LifecycleEventDecorator($productSearchApi, $this->decorators);
        $productSearchApi = new LegacyLifecycleEventDecorator($productSearchApi, $this->productDecorators);

        return $productSearchApi;
    }

    private function factorFromConfiguration(Project $project, object $productSearchConfig, object $engineConfig)
    {
        switch ($productSearchConfig->engine) {
            case 'commercetools':
                $clientFactory = $this->container->get(CommercetoolsClientFactory::class);
                $dataMapper = $this->container->get(CommercetoolsDataMapper::class);
                $localeCreatorFactory = $this->container->get(CommercetoolsLocaleCreatorFactory::class);

                $client = $clientFactory->factorForConfigs($productSearchConfig, $engineConfig);

                $productSearchApi = new CommercetoolsProductSearchApi(
                    $client,
                    $dataMapper,
                    $localeCreatorFactory->factor($project, $client),
                    $this->enabledFacetService,
                    $project->languages,
                    $project->defaultLanguage
                );
                break;
            case 'sap-commerce-cloud':
                $clientFactory = $this->container->get(SapClientFactory::class);
                $localeCreatorFactory = $this->container->get(SapLocaleCreatorFactory::class);

                $client = $clientFactory->factorForConfigs($productSearchConfig, $engineConfig);

                $productSearchApi = new SapProductSearchApi(
                    $client,
                    $localeCreatorFactory->factor($project, $client),
                    new SapDataMapper($client),
                    $project->languages
                );
                break;
            case 'shopware':
                $clientFactory = $this->container->get(ShopwareClientFactory::class);
                $dataMapper = $this->container->get(ShopwareDataMapperResolver::class);
                $localeCreatorFactory = $this->container->get(ShopwareLocaleCreatorFactory::class);

                $client = $clientFactory->factorForConfigs($productSearchConfig, $engineConfig);

                $productSearchApi = new ShopwareProductSearchApi(
                    $client,
                    $localeCreatorFactory->factor($project, $client),
                    $dataMapper,
                    $this->enabledFacetService,
                    $this->container->get(ShopwareProjectConfigApiFactory::class),
                    $project->languages,
                    $project->defaultLanguage
                );
                break;
            case 'shopify':
                $clientFactory = $this->container->get(ShopifyClientFactory::class);
                $client = $clientFactory->factorForConfigs($productSearchConfig, $engineConfig);
                $productMapper = $this->container->get(ShopifyProductMapper::class);

                $productSearchApi = new ShopifyProductSearchApi(
                    $client,
                    $productMapper
                );

                break;
            case 'spryker':
                $clientFactory = $this->container->get(SprykerClientFactory::class);
                $dataMapper = $this->container->get(SprykerMapperResolver::class);
                $localeCreatorFactory = $this->container->get(SprykerLocaleCreatorFactory::class);

                $client = $clientFactory->factorForConfigs($productSearchConfig, $engineConfig);

                $productSearchApi = new SprykerProductSearchApi(
                    $client,
                    $dataMapper,
                    $localeCreatorFactory->factor($project, $client),
                    $project->languages
                );

                break;
            case 'findologic':
                $clientFactory = $this->container->get(FindologicClientFactory::class);
                $mapperFactory = $this->container->get(FindologicMapperFactory::class);
                $queryValidator = $this->container->get(QueryValidator::class);

                $client = $clientFactory->factorForConfigs($project->languages, $productSearchConfig, $engineConfig);
                $dataMapper = $mapperFactory->factorForConfigs($productSearchConfig, $engineConfig);

                $originalDataSourceConfig =
                    (object)($productSearchConfig->originalDataSource ?? $engineConfig->originalDataSource);
                $originalDataSourceVendorConfig = $project->getConfigurationSection($originalDataSourceConfig->engine);

                $originalDataSource = $this->factorFromConfiguration(
                    $project,
                    $originalDataSourceConfig,
                    $originalDataSourceVendorConfig
                );

                $productSearchApi = new FindologicProductSearchApi(
                    $client,
                    $originalDataSource,
                    $dataMapper,
                    $queryValidator,
                    $this->logger,
                    $project->languages
                );
                break;
            default:
                throw new \OutOfBoundsException(
                    "No product search API configured for project {$project->name}. " .
                    "Check the provisioned customer configuration in app/config/customers/."
                );
        }

        return $productSearchApi;
    }

    /**
     * Try to fetch the "productSearch" configuration for this project and default to "project" configuration
     * for BC reasons.
     */
    private function getProductSearchConfigForProject(Project $project): object
    {
        $config = $project->getConfigurationSection(self::CONFIGURATION_TYPE_NAME_PRODUCT_SEARCH);

        if (!empty((array)$config)) {
            return $config;
        }

        return $project->getConfigurationSection(self::CONFIGURATION_TYPE_NAME_PRODUCT);
    }
}
