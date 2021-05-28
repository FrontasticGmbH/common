<?php

namespace Frontastic\Common\SprykerBundle\Domain\Cart\Mapper;

use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\Discount;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\SprykerBundle\Domain\MapperInterface;
use WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject;

abstract class AbstractCartMapper implements MapperInterface
{
    abstract protected function getRelationship(): string;

    /**
     * @var LineItemMapper
     */
    private $lineItemMapper;

    /**
     * @var DiscountMapper
     */
    private $discountMapper;

    public function __construct(LineItemMapper $lineItemMapper, DiscountMapper $discountMapper)
    {
        $this->lineItemMapper = $lineItemMapper;
        $this->discountMapper = $discountMapper;
    }

    /**
     * @param ResourceObject $resource
     * @return mixed
     */
    public function mapResource(ResourceObject $resource): Cart
    {
        $totals = $resource->attribute('totals');

        $cart = new Cart();
        $cart->cartId = $resource->id();
        $cart->sum = $totals['grandTotal'];
        $cart->discountCodes = $this->mapDiscounts($resource);
        $cart->lineItems = $this->mapLineItems($resource);

        $cart->dangerousInnerCart = $resource->attributes();

        return $cart;
    }

    /**
     * @param ResourceObject $resource
     * @return LineItem[]
     */
    private function mapLineItems(ResourceObject $resource): array
    {
        $lineItems = [];

        if ($resource->hasRelationship($this->getRelationship())) {
            foreach ($resource->relationship($this->getRelationship())->resources() as $item) {
                if ($item->hasRelationship($this->lineItemMapper->getRelationship())) {
                    $lineItems[] = $this->lineItemMapper->mapResource($item);
                }
            }
        }

        return $lineItems;
    }

    /**
     * @param ResourceObject $resource
     * @return Discount[]
     */
    private function mapDiscounts(ResourceObject $resource): array
    {
        $discounts = [];

        if ($resource->hasRelationship('cart-rules')) {
            foreach ($resource->relationship('cart-rules')->resources() as $cartRule) {
                $discounts[] = $this->discountMapper->mapResource($cartRule);
            }
        }

        if ($resource->hasRelationship('vouchers')) {
            foreach ($resource->relationship('vouchers')->resources() as $voucher) {
                $discounts[] = $this->discountMapper->mapResource($voucher);
            }
        }

        return $discounts;
    }
}
