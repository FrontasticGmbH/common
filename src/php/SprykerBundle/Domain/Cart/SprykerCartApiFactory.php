<?php

namespace Frontastic\Common\SprykerBundle\Domain\Cart;

use Frontastic\Common\CartApiBundle\Domain\CartApi;
use Frontastic\Common\ReplicatorBundle\Domain\Customer;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\SprykerBundle\BaseApi\Factory\AbstractSprykerBaseFactory;
use Frontastic\Common\SprykerBundle\Domain\Account\AccountHelper;
use Frontastic\Common\SprykerBundle\Domain\Cart\Request\CustomerCartRequestData;
use Frontastic\Common\SprykerBundle\Domain\Cart\SprykerCart\CustomerCart;
use Frontastic\Common\SprykerBundle\Domain\Cart\SprykerCart\GuestCart;
use Frontastic\Common\SprykerBundle\Domain\Cart\SprykerCart\SprykerCartInterface;
use Frontastic\Common\SprykerBundle\Domain\SprykerClient;

class SprykerCartApiFactory extends AbstractSprykerBaseFactory
{
    /**
     * @param Project $project
     *
     * @return CartApi
     */
    public function factor(Project $project): CartApi
    {
        $client = $this->createSprykerClient($project->configuration);

        return new SprykerCartApi(
            $client,
            $this->getMapperResolver(),
            $this->getAccountHelper(),
            $this->createGuestCart($client),
            $this->createCustomerCart($client, $project->configuration['spryker']->cart)
        );
    }

    /**
     * @param \Frontastic\Common\SprykerBundle\Domain\SprykerClient $client
     *
     * @return \Frontastic\Common\SprykerBundle\Domain\Cart\SprykerCart\GuestCart
     */
    protected function createGuestCart(SprykerClient $client): SprykerCartInterface
    {
        return new GuestCart(
            $client,
            $this->getMapperResolver(),
            $this->getAccountHelper()
        );
    }

    /**
     * @param \Frontastic\Common\SprykerBundle\Domain\SprykerClient $client
     * @param array $cartConfig
     *
     * @return \Frontastic\Common\SprykerBundle\Domain\Cart\SprykerCart\CustomerCart
     */
    protected function createCustomerCart(SprykerClient $client, array $cartConfig): SprykerCartInterface
    {
        return new CustomerCart(
            $client,
            $this->getMapperResolver(),
            $this->getAccountHelper(),
            $this->createCustomerCartRequestData($cartConfig)
        );
    }

    /**
     * @return AccountHelper
     */
    protected function getAccountHelper(): AccountHelper
    {
        return $this->container->get(AccountHelper::class);
    }

    /**
     * @param array $cartConfig
     *
     * @return \Frontastic\Common\SprykerBundle\Domain\Cart\Request\CustomerCartRequestData
     */
    protected function createCustomerCartRequestData(array $cartConfig): CustomerCartRequestData
    {
        return new CustomerCartRequestData(
            $cartConfig['priceMode'],
            $cartConfig['currency'],
            $cartConfig['shop']
        );
    }
}
