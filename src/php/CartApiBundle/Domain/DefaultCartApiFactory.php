<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Frontastic\Common\AccountApiBundle\Domain\AccountApiFactory;
use Frontastic\Common\CartApiBundle\Domain\CartApi\Commercetools\Mapper as CommercetoolsCartMapper;
use Frontastic\Common\CartApiBundle\Domain\CartApi\Commercetools\Options;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\ClientFactory as CommercetoolsClientFactoryAlias;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale\CommercetoolsLocaleCreatorFactory;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\SapCommerceCloudBundle\Domain\Locale\SapLocaleCreatorFactory;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapCartApi;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapClientFactory;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapDataMapper;
use Frontastic\Common\ShopifyBundle\Domain\CartApi\ShopifyCartApi;
use Frontastic\Common\ShopifyBundle\Domain\Mapper\ShopifyAccountMapper;
use Frontastic\Common\ShopifyBundle\Domain\Mapper\ShopifyProductMapper;
use Frontastic\Common\ShopifyBundle\Domain\ShopifyClientFactory;
use Frontastic\Common\ShopwareBundle\Domain\CartApi\ShopwareCartApi;
use Frontastic\Common\ShopwareBundle\Domain\ClientFactory as ShopwareClientFactory;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperResolver;
use Frontastic\Common\ShopwareBundle\Domain\Locale\LocaleCreatorFactory as ShopwareLocaleCreatorFactory;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareProjectConfigApiFactory;
use Frontastic\Common\SprykerBundle\Domain\Account\AccountHelper;
use Frontastic\Common\SprykerBundle\Domain\Locale\LocaleCreatorFactory as SprykerLocaleCreatorFactory;
use Frontastic\Common\SprykerBundle\Domain\Cart\Request\CustomerCartRequestData;
use Frontastic\Common\SprykerBundle\Domain\Cart\SprykerCart\CustomerCart;
use Frontastic\Common\SprykerBundle\Domain\Cart\SprykerCart\GuestCart;
use Frontastic\Common\SprykerBundle\Domain\Cart\SprykerCartApi;
use Frontastic\Common\SprykerBundle\Domain\SprykerClientFactory;
use Frontastic\Common\SprykerBundle\Domain\MapperResolver as SprykerMapperResolver;
use Frontastic\Common\SprykerBundle\Domain\SprykerUrlAppender;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) Factory
 */
class DefaultCartApiFactory implements CartApiFactory
{
    private const CONFIGURATION_TYPE_NAME = 'cart';

    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $container;

    /**
     * @var AccountApiFactory
     */
    private $accountApiFactory;

    /**
     * @var OrderIdGeneratorV2|OrderIdGenerator
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

    /**
     * @param ContainerInterface $container
     * @param AccountApiFactory $accountApiFactory
     * @param OrderIdGeneratorV2|OrderIdGenerator $orderIdGenerator
     * @param iterable $decorators
     * @param LoggerInterface $logger
     */
    public function __construct(
        ContainerInterface $container,
        AccountApiFactory $accountApiFactory,
        object $orderIdGenerator, // BC for OrderIdGenerator
        iterable $decorators,
        LoggerInterface $logger
    ) {
        $this->container = $container;
        $this->accountApiFactory = $accountApiFactory;
        $this->decorators = $decorators;
        $this->logger = $logger;

        $this->setOrderIdGenerator($orderIdGenerator);
    }

    public function factor(Project $project): CartApi
    {
        $cartConfig = $project->getConfigurationSection(self::CONFIGURATION_TYPE_NAME);

        switch ($cartConfig->engine) {
            case 'commercetools':
                $clientFactory = $this->container->get(CommercetoolsClientFactoryAlias::class);
                $localeCreatorFactory = $this->container->get(CommercetoolsLocaleCreatorFactory::class);

                $client = $clientFactory->factorForProjectAndType($project, self::CONFIGURATION_TYPE_NAME);
                $cartApi = new CartApi\Commercetools(
                    $client,
                    $this->container->get(CommercetoolsCartMapper::class),
                    $localeCreatorFactory->factor($project, $client),
                    $this->orderIdGenerator,
                    $this->logger,
                    (isset($cartConfig->options) ? new Options($cartConfig->options) : null)
                );
                break;

            case 'sap-commerce-cloud':
                $clientFactory = $this->container->get(SapClientFactory::class);
                $localeCreatorFactory = $this->container->get(SapLocaleCreatorFactory::class);

                $client = $clientFactory->factorForProjectAndType($project, self::CONFIGURATION_TYPE_NAME);
                $cartApi = new SapCartApi(
                    $client,
                    new SapDataMapper($client),
                    $localeCreatorFactory->factor($project, $client),
                    $this->orderIdGenerator
                );
                break;

            case 'shopware':
                $clientFactory = $this->container->get(ShopwareClientFactory::class);
                $localeCreatorFactory = $this->container->get(ShopwareLocaleCreatorFactory::class);

                $client = $clientFactory->factorForProjectAndType($project, self::CONFIGURATION_TYPE_NAME);
                $cartApi = new ShopwareCartApi(
                    $client,
                    $localeCreatorFactory->factor($project, $client),
                    $this->container->get(DataMapperResolver::class),
                    $this->container->get(ShopwareProjectConfigApiFactory::class),
                    $project->defaultLanguage
                );
                break;

            case 'shopify':
                $clientFactory = $this->container->get(ShopifyClientFactory::class);
                $client = $clientFactory->factorForProjectAndType($project, self::CONFIGURATION_TYPE_NAME);
                $accountApi = $this->accountApiFactory->factor($project);
                $productMapper = $this->container->get(ShopifyProductMapper::class);
                $accountMapper = $this->container->get(ShopifyAccountMapper::class);

                $cartApi = new ShopifyCartApi(
                    $client,
                    $accountApi,
                    $productMapper,
                    $accountMapper,
                    $this->logger,
                );

                break;

            case 'spryker':
                $dataMapper = $this->container->get(SprykerMapperResolver::class);
                $localeCreatorFactory = $this->container->get(SprykerLocaleCreatorFactory::class);
                $accountHelper = $this->container->get(AccountHelper::class);
                $urlAppender = $this->container->get(SprykerUrlAppender::class);

                $client = $this->container
                    ->get(SprykerClientFactory::class)
                    ->factorForProjectAndType($project, self::CONFIGURATION_TYPE_NAME);

                $localeCreator = $localeCreatorFactory->factor($project, $client);

                $customerCartRequestData = new CustomerCartRequestData(
                    $cartConfig->priceMode,
                    $cartConfig->currency,
                    $cartConfig->shop
                );

                $guestCart = new GuestCart(
                    $client,
                    $dataMapper,
                    $localeCreator,
                    $accountHelper,
                    [],
                    $project->defaultLanguage
                );

                $customerCart = new CustomerCart(
                    $client,
                    $dataMapper,
                    $localeCreator,
                    $accountHelper,
                    $customerCartRequestData,
                    [],
                    $project->defaultLanguage
                );

                $cartApi = new SprykerCartApi(
                    $client,
                    $dataMapper,
                    $accountHelper,
                    $guestCart,
                    $customerCart,
                    $localeCreator,
                    $urlAppender,
                    [],
                    $project->defaultLanguage
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

    private function setOrderIdGenerator(object $orderIdGenerator): void
    {
        if ($orderIdGenerator instanceof OrderIdGenerator) {
            $orderIdGenerator = new OrderIdGeneratorV2Adapter($orderIdGenerator);
        }

        if (!($orderIdGenerator instanceof OrderIdGeneratorV2)) {
            throw new \TypeError('orderIdGenerator is not an instance of ' . OrderIdGeneratorV2::class);
        }

        $this->orderIdGenerator = $orderIdGenerator;
    }
}
