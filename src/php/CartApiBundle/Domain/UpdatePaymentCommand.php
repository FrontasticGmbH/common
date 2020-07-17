<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\ApiDataObject;

class UpdatePaymentCommand extends ApiDataObject
{
    /**
     * The `Payment::$id` of the payment entity which should be updated
     *
     * @var string
     */
    public $id;

    /**
     * One of the `Payment::PAYMENT_STATUS_*` constants. If not `null` the `Payment::$paymentStatus` is updated.
     *
     * @var string|null
     */
    public $paymentStatus;

    /**
     * If not `null` the `Payment::$debug` is updated.
     *
     * @var string|null
     */
    public $debug;

    /**
     * If not `null` the `Payment::$paymentId` is updated.
     *
     * @var string|null
     */
    public $paymentInterfaceId;

    /**
     * If not `null` the `Payment::$details` are updated.
     *
     * @var array|null
     */
    public $details;
}
