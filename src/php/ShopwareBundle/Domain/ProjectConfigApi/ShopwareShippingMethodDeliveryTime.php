<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi;

use Kore\DataObject\DataObject;

class ShopwareShippingMethodDeliveryTime extends DataObject
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $name;
}
