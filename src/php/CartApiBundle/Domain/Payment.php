<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\ApiDataObject;

class Payment extends ApiDataObject
{
    public const PAYMENT_STATUS_INIT = 'init';
    public const PAYMENT_STATUS_PENDING = 'pending';
    public const PAYMENT_STATUS_PAID = 'paid';
    public const PAYMENT_STATUS_FAILED = 'failed';

    /**
     * An internal ID to identify this payment
     *
     * @var string
     */
    public $id;

    /**
     * The name of the payment service provider
     *
     * @var string
     */
    public $paymentProvider;

    /**
     * The ID used by the payment service provider for this payment
     *
     * @var string
     */
    public $paymentId;

    /**
     * In cent
     *
     * @var int
     */
    public $amount;

    /**
     * @var string
     */
    public $currency;

    /**
     * A text describing the current status of the payment
     *
     * @var string
     */
    public $debug;

    /**
     * One of the `PAYMENT_STATUS_*` constants
     *
     * @var string
     */
    public $paymentStatus;

    /**
     * @var int
     */
    public $version;

    /**
     * The descriptor of the payment method used for this payment
     *
     * @var string
     */
    public $paymentMethod;

    /**
     * This data is stored as is by the `CartApi`. The payment integration can use this to store additional data which
     * might be needed later in the payment process.
     *
     * @var array|null
     */
    public $details;
}
