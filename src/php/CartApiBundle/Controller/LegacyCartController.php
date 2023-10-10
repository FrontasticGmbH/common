<?php

namespace Frontastic\Common\CartApiBundle\Controller;

use Frontastic\Catwalk\ApiCoreBundle\Domain\Context;
use Frontastic\Catwalk\TrackingBundle\Domain\TrackingService;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\CartApi;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\CoreBundle\Controller\CrudController;
use Frontastic\Common\CoreBundle\Domain\Json\Json;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

/**
 * @deprecated use "Frontastic\Catwalk\FrontendBundle\Controller\CartController" instead.
 */
class LegacyCartController extends CrudController
{
    /**
     * @var CartApi
     */
    protected $cartApi;

    /**
     * @var CartFetcher
     */
    private $cartFetcher;

    public function getAction(Context $context, Request $request): array
    {
        $cart = $this->getCart($context, $request);
        return [
            'cart' => $cart,
            'availableShippingMethods' => $this->getCartApi($context)->getAvailableShippingMethods(
                $cart,
                $context->locale
            ),
        ];
    }

    public function getOrderAction(Context $context, Request $request, string $order): array
    {
        $cartApi = $this->getCartApi($context);
        return [
            'order' => $cartApi->getOrder(
                $context->session->account,
                $order,
                $context->locale
            ),
        ];
    }

    public function addAction(Context $context, Request $request): array
    {
        $payload = $this->getJsonContent($request);
        $cartApi = $this->getCartApi($context);

        $cart = $this->getCart($context, $request);
        $beforeItemIds = $this->getLineItemIds($cart);

        $lineItemVariant = LineItem\Variant::newWithProjectSpecificData(
            array_merge(
                $payload,
                [
                    'variant' => new Variant([
                        'id' => $payload['variant']['id'] ?? null,
                        'sku' => $payload['variant']['sku'] ?? null,
                        'attributes' => $payload['variant']['attributes'] ?? [],
                    ]),
                    'count' => $payload['count'] ?? 1,
                ]
            )
        );
        $lineItemVariant->projectSpecificData = $this->parseProjectSpecificDataByKey($payload, 'option');

        $cartApi->startTransaction($cart);
        $cartApi->addToCart($cart, $lineItemVariant, $context->locale);
        $cart = $cartApi->commit($context->locale);

        $this->get(TrackingService::class)->reachAddToBasket($context, $cart, $lineItemVariant);

        return [
            'cart' => $cart,
            'addedItems' => $this->getLineItems(
                $cart,
                array_diff(
                    $this->getLineItemIds($cart),
                    $beforeItemIds
                )
            ),
            'availableShippingMethods' => $cartApi->getAvailableShippingMethods(
                $cart,
                $context->locale
            ),
        ];
    }

    public function addMultipleAction(Context $context, Request $request): array
    {
        $payload = $this->getJsonContent($request);

        if (!isset($payload['lineItems']) || !is_array($payload['lineItems'])) {
            throw new BadRequestHttpException('Parameter "lineItems" in payload is not an array.');
        }

        $cartApi = $this->getCartApi($context);

        $cart = $this->getCart($context, $request);
        $beforeItemIds = $this->getLineItemIds($cart);

        $cartApi->startTransaction($cart);
        foreach (($payload['lineItems'] ?? []) as $lineItemData) {
            $lineItemVariant = LineItem\Variant::newWithProjectSpecificData(
                array_merge(
                    $lineItemData,
                    [
                        'variant' => new Variant([
                            'id' => $lineItemData['variant']['id'] ?? null,
                            'sku' => $lineItemData['variant']['sku'] ?? null,
                            'attributes' => $lineItemData['variant']['attributes'] ?? [],
                        ]),
                        'count' => $lineItemData['count'] ?? 1,
                    ]
                )
            );
            // BC
            $lineItemVariant->projectSpecificData = $this->parseProjectSpecificDataByKey($lineItemData, 'option');

            $this->get(TrackingService::class)->reachAddToBasket($context, $cart, $lineItemVariant);
            $cartApi->addToCart($cart, $lineItemVariant, $context->locale);
        }
        $cart = $cartApi->commit($context->locale);

        return [
            'cart' => $cart,
            'addedItems' => $this->getLineItems(
                $cart,
                array_diff(
                    $this->getLineItemIds($cart),
                    $beforeItemIds
                )
            ),
            'availableShippingMethods' => $cartApi->getAvailableShippingMethods(
                $cart,
                $context->locale
            ),
        ];
    }

    public function updateLineItemAction(Context $context, Request $request): array
    {
        $payload = $this->getJsonContent($request);
        $cartApi = $this->getCartApi($context);

        $cart = $this->getCart($context, $request);
        $lineItem = $this->getLineItem($cart, $payload['lineItemId']);
        $lineItem->projectSpecificData = $this->parseProjectSpecificDataByKey($payload, 'custom');

        $cartApi->startTransaction($cart);
        $cartApi->updateLineItem(
            $cart,
            $lineItem,
            $payload['count'],
            null,
            $context->locale
        );
        $cart = $cartApi->commit($context->locale);

        return [
            'cart' => $cart,
            'availableShippingMethods' => $cartApi->getAvailableShippingMethods(
                $cart,
                $context->locale
            ),
        ];
    }

    public function removeLineItemAction(Context $context, Request $request): array
    {
        $payload = $this->getJsonContent($request);
        $cartApi = $this->getCartApi($context);

        $cart = $this->getCart($context, $request);

        $cartApi->startTransaction($cart);
        $cartApi->removeLineItem(
            $cart,
            $item = $this->getLineItem($cart, $payload['lineItemId']),
            $context->locale
        );
        $cart = $cartApi->commit($context->locale);

        return [
            'cart' => $cart,
            'removedItems' => [$item],
            'availableShippingMethods' => $cartApi->getAvailableShippingMethods(
                $cart,
                $context->locale
            ),
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

        $cart = $this->getCart($context, $request);
        $cartApi->startTransaction($cart);

        if (!empty($payload['account'])) {
            $cart = $cartApi->setEmail(
                $cart,
                $payload['account']['email'],
                $context->locale
            );
        }

        if (!empty($payload['shipping']) || !empty($payload['billing'])) {
            $cart = $cartApi->setShippingAddress(
                $cart,
                Address::newWithProjectSpecificData(($payload['shipping'] ?? []) ?: $payload['billing']),
                $context->locale
            );

            $cart = $cartApi->setBillingAddress(
                $cart,
                Address::newWithProjectSpecificData(($payload['billing'] ?? []) ?: $payload['shipping']),
                $context->locale
            );
        }

        if (array_key_exists('shippingMethodName', $payload)) {
            $cart = $cartApi->setShippingMethod(
                $cart,
                $payload['shippingMethodName'] ?? '',
                $context->locale
            );
        }

        $cart->projectSpecificData = $this->parseProjectSpecificDataByKey($payload, 'custom');
        $cart = $cartApi->setRawApiInput($cart, $context->locale);
        $cart = $cartApi->commit($context->locale);

        return [
            'cart' => $cart,
            'availableShippingMethods' => $cartApi->getAvailableShippingMethods(
                $cart,
                $context->locale
            ),
        ];
    }

    public function checkoutAction(Context $context, Request $request): array
    {
        $cartApi = $this->getCartApi($context);
        $cart = $this->getCart($context, $request);

        if (!$cart->isReadyForCheckout()) {
            throw new \DomainException('Cart not complete yet.');
        }

        $order = $cartApi->order($cart, $context->locale);
        $this->get(TrackingService::class)->reachOrder($context, $order);

        $symfonySession = $request->hasSession() ? $request->getSession() : null;
        if ($symfonySession !== null) {
            // Increase security
            session_regenerate_id();
            $symfonySession->remove('cart_id');
        }

        return [
            'order' => $order,
        ];
    }

    public function redeemDiscountAction(Context $context, Request $request, string $code): array
    {
        $cartApi = $this->getCartApi($context);
        $cart = $cartApi->redeemDiscountCode($this->getCart($context, $request), $code, $context->locale);
        return [
            'cart' => $cart,
            'availableShippingMethods' => $cartApi->getAvailableShippingMethods(
                $cart,
                $context->locale
            ),
        ];
    }

    public function removeDiscountAction(Context $context, Request $request): array
    {
        $cartApi = $this->getCartApi($context);
        $payload = $this->getJsonContent($request);
        $cart = $cartApi->removeDiscountCode(
            $this->getCart($context, $request),
            new LineItem(['lineItemId' => $payload['discountId']])
        );
        return [
            'cart' => $cart,
            'availableShippingMethods' => $cartApi->getAvailableShippingMethods(
                $cart,
                $context->locale
            ),
        ];
    }

    public function getShippingMethodsAction(Context $context, Request $request): array
    {
        return [
            'shippingMethods' => $this->getCartApi($context)->getShippingMethods(
                $context->locale,
                $request->query->getBoolean('onlyMatching')
            ),
        ];
    }

    protected function getCartApi(Context $context): CartApi
    {
        if ($this->cartApi) {
            return $this->cartApi;
        }

        /** @var \Frontastic\Common\CartApiBundle\Domain\CartApiFactory $cartApiFactory */
        $cartApiFactory = $this->get('Frontastic\Common\CartApiBundle\Domain\CartApiFactory');
        return $this->cartApi = $cartApiFactory->factor($context->project);
    }

    protected function getCart(Context $context, Request $request): Cart
    {
        return $this->getCartFetcher($context)->fetchCart($context, $request);
    }

    // The CartFetcher class is created dynamically, so that PHPStan can't find.
    // @phpstan-ignore-next-line
    private function getCartFetcher(Context $context): CartFetcher
    {
        if (!isset($this->cartFetcher)) {
            // @phpstan-ignore-next-line
            $this->cartFetcher = new CartFetcher($this->getCartApi($context), $this->get('logger'));
        }
        // @phpstan-ignore-next-line
        return $this->cartFetcher;
    }

    /**
     * @param Request $request
     *
     * @return array|mixed
     */
    protected function getJsonContent(Request $request)
    {
        if (!$request->getContent() ||
            !($body = Json::decode($request->getContent(), true))) {
            throw new \InvalidArgumentException("Invalid data passed: " . $request->getContent());
        }

        return $body;
    }

    protected function parseProjectSpecificDataByKey(array $requestBody, string $key): array
    {
        $projectSpecificData = $requestBody['projectSpecificData'] ?? [];

        if (!key_exists($key, $projectSpecificData) && key_exists($key, $requestBody)) {
            $this->get('logger')
                ->warning(
                    'This usage of the key "{key}" is deprecated, move it into "projectSpecificData" instead',
                    ['key' => $key]
                );
            $projectSpecificData['custom'] = $requestBody[$key] ?? [];
        }

        return $projectSpecificData;
    }
}
