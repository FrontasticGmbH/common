<?php

namespace Frontastic\Common\SprykerBundle\Domain\Wishlist\Request;

use Frontastic\Common\SprykerBundle\Domain\AbstractRequestData;

class WishlistItemsRequestData extends AbstractRequestData
{
    /**
     * @var string
     */
    private $sku;

    public function __construct(string $sku)
    {
        $this->sku = $sku;
    }

    /**
     * @return array
     */
    protected function getAttributes(): array
    {
        return [
            'sku' => $this->sku,
        ];
    }

    /**
     * @return string
     */
    protected function getType(): string
    {
        return 'wishlist-items';
    }
}
