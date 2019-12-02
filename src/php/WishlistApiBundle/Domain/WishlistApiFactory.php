<?php

namespace Frontastic\Common\WishlistApiBundle\Domain;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\ClientFactory;
use Frontastic\Common\ProductApiBundle\Domain\ProductApiFactory;
use Frontastic\Common\ReplicatorBundle\Domain\Project;

class WishlistApiFactory
{
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
        ProductApiFactory $productApiFactory,
        ClientFactory $commercetoolsClientFactory,
        iterable $decorators
    ) {
        $this->productApiFactory = $productApiFactory;
        $this->commercetoolsClientFactory = $commercetoolsClientFactory;
        $this->decorators = $decorators;
    }

    public function factor(Project $project): WishlistApi
    {
        $config = $project->getConfigurationSection('wishlist');

        switch ($config->engine) {
            case 'commercetools':
                $wishlistApi = new WishlistApi\Commercetools(
                    $this->commercetoolsClientFactory->factorForConfiguration($config),
                    $this->productApiFactory->factor($project)
                );
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
