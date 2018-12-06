<?php

namespace Frontastic\Common\WishlistApiBundle\Domain;

use Kore\DataObject\DataObject;

class LineItem extends DataObject
{
    /**
     * @var string
     */
    public $lineItemId;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $type;

    /**
     * @var \DateTimeImmutable
     */
    public $addedAt;

    /**
     * @var integer
     */
    public $count = 0;

    /**
     * Access original object from backend
     *
     * This should only be used if you need very specific features
     * right NOW. Please notify Frontastic about your need so that
     * we can integrate those twith the common API. Any usage off
     * this property might make your code unstable against future
     * changes.
     *
     * @var mixed
     */
    public $dangerousInnerItem;
}
