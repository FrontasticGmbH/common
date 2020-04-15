<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\DataMapper;

use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareShippingMethod;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareShippingMethodDeliveryTime;

class ShippingMethodsMapper extends AbstractDataMapper
{
    public const MAPPER_NAME = 'shipping-methods';

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map(array $resource)
    {
        $shippingMethodsData = $this->extractData($resource);

        $result = [];
        foreach ($shippingMethodsData as $shippingMethodData) {
            $result[] = $this->mapDataToShopwareShippingMethod($shippingMethodData);
        }

        return $result;
    }

    private function mapDataToShopwareShippingMethod(array $shippingMethodData): ShopwareShippingMethod
    {
        $shippingMethod = new ShopwareShippingMethod($shippingMethodData, true);
        $shippingMethod->name = $this->resolveTranslatedValue($shippingMethodData, 'name');
        $shippingMethod->deliveryTime = new ShopwareShippingMethodDeliveryTime(
            $shippingMethodData['deliveryTime'],
            true
        );

        return $shippingMethod;
    }
}
