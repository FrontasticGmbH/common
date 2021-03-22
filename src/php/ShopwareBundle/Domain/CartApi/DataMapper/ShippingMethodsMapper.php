<?php

namespace Frontastic\Common\ShopwareBundle\Domain\CartApi\DataMapper;

use Frontastic\Common\CartApiBundle\Domain\ShippingMethod;
use Frontastic\Common\CartApiBundle\Domain\ShippingRate;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;

class ShippingMethodsMapper extends AbstractDataMapper
{
    public const MAPPER_NAME = 'shipping-methods';

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map($resource)
    {
        $shippingMethodsData = $this->extractData($resource);

        $result = [];
        foreach ($shippingMethodsData as $shippingMethodData) {
            $result[] = $this->mapDataToShippingMethod($shippingMethodData);
        }

        return $result;
    }

    private function mapDataToShippingMethod(array $shippingMethodData): ShippingMethod
    {
        $shippingMethod = new ShippingMethod($shippingMethodData, true);
        $shippingMethod->shippingMethodId = $shippingMethodData['id'] ?? null;
        $shippingMethod->name = $this->resolveTranslatedValue($shippingMethodData, 'name');
        $shippingMethod->description = $this->resolveTranslatedValue($shippingMethodData, 'description');
        $shippingMethod->dangerousInnerShippingMethod = $shippingMethodData;

        foreach ($shippingMethodData['prices'] as $price) {
            $shippingMethod->rates[] = new ShippingRate([
                'zoneId' => $price['id'] ?? null,
                'price' => $price['currencyPrice'][0]['gross'] ?? null,
            ]);
        }

        return $shippingMethod;
    }
}
