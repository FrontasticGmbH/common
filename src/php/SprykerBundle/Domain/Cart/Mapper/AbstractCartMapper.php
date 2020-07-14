<?php

namespace Frontastic\Common\SprykerBundle\Domain\Cart\Mapper;

use Frontastic\Common\CartApiBundle\Domain\Cart;
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

    public function __construct(LineItemMapper $lineItemMapper)
    {
        $this->lineItemMapper = $lineItemMapper;
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
        $cart->discountCodes = $resource->attribute('discounts', []);
        $cart->lineItems = $this->mapLineItems($resource);
        $cart->custom['_taxTotal'] = $totals['taxTotal'] ?? 0;
        $cart->custom['_discountTotal'] = $totals['discountTotal'] ?? 0;
        $cart->custom['_shippingTotal'] = $totals['expenseTotal'] ?? null;

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
                $lineItems[] = $this->lineItemMapper->mapResource($item);
            }
        }

        return $lineItems;
    }

}
