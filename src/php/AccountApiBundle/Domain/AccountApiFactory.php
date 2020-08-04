<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Frontastic\Common\AccountApiBundle\Domain\AccountApi\Commercetools\Mapper as CommercetoolsAccountMapper;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\ClientFactory as CommercetoolsClientFactory;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\SapCommerceCloudBundle\Domain\Locale\SapLocaleCreatorFactory;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapAccountApi;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapClientFactory;
use Frontastic\Common\SapCommerceCloudBundle\Domain\SapDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\AccountApi\ShopwareAccountApi;
use Frontastic\Common\ShopwareBundle\Domain\ClientFactory as ShopwareClientFactory;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperResolver;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareProjectConfigApiFactory;

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
            default:
                throw new \OutOfBoundsException(
                    "No account API configured for project {$project->name}. " .
                    "Check the provisioned customer configuration in app/config/customers/."
                );
        }

        return new AccountApi\LifecycleEventDecorator($accountApi, $this->decorators);
    }
}
