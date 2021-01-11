<?php

namespace Frontastic\Common\CartApiBundle\Domain\CartApi\Commercetools;

use Kore\DataObject\DataObject;

/**
 * General purpose option class for CommerceTools implementations, but for now only used in the context of a Cart.
 */
class Options extends DataObject
{

    /**
     * HashMap of CommerceTools cart default values.
     *
     * Will be merged with Frontastic set data when a new cart is created. Frontastic data override values in this array.
     * See https://docs.commercetools.com/api/projects/carts for details
     *
     * @var mixed[string]
     */
    public $cartDefaults = [
        'inventoryMode' => 'ReserveOnOrder',
    ];
}
