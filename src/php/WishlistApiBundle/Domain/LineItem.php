<?php

namespace Frontastic\Common\WishlistApiBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\ApiDataObject;

/**
 * @type
 */
class LineItem extends ApiDataObject
{
    /**
     * @var string
     * @required
     */
    public $lineItemId;

    /**
     * @var string
     * @required
     */
    public $name;

    /**
     * @var string
     * @required
     */
    public $type;

    /**
     * @var \DateTimeImmutable
     * @required
     */
    public $addedAt;

    /**
     * @var integer
     * @required
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
