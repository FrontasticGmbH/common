<?php

namespace Frontastic\Common\CartApiBundle\Domain;

class DefaultCartCheckoutService implements CartCheckoutService
{
    public function getPayedAmount(Cart $cart): int
    {
        return array_sum(
            array_map(
                function (Payment $payment) {
                    return $payment->amount;
                },
                array_filter(
                    $cart->payments ?: [],
                    function (Payment $payment) {
                        // We'll only consider the amount of payments with paid status.
                        return $this->isPaymentCompleted($payment);
                    }
                )
            )
        );
    }

    public function hasCompletePayments(Cart $cart): bool
    {
        $paymentPaid = false;

        foreach ($cart->payments as $payment) {
            $paymentPaid = $this->isPaymentCompleted($payment);

            // A cart might have multiple payments and not all of them might be successful.
            // If only one payment is successful payment it's enough.
            if ($paymentPaid) {
                break;
            }
        }

        return ($paymentPaid && ($this->getPayedAmount($cart) >= $cart->sum));
    }

    /**
     * We only consider payments with status "paid" as completed
     *
     * @param Payment $payment
     * @return bool
     */
    public function isPaymentCompleted(Payment $payment): bool
    {
        return $payment->paymentStatus === Payment::PAYMENT_STATUS_PAID;
    }

    /**
     * Some commerce backends might consider a cart ready without payment(s).
     *
     * This method will return true if there are no payments or if all payments
     * had paid status and the total amounts are equal to cart total amount.
     *
     * @return bool
     */
    public function isReadyForCheckout(Cart $cart): bool
    {
        return
            $cart->hasUser() &&
            $cart->hasAddresses() &&
            (empty($cart->payments) || $this->hasCompletePayments($cart));
    }
}
