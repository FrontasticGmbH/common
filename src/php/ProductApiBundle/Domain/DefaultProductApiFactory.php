<?php

namespace Frontastic\Common\ProductApiBundle\Domain;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools as CommercetoolsProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\ClientFactory as CommercetoolsClientFactory;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale\CommercetoolsLocaleCreatorFactory;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Mapper as CommercetoolsDataMapper;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\EnabledFacetService;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApiFactory;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\SapCommerceCloudBundle\Domain\Locale\SapLocaleCreatorFactory;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapClientFactory;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapDataMapper;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapProductApi;
use Frontastic\Common\ShopifyBundle\Domain\Mapper\ShopifyProductMapper;
use Frontastic\Common\ShopifyBundle\Domain\ProductApi\ShopifyProductApi;
use Frontastic\Common\ShopifyBundle\Domain\ShopifyClientFactory;
use Frontastic\Common\ShopwareBundle\Domain\ClientFactory as ShopwareClientFactory;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperResolver as ShopwareDataMapperResolver;
use Frontastic\Common\ShopwareBundle\Domain\Locale\LocaleCreatorFactory as ShopwareLocaleCreatorFactory;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\ShopwareProductApi;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareProjectConfigApiFactory;
use Frontastic\Common\SprykerBundle\Domain\Locale\LocaleCreatorFactory as SprykerLocaleCreatorFactory;
use Frontastic\Common\SprykerBundle\Domain\MapperResolver as SprykerMapperResolver;
use Frontastic\Common\SprykerBundle\Domain\Product\SprykerProductApi;
use Frontastic\Common\SprykerBundle\Domain\SprykerClientFactory;
use Frontastic\Common\SprykerBundle\Domain\SprykerUrlAppender;
use Psr\Container\ContainerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DefaultProductApiFactory implements ProductApiFactory
{
    private const CONFIGURATION_TYPE_NAME = 'product';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var EnabledFacetService
     */
    private $enabledFacetService;

    /**
     * @var ProductSearchApiFactory
     */
    private $productSearchApiFactory;

    /**
     * @var array
     */
    private $decorators;

    public function __construct(
        ContainerInterface $container,
        EnabledFacetService $enabledFacetService,
        ProductSearchApiFactory $productSearchApiFactory,
        iterable $decorators = []
    ) {
        $this->container = $container;
        $this->enabledFacetService = $enabledFacetService;
        $this->productSearchApiFactory = $productSearchApiFactory;
        $this->decorators = $decorators;
    }

    public function factor(Project $project): ProductApi
    {
        $productConfig = $project->getConfigurationSection(self::CONFIGURATION_TYPE_NAME);
        $productSearchApi = $this->productSearchApiFactory->factor($project);

        switch ($productConfig->engine) {
            case 'commercetools':
                $clientFactory = $this->container->get(CommercetoolsClientFactory::class);
                $dataMapper = $this->container->get(CommercetoolsDataMapper::class);
                $localeCreatorFactory = $this->container->get(CommercetoolsLocaleCreatorFactory::class);

                $client = $clientFactory->factorForProjectAndType($project, self::CONFIGURATION_TYPE_NAME);

                $productApi = new CommercetoolsProductApi(
                    $client,
                    $dataMapper,
                    $localeCreatorFactory->factor($project, $client),
                    $this->enabledFacetService,
                    $productSearchApi,
                    $project->languages,
                    $project->defaultLanguage
                );
                break;
            case 'sap-commerce-cloud':
                $clientFactory = $this->container->get(SapClientFactory::class);
                $localeCreatorFactory = $this->container->get(SapLocaleCreatorFactory::class);

                $client = $clientFactory->factorForProjectAndType($project, self::CONFIGURATION_TYPE_NAME);
                $productApi = new SapProductApi(
                    $client,
                    $localeCreatorFactory->factor($project, $client),
                    new SapDataMapper($client),
                    $productSearchApi
                );
                break;
            case 'shopware':
                $clientFactory = $this->container->get(ShopwareClientFactory::class);
                $dataMapper = $this->container->get(ShopwareDataMapperResolver::class);
                $localeCreatorFactory = $this->container->get(ShopwareLocaleCreatorFactory::class);

                $client = $clientFactory->factorForProjectAndType($project, self::CONFIGURATION_TYPE_NAME);

                $productApi = new ShopwareProductApi(
                    $client,
                    $localeCreatorFactory->factor($project, $client),
                    $dataMapper,
                    $this->enabledFacetService,
                    $this->container->get(ShopwareProjectConfigApiFactory::class),
                    $productSearchApi,
                    $project->defaultLanguage
                );
                break;
            case 'shopify':
                $clientFactory = $this->container->get(ShopifyClientFactory::class);
                $client = $clientFactory->factorForProjectAndType($project, self::CONFIGURATION_TYPE_NAME);
                $productMapper = $this->container->get(ShopifyProductMapper::class);

                $productApi = new ShopifyProductApi(
                    $client,
                    $productSearchApi,
                    $productMapper
                );

                break;
            case 'spryker':
                $clientFactory = $this->container->get(SprykerClientFactory::class);
                $dataMapper = $this->container->get(SprykerMapperResolver::class);
                $localeCreatorFactory = $this->container->get(SprykerLocaleCreatorFactory::class);
                $urlAppender = $this->container->get(SprykerUrlAppender::class);

                $client = $clientFactory->factorForProjectAndType($project, self::CONFIGURATION_TYPE_NAME);

                $productApi = new SprykerProductApi(
                    $client,
                    $dataMapper,
                    $localeCreatorFactory->factor($project, $client),
                    $urlAppender,
                    $productSearchApi,
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
