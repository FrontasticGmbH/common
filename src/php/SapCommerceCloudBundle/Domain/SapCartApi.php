<?php

namespace Frontastic\Common\SapCommerceCloudBundle\Domain;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\CartApi;
use Frontastic\Common\CartApiBundle\Domain\CartApiBase;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\CartApiBundle\Domain\Order;
use Frontastic\Common\CartApiBundle\Domain\OrderIdGenerator;
use Frontastic\Common\CartApiBundle\Domain\Payment;
use Frontastic\Common\CartApiBundle\Domain\UpdatePaymentCommand;
use Frontastic\Common\SapCommerceCloudBundle\Domain\Locale\SapLocale;
use Frontastic\Common\SapCommerceCloudBundle\Domain\Locale\SapLocaleCreator;

class SapCartApi extends CartApiBase
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

    protected function getForUserImplementation(Account $account, string $locale): Cart
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function getAnonymousImplementation(string $anonymousId, string $locale): Cart
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

    protected function getByIdImplementation(string $cartId, string $locale = null): Cart
    {
        list($userId, $sapCartId) = $this->splitCartId($cartId);
        return $this->fetchCart($userId, $sapCartId, $this->createLocaleFromString($locale));
    }

    protected function setCustomLineItemTypeImplementation(array $lineItemType): void
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function getCustomLineItemTypeImplementation(): array
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function setTaxCategoryImplementation(array $taxCategory): void
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function getTaxCategoryImplementation(): ?array
    {
        return null;
    }

    protected function addToCartImplementation(Cart $cart, LineItem $lineItem, string $locale = null): Cart
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

    protected function updateLineItemImplementation(
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

    protected function removeLineItemImplementation(Cart $cart, LineItem $lineItem, string $locale = null): Cart
    {
        list($userId, $sapCartId) = $this->splitCartId($cart->cartId);

        $this->client
            ->delete(
                '/rest/v2/{siteId}/users/' . $userId . '/carts/' . $sapCartId . '/entries/' . $lineItem->lineItemId
            )
            ->wait();

        return $cart;
    }

    protected function setEmailImplementation(Cart $cart, string $email, string $locale = null): Cart
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function setShippingMethodImplementation(Cart $cart, string $shippingMethod, string $locale = null): Cart
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function setCustomFieldImplementation(Cart $cart, array $fields, string $locale = null): Cart
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function setRawApiInputImplementation(Cart $cart, string $locale = null): Cart
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function setShippingAddressImplementation(Cart $cart, Address $address, string $locale = null): Cart
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

    protected function setBillingAddressImplementation(Cart $cart, Address $address, string $locale = null): Cart
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function addPaymentImplementation(
        Cart $cart,
        Payment $payment,
        ?array $custom = null,
        string $locale = null
    ): Cart {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function updatePaymentImplementation(Cart $cart, Payment $payment, string $localeString): Payment
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function redeemDiscountCodeImplementation(Cart $cart, string $code, string $locale = null): Cart
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function removeDiscountCodeImplementation(
        Cart $cart,
        LineItem $discountLineItem,
        string $locale = null
    ): Cart {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function orderImplementation(Cart $cart, string $locale = null): Order
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function getOrderImplementation(Account $account, string $orderId, string $locale = null): Order
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function getOrdersImplementation(Account $account, string $locale = null): array
    {
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function startTransactionImplementation(Cart $cart): void
    {
        $this->currentTransaction = $this->splitCartId($cart->cartId);
    }

    protected function commitImplementation(string $locale = null): Cart
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
