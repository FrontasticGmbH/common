<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Kore\DataObject\DataObject;

class Payment extends DataObject
{
    public const PAYMENT_STATUS_INIT = 'init';
    public const PAYMENT_STATUS_PENDING = 'pending';
    public const PAYMENT_STATUS_PAID = 'paid';
    public const PAYMENT_STATUS_FAILED   = 'failed';

    /**
     * @var string
     */
    public $id;
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

    public $paymentStatus;
    public $version;
    public $paymentMethod;
}
