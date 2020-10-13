<?php

namespace Frontastic\Common\WishlistApiBundle\Domain;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\ClientFactory;
use Frontastic\Common\ProductApiBundle\Domain\ProductApiFactory;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\SprykerBundle\Domain\Account\AccountHelper;
use Frontastic\Common\SprykerBundle\Domain\Locale\LocaleCreatorFactory as SprykerLocaleCreatorFactory;
use Frontastic\Common\SprykerBundle\Domain\SprykerClientFactory;
use Frontastic\Common\SprykerBundle\Domain\MapperResolver as SprykerMapperResolver;
use Frontastic\Common\SprykerBundle\Domain\Wishlist\SprykerWishlistApi;
use Frontastic\Common\WishlistApiBundle\Domain\WishlistApi\NoWishlistApi;
use Psr\Container\ContainerInterface;

class WishlistApiFactory
{
    private const CONFIGURATION_TYPE_NAME = 'wishlist';

    /**
     * @var \Psr\Container\ContainerInterface
     */
    private $container;

    /**
     * @var ProductApiFactory
     */
    private $productApiFactory;

    /**
     * @var ClientFactory
     */
    private $commercetoolsClientFactory;

    private $decorators = [];

    public function __construct(
        ContainerInterface $container,
        ProductApiFactory $productApiFactory,
        ClientFactory $commercetoolsClientFactory,
        iterable $decorators
    ) {
        $this->container = $container;
        $this->productApiFactory = $productApiFactory;
        $this->commercetoolsClientFactory = $commercetoolsClientFactory;
        $this->decorators = $decorators;
    }

    public function factor(Project $project): WishlistApi
    {
        $config = $project->getConfigurationSection(self::CONFIGURATION_TYPE_NAME);

        switch ($config->engine) {
            case 'commercetools':
                $wishlistApi = new WishlistApi\Commercetools(
                    $this->commercetoolsClientFactory->factorForProjectAndType($project, self::CONFIGURATION_TYPE_NAME),
                    $this->productApiFactory->factor($project)
                );
                break;

            case 'spryker':
                $dataMapper = $this->container->get(SprykerMapperResolver::class);
                $localeCreatorFactory = $this->container->get(SprykerLocaleCreatorFactory::class);
                $accountHelper = $this->container->get(AccountHelper::class);

                $client = $this->container
                    ->get(SprykerClientFactory::class)
                    ->factorForProjectAndType($project, self::CONFIGURATION_TYPE_NAME);

                $wishlistApi = new SprykerWishlistApi(
                    $client,
                    $dataMapper,
                    $accountHelper,
                    $localeCreatorFactory->factor($project, $client)
                );

                break;

            case 'no-wishlist':
                $wishlistApi = new NoWishlistApi();
                break;

            default:
                throw new \OutOfBoundsException(
                    "No wishlist API configured for project {$project->name}. " .
                    "Check the provisioned customer configuration in app/config/customers/."
                );
        }

        return new WishlistApi\LifecycleEventDecorator($wishlistApi, $this->decorators);
    }
}
