<?php

namespace Frontastic\Common\ShopifyBundle\Domain\CartApi;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\CartApi;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\CartApiBundle\Domain\Order;
use Frontastic\Common\CartApiBundle\Domain\Payment;
use Frontastic\Common\ShopifyBundle\Domain\ShopifyClient;

class ShopifyCartApi implements CartApi
{
    /**
     * @var ShopifyClient
     */
    private $client;

    public function __construct(ShopifyClient $client)
    {
        $this->client = $client;
    }

    public function getForUser(Account $account, string $locale): Cart
    {
        // TODO: Implement getForUser() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function getAnonymous(string $anonymousId, string $locale): Cart
    {
        $mutation = "
            mutation {
                checkoutCreate(input: {}) {
                    checkout {
                        id
                        createdAt
                        email
                        totalPriceV2 {
                            amount
                            currencyCode
                        }
                    }
                    checkoutUserErrors {
                        code
                        field
                        message
                    }
                }
            }";

        return $this->client
            ->request($mutation, $locale)
            ->then(function ($result) : Cart {
                if ($result['errors']) {
                    // TODO handle error
                }

                return $this->mapDataToCart($result['body']['data']['checkoutCreate']['checkout']);
            })
            ->wait();
    }

    public function getById(string $cartId, string $locale = null): Cart
    {
        $query = "
            query {
                node(id: \"{$cartId}\") {
                    ... on Checkout {
                        id
                        createdAt
                        email
                        totalPriceV2 {
                            amount
                            currencyCode
                        }
                    }
                }
            }
        ";

        return $this->client
            ->request($query)
            ->then(function (array $result): Cart {
                if ($result['errors']) {
                    // TODO handle error
                }

                return $this->mapDataToCart($result['body']['data']['node']);
            })
            ->wait();
    }

    public function setCustomLineItemType(array $lineItemType): void
    {
        // TODO: Implement setCustomLineItemType() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function getCustomLineItemType(): array
    {
        // TODO: Implement getCustomLineItemType() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function setTaxCategory(array $taxCategory): void
    {
        // TODO: Implement setTaxCategory() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function getTaxCategory(): array
    {
        // TODO: Implement getTaxCategory() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function addToCart(Cart $cart, LineItem $lineItem, string $locale = null): Cart
    {
        // TODO: Implement addToCart() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function updateLineItem(Cart $cart, LineItem $lineItem, int $count, ?array $custom = null, string $locale = null): Cart
    {
        // TODO: Implement updateLineItem() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function removeLineItem(Cart $cart, LineItem $lineItem, string $locale = null): Cart
    {
        // TODO: Implement removeLineItem() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function setEmail(Cart $cart, string $email, string $locale = null): Cart
    {
        // TODO: Implement setEmail() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function setShippingMethod(Cart $cart, string $shippingMethod, string $locale = null): Cart
    {
        // TODO: Implement setShippingMethod() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function setCustomField(Cart $cart, array $fields, string $locale = null): Cart
    {
        // TODO: Implement setCustomField() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function setRawApiInput(Cart $cart, string $locale = null): Cart
    {
        // TODO: Implement setRawApiInput() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function setShippingAddress(Cart $cart, Address $address, string $locale = null): Cart
    {
        // TODO: Implement setShippingAddress() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function setBillingAddress(Cart $cart, Address $address, string $locale = null): Cart
    {
        // TODO: Implement setBillingAddress() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function addPayment(Cart $cart, Payment $payment, ?array $custom = null, string $locale = null): Cart
    {
        // TODO: Implement addPayment() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function updatePayment(Cart $cart, Payment $payment, string $localeString): Payment
    {
        // TODO: Implement updatePayment() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function redeemDiscountCode(Cart $cart, string $code, string $locale = null): Cart
    {
        // TODO: Implement redeemDiscountCode() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function removeDiscountCode(Cart $cart, LineItem $discountLineItem, string $locale = null): Cart
    {
        // TODO: Implement removeDiscountCode() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function order(Cart $cart, string $locale = null): Order
    {
        // TODO: Implement order() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function getOrder(Account $account, string $orderId, string $locale = null): Order
    {
        // TODO: Implement getOrder() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function getOrders(Account $account, string $locale = null): array
    {
        // TODO: Implement getOrders() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function startTransaction(Cart $cart): void
    {
        // TODO: Implement startTransaction() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function commit(string $locale = null): Cart
    {
        // TODO: Implement commit() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function getDangerousInnerClient()
    {
        // TODO: Implement getDangerousInnerClient() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    private function convertPriceToCent($price): int
    {
        return (int)round($price * 100);
    }

    private function mapDataToCart(array $cartData): Cart
    {
        return new Cart([
            'cartId' => $cartData['id'],
            'cartVersion' => $cartData['createdAt'],
            'email' => $cartData['email'],
            'sum' => $this->convertPriceToCent(
                $cartData['totalPriceV2']['amount']
            ),
            'currency' => $cartData['totalPriceV2']['currencyCode'],
        ]);
    }
}
