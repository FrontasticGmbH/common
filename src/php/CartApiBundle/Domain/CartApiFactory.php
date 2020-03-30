<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\ClientFactory;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale\CommercetoolsLocaleCreatorFactory;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Mapper;
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
     * @var ClientFactory
     */
    private $commercetoolsClientFactory;

    /**
     * @var CommercetoolsLocaleCreatorFactory
     */
    private $commercetoolsLocaleCreatorFactory;

    /**
     * @var SapClientFactory
     */
    private $sapClientFactory;

    /**
     * @var SapLocaleCreatorFactory
     */
    private $sapLocaleCreatorFactory;

    /**
     * @var OrderIdGenerator
     */
    private $orderIdGenerator;

    /**
     * @var iterable
     */
    private $decorators = [];

    public function __construct(
        ClientFactory $commercetoolsClientFactory,
        CommercetoolsLocaleCreatorFactory $commercetoolsLocaleCreatorFactory,
        SapClientFactory $sapClientFactory,
        SapLocaleCreatorFactory $sapLocaleCreatorFactory,
        OrderIdGenerator $orderIdGenerator,
        iterable $decorators
    ) {
        $this->commercetoolsClientFactory = $commercetoolsClientFactory;
        $this->commercetoolsLocaleCreatorFactory = $commercetoolsLocaleCreatorFactory;
        $this->sapClientFactory = $sapClientFactory;
        $this->sapLocaleCreatorFactory = $sapLocaleCreatorFactory;
        $this->orderIdGenerator = $orderIdGenerator;
        $this->decorators = $decorators;
    }

    public function factor(Project $project): CartApi
    {
        $cartConfig = $project->getConfigurationSection('cart');

        switch ($cartConfig->engine) {
            case 'commercetools':
                $client = $this->commercetoolsClientFactory->factorForProjectAndType($project, 'cart');
                $cartApi = new CartApi\Commercetools(
                    $client,
                    new Mapper(),
                    $this->commercetoolsLocaleCreatorFactory->factor($project, $client),
                    $this->orderIdGenerator
                );
                break;

            case 'sap-commerce-cloud':
                $client = $this->sapClientFactory->factorForProjectAndType($project, 'product');
                $cartApi = new SapCartApi(
                    $client,
                    $this->sapLocaleCreatorFactory->factor($project, $client),
                    new SapDataMapper($client),
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
