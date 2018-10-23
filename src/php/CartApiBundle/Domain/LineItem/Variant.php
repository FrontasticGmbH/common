<?php

namespace Frontastic\Common\CartApiBundle\Domain\LineItem;

use Frontastic\Common\CartApiBundle\Domain\LineItem;

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
