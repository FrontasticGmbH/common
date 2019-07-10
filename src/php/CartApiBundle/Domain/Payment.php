<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Kore\DataObject\DataObject;

class Payment extends DataObject
{

    public const INTERFACE_CODE_APPROVED = 'success';
    public const INTERFACE_CODE_ACCEPTED = 'accepted';
    public const INTERFACE_CODE_FAILED   = 'failed';

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
