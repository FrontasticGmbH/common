<?php

namespace Frontastic\Common\SprykerBundle\Domain\Cart\Request;

class CustomerCartItemRequestData extends GuestCartItemRequestData
{
    /**
     * @return string
     */
    protected function getType(): string
    {
        return 'items';
    }
}
