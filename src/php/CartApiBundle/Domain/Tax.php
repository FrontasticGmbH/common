<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\ApiDataObject;

/**
 * @type
 */
class Tax extends ApiDataObject
{
    /**
     * Net amount in cent
     *
     * @var int
     * @required
     */
    public $amount;

    /**
     * @var string
     * @required
     */
    public $currency;

    /**
     * @var TaxPortion[]
     */
    public $taxPortions;
}
