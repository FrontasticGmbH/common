<?php

namespace Frontastic\Common\SprykerBundle\Domain\Cart\Mapper;

use Frontastic\Common\CartApiBundle\Domain\Order;
use Frontastic\Common\SprykerBundle\Domain\MapperInterface;
use WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject;

class CheckoutMapper implements MapperInterface
{
    public const MAPPER_NAME = 'checkout';

    /**
     * @param ResourceObject $resource
     * @return Order
     */
    public function mapResource(ResourceObject $resource): Order
    {
        $order = new Order();

        $order->orderId = $resource->attribute('orderReference');
        $order->dangerousInnerOrder = $resource->attributes();

        return $order;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::MAPPER_NAME;
    }
}
