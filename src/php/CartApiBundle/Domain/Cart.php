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

    /**
     * @deprecated use "Frontastic\Common\CartApiBundle\Domain\CartCheckout::getPayedAmount" instead.
     */
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

    /**
     * @deprecated use "Frontastic\Common\CartApiBundle\Domain\CartCheckout::hasCompletePayments" instead.
     */
    public function hasCompletePayments(): bool
    {
        $paymentPaid = false;
        if (0 < count($this->payments)) {
            foreach ($this->payments as $payment) {
                $paymentPaid = ($payment->paymentStatus === Payment::PAYMENT_STATUS_PAID) ? true : false;
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

    /**
     * @deprecated use "Frontastic\Common\CartApiBundle\Domain\CartCheckout::isReadyForCheckout" instead.
     */
    public function isReadyForCheckout(): bool
    {
        return $this->hasUser() && $this->hasAddresses();
    }

    public function isComplete(): bool
    {
        return $this->hasUser() && $this->hasAddresses() && $this->hasCompletePayments();
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
