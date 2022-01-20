<?php

namespace Frontastic\Common\ShopwareBundle\Domain\CartApi\DataMapper;

use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;

class ShippingMethodsMapper extends AbstractDataMapper
{
    public const MAPPER_NAME = 'shipping-methods';

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\CartApi\DataMapper\ShippingMethodMapper
     */
    private $shippingMethodMapper;

    public function __construct(ShippingMethodMapper $shippingMethodMapper)
    {
        $this->shippingMethodMapper = $shippingMethodMapper;
    }

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map($resource)
    {
        $shippingMethodsData = $this->extractElements($resource, $resource);

        $result = [];
        foreach ($shippingMethodsData as $shippingMethodData) {
            $result[] = $this->getShippingMethodMapper()->map($shippingMethodData);
        }

        return $result;
    }

    private function getShippingMethodMapper(): ShippingMethodMapper
    {
        return $this->shippingMethodMapper;
    }
}
