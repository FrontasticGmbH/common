<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Frontastic\Common\AccountApiBundle\Domain\AccountApi\Commercetools\Mapper as CommercetoolsAccountMapper;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\ClientFactory as CommercetoolsClientFactory;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\SapCommerceCloudBundle\Domain\Locale\SapLocaleCreatorFactory;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapAccountApi;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapClientFactory;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapDataMapper;
use Frontastic\Common\ShopifyBundle\Domain\AccountApi\ShopifyAccountApi;
use Frontastic\Common\ShopifyBundle\Domain\Mapper\ShopifyAccountMapper;
use Frontastic\Common\ShopifyBundle\Domain\ShopifyClientFactory;
use Frontastic\Common\ShopwareBundle\Domain\AccountApi\ShopwareAccountApi;
use Frontastic\Common\ShopwareBundle\Domain\ClientFactory as ShopwareClientFactory;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperResolver;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareProjectConfigApiFactory;
use Frontastic\Common\SprykerBundle\Domain\Account\AccountHelper;
use Frontastic\Common\SprykerBundle\Domain\Account\SprykerAccountApi;
use Frontastic\Common\SprykerBundle\Domain\Account\TokenDecoder;
use Frontastic\Common\SprykerBundle\Domain\Locale\LocaleCreatorFactory as SprykerLocaleCreatorFactory;
use Frontastic\Common\SprykerBundle\Domain\SprykerClientFactory;
use Frontastic\Common\SprykerBundle\Domain\MapperResolver as SprykerMapperResolver;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class AccountApiFactory
{
    private const CONFIGURATION_TYPE_NAME = 'account';

    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $container;

    private $decorators = [];

    private $logger;

    public function __construct(
        ContainerInterface $container,
        iterable $decorators,
        LoggerInterface $logger
    ) {
        $this->container = $container;
        $this->decorators = $decorators;
        $this->logger = $logger;
    }

    public function factor(Project $project): AccountApi
    {
        $accountConfig = $project->getConfigurationSection(self::CONFIGURATION_TYPE_NAME);

        switch ($accountConfig->engine) {
            case 'commercetools':
                $client = $this->container
                    ->get(CommercetoolsClientFactory::class)
                    ->factorForProjectAndType($project, self::CONFIGURATION_TYPE_NAME);
                $commercetoolsAccountMapper = $this->container->get(CommercetoolsAccountMapper::class);

                $logger = $this->logger;

                $accountApi = new AccountApi\Commercetools($client, $commercetoolsAccountMapper, $logger);
                break;
            case 'sap-commerce-cloud':
                $client = $this->container
                    ->get(SapClientFactory::class)
                    ->factorForProjectAndType($project, self::CONFIGURATION_TYPE_NAME);

                $accountApi = new SapAccountApi(
                    $client,
                    $this->container
                        ->get(SapLocaleCreatorFactory::class)
                        ->factor($project, $client),
                    new SapDataMapper($client)
                );
                break;
            case 'shopware':
                $client = $this->container
                    ->get(ShopwareClientFactory::class)
                    ->factorForProjectAndType($project, self::CONFIGURATION_TYPE_NAME);

                $accountApi = new ShopwareAccountApi(
                    $client,
                    $this->container->get(DataMapperResolver::class),
                    $this->container->get(ShopwareProjectConfigApiFactory::class)
                );

                break;
            case 'shopify':
                $clientFactory = $this->container->get(ShopifyClientFactory::class);
                $client = $clientFactory->factorForProjectAndType($project, self::CONFIGURATION_TYPE_NAME);
                $accountMapper = $this->container->get(ShopifyAccountMapper::class);

                $accountApi = new ShopifyAccountApi(
                    $client,
                    $accountMapper
                );

                break;
            case 'spryker':
                $dataMapper = $this->container->get(SprykerMapperResolver::class);
                $localeCreatorFactory = $this->container->get(SprykerLocaleCreatorFactory::class);
                $accountHelper = $this->container->get(AccountHelper::class);
                $tokenDecoder = $this->container->get(TokenDecoder::class);

                $client = $this->container
                    ->get(SprykerClientFactory::class)
                    ->factorForProjectAndType($project, self::CONFIGURATION_TYPE_NAME);

                $accountApi = new SprykerAccountApi(
                    $client,
                    $dataMapper,
                    $accountHelper,
                    $tokenDecoder,
                    $localeCreatorFactory->factor($project, $client)
                );

                break;
            default:
                throw new \OutOfBoundsException(
                    "No account API configured for project {$project->name}. " .
                    "Check the provisioned customer configuration in app/config/customers/."
                );
        }

        return new AccountApi\LifecycleEventDecorator($accountApi, $this->decorators);
    }
}
