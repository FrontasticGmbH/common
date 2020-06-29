<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Frontastic\Common\CartApiBundle\Domain\CartApi\Commercetools\Mapper as CommercetoolsCartMapper;
use Frontastic\Common\CoreBundle\Domain\Api\FactoryServiceLocator;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\ClientFactory as CommercetoolsClientFactoryAlias;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale\CommercetoolsLocaleCreatorFactory;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\SapCommerceCloudBundle\Domain\Locale\SapLocaleCreatorFactory;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapCartApi;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapClientFactory;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\CartApi\ShopwareCartApi;
use Frontastic\Common\ShopwareBundle\Domain\ClientFactory as ShopwareClientFactory;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperResolver;
use Frontastic\Common\ShopwareBundle\Domain\Locale\LocaleCreatorFactory as ShopwareLocaleCreatorFactory;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareProjectConfigApiFactory;
use Psr\Log\LoggerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) Factory
 */
class CartApiFactory
{
    private const CONFIGURATION_TYPE_NAME = 'cart';

    /**
     * @var \Frontastic\Common\CoreBundle\Domain\Api\FactoryServiceLocator
     */
    private $factoryServiceLocator;

    /**
     * @var OrderIdGenerator
     */
    private $orderIdGenerator;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var iterable
     */
    private $decorators = [];

    public function __construct(
        FactoryServiceLocator $factoryServiceLocator,
        OrderIdGenerator $orderIdGenerator,
        iterable $decorators,
        LoggerInterface $logger
    ) {
        $this->factoryServiceLocator = $factoryServiceLocator;
        $this->orderIdGenerator = $orderIdGenerator;
        $this->decorators = $decorators;
        $this->logger = $logger;
    }

    public function factor(Project $project): CartApi
    {
        $cartConfig = $project->getConfigurationSection(self::CONFIGURATION_TYPE_NAME);

        switch ($cartConfig->engine) {
            case 'commercetools':
                $clientFactory = $this->factoryServiceLocator->get(CommercetoolsClientFactoryAlias::class);
                $localeCreatorFactory = $this->factoryServiceLocator->get(CommercetoolsLocaleCreatorFactory::class);

                $client = $clientFactory->factorForProjectAndType($project, self::CONFIGURATION_TYPE_NAME);
                $cartApi = new CartApi\Commercetools(
                    $client,
                    $this->factoryServiceLocator->get(CommercetoolsCartMapper::class),
                    $localeCreatorFactory->factor($project, $client),
                    $this->orderIdGenerator,
                    $this->logger
                );
                break;

            case 'sap-commerce-cloud':
                $clientFactory = $this->factoryServiceLocator->get(SapClientFactory::class);
                $localeCreatorFactory = $this->factoryServiceLocator->get(SapLocaleCreatorFactory::class);

                $client = $clientFactory->factorForProjectAndType($project, self::CONFIGURATION_TYPE_NAME);
                $cartApi = new SapCartApi(
                    $client,
                    new SapDataMapper($client),
                    $localeCreatorFactory->factor($project, $client),
                    $this->orderIdGenerator
                );
                break;

            case 'shopware':
                $clientFactory = $this->factoryServiceLocator->get(ShopwareClientFactory::class);
                $localeCreatorFactory = $this->factoryServiceLocator->get(ShopwareLocaleCreatorFactory::class);

                $client = $clientFactory->factorForProjectAndType($project, self::CONFIGURATION_TYPE_NAME);
                $cartApi = new ShopwareCartApi(
                    $client,
                    $this->factoryServiceLocator->get(DataMapperResolver::class),
                    $localeCreatorFactory->factor($project, $client),
                    $project->defaultLanguage,
                    $this->factoryServiceLocator->get(ShopwareProjectConfigApiFactory::class)
                );
                break;

            default:
                throw new \OutOfBoundsException(
                    "No cart API configured for project {$project->name}. " .
                    "Check the provisioned customer configuration in app/config/customers/."
                );
        }

        return new CartApi\LifecycleEventDecorator($cartApi, $this->decorators);
    }
}
