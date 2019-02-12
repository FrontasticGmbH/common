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

    public function addMultipleAction(Context $context, Request $request): array
    {
        $payload = $this->getJsonContent($request);
        $cartApi = $this->getCartApi($context);

        $cart = $this->getCart($context);
        $cartApi->startTransaction($cart);
        foreach (($payload['lineItems'] ?? []) as $lineItemData) {
            debug(new LineItem\Variant([
                    'variant' => new Variant(['sku' => $lineItemData['variant']['sku']]),
                    'custom' => $lineItemData['option'] ?? [],
                    'count' => $lineItemData['count'] ?? 1,
                ]));
            $cartApi->addToCart(
                $cart,
                new LineItem\Variant([
                    'variant' => new Variant(['sku' => $lineItemData['variant']['sku']]),
                    'custom' => $lineItemData['option'] ?? [],
                    'count' => $lineItemData['count'] ?? 1,
                ])
            );
        }
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

    public function updateAction(Context $context, Request $request): array
    {
        $payload = $this->getJsonContent($request);
        $cartApi = $this->getCartApi($context);

        $cart = $this->getCart($context);
        $cartApi->startTransaction($cart);

        if (!empty($payload['account'])) {
            // @TODO: This is apollo specific and must be extracted

            // @TODO: This is apollo specific and must be extracted
            if (!empty($payload['account']['birthday'])) {
                $cart = $cartApi->setCustomField(
                    $cart,
                    ['birthday' => (new \DateTime($payload['account']['birthday']))->format('Y-m-d')]
                );
            } elseif (!empty($payload['account']['year'])) {
                $cart = $cartApi->setCustomField(
                    $cart,
                    [
                        'birthday' => sprintf(
                            '%04d-%02d-%02d',
                            $payload['account']['year'] ?? 1900,
                            $payload['account']['month'] ?? 1,
                            $payload['account']['day'] ?? 1
                        ),
                    ]
                );
            }

            $cart = $cartApi->setEmail(
                $cart,
                $payload['account']['email']
            );
        }

        if (!empty($payload['shipping'])) {
            // @TODO: This is apollo specific and must be extracted
            if (!empty($payload['shipping']['storeId'])) {
                $cart = $cartApi->setCustomField(
                    $cart,
                    ['storeId' => $payload['shipping']['storeId']]
                );
            }

            $cart = $cartApi->setShippingAddress(
                $cart,
                $payload['shipping']
            );
        }

        if (!empty($payload['billing']) || !empty($payload['shipping'])) {
            $cart = $cartApi->setBillingAddress(
                $cart,
                $payload['billing'] ?: $payload['shipping']
            );
        }

        if (!empty($payload['shippingMethod'])) {
            // @TODO: This is apollo specific and must be extracted
            $shippingMethodMap = [
                'home' => '850b8c3a-974a-4f07-bf29-e1fdcdad5406',
                'store' => '849328bb-f69a-4c86-9ab4-becc28478a0f',
            ];

            $cart = $cartApi->setShippingMethod(
                $cart,
                $shippingMethodMap[$payload['shippingMethod']]
            );
        }

        // @TODO: How do we want to handle dublicate payment assigments?
        if (!empty($payload['payment']) && empty($cart->payment)) {
            $cartApi->setPayment(
                $cart,
                new Payment([
                    'paymentProvider' => $payload['payment']['paymentProvider'],
                    'paymentId' => $payload['payment']['paymentId'],
                    'amount' => $this->getCart($context)->sum,
                    'currency' => $context->currency,
                    'debug' => json_encode($payload['payment']['rawInfo'] ?? null),
                ])
            );
        }

        return ['cart' => $cartApi->commit()];
    }

    public function checkoutAction(Context $context, Request $request): array
    {
        $payload = $this->getJsonContent($request);
        $cartApi = $this->getCartApi($context);

        $cart = $this->updateAction($context, $request)['cart'];
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
