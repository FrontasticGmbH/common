<?php

namespace Frontastic\Common\WishlistApiBundle\Domain;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\ClientFactory;
use Frontastic\Common\ProductApiBundle\Domain\ProductApiFactory;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\WishlistApiBundle\Domain\WishlistApi\NoWishlistApi;

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
                    $this->commercetoolsClientFactory->factorForProjectAndType($project, 'wishlist'),
                    $this->productApiFactory->factor($project)
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
