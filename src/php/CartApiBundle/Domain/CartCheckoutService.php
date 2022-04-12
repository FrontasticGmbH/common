<?php

namespace Frontastic\Common\CartApiBundle\Domain;

interface CartCheckoutService
{
    public function getPayedAmount(Cart $cart): int;

    public function hasCompletePayments(Cart $cart): bool;

    public function isPaymentCompleted(Payment $payment): bool;

    public function isReadyForCheckout(Cart $cart): bool;
}
