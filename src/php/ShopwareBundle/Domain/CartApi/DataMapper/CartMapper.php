<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\CartApi\DataMapper;

use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\CartApiBundle\Domain\ShippingMethod;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\ShopwareBundle\Domain\AccountApi\DataMapper\AddressMapper;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;

class CartMapper extends AbstractDataMapper
{
    public const MAPPER_NAME = 'cart';

    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\AccountApi\DataMapper\AddressMapper
     */
    private $addressMapper;

    public function __construct(AddressMapper $addressMapper)
    {
        $this->addressMapper = $addressMapper;
    }

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map($resource)
    {
        $cartData = $this->extractData($resource);

        $locationData = $this->extractFromDeliveries($cartData, 'location')['address'] ?? null;
        $shippingMethodData = $this->extractFromDeliveries($cartData, 'shippingMethod');

        return new Cart([
            'cartId' => (string)$cartData['token'],
            'cartVersion' => (string)$cartData['name'],
            'sum' => $this->convertPriceToCent($cartData['price']['totalPrice']),
            // @TODO: pass ShopwareLocale and take currencyId and resolve it to Frontastic currency
            'currency' => null,// $cartData['totalPrice']['currencyIso'],
            'lineItems' => $this->mapDataToLineItems($cartData['lineItems'] ?? []),
            'shippingAddress' => empty($locationData) ? null : $this->addressMapper->map($locationData),
            'shippingMethod' => empty($shippingMethodData) ? null : $this->mapDataToShippingMethod($shippingMethodData)
            // @TODO: resolve billing address?
        ]);
    }

    /**
     * @param array $lineItemData
     *
     * @return \Frontastic\Common\CartApiBundle\Domain\LineItem[]
     */
    private function mapDataToLineItems(array $lineItemData): array
    {
        $result = [];
        foreach ($lineItemData as $lineItem) {
            $result[] = new LineItem\Variant([
                'lineItemId' => (string)$lineItem['id'],
                'name' => $lineItem['label'],
                'count' => $lineItem['quantity'],
                'price' => $this->convertPriceToCent($lineItem['price']['unitPrice']),
                'totalPrice' => $this->convertPriceToCent($lineItem['price']['totalPrice']),
                'variant' => new Variant([
                    'id' => $lineItem['referencedId'],
                    'sku' => $lineItem['referencedId'],
                    'images' => [
                        $lineItem['cover']['url'],
                    ],
                    'attributes' => array_map(static function ($option) {
                        return [$option['group'] => $option['option']];
                    }, $lineItem['payload']['options'])
                ]),
                // @TODO: pass ShopwareLocale and take currencyId and resolve it to Frontastic currency
                'currency' => null,
            ]);
        }

        return $result;
    }

    private function extractFromDeliveries(array $cartData, string $deliveryItemKey): array
    {
        return $cartData['deliveries'][0][$deliveryItemKey] ?? [];
    }

    private function mapDataToShippingMethod(array $shippingMethodData): ?ShippingMethod
    {
        if (empty($shippingMethodData)) {
            return null;
        }

        return new ShippingMethod([
            'name' => $shippingMethodData['name'] ?? null,
            'price' => $this->convertPriceToCent($shippingMethodData['prices'][0]['price'] ?? 0),
        ]);
    }
}
