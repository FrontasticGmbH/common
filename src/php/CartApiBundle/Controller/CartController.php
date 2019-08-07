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

    public function getOrderAction(Context $context, Request $request, string $order): array
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
        $beforeItemIds = $this->getLineItemIds($cart);

        $cartApi->startTransaction($cart);
        $cartApi->addToCart(
            $cart,
            new LineItem\Variant([
                'variant' => new Variant([
                    'sku' => $payload['variant']['sku'],
                    'attributes' => $payload['variant']['attributes'],
                ]),
                'custom' => $payload['option'] ?: [],
                'count' => $payload['count']
            ])
        );
        $cart = $cartApi->commit();

        return [
            'cart' => $cart,
            'addedItems' => $this->getLineItems(
                $cart,
                array_diff(
                    $this->getLineItemIds($cart),
                    $beforeItemIds
                )
            ),
        ];
    }

    public function addMultipleAction(Context $context, Request $request): array
    {
        $payload = $this->getJsonContent($request);
        $cartApi = $this->getCartApi($context);

        $cart = $this->getCart($context);
        $beforeItemIds = $this->getLineItemIds($cart);

        $cartApi->startTransaction($cart);
        foreach (($payload['lineItems'] ?? []) as $lineItemData) {
            $cartApi->addToCart(
                $cart,
                new LineItem\Variant([
                    'variant' => new Variant([
                        'sku' => $lineItemData['variant']['sku'],
                        'attributes' => $lineItemData['variant']['attributes'],
                    ]),
                    'custom' => $lineItemData['option'] ?? [],
                    'count' => $lineItemData['count'] ?? 1,
                ])
            );
        }
        $cart = $cartApi->commit();

        return [
            'cart' => $cart,
            'addedItems' => $this->getLineItems(
                $cart,
                array_diff(
                    $this->getLineItemIds($cart),
                    $beforeItemIds
                )
            ),
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
            $payload['count'],
            $payload['custom'] ?? null
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
            ($item = $this->getLineItem($cart, $payload['lineItemId']))
        );
        $cart = $cartApi->commit();

        return [
            'cart' => $cart,
            'removedItems' => [$item],
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

    private function getLineItems(Cart $cart, array $lineItemIds): array
    {
        $items = [];
        foreach ($cart->lineItems as $lineItem) {
            if (in_array($lineItem->lineItemId, $lineItemIds)) {
                $items[] = $lineItem;
            }
        }
        return $items;
    }

    private function getLineItemIds(Cart $cart): array
    {
        return array_map(
            function (LineItem $lineItem) {
                return $lineItem->lineItemId;
            },
            $cart->lineItems
        );
    }

    public function updateAction(Context $context, Request $request): array
    {
        $payload = $this->getJsonContent($request);
        $cartApi = $this->getCartApi($context);

        $cart = $this->getCart($context);
        $cartApi->startTransaction($cart);

        if (!empty($payload['account'])) {
            $cart = $cartApi->setEmail(
                $cart,
                $payload['account']['email']
            );
        }

        if (!empty($payload['shipping']) || !empty($payload['billing'])) {
            $cart = $cartApi->setShippingAddress(
                $cart,
                $payload['shipping'] ?: $payload['billing']
            );
        }

        if (!empty($payload['billing']) || !empty($payload['shipping'])) {
            $cart = $cartApi->setBillingAddress(
                $cart,
                $payload['billing'] ?: $payload['shipping']
            );
        }

        return ['cart' => $cartApi->commit()];
    }

    public function checkoutAction(Context $context, Request $request): array
    {
        $cartApi = $this->getCartApi($context);
        $cart = $this->getCart($context);

        if (!$cart->isComplete()) {
            throw new \DomainException('Cart not complete yet.');
        }

        $order = $cartApi->order($cart);

        // @TODO: Remove old cart instead (also for logged in users)
        // @HACK: Regenerate session ID to get a "new" cart:
        session_regenerate_id();

        return [
            'order' => $order,
        ];
    }

    public function redeemDiscountAction(Context $context, string $code): array
    {
        return [
            'cart' => $this->getCartApi($context)->redeemDiscountCode($this->getCart($context), $code),
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
            return $cartApi->getForUser($context->session->account->accountId, $context->locale);
        } else {
            return $cartApi->getAnonymous(session_id(), $context->locale);
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
