<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Kore\DataObject\DataObject;

use Frontastic\Common\AccountApiBundle\Domain\Address;

class Cart extends DataObject
{
    /**
     * @var string
     */
    public $cartId;

    /**
     * @var string
     */
    public $cartVersion;

    /**
     * @var [string => mixed]
     */
    public $custom = [];

    /**
     * @var \Frontastic\Common\CartApiBundle\Domain\LineItem[]
     */
    public $lineItems = [];

    /**
     * @var string
     */
    public $email;

    /**
     * @var \DateTimeImmutable
     */
    public $birthday;

    /**
     * @var ?ShippingMethod
     */
    public $shippingMethod;

    /**
     * @var ?Address
     */
    public $shippingAddress;

    /**
     * @var ?Address
     */
    public $billingAddress;

    /**
     * @var integer
     */
    public $sum = 0;

    /**
     * @var string
     */
    public $currency;

    /**
     * @var Payment[]
     */
    public $payments = [];

    /**
     * @var string[]
     */
    public $discountCodes = [];

    /**
     * Access original object from backend
     *
     * This should only be used if you need very specific features
     * right NOW. Please notify Frontastic about your need so that
     * we can integrate those twith the common API. Any usage off
     * this property might make your code unstable against future
     * changes.
     *
     * @var mixed
     */
    public $dangerousInnerCart;

    public function getPayedAmount(): int
    {
        return array_sum(
            array_map(
                function (Payment $payment) {
                    return $payment->amount;
                },
                $this->payments ?: []
            )
        );
    }

    public function hasUser(): bool
    {
        return (bool) $this->email;
    }

    public function hasShippingAddress(): bool
    {
        return (
            $this->shippingAddress &&
            $this->shippingAddress->firstName &&
            $this->shippingAddress->lastName &&
            $this->shippingAddress->postalCode &&
            $this->shippingAddress->city &&
            $this->shippingAddress->country
        );
    }

    public function hasBillingAddress(): bool
    {
        return (
            $this->billingAddress &&
            $this->billingAddress->firstName &&
            $this->billingAddress->lastName &&
            $this->billingAddress->postalCode &&
            $this->billingAddress->city &&
            $this->billingAddress->country
        );
    }

    public function hasAddresses(): bool
    {
        return (
            $this->hasShippingAddress() &&
            $this->hasBillingAddress()
        );
    }

    public function hasCompletePayments(): bool
    {
        $paymentPaid = false;
        if (0 < count($this->payments)) {
            foreach ($this->payments as $payment) {
                $paymentPaid = ($payment->paymentStatus === Payment::INTERFACE_CODE_PAID) ? true : false;
                if ($paymentPaid) {
                    break;
                }
            }
        }

        return (
            $paymentPaid &&
            ($this->getPayedAmount() >= $this->sum)
        );
    }

    public function isComplete(): bool
    {
        return $this->hasUser() && $this->hasAddresses() && $this->hasCompletePayments();
    }
}
