<?php

namespace Frontastic\Common\ShopwareBundle\Domain\CartApi\DataMapper;

use Frontastic\Common\CartApiBundle\Domain\ShippingInfo;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;

class ShippingInfoMapper extends AbstractDataMapper
{
    public const MAPPER_NAME = 'shipping-info';

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\CartApi\DataMapper\ShippingMethodMapper
     */
    private $shippingMethodMapper;

    public function __construct(ShippingMethodMapper $shippingMethodMapper) {
        $this->shippingMethodMapper = $shippingMethodMapper;
    }

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map($resource)
    {
        $shippingInfoData = $this->extractElements($resource, $resource);
        $shippingMethod = $this->getShippingMethodMapper()->map($shippingInfoData['shippingMethod']);

        return new ShippingInfo(
            array_merge(
                (array)$shippingMethod,
                [
                'price' => $this->convertPriceToCent($shippingInfoData['shippingCosts']['totalPrice'] ?? 0),
                ]
            )
        );
    }

    private function getShippingMethodMapper(): ShippingMethodMapper
    {
        return $this->shippingMethodMapper;
    }
}
