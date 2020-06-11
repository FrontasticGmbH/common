<?php

namespace Frontastic\Common\SapCommerceCloudBundle\Domain;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\CartApi;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\CartApiBundle\Domain\Order;
use Frontastic\Common\CartApiBundle\Domain\OrderIdGenerator;
use Frontastic\Common\CartApiBundle\Domain\Payment;
use Frontastic\Common\SapCommerceCloudBundle\Domain\Locale\SapLocale;
use Frontastic\Common\SapCommerceCloudBundle\Domain\Locale\SapLocaleCreator;

class SapCartApi implements CartApi
{
    /** @var SapClient */
    private $client;

    /** @var SapLocaleCreator */
    private $localeCreator;

    /** @var SapDataMapper */
    private $dataMapper;

    /** @var OrderIdGenerator */
    private $orderIdGenerator;

    /** @var ?array */
    private $currentTransaction = null;

    public function __construct(
        SapClient $client,
        SapDataMapper $dataMapper,
        SapLocaleCreator $localeCreator,
        OrderIdGenerator $orderIdGenerator
    ) {
        $this->client = $client;
        $this->localeCreator = $localeCreator;
        $this->dataMapper = $dataMapper;
        $this->orderIdGenerator = $orderIdGenerator;
    }

    public function getForUser(Account $account, string $locale): Cart
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function getAnonymous(string $anonymousId, string $locale): Cart
    {
        return $this->client
            ->post(
                '/rest/v2/{siteId}/users/' . SapDataMapper::ANONYMOUS_USER_ID . '/carts',
                [],
                $this->createLocaleFromString($locale)->toQueryParameters()
            )
            ->then(function (array $data): Cart {
                return $this->dataMapper->mapDataToCart($data, SapDataMapper::ANONYMOUS_USER_ID);
            })
            ->wait();
    }

    public function getById(string $cartId, string $locale = null): Cart
    {
        list($userId, $sapCartId) = $this->splitCartId($cartId);
        return $this->fetchCart($userId, $sapCartId, $this->createLocaleFromString($locale));
    }

    public function setCustomLineItemType(array $lineItemType): void
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function getCustomLineItemType(): array
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function setTaxCategory(array $taxCategory): void
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function getTaxCategory(): array
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function addToCart(Cart $cart, LineItem $lineItem, string $locale = null): Cart
    {
        list($userId, $sapCartId) = $this->splitCartId($cart->cartId);

        if (!($lineItem instanceof LineItem\Variant)) {
            throw new \RuntimeException('line item has to be of type variant');
        }

        $this->client
            ->post(
                '/rest/v2/{siteId}/users/' . $userId . '/carts/' . $sapCartId . '/entries',
                [
                    'product' => [
                        'code' => $lineItem->variant->sku,
                    ],
                    'quantity' => $lineItem->count,
                ],
                $this->createLocaleFromString($locale)->toQueryParameters()
            )
            ->then(function (array $data): void {
                if ($data['statusCode'] !== 'success') {
                    throw new CartApi\Exception\RequestException('Error adding item to cart');
                }
            })
            ->wait();

        return $cart;
    }

    public function updateLineItem(
        Cart $cart,
        LineItem $lineItem,
        int $count,
        ?array $custom = null,
        string $locale = null
    ): Cart {
        list($userId, $sapCartId) = $this->splitCartId($cart->cartId);

        $this->client
            ->put(
                '/rest/v2/{siteId}/users/' . $userId . '/carts/' . $sapCartId . '/entries/' . $lineItem->lineItemId,
                [
                    'quantity' => $count,
                ],
                $this->createLocaleFromString($locale)->toQueryParameters()
            )
            ->then(function (array $data): void {
                if ($data['statusCode'] !== 'success') {
                    throw new CartApi\Exception\RequestException('Error adding item to cart');
                }
            })
            ->wait();

        return $cart;
    }

    public function removeLineItem(Cart $cart, LineItem $lineItem, string $locale = null): Cart
    {
        list($userId, $sapCartId) = $this->splitCartId($cart->cartId);

        $this->client
            ->delete(
                '/rest/v2/{siteId}/users/' . $userId . '/carts/' . $sapCartId . '/entries/' . $lineItem->lineItemId
            )
            ->wait();

        return $cart;
    }

    public function setEmail(Cart $cart, string $email, string $locale = null): Cart
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function setShippingMethod(Cart $cart, string $shippingMethod, string $locale = null): Cart
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function setCustomField(Cart $cart, array $fields, string $locale = null): Cart
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function setShippingAddress(Cart $cart, Address $address, string $locale = null): Cart
    {
        list($userId, $sapCartId) = $this->splitCartId($cart->cartId);

        $this->client
            ->post(
                '/rest/v2/{siteId}/users/' . $userId . '/carts/' . $sapCartId . '/addresses/delivery',
                $this->dataMapper->mapAddressToData($address),
                $this->createLocaleFromString($locale)->toQueryParameters()
            )
            ->then(function (array $data): void {
                if ($data['statusCode'] !== 'success') {
                    throw new CartApi\Exception\RequestException('Error adding item to cart');
                }
            })
            ->wait();

        return $cart;
    }

    public function setBillingAddress(Cart $cart, Address $address, string $locale = null): Cart
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function addPayment(Cart $cart, Payment $payment, ?array $custom = null, string $locale = null): Cart
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function redeemDiscountCode(Cart $cart, string $code, string $locale = null): Cart
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function removeDiscountCode(Cart $cart, LineItem $discountLineItem, string $locale = null): Cart
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function order(Cart $cart, string $locale = null): Order
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function getOrder(Account $account, string $orderId, string $locale = null): Order
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function getOrders(Account $account, string $locale = null): array
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function startTransaction(Cart $cart): void
    {
        $this->currentTransaction = $this->splitCartId($cart->cartId);
    }

    public function commit(string $locale = null): Cart
    {
        if ($this->currentTransaction === null) {
            throw new \RuntimeException('No transaction currently in progress');
        }

        list($userId, $sapCartId) = $this->currentTransaction;
        $sapLocale = $this->createLocaleFromString($locale);

        $this->currentTransaction = null;

        return $this->fetchCart($userId, $sapCartId, $sapLocale);
    }

    public function getDangerousInnerClient()
    {
        return $this->client;
    }

    private function createLocaleFromString(?string $locale): SapLocale
    {
        if ($locale === null) {
            throw new \RuntimeException('Fetching a cart without specifying the locale is not supported');
        }

        return $this->localeCreator->createLocaleFromString($locale);
    }

    private function splitCartId(string $cartId): array
    {
        $cartIdComponents = explode(':', $cartId, 2);
        if (count($cartIdComponents) !== 2) {
            throw new \RuntimeException('Invalid SAP cart id: ' . $cartId);
        }
        return $cartIdComponents;
    }

    private function fetchCart(string $userId, string $sapCartId, SapLocale $locale): Cart
    {
        return $this->client
            ->get(
                '/rest/v2/{siteId}/users/' . $userId . '/carts/' . $sapCartId,
                array_merge(
                    $locale->toQueryParameters(),
                    [
                        'fields' => 'FULL',
                    ]
                )
            )
            ->then(function (array $data) use ($userId): Cart {
                return $this->dataMapper->mapDataToCart($data, $userId);
            })
            ->wait();
    }
}
