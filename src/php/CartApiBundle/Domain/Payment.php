<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Kore\DataObject\DataObject;

class Payment extends DataObject
{
    /**
     * @var string
     */
    public $paymentProvider;

    /**
     * @var string
     */
    public $paymentId;

    /**
     * @var int Cent
     */
    public $amount;

    /**
     * @var string
     */
    public $currency;

    /**
     * @var string
     */
    public $debug;
}
