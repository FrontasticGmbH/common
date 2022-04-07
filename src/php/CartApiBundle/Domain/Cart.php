<?php

namespace Frontastic\Common\CartApiBundle\Domain;

use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CoreBundle\Domain\ApiDataObject;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * @type
 */
class Cart extends ApiDataObject
{
    /**
     * @var string
     * @required
     */
    public $cartId;

    /**
     * @var string
     */
    public $cartVersion;

    /**
     * @var \Frontastic\Common\CartApiBundle\Domain\LineItem[]
     * @required
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
     * @var ?ShippingInfo
     */
    public $shippingInfo;

    /**
     * @deprecated use `shippingInfo` instead
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
     * @required
     */
    public $sum = 0;

    /**
     * @var string
     * @required
     */
    public $currency;

    /**
     * @var Payment[]
     * @required
     */
    public $payments = [];

    /**
     * @var Discount[]
     * @required
     */
    public $discountCodes = [];

    /**
     * @var ?Tax
     */
    public $taxed;

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
        return (bool)$this->email;
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
        foreach ($this->payments as $payment) {
            if ($payment->paymentStatus !== Payment::PAYMENT_STATUS_PAID) {
                return false;
            }
        }

        if ($this->getPayedAmount() < $this->sum) {
            return false;
        }

        return true;
    }

    public function isReadyForCheckout(): bool
    {
        return $this->hasUser() && $this->hasAddresses();
    }

    /**
     * Some commerce backends might consider a cart completed without payment(s).
     *
     * This method will return true if there are no payments or if all payments
     * had paid status and the total amounts are equal to cart total amount.
     *
     * @return bool
     */
    public function isComplete(): bool
    {
        return $this->hasUser() && $this->hasAddresses() && (empty($this->payments) || $this->hasCompletePayments());
    }

    public function getPaymentById(string $paymentId): Payment
    {
        foreach ($this->payments as $payment) {
            if ($payment->id === $paymentId) {
                return $payment;
            }
        }

        throw new NotFoundHttpException('Payment ' . $paymentId . ' not found in cart ' . $this->cartId);
    }
}
