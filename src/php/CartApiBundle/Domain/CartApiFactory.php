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

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) Factory
 */
class CartApiFactory
{
    /**
     * @var \Frontastic\Common\CoreBundle\Domain\Api\FactoryServiceLocator
     */
    private $factoryServiceLocator;

    /**
     * @var OrderIdGenerator
     */
    private $orderIdGenerator;

    /**
     * @var iterable
     */
    private $decorators = [];

    public function __construct(
        FactoryServiceLocator $factoryServiceLocator,
        OrderIdGenerator $orderIdGenerator,
        iterable $decorators
    ) {
        $this->factoryServiceLocator = $factoryServiceLocator;
        $this->orderIdGenerator = $orderIdGenerator;
        $this->decorators = $decorators;
    }

    public function factor(Project $project): CartApi
    {
        $cartConfig = $project->getConfigurationSection('cart');

        switch ($cartConfig->engine) {
            case 'commercetools':
                $clientFactory = $this->factoryServiceLocator->get(CommercetoolsClientFactoryAlias::class);
                $localeCreatorFactory = $this->factoryServiceLocator->get(CommercetoolsLocaleCreatorFactory::class);

                $client = $clientFactory->factorForProjectAndType($project, 'cart');
                $cartApi = new CartApi\Commercetools(
                    $client,
                    $this->factoryServiceLocator->get(CommercetoolsCartMapper::class),
                    $localeCreatorFactory->factor($project, $client),
                    $this->orderIdGenerator
                );
                break;

            case 'sap-commerce-cloud':
                $clientFactory = $this->factoryServiceLocator->get(SapClientFactory::class);
                $localeCreatorFactory = $this->factoryServiceLocator->get(SapLocaleCreatorFactory::class);

                $client = $clientFactory->factorForProjectAndType($project, 'product');
                $cartApi = new SapCartApi(
                    $client,
                    new SapDataMapper($client),
                    $localeCreatorFactory->factor($project, $client),
                    $this->orderIdGenerator
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
