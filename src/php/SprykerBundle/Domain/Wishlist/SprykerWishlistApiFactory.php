<?php

namespace Frontastic\Common\SprykerBundle\Domain\Wishlist;

use Frontastic\Common\ReplicatorBundle\Domain\Customer;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\WishlistApiBundle\Domain\WishlistApi;
use Frontastic\Common\SprykerBundle\BaseApi\Factory\AbstractSprykerBaseFactory;
use Frontastic\Common\SprykerBundle\Domain\Account\AccountHelper;
use Frontastic\Common\SprykerBundle\Domain\MapperResolver;
use Frontastic\Common\SprykerBundle\Domain\SprykerClient;
use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SprykerWishlistApiFactory extends AbstractSprykerBaseFactory
{
    /**
     * @param Project $project
     * @return WishlistApi|SprykerWishlistApi
     */
    public function factor(Project $project): WishlistApi
    {
        return new SprykerWishlistApi(
            $this->createSprykerClient($project->configuration),
            $this->getMapperResolver(),
            $this->getAccountHelper(),
            WishlistConstants::RESOURCES_MAIN
        );
    }

    /**
     * @return AccountHelper
     */
    private function getAccountHelper(): AccountHelper
    {
        return $this->container->get(AccountHelper::class);
    }
}
