<?php

namespace Frontastic\Common\AccountApiBundle\Domain\LineItem;

use Frontastic\Common\AccountApiBundle\Domain\LineItem;

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
