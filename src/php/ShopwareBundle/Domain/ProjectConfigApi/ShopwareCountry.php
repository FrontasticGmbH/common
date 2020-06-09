<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi;

use Kore\DataObject\DataObject;

/**
 * @type
 */
class ShopwareCountry extends DataObject
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $iso;

    /**
     * @var string
     */
    public $iso3;

    /**
     * @var bool
     */
    public $taxFree;

    /**
     * @var bool
     */
    public $active;

    /**
     * @var bool
     */
    public $shippingAvailable;

    /**
     * @var int
     */
    public $position;
}
