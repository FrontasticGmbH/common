<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\ClientFactory;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Mapper;
use Frontastic\Common\ReplicatorBundle\Domain\Project;

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
     * @var OrderIdGenerator
     */
    private $orderIdGenerator;

    /**
     * @var iterable
     */
    private $decorators = [];

    public function __construct(
        ClientFactory $commercetoolsClientFactory,
        OrderIdGenerator $orderIdGenerator,
        iterable $decorators
    ) {
        $this->commercetoolsClientFactory = $commercetoolsClientFactory;
        $this->orderIdGenerator = $orderIdGenerator;
        $this->decorators = $decorators;
    }

    public function factor(Project $project): CartApi
    {
        $cartConfig = $project->getConfigurationSection('cart');

        switch ($cartConfig->engine) {
            case 'commercetools':
                $cartApi = new CartApi\Commercetools(
                    $this->commercetoolsClientFactory->factorForProjectAndType($project, 'cart'),
                    new Mapper(),
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
