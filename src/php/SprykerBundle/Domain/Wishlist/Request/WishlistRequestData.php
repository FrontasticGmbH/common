<?php

namespace Frontastic\Common\SprykerBundle\Domain\Wishlist\Request;

use Frontastic\Common\SprykerBundle\Domain\AbstractRequestData;

class WishlistRequestData extends AbstractRequestData
{
    /**
     * @var string
     */
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    protected function getAttributes(): array
    {
        return [
            'name' => $this->name,
        ];
    }

    /**
     * @return string
     */
    protected function getType(): string
    {
        return 'wishlists';
    }
}
