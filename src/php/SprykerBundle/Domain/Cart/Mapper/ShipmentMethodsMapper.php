<?php

namespace Frontastic\Common\SprykerBundle\Domain\Cart\Mapper;

use Frontastic\Common\CartApiBundle\Domain\ShippingMethod;
use Frontastic\Common\CartApiBundle\Domain\ShippingRate;
use Frontastic\Common\SprykerBundle\Domain\MapperInterface;
use WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject;

class ShipmentMethodsMapper implements MapperInterface
{
    public const MAPPER_NAME = 'shipmentMethods';

    /**
     * @var ShippingMethod[]
     */
    private $shippingMethods = [];

    /**
     * @param ResourceObject $resource
     * @return ShippingMethod[]
     */
    public function mapResource(ResourceObject $resource): array
    {
        $this->shippingMethods = [];

        if ($resource->hasRelationship('shipment-methods')) {
            foreach ($resource->relationship('shipment-methods')->resources() as $resource) {
                $shippingMethod = new ShippingMethod();
                $shippingMethod->shippingMethodId = $resource->id();
                $shippingMethod->name = $resource->attribute('name');

                $shippingRate = new ShippingRate();
                $shippingRate->price = $resource->attribute('price');
                $shippingRate->currency = $resource->attribute('currencyIsoCode');
                $shippingMethod->rates[] = $shippingRate;

                $shippingMethod->dangerousInnerShippingMethod = $resource->attributes();
                $this->shippingMethods[] = $shippingMethod;
            }
        }

        return $this->shippingMethods;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::MAPPER_NAME;
    }
}
