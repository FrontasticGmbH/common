<?php

namespace Frontastic\Common\WishlistApiBundle\Domain\LineItem;

use Frontastic\Common\WishlistApiBundle\Domain\LineItem;

class Variant extends LineItem
{
    /**
     * @var Variant
     */
    public $variant;

    /**
     * @var string
     */
    public $type = 'variant';
}
