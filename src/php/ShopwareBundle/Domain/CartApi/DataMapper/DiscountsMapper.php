<?php

namespace Frontastic\Common\ShopwareBundle\Domain\CartApi\DataMapper;

use Frontastic\Common\CartApiBundle\Domain\Discount;
use Frontastic\Common\ShopwareBundle\Domain\CartApi\ShopwareCartApi;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;

class DiscountsMapper extends AbstractDataMapper
{
    public const MAPPER_NAME = 'discounts';

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map($resource)
    {
        $lineItemsData = $this->extractElements($resource, $resource);

        $discounts = [];
        foreach ($lineItemsData as $lineItemData) {
            if ($lineItemData['type'] === ShopwareCartApi::LINE_ITEM_TYPE_PROMOTION) {
                $discounts[] = new Discount([
                    'discountId' => $lineItemData['id'],
                    'code' => $lineItemData['referencedId'] ?? null,
                    'name' => $lineItemData['label'] ?? null,
                    'description' => $lineItemData['description'] ?? null,
                    'discountedAmount' => $lineItemData['price']['totalPrice'] ?? $lineItemData['totalPrice'] ?? null,
                ]);
            }
        }

        return $discounts;
    }
}
