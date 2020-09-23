<?php

namespace Frontastic\Common\ShopifyBundle\Domain\CartApi;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\CartApi;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\CartApiBundle\Domain\Order;
use Frontastic\Common\CartApiBundle\Domain\Payment;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\ShopifyBundle\Domain\Mapper\ShopifyProductMapper;
use Frontastic\Common\ShopifyBundle\Domain\ShopifyClient;

class ShopifyCartApi implements CartApi
{
    private const DEFAULT_ELEMENTS_TO_FETCH = 10;

    /**
     * @var ShopifyClient
     */
    private $client;

    /**
     * @var string
     */
    private $currentTransaction;

    /**
     * @var ShopifyProductMapper
     */
    private $productMapper;

    public function __construct(ShopifyClient $client, ShopifyProductMapper $productMapper)
    {
        $this->client = $client;
        $this->productMapper = $productMapper;
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
                        {$this->getCheckoutQueryFields()}
                        lineItems(first: " . self::DEFAULT_ELEMENTS_TO_FETCH . ") {
                            edges {
                                node {
                                    {$this->getLineItemQueryFields()}
                                    variant {
                                        {$this->getVariantQueryFields()}
                                    }
                                }
                            }
                        }
                    }
                    checkoutUserErrors {
                        {$this->getErrorsQueryFields()}
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
                        {$this->getCheckoutQueryFields()}
                        lineItems(first: " . self::DEFAULT_ELEMENTS_TO_FETCH . ") {
                            edges {
                                node {
                                    {$this->getLineItemQueryFields()}
                                    variant {
                                        {$this->getVariantQueryFields()}
                                    }
                                }
                            }
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
        $mutation = "
            mutation {
                checkoutLineItemsAdd(
                    checkoutId: \"{$cart->cartId}\",
                    lineItems: {
                        quantity: {$lineItem->count}
                        variantId: \"{$lineItem->variant->sku}\"
                    }
                ) {
                    checkout {
                        {$this->getCheckoutQueryFields()}
                        lineItems(first: " . self::DEFAULT_ELEMENTS_TO_FETCH . ") {
                            edges {
                                node {
                                    {$this->getLineItemQueryFields()}
                                    variant {
                                        {$this->getVariantQueryFields()}
                                    }
                                }
                            }
                        }
                    }
                    checkoutUserErrors {
                        {$this->getErrorsQueryFields()}
                    }
                }
            }";

        return $this->client
            ->request($mutation, $locale)
            ->then(function ($result) : Cart {
                if ($result['errors']) {
                    // TODO handle error
                }

                return $this->mapDataToCart($result['body']['data']['checkoutLineItemsAdd']['checkout']);
            })
            ->wait();
    }

    public function updateLineItem(Cart $cart, LineItem $lineItem, int $count, ?array $custom = null, string $locale = null): Cart
    {
        $mutation = "
            mutation {
                checkoutLineItemsUpdate(
                    checkoutId: \"{$cart->cartId}\",
                    lineItems: {
                        id: \"{$lineItem->lineItemId}\"
                        quantity: {$count}
                    }
                ) {
                    checkout {
                        {$this->getCheckoutQueryFields()}
                        lineItems(first: " . self::DEFAULT_ELEMENTS_TO_FETCH . ") {
                            edges {
                                node {
                                    {$this->getLineItemQueryFields()}
                                    variant {
                                        {$this->getVariantQueryFields()}
                                    }
                                }
                            }
                        }
                    }
                    checkoutUserErrors {
                        {$this->getErrorsQueryFields()}
                    }
                }
            }";

        return $this->client
            ->request($mutation, $locale)
            ->then(function ($result) : Cart {
                if ($result['errors']) {
                    // TODO handle error
                }

                return $this->mapDataToCart($result['body']['data']['checkoutLineItemsUpdate']['checkout']);
            })
            ->wait();
    }

    public function removeLineItem(Cart $cart, LineItem $lineItem, string $locale = null): Cart
    {
        $mutation = "
            mutation {
                checkoutLineItemsRemove(
                    checkoutId: \"{$cart->cartId}\",
                    lineItemIds: \"{$lineItem->lineItemId}\"
                ) {
                    checkout {
                        {$this->getCheckoutQueryFields()}
                        lineItems(first: " . self::DEFAULT_ELEMENTS_TO_FETCH . ") {
                            edges {
                                node {
                                    {$this->getLineItemQueryFields()}
                                    variant {
                                        {$this->getVariantQueryFields()}
                                    }
                                }
                            }
                        }
                    }
                    checkoutUserErrors {
                        {$this->getErrorsQueryFields()}
                    }
                }
            }";

        return $this->client
            ->request($mutation, $locale)
            ->then(function ($result) : Cart {
                if ($result['errors']) {
                    // TODO handle error
                }

                return $this->mapDataToCart($result['body']['data']['checkoutLineItemsRemove']['checkout']);
            })
            ->wait();
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
        $this->currentTransaction = $cart->cartId;
    }

    public function commit(string $locale = null): Cart
    {
        if ($this->currentTransaction === null) {
            throw new \RuntimeException('No transaction currently in progress');
        }

        $cartId = $this->currentTransaction;

        $this->currentTransaction = null;

        return $this->getById($cartId, $locale);

    }

    public function getDangerousInnerClient()
    {
        // TODO: Implement getDangerousInnerClient() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    private function mapDataToCart(array $cartData): Cart
    {
        return new Cart([
            'cartId' => $cartData['id'],
            'cartVersion' => $cartData['createdAt'],
            'email' => $cartData['email'],
            'sum' => $this->productMapper->mapDataToPriceValue(
                $cartData['totalPriceV2']
            ),
            'currency' => $cartData['totalPriceV2']['currencyCode'],
            'lineItems' => $this->mapDataToLineItems($cartData['lineItems']['edges']),
        ]);
    }

    private function mapDataToLineItems(array $lineItemsData): array
    {
        $lineItems = [];

        foreach ($lineItemsData as $lineItemData) {
            $lineItems[] = new LineItem\Variant([
                'lineItemId' => $lineItemData['node']['id'],
                'name' => $lineItemData['node']['title'],
                'count' => $lineItemData['node']['quantity'],
                'price' => $lineItemData['node']['unitPrice']['amount'],
                'variant' => $this->productMapper->mapDataToVariant($lineItemData['node']['variant']),
            ]);
        }

        return $lineItems;
    }

    protected function getCheckoutQueryFields(): string
    {
        return '
            id
            createdAt
            email
            totalPriceV2 {
                amount
                currencyCode
            }
        ';
    }

    protected function getLineItemQueryFields(): string
    {
        return '
            id
            quantity
            title
            unitPrice {
                amount
                currencyCode
            }
        ';
    }

    protected function getVariantQueryFields(): string
    {
        return '
            id
            sku
            title
            currentlyNotInStock
            priceV2 {
                amount
                currencyCode
            }
            product {
                id
            }
            selectedOptions {
                name
                value
            }
            image {
                originalSrc
            }
        ';
    }

    protected function getErrorsQueryFields(): string
    {
        return '
            code
            field
            message
        ';
    }
}
