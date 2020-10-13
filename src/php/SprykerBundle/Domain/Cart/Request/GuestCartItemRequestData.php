<?php

namespace Frontastic\Common\SprykerBundle\Domain\Cart\Request;

use Frontastic\Common\SprykerBundle\Domain\AbstractRequestData;

class GuestCartItemRequestData extends AbstractRequestData implements CartItemRequestDataInterface
{
    /**
     * @var string
     */
    private $sku;

    /**
     * @var int
     */
    private $quantity;

    public function __construct(string $sku, int $quantity)
    {
        $this->sku = $sku;
        $this->quantity = $quantity;
    }

    /**
     * @return array
     */
    protected function getAttributes(): array
    {
        return [
            'sku' => $this->sku,
            'quantity' => $this->quantity,
        ];
    }

    /**
     * @return string
     */
    protected function getType(): string
    {
        return 'guest-cart-items';
    }
}
