<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\CartApi\DataMapper;

use Frontastic\Common\ShopwareBundle\Domain\CartApi\ShopwareCartApi;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;

class CartItemRequestDataMapper extends AbstractDataMapper
{
    public const MAPPER_NAME = 'cart-item-request';

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\LineItem $lineItem
     *
     * @return string[]
     */
    public function map($lineItem)
    {
        return [
            'type' => ShopwareCartApi::LINE_ITEM_TYPE_PRODUCT,
            'quantity' => $lineItem->count,
            'stackable' => true,
            'removable' => true,
            'label' => $lineItem->name,
            'coverId' => null,
            'referencedId' => $lineItem->variant->id ?? $lineItem->lineItemId,
        ];
    }
}
