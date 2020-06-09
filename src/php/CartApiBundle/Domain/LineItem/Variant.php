<?php

namespace Frontastic\Common\CartApiBundle\Domain\LineItem;

use Frontastic\Common\CartApiBundle\Domain\LineItem;

/**
 * @type
 */
class Variant extends LineItem
{
    /**
     * @var \Frontastic\Common\ProductApiBundle\Domain\Variant
     */
    public $variant;

    /**
     * @var string
     */
    public $type = 'variant';
}
