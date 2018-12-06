<?php

namespace Frontastic\Common\CartApiBundle\Controller;

use Frontastic\Common\CartApiBundle\Domain\Payment;
use Symfony\Component\HttpFoundation\Request;

use Frontastic\Common\CoreBundle\Controller\CrudController;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\CartApiBundle\Domain\CartApi;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Catwalk\ApiCoreBundle\Domain\Context;

class CartController extends CrudController
{
    /**
     * @var CartApi
     */
    protected $cartApi;

    public function getAction(Context $context): array
    {
        return [
            'cart' => $this->getCart($context),
        ];
    }

    public function getOrderAction(Context $context, string $order): array
    {
        $cartApi = $this->getCartApi($context);
        return [
            'order' => $cartApi->getOrder($order),
        ];
    }

    public function addAction(Context $context, Request $request): array
    {
        $payload = $this->getJsonContent($request);
        $cartApi = $this->getCartApi($context);

        $cart = $this->getCart($context);
        $cartApi->startTransaction($cart);
        $cartApi->addToCart(
            $cart,
            new LineItem\Variant([
                'variant' => new Variant(['sku' => $payload['variant']['sku']]),
                'custom' => $payload['option'] ?: [],
                'count' => $payload['count']
            ])
        );
        $cart = $cartApi->commit();

        return [
            'cart' => $cart,
        ];
    }

    public function updateLineItemAction(Context $context, Request $request): array
    {
        $payload = $this->getJsonContent($request);
        $cartApi = $this->getCartApi($context);

        $cart = $this->getCart($context);
        $cartApi->startTransaction($cart);
        $cartApi->updateLineItem(
            $cart,
            $this->getLineItem($cart, $payload['lineItemId']),
            $payload['count']
        );
        $cart = $cartApi->commit();

        return [
            'cart' => $cart,
        ];
    }

    public function removeLineItemAction(Context $context, Request $request): array
    {
        $payload = $this->getJsonContent($request);
        $cartApi = $this->getCartApi($context);

        $cart = $this->getCart($context);
        $cartApi->startTransaction($cart);
        $cartApi->removeLineItem(
            $cart,
            $this->getLineItem($cart, $payload['lineItemId'])
        );
        $cart = $cartApi->commit();

        return [
            'cart' => $cart,
        ];
    }

    private function getLineItem(Cart $cart, string $lineItemId): LineItem
    {
        foreach ($cart->lineItems as $lineItem) {
            if ($lineItem->lineItemId === $lineItemId) {
                return $lineItem;
            }
        }

        throw new \OutOfBoundsException("Could not find line item with ID $lineItemId");
    }

    public function checkoutAction(Context $context, Request $request): array
    {
        $payload = $this->getJsonContent($request);
        $cartApi = $this->getCartApi($context);

        // @TODO:
        // [ ] Create new user, if requested
        // [ ] Register for newsletter if requested

        $cart = $this->getCart($context);
        $cartApi->startTransaction($cart);
        $cart = $cartApi->setEmail(
            $cart,
            $payload['account']['email']
        );
        $cart = $cartApi->setShippingAddress(
            $cart,
            $payload['shipping']
        );
        $cart = $cartApi->setBillingAddress(
            $cart,
            $payload['billing'] ?: $payload['shipping']
        );
        $cartApi->setPayment(
            $cart,
            new Payment([
                'paymentProvider' => $payload['payment']['provider'],
                'paymentId' => $payload['payment']['id'],
                'amount' => $this->getCart($context)->sum,
                'currency' => $context->currency
            ])
        );
        $cart = $cartApi->commit();

        $order = $cartApi->order($cart);

        // @TODO: Remove old cart instead (also for logged in users)
        // @HACK: Regenerate session ID to get a "new" cart:
        session_regenerate_id();

        return [
            'order' => $order,
        ];
    }

    protected function getCartApi(Context $context): CartApi
    {
        if ($this->cartApi) {
            return $this->cartApi;
        }

        /** @var \Frontastic\Common\CartApiBundle\Domain\CartApiFactory $cartApiFactory */
        $cartApiFactory = $this->get('Frontastic\Common\CartApiBundle\Domain\CartApiFactory');
        return $this->cartApi = $cartApiFactory->factor($context->customer);
    }

    protected function getCart(Context $context): Cart
    {
        $cartApi = $this->getCartApi($context);
        if ($context->session->loggedIn) {
            return $cartApi->getForUser($context->session->account->accountId);
        } else {
            return $cartApi->getAnonymous(session_id());
        }
    }

    /**
     * @param Request $request
     * @return array|mixed
     */
    protected function getJsonContent(Request $request)
    {
        if (!$request->getContent() ||
            !($body = json_decode($request->getContent(), true))) {
            throw new \InvalidArgumentException("Invalid data passed: " . $request->getContent());
        }

        return $body;
    }
}
