<?php

namespace Frontastic\Common\ProjectApiBundle\Domain;

use Doctrine\Common\Cache\Cache;
use Frontastic\Common\CoreBundle\Domain\Api\FactoryServiceLocator;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\ShopwareBundle\Domain\ProjectApi\CachedShopwareProjectApi;
use Frontastic\Common\ShopwareBundle\Domain\ProjectApi\ShopwareProjectApi;

class DefaultProjectApiFactory implements ProjectApiFactory
{
    /**
     * @var \Frontastic\Common\CoreBundle\Domain\Api\FactoryServiceLocator
     */
    private $serviceLocator;

    /**
     * @var \Doctrine\Common\Cache\Cache
     */
    private $cache;

    public function __construct(FactoryServiceLocator $serviceLocator, Cache $cache)
    {
        $this->serviceLocator = $serviceLocator;
        $this->cache = $cache;
    }

    public function factor(Project $project): ProjectApi
    {
        $productConfig = $project->getConfigurationSection('product');

        switch ($productConfig->engine) {
            case 'commercetools':
                /**
                 * @var \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\ClientFactory $clientFactory
                 * @var \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale\DefaultCommercetoolsLocaleCreatorFactory $localeCreatorFactory
                 */
                $clientFactory = $this->serviceLocator->resolveClientFactory($productConfig->engine);
                $localeCreatorFactory = $this->serviceLocator->resolveLocaleCreatorFactory($productConfig->engine);

                $client = $clientFactory->factorForProjectAndType($project, 'product');

                return new ProjectApi\Commercetools(
                    $client,
                    $localeCreatorFactory->factor($project, $client),
                    $project->languages
                );
            case 'shopware':
                /**
                 * @var \Frontastic\Common\ShopwareBundle\Domain\ClientFactory $clientFactory
                 */
                $clientFactory = $this->serviceLocator->resolveClientFactory($productConfig->engine);

                return new CachedShopwareProjectApi(
                    new ShopwareProjectApi($clientFactory->factor($project)),
                    $this->cache
                );
            default:
                throw new \OutOfBoundsException(
                    "No product API configured for project {$project->name}. " .
                    "Check the provisioned customer configuration in app/config/customers/."
                );
        }
    }
}
