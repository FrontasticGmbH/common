<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Kore\DataObject\DataObject;

class Payment extends DataObject
{

    public const INTERFACE_CODE_PAID = 'Paid';
    public const INTERFACE_CODE_PENDING = 'Pending';
    public const INTERFACE_CODE_FAILED   = 'failed';
    public const INTERFACE_CODE_CREDIT_OWED   = 'Credit owed';

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


    public $interfaceCode;
}
