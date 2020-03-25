<?php

namespace Frontastic\Common\ProductApiBundle\Domain;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\ShopwareProductApi;
use Symfony\Component\DependencyInjection\ServiceLocator;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DefaultProductApiFactory implements ProductApiFactory
{
    private const SERVICE_ID_TEMPLATE_CLIENT_FACTORY = '%s.client_factory';
    private const SERVICE_ID_TEMPLATE_DATA_MAPPER = '%s.data_mapper';
    private const SERVICE_ID_TEMPLATE_LOCALE_CREATOR_FACTORY = '%s.locale_creator_factory';

    /**
     * @var \Symfony\Component\DependencyInjection\ServiceLocator
     */
    private $serviceLocator;

    /**
     * @var array
     */
    private $decorators;

    public function __construct(
        ServiceLocator $serviceLocator,
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
                $clientFactory = $this->resolveClientFactory($productConfig->engine);
                $dataMapper = $this->resolveDataMapper($productConfig->engine);
                $localeCreatorFactory = $this->resolveLocaleCreatorFactory($productConfig->engine);

                $client = $clientFactory->factorForProjectAndType($project, 'product');

                $productApi = new Commercetools(
                    $client,
                    $dataMapper,
                    $localeCreatorFactory->factor($project, $client),
                    $project->defaultLanguage
                );
                break;
            case 'shopware':
                /**
                 * @var \Frontastic\Common\ShopwareBundle\Domain\ClientFactory $clientFactory
                 * @var \Frontastic\Common\ShopwareBundle\Domain\DataMapperResolver $dataMapper
                 * @var \Frontastic\Common\ShopwareBundle\Domain\Locale\LocaleCreatorFactory $localeCreatorFactory
                 */
                $clientFactory = $this->resolveClientFactory($productConfig->engine);
                $dataMapper = $this->resolveDataMapper($productConfig->engine);
                $localeCreatorFactory = $this->resolveLocaleCreatorFactory($productConfig->engine);

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

    private function resolveClientFactory(string $engine)
    {
        $service = $this->buildClientFactoryServiceId($engine);

        return $this->serviceLocator->get($service);
    }

    private function buildClientFactoryServiceId(string $engine): string
    {
        return sprintf(self::SERVICE_ID_TEMPLATE_CLIENT_FACTORY, $engine);
    }

    private function resolveDataMapper(string $engine)
    {
        $service = $this->buildDataMapperServiceId($engine);

        return $this->serviceLocator->get($service);
    }

    private function buildDataMapperServiceId(string $engine): string
    {
        return sprintf(self::SERVICE_ID_TEMPLATE_DATA_MAPPER, $engine);
    }

    private function resolveLocaleCreatorFactory(string $engine)
    {
        $service = $this->buildLocaleCreatorFactoryServiceId($engine);

        return $this->serviceLocator->get($service);
    }

    private function buildLocaleCreatorFactoryServiceId(string $engine): string
    {
        return sprintf(self::SERVICE_ID_TEMPLATE_LOCALE_CREATOR_FACTORY, $engine);
    }
}
