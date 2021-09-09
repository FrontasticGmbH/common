<?php

namespace Frontastic\Common\ShopifyBundle\Domain\CartApi;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\AccountApi;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\CartApi\Exception\CartNotActiveException;
use Frontastic\Common\CartApiBundle\Domain\CartApiBase;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\CartApiBundle\Domain\Order;
use Frontastic\Common\CartApiBundle\Domain\Payment;
use Frontastic\Common\CartApiBundle\Domain\ShippingInfo;
use Frontastic\Common\CartApiBundle\Domain\ShippingMethod;
use Frontastic\Common\CartApiBundle\Domain\ShippingRate;
use Frontastic\Common\ShopifyBundle\Domain\Mapper\ShopifyAccountMapper;
use Frontastic\Common\ShopifyBundle\Domain\Mapper\ShopifyProductMapper;
use Frontastic\Common\ShopifyBundle\Domain\ShopifyClient;
use Psr\Log\LoggerInterface;

class ShopifyCartApi extends CartApiBase
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
     * @var AccountApi
     */
    private $accountApi;
    /**
     * @var ShopifyProductMapper
     */
    private $productMapper;

    /**
     * @var ShopifyAccountMapper
     */
    private $accountMapper;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ShopifyClient $client,
        AccountApi $accountApi,
        ShopifyProductMapper $productMapper,
        ShopifyAccountMapper $accountMapper,
        LoggerInterface $logger
    ) {
        $this->client = $client;
        $this->accountApi = $accountApi;
        $this->productMapper = $productMapper;
        $this->accountMapper = $accountMapper;
        $this->logger = $logger;
    }

    protected function getForUserImplementation(Account $account, string $locale): Cart
    {
        if (is_null($account->apiToken)) {
            throw new \RuntimeException(sprintf('Account %s is not logged in', $account->email));
        }

        if ($cartId = $this->getLastIncompleteCheckout($account, $locale)) {
            try {
                return $this->getById($cartId, $locale);
            } catch (CartNotActiveException $exception) {
                $this->logger
                    ->info(
                        'The cart {cartId} is not active for account {accountEmail}, creating new one',
                        [
                            'cartId' => $cartId,
                            'accountEmail' => $account->email,
                            'exception' => $exception,
                        ]
                    );
            }
        }

        $anonymousCart = $this->getAnonymous(uniqid(), $locale);

        $mutation = "
            mutation {
                checkoutCustomerAssociateV2(
                    checkoutId: \"{$anonymousCart->cartId}\",
                    customerAccessToken: \"{$account->apiToken}\"
                ) {
                    checkout {
                        {$this->getCheckoutQueryFields()}
                        lineItems(first: " . self::DEFAULT_ELEMENTS_TO_FETCH . ") {
                            edges {
                                node {
                                    id
                                    {$this->getLineItemQueryFields()}
                                    variant {
                                        {$this->getVariantQueryFields()}
                                    }
                                }
                            }
                        }
                        shippingAddress {
                            {$this->getAddressQueryFields()}
                        }
                        shippingLine {
                            {$this->getShippingLineQueryFields()}
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
                return $this->mapDataToCart($result['body']['data']['checkoutCustomerAssociateV2']['checkout']);
            })
            ->wait();
    }

    protected function getAnonymousImplementation(string $anonymousId, string $locale): Cart
    {
        $mutation = "
            mutation {
                checkoutCreate(input: {}) {
                    checkout {
                        {$this->getCheckoutQueryFields()}
                        lineItems(first: " . self::DEFAULT_ELEMENTS_TO_FETCH . ") {
                            edges {
                                node {
                                    id
                                    {$this->getLineItemQueryFields()}
                                    variant {
                                        {$this->getVariantQueryFields()}
                                    }
                                }
                            }
                        }
                        shippingAddress {
                            {$this->getAddressQueryFields()}
                        }
                        shippingLine {
                            {$this->getShippingLineQueryFields()}
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
                return $this->mapDataToCart($result['body']['data']['checkoutCreate']['checkout']);
            })
            ->wait();
    }

    protected function getByIdImplementation(string $cartId, string $locale = null): Cart
    {
        $query = "
            query {
                node(id: \"{$cartId}\") {
                    ... on Checkout {
                        {$this->getCheckoutQueryFields()}
                        lineItems(first: " . self::DEFAULT_ELEMENTS_TO_FETCH . ") {
                            edges {
                                node {
                                    id
                                    {$this->getLineItemQueryFields()}
                                    variant {
                                        {$this->getVariantQueryFields()}
                                    }
                                }
                            }
                        }
                        shippingAddress {
                            {$this->getAddressQueryFields()}
                        }
                        shippingLine {
                            {$this->getShippingLineQueryFields()}
                        }
                    }
                }
            }
        ";

        return $this->client
            ->request($query)
            ->then(function (array $result) use ($cartId): Cart {
                // Shopify does not clear the cart after checkout(cart) is completed.
                // The following statement prevent to use the same checkout(cart) if it's already completed.
                if ($result['body']['data']['node'] === null ||
                    (
                        isset($result['body']['data']['node']['completedAt']) &&
                        $result['body']['data']['node']['completedAt'] !== null
                    )
                ) {
                    throw new CartNotActiveException(sprintf('Cart %s is not active', $cartId));
                }

                return $this->mapDataToCart($result['body']['data']['node']);
            })
            ->wait();
    }

    protected function setCustomLineItemTypeImplementation(array $lineItemType): void
    {
        // TODO: Implement setCustomLineItemType() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function getCustomLineItemTypeImplementation(): array
    {
        // TODO: Implement getCustomLineItemType() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function setTaxCategoryImplementation(array $taxCategory): void
    {
        // TODO: Implement setTaxCategory() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function getTaxCategoryImplementation(): array
    {
        // TODO: Implement getTaxCategory() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function addToCartImplementation(Cart $cart, LineItem $lineItem, string $locale = null): Cart
    {
        $mutation = "
            mutation {
                checkoutLineItemsAdd(
                    checkoutId: \"{$cart->cartId}\",
                    lineItems: {
                        quantity: {$lineItem->count}
                        variantId: \"{$lineItem->variant->id}\"
                    }
                ) {
                    checkout {
                        {$this->getCheckoutQueryFields($cart)}
                        lineItems(first: " . self::DEFAULT_ELEMENTS_TO_FETCH . ") {
                            edges {
                                node {
                                    id
                                    {$this->getLineItemQueryFields()}
                                    variant {
                                        {$this->getVariantQueryFields()}
                                    }
                                }
                            }
                        }
                        shippingAddress {
                            {$this->getAddressQueryFields()}
                        }
                        shippingLine {
                            {$this->getShippingLineQueryFields()}
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
                return $this->mapDataToCart($result['body']['data']['checkoutLineItemsAdd']['checkout']);
            })
            ->wait();
    }

    protected function updateLineItemImplementation(
        Cart $cart,
        LineItem $lineItem,
        int $count,
        ?array $custom = null,
        string $locale = null
    ): Cart {
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
                        {$this->getCheckoutQueryFields($cart)}
                        lineItems(first: " . self::DEFAULT_ELEMENTS_TO_FETCH . ") {
                            edges {
                                node {
                                    id
                                    {$this->getLineItemQueryFields()}
                                    variant {
                                        {$this->getVariantQueryFields()}
                                    }
                                }
                            }
                        }
                        shippingAddress {
                            {$this->getAddressQueryFields()}
                        }
                        shippingLine {
                            {$this->getShippingLineQueryFields()}
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
                return $this->mapDataToCart($result['body']['data']['checkoutLineItemsUpdate']['checkout']);
            })
            ->wait();
    }

    protected function removeLineItemImplementation(Cart $cart, LineItem $lineItem, string $locale = null): Cart
    {
        $mutation = "
            mutation {
                checkoutLineItemsRemove(
                    checkoutId: \"{$cart->cartId}\",
                    lineItemIds: \"{$lineItem->lineItemId}\"
                ) {
                    checkout {
                        {$this->getCheckoutQueryFields($cart)}
                        lineItems(first: " . self::DEFAULT_ELEMENTS_TO_FETCH . ") {
                            edges {
                                node {
                                    id
                                    {$this->getLineItemQueryFields()}
                                    variant {
                                        {$this->getVariantQueryFields()}
                                    }
                                }
                            }
                        }
                        shippingAddress {
                            {$this->getAddressQueryFields()}
                        }
                        shippingLine {
                            {$this->getShippingLineQueryFields()}
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
                return $this->mapDataToCart($result['body']['data']['checkoutLineItemsRemove']['checkout']);
            })
            ->wait();
    }

    protected function setEmailImplementation(Cart $cart, string $email, string $locale = null): Cart
    {
        $mutation = "
            mutation {
                checkoutEmailUpdateV2(
                    checkoutId: \"{$cart->cartId}\",
                    email: \"{$email}\",
                ) {
                    checkout {
                        {$this->getCheckoutQueryFields($cart)}
                        lineItems(first: " . self::DEFAULT_ELEMENTS_TO_FETCH . ") {
                            edges {
                                node {
                                    id
                                    {$this->getLineItemQueryFields()}
                                    variant {
                                        {$this->getVariantQueryFields()}
                                    }
                                }
                            }
                        }
                        shippingAddress {
                            {$this->getAddressQueryFields()}
                        }
                        shippingLine {
                            {$this->getShippingLineQueryFields()}
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
                return $this->mapDataToCart($result['body']['data']['checkoutEmailUpdateV2']['checkout']);
            })
            ->wait();
    }

    protected function setShippingMethodImplementation(Cart $cart, string $shippingMethod, string $locale = null): Cart
    {
        $mutation = "
            mutation {
                checkoutShippingLineUpdate(
                    checkoutId: \"{$cart->cartId}\",
                    shippingRateHandle: \"{$shippingMethod}\",
                ) {
                    checkout {
                        {$this->getCheckoutQueryFields($cart)}
                        lineItems(first: " . self::DEFAULT_ELEMENTS_TO_FETCH . ") {
                            edges {
                                node {
                                    id
                                    {$this->getLineItemQueryFields()}
                                    variant {
                                        {$this->getVariantQueryFields()}
                                    }
                                }
                            }
                        }
                        shippingAddress {
                            {$this->getAddressQueryFields()}
                        }
                        shippingLine {
                            {$this->getShippingLineQueryFields()}
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
                return $this->mapDataToCart($result['body']['data']['checkoutShippingLineUpdate']['checkout']);
            })
            ->wait();
    }

    protected function setRawApiInputImplementation(Cart $cart, string $locale = null): Cart
    {
        // TODO: Implement setRawApiInput() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function setShippingAddressImplementation(Cart $cart, Address $address, string $locale = null): Cart
    {
        $mutation = "
            mutation {
                 checkoutShippingAddressUpdateV2(
                    checkoutId: \"{$cart->cartId}\",
                    shippingAddress: {
                        {$this->accountMapper->mapAddressToData($address)}
                    },
                ) {
                    checkout {
                        {$this->getCheckoutQueryFields($cart)}
                        lineItems(first: " . self::DEFAULT_ELEMENTS_TO_FETCH . ") {
                            edges {
                                node {
                                    id
                                    {$this->getLineItemQueryFields()}
                                    variant {
                                        {$this->getVariantQueryFields()}
                                    }
                                }
                            }
                        }
                        shippingAddress {
                            {$this->getAddressQueryFields()}
                        }
                        shippingLine {
                            {$this->getShippingLineQueryFields()}
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
                return $this->mapDataToCart($result['body']['data']['checkoutShippingAddressUpdateV2']['checkout']);
            })
            ->wait();
    }

    protected function setBillingAddressImplementation(Cart $cart, Address $address, string $locale = null): Cart
    {
        // Not supported by Shopify.
        // The billing address should be set up on checkout-complete flow.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function addPaymentImplementation(
        Cart $cart,
        Payment $payment,
        ?array $custom = null,
        string $locale = null
    ): Cart {
        // TODO: Implement addPayment() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function updatePaymentImplementation(Cart $cart, Payment $payment, string $localeString): Payment
    {
        // TODO: Implement updatePayment() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function redeemDiscountCodeImplementation(Cart $cart, string $code, string $locale = null): Cart
    {
        // TODO: Implement redeemDiscountCode() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function removeDiscountCodeImplementation(
        Cart $cart,
        LineItem $discountLineItem,
        string $locale = null
    ): Cart {
        // TODO: Implement removeDiscountCode() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function orderImplementation(Cart $cart, string $locale = null): Order
    {
        // Shopify handle the checkout complete action in their side.
        // The url where the customer should be redirected to complete this process
        // can be found in $cart->dangerousInnerCart['webUrl'].

        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    protected function getOrderImplementation(Account $account, string $orderId, string $locale = null): Order
    {
        $query = "
            query {
                node(id: \"{$orderId}\") {
                    ... on Order {
                        {$this->getOrderQueryFields()}
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
                        shippingAddress {
                            {$this->getAddressQueryFields()}
                        }
                    }
                }
            }
        ";

        return $this->client
            ->request($query)
            ->then(function (array $result): Cart {
                return $this->mapDataToOrder($result['body']['data']['node']);
            })
            ->wait();
    }

    protected function getOrdersImplementation(Account $account, string $locale = null): array
    {
        if (is_null($account->apiToken)) {
            throw new \RuntimeException(sprintf('Account %s is not logged in', $account->email));
        }

        $query = "
            query {
                customer(customerAccessToken: \"{$account->apiToken}\") {
                    orders(first: " . self::DEFAULT_ELEMENTS_TO_FETCH . ") {
                        edges {
                            node {
                                {$this->getOrderQueryFields()}
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
                                shippingAddress {
                                    {$this->getAddressQueryFields()}
                                }
                            }
                        }
                    }
                }
            }";

        return $this->client
            ->request($query)
            ->then(function (array $result): array {
                return $this->mapDataToOrders($result['body']['data']['customer']);
            })
            ->wait();
    }

    protected function startTransactionImplementation(Cart $cart): void
    {
        $this->currentTransaction = $cart->cartId;
    }

    protected function commitImplementation(string $locale = null): Cart
    {
        if ($this->currentTransaction === null) {
            throw new \RuntimeException('No transaction currently in progress');
        }

        $cartId = $this->currentTransaction;

        $this->currentTransaction = null;

        return $this->getById($cartId, $locale);
    }

    public function getAvailableShippingMethodsImplementation(Cart $cart, string $locale): array
    {
        if (key_exists('availableShippingRates', $cart->dangerousInnerCart)) {
            return array_map(
                function (array $shippingMethodData): ShippingMethod {
                    return $this->mapDataToShippingMethod($shippingMethodData);
                },
                $cart->dangerousInnerCart['availableShippingRates']['shippingRates']
            );
        }

        $query = "
            query {
                node(id: \"{$cart->cartId}\") {
                    ... on Checkout {
                        {$this->getCheckoutQueryFields($cart)}
                    }
                }
            }
        ";

        return $this->client
            ->request($query)
            ->then(function (array $result): array {
                $cartData = $result['body']['data']['node'];
                if (!key_exists('availableShippingRates', $cartData)) {
                    return [];
                }

                return array_map(
                    function (array $shippingMethodData): ShippingMethod {
                        return $this->mapDataToShippingMethod($shippingMethodData);
                    },
                    $cartData['availableShippingRates']['shippingRates']
                );
            })
            ->wait();
    }

    public function getShippingMethodsImplementation(string $locale, bool $onlyMatching = false): array
    {
        // TODO: Implement getShippingMethods() method.
        throw new \RuntimeException(__METHOD__ . ' not implemented');
    }

    public function getDangerousInnerClient()
    {
        return $this->client;
    }

    private function getLastIncompleteCheckout(Account $account, string $locale): ?string
    {
        if (isset($account->dangerousInnerAccount['lastIncompleteCheckout']['id']) &&
            $account->dangerousInnerAccount['lastIncompleteCheckout']['id'] !== null
        ) {
            return $account->dangerousInnerAccount['lastIncompleteCheckout']['id'];
        }

        $account = $this->accountApi->refreshAccount($account, $locale);

        return $account->dangerousInnerAccount['lastIncompleteCheckout']['id'] ?? null;
    }

    private function mapDataToCart(array $cartData): Cart
    {
        return new Cart([
            'cartId' => $cartData['id'] ?? null,
            'cartVersion' => $cartData['createdAt'] ?? null,
            'email' => $cartData['email'] ?? null,
            'sum' => $this->productMapper->mapDataToPriceValue(
                $cartData['totalPriceV2'] ?? []
            ),
            'currency' => $cartData['totalPriceV2']['currencyCode'] ?? null,
            'lineItems' => $this->mapDataToLineItems($cartData['lineItems']['edges'] ?? []),
            'shippingAddress' => $this->accountMapper->mapDataToAddress(
                $cartData['shippingAddress'] ?? []
            ),
            'shippingInfo' => $this->mapDataToShippingInfo(
                $cartData['shippingLine'] ?? []
            ),
            'shippingMethod' => $this->mapDataToShippingInfo(
                $cartData['shippingLine'] ?? []
            ),
            'dangerousInnerCart' => $cartData,
        ]);
    }

    private function mapDataToOrders(array $orderData): array
    {
        $orders = [];
        foreach ($orderData['edges'] as $orderData) {
            $orders[] = $this->mapDataToOrder($orderData['node']);
        }

        return $orders;
    }

    private function mapDataToOrder(array $orderData): Order
    {
        return new Order([
            'orderId' => $orderData['orderNumber'],
            'cartId' => $orderData['id'] ?? null,
            'orderState' => $orderData['financialStatus'],
            'createdAt' => new \DateTimeImmutable($orderData['processedAt']),
            'email' => $orderData['email'] ?? null,
            'lineItems' => $this->mapDataToLineItems($orderData['lineItems']['edges'] ?? []),
            'shippingAddress' => $this->accountMapper->mapDataToAddress(
                $orderData['shippingAddress'] ?? []
            ),
            'shippingInfo' => $this->mapDataToShippingInfo(
                $orderData['shippingLine'] ?? []
            ),
            'shippingMethod' => $this->mapDataToShippingInfo(
                $orderData['shippingLine'] ?? []
            ),
            'sum' => $this->productMapper->mapDataToPriceValue(
                $orderData['totalPriceV2'] ?? []
            ),
            'currency' => $orderData['totalPriceV2']['currencyCode'] ?? null,
            'dangerousInnerCart' => $orderData,
            'dangerousInnerOrder' => $orderData,
        ]);
    }

    private function mapDataToLineItems(array $lineItemsData): array
    {
        $lineItems = [];

        foreach ($lineItemsData as $lineItemData) {
            $lineItems[] = new LineItem\Variant([
                'lineItemId' => $lineItemData['node']['id'] ?? null,
                'name' => $lineItemData['node']['title'] ?? null,
                'count' => $lineItemData['node']['quantity'] ?? null,
                'price' => $lineItemData['node']['unitPrice'] ?? null,
                'currency' => $lineItemData['node']['unitPrice']['currency'] ?? null,
                'variant' => $this->productMapper->mapDataToVariant($lineItemData['node']['variant']),
                'dangerousInnerItem' => $lineItemData['node'],
            ]);
        }

        return $lineItems;
    }

    private function mapDataToShippingInfo(array $shippingMethodData): ?ShippingInfo
    {
        if (empty($shippingMethodData)) {
            return null;
        }

        return new ShippingInfo([
            'shippingMethodId' => $shippingMethodData['handle'] ?? null,
            'name' => $shippingMethodData['title'] ?? null,
            'price' => $this->productMapper->mapDataToPriceValue(
                $shippingMethodData['priceV2'] ?? []
            ),
            'dangerousInnerShippingInfo' => $shippingMethodData,
        ]);
    }

    private function mapDataToShippingMethod(array $shippingMethodData): ?ShippingMethod
    {
        if (empty($shippingMethodData)) {
            return null;
        }

        return new ShippingMethod([
            'shippingMethodId' => $shippingMethodData['handle'] ?? null,
            'name' => $shippingMethodData['title'] ?? null,
            'rates' => [
                new ShippingRate([
                    'price' => $this->productMapper->mapDataToPriceValue($shippingMethodData['priceV2'] ?? []),
                    'currency' => $shippingMethodData['priceV2']['currencyCode'] ?? null,
                ])
            ],
            'dangerousInnerShippingMethod' => $shippingMethodData,
        ]);
    }

    protected function getCheckoutQueryFields(?Cart $cart = null): string
    {
        $checkoutQueryFields = '
            id
            createdAt
            completedAt
            email
            webUrl
            requiresShipping
            totalPriceV2 {
                amount
                currencyCode
            }
        ';

        if (!isset($cart)) {
            return $checkoutQueryFields;
        }

        // Shopify requires a shipping address and the flag requiresShipping
        // in order to return the available shipping methods
        if ($cart->shippingAddress instanceof Address &&
            key_exists('requiresShipping', $cart->dangerousInnerCart) &&
            $cart->dangerousInnerCart['requiresShipping'] !== false
        ) {
            $checkoutQueryFields .= $this->getAvailableShippingRatesQueryFields();
        }

        return $checkoutQueryFields;
    }

    protected function getOrderQueryFields(): string
    {
        return '
            id
            email
            orderNumber
            processedAt
            financialStatus
            totalPriceV2 {
                amount
                currencyCode
            }
        ';
    }

    protected function getLineItemQueryFields(): string
    {
        return '
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
        return "
            id
            sku
            title
            availableForSale
            quantityAvailable
            priceV2 {
                amount
                currencyCode
            }
            compareAtPriceV2 {
                amount
                currencyCode
            }
            product {
                id
                images(first: " . self::DEFAULT_ELEMENTS_TO_FETCH . ") {
                    edges {
                        node {
                            originalSrc
                        }
                    }
                }
            }
            selectedOptions {
                name
                value
            }
            image {
                originalSrc
            }
        ";
    }

    protected function getAddressQueryFields(): string
    {
        return '
            id
            address1
            address2
            city
            country
            firstName
            lastName
            phone
            province
            zip
        ';
    }

    protected function getAvailableShippingRatesQueryFields(): string
    {
        return "
            availableShippingRates {
                shippingRates {
                    {$this->getShippingLineQueryFields()}
                }
            }
        ";
    }

    protected function getShippingLineQueryFields(): string
    {
        return '
            handle
            title
            priceV2 {
                amount
                currencyCode
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
