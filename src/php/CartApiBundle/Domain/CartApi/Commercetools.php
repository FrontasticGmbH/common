<?php

namespace Frontastic\Common\CartApiBundle\Domain\CartApi;

use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\CartApi;
use Frontastic\Common\CartApiBundle\Domain\Discount;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\CartApiBundle\Domain\Order;
use Frontastic\Common\CartApiBundle\Domain\OrderIdGenerator;
use Frontastic\Common\CartApiBundle\Domain\Payment;
use Frontastic\Common\CartApiBundle\Domain\ShippingMethod;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale\CommercetoolsLocale;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale\CommercetoolsLocaleCreator;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Mapper;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects) Due to implementation of CartApi
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity) FIXME: Refactor!
 */
class Commercetools implements CartApi
{
    const EXPAND = [
        'lineItems[*].discountedPrice.includedDiscounts[*].discount',
        'discountCodes[*].discountCode',
        'paymentInfo.payments[*]',
    ];

    /**
     * @var Client
     */
    private $client;

    /**
     * @var Mapper
     */
    private $mapper;

    /**
     * @var CommercetoolsLocaleCreator
     */
    private $localeCreator;

    /**
     * @var OrderIdGenerator
     */
    private $orderIdGenerator;

    /**
     * @var Cart?
     */
    private $inTransaction = null;

    /**
     * @var array[]
     */
    private $actions = [];

    /**
     * @var array
     */
    private $lineItemType = null;

    /**
     * @var array
     */
    private $taxCategory = null;

    /**
     * Commercetools constructor.
     *
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client $client
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Mapper $mapper
     * @param \Frontastic\Common\CartApiBundle\Domain\OrderIdGenerator $orderIdGenerator
     */
    public function __construct(
        Client $client,
        Mapper $mapper,
        CommercetoolsLocaleCreator $localeCreator,
        OrderIdGenerator $orderIdGenerator
    ) {
        $this->client = $client;
        $this->mapper = $mapper;
        $this->localeCreator = $localeCreator;
        $this->orderIdGenerator = $orderIdGenerator;
    }

    /**
     * @param string $userId
     * @param string $localeString
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     * @todo Should we catch the RequestException here?
     */
    public function getForUser(string $userId, string $localeString): Cart
    {
        $locale = $this->localeCreator->createLocaleFromString($localeString);

        try {
            $cart = $this->mapCart(
                $this->client->get(
                    '/carts',
                    [
                        'customerId' => $userId,
                        'expand' => self::EXPAND,
                    ]
                ),
                $locale
            );

            return $this->assertCorrectLocale($cart, $locale);
        } catch (RequestException $e) {
            return $this->mapCart(
                $this->client->post(
                    '/carts',
                    ['expand' => self::EXPAND],
                    [],
                    json_encode([
                        'country' => $locale->country,
                        'currency' => $locale->currency,
                        'customerId' => $userId,
                        'state' => 'Active',
                        'inventoryMode' => 'ReserveOnOrder',
                    ])
                ),
                $locale
            );
        }
    }

    private function assertCorrectLocale(Cart $cart, CommercetoolsLocale $locale): Cart
    {
        if ($cart->currency !== strtoupper($locale->currency)
            || $cart->dangerousInnerCart['country'] !== strtoupper($locale->country)
        ) {
            $actions = [];
            $actions[] = [
                'action' => 'setCountry',
                'country' => $locale->country,
            ];

            return $this->postCartActions($cart, $actions, $locale);
        }
        return $cart;
    }

    /**
     * @param string $anonymousId
     * @param string $localeString
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     * @todo Should we catch the RequestException here?
     */
    public function getAnonymous(string $anonymousId, string $localeString): Cart
    {
        $locale = $this->localeCreator->createLocaleFromString($localeString);

        $result = $this->client
            ->fetchAsync(
                '/carts',
                [
                    'where' => 'anonymousId="' . $anonymousId . '"',
                    'limit' => 1,
                    'expand' => self::EXPAND,
                ]
            )
            ->wait();

        if ($result->count >= 1) {
            return $this->assertCorrectLocale($this->mapCart($result->results[0], $locale), $locale);
        }

        return $this->mapCart(
            $this->client->post(
                '/carts',
                ['expand' => self::EXPAND],
                [],
                json_encode([
                    'country' => $locale->country,
                    'currency' => $locale->currency,
                    'anonymousId' => $anonymousId,
                    'state' => 'Active',
                    'inventoryMode' => 'ReserveOnOrder',
                ])
            ),
            $locale
        );
    }

    /**
     * @param string $cartId
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     * @throws \RuntimeException if cart with $cartId was not found
     */
    public function getById(string $cartId, string $localeString = null): Cart
    {
        return $this->mapCart(
            $this->client->get(
                '/carts/' . urlencode($cartId)
            ),
            $this->parseLocaleString($localeString)
        );
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param \Frontastic\Common\CartApiBundle\Domain\LineItem $lineItem
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function addToCart(Cart $cart, LineItem $lineItem, string $localeString = null): Cart
    {
        $locale = $this->parseLocaleString($localeString);

        if ($lineItem instanceof LineItem\Variant) {
            return $this->addVariantToCart($cart, $lineItem, $locale);
        }

        return $this->addCustomToCart($cart, $lineItem, $locale);
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param \Frontastic\Common\CartApiBundle\Domain\LineItem\Variant $lineItem
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    private function addVariantToCart(Cart $cart, LineItem\Variant $lineItem, CommercetoolsLocale $locale): Cart
    {
        return $this->postCartActions(
            $cart,
            [
                [
                    'action' => 'addLineItem',
                    'sku' => $lineItem->variant->sku,
                    'quantity' => $lineItem->count,
                    'custom' => !$lineItem->custom ? null : [
                        'type' => $this->getCustomLineItemType(),
                        'fields' => $lineItem->custom,
                    ],
                ],
            ],
            $locale
        );
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param \Frontastic\Common\CartApiBundle\Domain\LineItem $lineItem
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    private function addCustomToCart(Cart $cart, LineItem $lineItem, CommercetoolsLocale $locale): Cart
    {
        return $this->postCartActions(
            $cart,
            [
                [
                    'action' => 'addCustomLineItem',
                    'name' => [$locale->language => $lineItem->name],
                    // Must be unique inside the entire cart. We do not use
                    // this for anything relevant. Random seems fine for now.
                    'slug' => md5(microtime()),
                    'taxCategory' => $this->getTaxCategory(),
                    'money' => [
                        'type' => 'centPrecision',
                        'currencyCode' => $locale->currency,
                        'centAmount' => $lineItem->totalPrice,
                    ],
                    'custom' => !$lineItem->custom ? null : [
                        'type' => $this->getCustomLineItemType(),
                        'fields' => $lineItem->custom,
                    ],
                    'quantity' => $lineItem->count,
                ],
            ],
            $locale
        );
    }

    public function updateLineItem(
        Cart $cart,
        LineItem $lineItem,
        int $count,
        ?array $custom = null,
        string $localeString = null
    ): Cart {
        $actions = [];
        if ($lineItem instanceof LineItem\Variant) {
            $actions[] = [
                'action' => 'changeLineItemQuantity',
                'lineItemId' => $lineItem->lineItemId,
                'quantity' => $count,
            ];
        } else {
            $actions[] = [
                'action' => 'changeCustomLineItemQuantity',
                'customLineItemId' => $lineItem->lineItemId,
                'quantity' => $count,
            ];
        }

        if ($custom) {
            foreach ($custom as $field => $value) {
                $actions[] = [
                    'action' => 'setLineItemCustomField',
                    'lineItemId' => $lineItem->lineItemId,
                    'name' => $field,
                    'value' => $value,
                ];
            }
        }

        return $this->postCartActions($cart, $actions, $this->parseLocaleString($localeString));
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param \Frontastic\Common\CartApiBundle\Domain\LineItem $lineItem
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function removeLineItem(Cart $cart, LineItem $lineItem, string $localeString = null): Cart
    {
        $locale = $this->parseLocaleString($localeString);

        if ($lineItem instanceof LineItem\Variant) {
            return $this->postCartActions(
                $cart,
                [
                    [
                        'action' => 'removeLineItem',
                        'lineItemId' => $lineItem->lineItemId,
                    ],
                ],
                $locale
            );
        } else {
            return $this->postCartActions(
                $cart,
                [
                    [
                        'action' => 'removeCustomLineItem',
                        'customLineItemId' => $lineItem->lineItemId,
                    ],
                ],
                $locale
            );
        }
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param string $email
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function setEmail(Cart $cart, string $email, string $localeString = null): Cart
    {
        return $this->postCartActions(
            $cart,
            [
                [
                    'action' => 'setCustomerEmail',
                    'email' => $email,
                ],
            ],
            $this->parseLocaleString($localeString)
        );
    }

    public function setShippingMethod(Cart $cart, string $shippingMethod, string $localeString = null): Cart
    {
        return $this->postCartActions(
            $cart,
            [
                [
                    'action' => 'setShippingMethod',
                    'shippingMethod' => [
                        'typeId' => 'shipping-method',
                        'id' => $shippingMethod,
                    ],
                ],
            ],
            $this->parseLocaleString($localeString)
        );
    }

    public function setCustomField(Cart $cart, array $fields, string $localeString = null): Cart
    {
        if (!count(array_filter($fields))) {
            return $cart;
        }

        $actions = [];
        foreach ($fields as $name => $value) {
            $actions[] = [
                'action' => 'setCustomField',
                'name' => $name,
                'value' => $value,
            ];
        }

        return $this->postCartActions($cart, $actions, $this->parseLocaleString($localeString));
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param array $address
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function setShippingAddress(Cart $cart, array $address, string $localeString = null): Cart
    {
        return $this->postCartActions(
            $cart,
            [
                [
                    'action' => 'setShippingAddress',
                    'address' => $this->reverseMapAddress($address),
                ],
            ],
            $this->parseLocaleString($localeString)
        );
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param array $address
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function setBillingAddress(Cart $cart, array $address, string $localeString = null): Cart
    {
        return $this->postCartActions(
            $cart,
            [
                [
                    'action' => 'setBillingAddress',
                    'address' => $this->reverseMapAddress($address),
                ],
            ],
            $this->parseLocaleString($localeString)
        );
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param \Frontastic\Common\CartApiBundle\Domain\Payment $payment
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     * @todo Should we catch the RequestException here?
     */
    public function addPayment(Cart $cart, Payment $payment, ?array $custom = null, string $localeString = null): Cart
    {
        $payment = $this->client->post(
            '/payments',
            [],
            [],
            json_encode([
                'amountPlanned' => [
                    'centAmount' => $payment->amount,
                    'currencyCode' => $payment->currency,
                ],
                'interfaceId' => $payment->paymentId,
                'paymentMethodInfo' => [
                    'paymentInterface' => $payment->paymentProvider,
                ],
                'paymentStatus' => [
                    'interfaceCode' => 'frontastic',
                    'interfaceText' => $payment->debug,
                ],
                'custom' => $custom,
            ])
        );

        return $this->postCartActions(
            $cart,
            [
                [
                    'action' => 'addPayment',
                    'payment' => [
                        'typeId' => 'payment',
                        'id' => $payment['id'],
                    ],
                ],
            ],
            $this->parseLocaleString($localeString)
        );
    }

    public function redeemDiscountCode(Cart $cart, string $code, string $localeString = null): Cart
    {
        return $this->postCartActions(
            $cart,
            [
                [
                    'action' => 'addDiscountCode',
                    'code' => str_replace('%', '', $code),
                ],
            ],
            $this->parseLocaleString($localeString)
        );
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @return \Frontastic\Common\CartApiBundle\Domain\Order
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     * @todo Should we catch the RequestException here?
     */
    public function order(Cart $cart): Order
    {
        return $this->mapOrder($this->client->post(
            '/orders',
            ['expand' => self::EXPAND],
            [],
            json_encode([
                'id' => $cart->cartId,
                'version' => $cart->cartVersion,
                'orderNumber' => $this->orderIdGenerator->getOrderId($cart),
            ])
        ));
    }

    /**
     * @param string $orderId
     * @return \Frontastic\Common\CartApiBundle\Domain\Order
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     * @todo Should we catch the RequestException here?
     */
    public function getOrder(string $orderId): Order
    {
        return $this->mapOrder($this->client->get(
            '/orders/order-number=' . $orderId,
            ['expand' => self::EXPAND]
        ));
    }

    /**
     * @param string $accountId
     * @return \Frontastic\Common\CartApiBundle\Domain\Order[]
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     * @todo Should we catch the RequestException here?
     */
    public function getOrders(string $accountId): array
    {
        $result = $this->client
            ->fetchAsync(
                '/orders',
                [
                    'where' => 'customerId="' . $accountId . '"',
                    'expand' => self::EXPAND,
                ]
            )
            ->wait();

        return array_map(
            [$this, 'mapOrder'],
            $result->results
        );
    }

    /**
     * @param array $cart
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    private function mapCart(array $cart, CommercetoolsLocale $locale): Cart
    {
        /**
         * @TODO:
         *
         * [ ] Map delivery costs / properties
         * [ ] Map product discounts
         * [ ] Map discount codes
         * [ ] Map tax information
         * [ ] Map discount text locales to our scheme
         */
        return new Cart([
            'cartId' => $cart['id'],
            'cartVersion' => $cart['version'],
            'custom' => $cart['custom']['fields'] ?? [],
            'lineItems' => $this->mapLineItems($cart, $locale),
            'email' => $cart['customerEmail'] ?? null,
            'birthday' => isset($cart['custom']['fields']['birthday']) ?
                new \DateTimeImmutable($cart['custom']['fields']['birthday']) :
                null,
            'shippingMethod' => $this->mapShippingMethod($cart['shippingInfo'] ?? []),
            'shippingAddress' => $this->mapAddress($cart['shippingAddress'] ?? []),
            'billingAddress' => $this->mapAddress($cart['billingAddress'] ?? []),
            'sum' => $cart['totalPrice']['centAmount'],
            'currency' => $cart['totalPrice']['currencyCode'],
            'payments' => $this->mapPayments($cart),
            'discountCodes' => $this->mapDiscounts($cart),
            'dangerousInnerCart' => $cart,
        ]);
    }

    /**
     * @param array $order
     * @return \Frontastic\Common\CartApiBundle\Domain\Order
     */
    private function mapOrder(array $order, CommercetoolsLocale $locale = null): Order
    {
        if ($locale === null) {
            $locale = $this->parseLocaleString(null);
        }

        /**
         * @TODO:
         *
         * [ ] Map delivery costs / properties
         * [ ] Map product discounts
         * [ ] Map discount codes
         * [ ] Map tax information
         * [ ] Map delivery status
         * [ ] Map order status
         */
        $order = new Order([
            'cartId' => $order['id'],
            'custom' => $order['custom']['fields'] ?? [],
            'orderState' => $order['orderState'],
            'createdAt' => new \DateTimeImmutable($order['createdAt']),
            'orderId' => $order['orderNumber'],
            'orderVersion' => $order['version'],
            'lineItems' => $this->mapLineItems($order, $locale),
            'email' => $order['customerEmail'] ?? null,
            'birthday' => isset($order['custom']['fields']['birthday']) ?
                new \DateTimeImmutable($order['custom']['fields']['birthday']) :
                null,
            'shippingMethod' => $this->mapShippingMethod($order['shippingInfo'] ?? []),
            'shippingAddress' => $this->mapAddress($order['shippingAddress'] ?? []),
            'billingAddress' => $this->mapAddress($order['billingAddress'] ?? []),
            'sum' => $order['totalPrice']['centAmount'],
            'payments' => $this->mapPayments($order),
            'discountCodes' => $this->mapDiscounts($order),
            'dangerousInnerCart' => $order,
            'dangerousInnerOrder' => $order,
        ]);
        return $order;
    }

    private function mapAddress(array $address): ?Address
    {
        if (!count($address)) {
            return null;
        }

        return new Address([
            'addressId' => $address['id'] ?? null,
            'salutation' => $address['salutation'] ?? null,
            'firstName' => $address['firstName'] ?? null,
            'lastName' => $address['lastName'] ?? null,
            'streetName' => $address['streetName'] ?? null,
            'streetNumber' => $address['streetNumber'] ?? null,
            'additionalStreetInfo' => $address['additionalStreetInfo'] ?? null,
            'additionalAddressInfo' => $address['additionalAddressInfo'] ?? null,
            'postalCode' => $address['postalCode'] ?? null,
            'city' => $address['city'] ?? null,
            'country' => $address['country'] ?? null,
            'phone' => $address['phone'] ?? null,
        ]);
    }

    private function reverseMapAddress(array $address): array
    {
        return [
            'id' => $address['addressId'] ?? null,
            'salutation' => $address['salutation'] ?? null,
            'firstName' => $address['firstName'] ?? null,
            'lastName' => $address['lastName'] ?? null,
            'streetName' => $address['streetName'] ?? null,
            'streetNumber' => $address['streetNumber'] ?? null,
            'additionalStreetInfo' => $address['additionalStreetInfo'] ?? null,
            'additionalAddressInfo' => $address['additionalAddressInfo'] ?? null,
            'postalCode' => $address['postalCode'] ?? null,
            'city' => $address['city'] ?? null,
            'country' => $address['country'] ?? null,
            'phone' => $address['phone'] ?? null,
        ];
    }

    private function mapShippingMethod(array $shipping): ?ShippingMethod
    {
        if (!count($shipping)) {
            return null;
        }

        return new ShippingMethod([
            'name' => $shipping['shippingMethodName'] ?? null,
            'price' => $shipping['price']['centAmount'] ?? null,
        ]);
    }

    /**
     * @param array $cart
     * @return \Frontastic\Common\CartApiBundle\Domain\LineItem[]
     */
    private function mapLineItems(array $cart, CommercetoolsLocale $locale): array
    {
        $lineItems = array_merge(
            array_map(
                function (array $lineItem) use ($locale): LineItem {
                    return new LineItem\Variant([
                        'lineItemId' => $lineItem['id'],
                        'name' => reset($lineItem['name']),
                        'type' => 'variant',
                        'variant' => $this->mapper->dataToVariant(
                            $lineItem['variant'],
                            new Query(),
                            $locale
                        ),
                        'custom' => $lineItem['custom']['fields'] ?? [],
                        'count' => $lineItem['quantity'],
                        'price' => $lineItem['price']['value']['centAmount'],
                        'discountedPrice' => (isset($lineItem['discountedPrice'])
                            ? $lineItem['totalPrice']['centAmount']
                            : null
                        ),
                        'discountTexts' => array_map(
                            function ($discount): array {
                                return $discount['discount']['obj']['name'] ?? [];
                            },
                            (isset($lineItem['discountedPrice']['includedDiscounts'])
                                ? $lineItem['discountedPrice']['includedDiscounts']
                                : []
                            )
                        ),
                        'totalPrice' => $lineItem['totalPrice']['centAmount'],
                        'currency' => $lineItem['totalPrice']['currencyCode'],
                        'isGift' => ($lineItem['lineItemMode'] === 'GiftLineItem'),
                        'dangerousInnerItem' => $lineItem,
                    ]);
                },
                $cart['lineItems']
            ),
            array_map(
                function (array $lineItem): LineItem {
                    return new LineItem([
                        'lineItemId' => $lineItem['id'],
                        'name' => reset($lineItem['name']),
                        'type' => $lineItem['custom']['type'] ?? $lineItem['slug'],
                        'custom' => $lineItem['custom']['fields'] ?? [],
                        'count' => $lineItem['quantity'],
                        'price' => $lineItem['money']['centAmount'],
                        'discountedPrice' => (isset($lineItem['discountedPrice'])
                            ? $lineItem['totalPrice']['centAmount']
                            : null
                        ),
                        'discountTexts' => array_map(
                            function ($discount): array {
                                return $discount['discount']['obj']['name'] ?? [];
                            },
                            (isset($lineItem['discountedPrice']['includedDiscounts'])
                                ? $lineItem['discountedPrice']['includedDiscounts']
                                : []
                            )
                        ),
                        'totalPrice' => $lineItem['totalPrice']['centAmount'],
                        'dangerousInnerItem' => $lineItem,
                    ]);
                },
                $cart['customLineItems']
            )
        );

        usort(
            $lineItems,
            function (LineItem $a, LineItem $b): int {
                return ($a->custom['bundleNumber'] ?? $a->name) <=>
                    ($b->custom['bundleNumber'] ?? $b->name);
            }
        );

        return $lineItems;
    }

    private function mapPayments(array $cart): array
    {
        if (empty($cart['paymentInfo']['payments'])) {
            return [];
        }

        $payments = [];
        foreach ($cart['paymentInfo']['payments'] as $payment) {
            $payment = isset($payment['obj']) ? $payment['obj'] : $payment;
            $payments[] = new Payment([
                'paymentId' => $payment['interfaceId'] ?? null,
                'paymentProvider' => $payment['paymentMethodInfo']['paymentInterface'] ?? null,
                'amount' => $payment['amountPlanned']['centAmount'] ?? null,
                'currency' => $payment['amountPlanned']['currencyCode'] ?? null,
                'debug' => json_encode($payment),
            ]);
        }

        return $payments;
    }

    private function mapDiscounts(array $cart): array
    {
        if (empty($cart['discountCodes'])) {
            return [];
        }

        $discounts = [];
        foreach ($cart['discountCodes'] as $discount) {
            $discount = $discount['discountCode'] ?? [];
            $discount = isset($discount['obj']) ? $discount['obj'] : $discount;
            $discounts[] = new Discount([
                'discountId' => $discount['id'] ?? 'undefined',
                'name' => $discount['name'] ?? null,
                'description' => $discount['description'] ?? null,
                'dangerousInnerDiscount' => $discount,
            ]);
        }

        return $discounts;
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param array $actions
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     */
    protected function postCartActions(Cart $cart, array $actions, CommercetoolsLocale $locale)
    {
        if ($cart === $this->inTransaction) {
            $this->actions = array_merge(
                $this->actions,
                $actions
            );

            return $cart;
        }

        // The idea to fetch the current cart seems not to work. Updates do not
        // seem to be instant, so that we stll run into version conflicts here…
        // $currentCart = $this->client->get('/carts/' . $cart->cartId);

        return $this->mapCart(
            $this->client->post(
                '/carts/' . $cart->cartId,
                ['expand' => self::EXPAND],
                [],
                json_encode([
                    'version' => $cart->cartVersion,
                    'actions' => $actions,
                ])
            ),
            $locale
        );
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     */
    public function startTransaction(Cart $cart): void
    {
        $this->inTransaction = $cart;
    }

    /**
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     * @todo Should we catch the RequestException here?
     */
    public function commit(string $localeString = null): Cart
    {
        $cart = $this->inTransaction;
        $this->inTransaction = null;
        $cart = $this->postCartActions($cart, $this->actions, $this->parseLocaleString($localeString));
        $this->actions = [];

        return $cart;
    }

    /**
     * Get *dangerous* inner client
     *
     * This method exists to enable you to use features which are not yet part
     * of the abstraction layer.
     *
     * Be aware that any usage of this method might seriously hurt backwards
     * compatibility and the future abstractions might differ a lot from the
     * vendor provided abstraction.
     *
     * Use this with care for features necessary in your customer and talk with
     * Frontastic about provising an abstraction.
     *
     * @return \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client
     */
    public function getDangerousInnerClient()
    {
        return $this->client;
    }

    /**
     * @param array $lineItemType
     */
    public function setCustomLineItemType(array $lineItemType): void
    {
        $this->lineItemType = $lineItemType;
    }

    /**
     * @return array
     */
    public function getCustomLineItemType(): array
    {
        if (!$this->lineItemType) {
            throw new \RuntimeException(
                'Before inserting custom properties into Commercetools you must
                define (https://docs.commercetools.com/http-api-projects-types.html)
                and provide a custom type for it. Use a beforeAddToCart() hook
                to set your custom type into this API ($cartApi->setCustomLineItemType).'
            );
        }

        return $this->lineItemType;
    }

    /**
     * @param array $taxCategory
     */
    public function setTaxCategory(array $taxCategory): void
    {
        $this->taxCategory = $taxCategory;
    }

    /**
     * @return array
     */
    public function getTaxCategory(): array
    {
        if (!$this->taxCategory) {
            throw new \RuntimeException(
                'Before inserting custom line items into Commercetools you must
                define (https://docs.commercetools.com/http-api-projects-taxCategories)
                and provide a tax category for it. Use a beforeAddToCart() hook
                to set your tax category into this API ($cartApi->setTaxCategory).'
            );
        }

        return $this->taxCategory;
    }

    /**
     * @param string $localeString
     * @return CommercetoolsLocale
     */
    private function parseLocaleString(?string $localeString): CommercetoolsLocale
    {
        if ($localeString !== null) {
            return $this->localeCreator->createLocaleFromString($localeString);
        }

        return new CommercetoolsLocale([
            'language' => 'de',
            'country' => 'DE',
            'currency' => 'EUR',
        ]);
    }
}
