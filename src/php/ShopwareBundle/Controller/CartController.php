<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Controller;

use Frontastic\Catwalk\ApiCoreBundle\Domain\Context;
use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\CartApiBundle\Controller\CartController as CommonCartController;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\Payment;
use Symfony\Component\HttpFoundation\Request;

class CartController extends CommonCartController
{
    /**
     * @param \Frontastic\Catwalk\ApiCoreBundle\Domain\Context $context
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function checkoutAction(Context $context, Request $request): array
    {
        $cartApi = $this->getCartApi($context);
        $cart = $this->getCart($context, $request);

        $payload = $this->getJsonContent($request);

        if ($context->session->loggedIn) {
            $account = $context->session->account;
        } else {
            $account = $this->createAccount($payload);
        }

//        $cart = $cartApi->setAccount(
//            $cart,
//            $account
//        );
//
//        $cart = $cartApi->setShippingAddress(
//            $cart,
//            $payload['shipping']
//        );
//
//        $cart = $cartApi->setBillingAddress(
//            $cart,
//            $payload['billing'] ?: $payload['shipping']
//        );
//
//        $cart = $cartApi->addPayment(
//            $cart,
//            $this->createPayment($cart, $payload, $context),
//            $this->createPaymentCustomOptions($payload)
//        );
//
//        $cart = $cartApi->setShippingMethod(
//            $cart,
//            $payload['shipmentMethod']['id']
//        );

        $order = $cartApi->order($cart);

        return [
            'order' => $order,
        ];
    }

    /**
     * @param array $payload
     *
     * @return \Frontastic\Common\AccountApiBundle\Domain\Account
     */
    protected function createAccount(array $payload): Account
    {
        return new Account([
            'email' => $payload['account']['email'] ?? $payload['user']['email'],
            'salutation' => $payload['account']['salutation'] ?? $payload['user']['salutation'],
        ]);
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param array $payload
     * @param \Frontastic\Catwalk\ApiCoreBundle\Domain\Context $context
     *
     * @return \Frontastic\Common\CartApiBundle\Domain\Payment
     */
    protected function createPayment(Cart $cart, array $payload, Context $context): Payment
    {
        return new Payment([
            'paymentProvider' => $payload['payment']['provider'],
            'paymentId' => $payload['payment']['id'],
            'amount' => $cart->sum,
            'currency' => $context->currency
        ]);
    }

    /**
     * @param array $payload
     *
     * @return array|null
     */
    protected function createPaymentCustomOptions(array $payload): ?array
    {
        return null;
    }
}
