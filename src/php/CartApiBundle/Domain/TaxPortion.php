<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\ApiDataObject;

/**
 * @type
 */
class TaxPortion extends ApiDataObject
{
    /**
     * Amount in cent
     *
     * @var int
     */
    public $amount;

    /**
     * @var string
     */
    public $currency;

    /**
     * @var string
     */
    public $name;

    /**
     * Rate number in the range [0..1]
     *
     * @var float
     */
    public $rate;
}
