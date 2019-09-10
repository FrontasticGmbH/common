<?php

namespace Frontastic\Common\CartApiBundle\Domain\CartApi;

use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\CartApi;
use Frontastic\Common\CartApiBundle\Domain\Category;
use Frontastic\Common\CartApiBundle\Domain\Discount;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\CartApiBundle\Domain\Order;
use Frontastic\Common\CartApiBundle\Domain\OrderIdGenerator;
use Frontastic\Common\CartApiBundle\Domain\Payment;
use Frontastic\Common\CartApiBundle\Domain\ShippingMethod;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Mapper;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale;
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
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client  $client
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Mapper  $mapper
     * @param \Frontastic\Common\CartApiBundle\Domain\OrderIdGenerator                    $orderIdGenerator
     */
    public function __construct(Client $client, Mapper $mapper, OrderIdGenerator $orderIdGenerator)
    {
        $this->client = $client;
        $this->mapper = $mapper;
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
        $locale = Locale::createFromPosix($localeString);

        try {
            $cart = $this->mapCart($this->client->get('/carts', [
                'customerId' => $userId,
                'expand' => self::EXPAND,
            ]));

            return $this->assertCorrectLocale($cart, $locale);
        } catch (RequestException $e) {
            return $this->mapCart($this->client->post(
                '/carts',
                ['expand' => self::EXPAND],
                [],
                json_encode([
                    'country' => $locale->territory,
                    'currency' => $locale->currency,
                    'locale' => $locale->language,
                    'customerId' => $userId,
                    'state' => 'Active',
                    'inventoryMode' => 'ReserveOnOrder',
                ])
            ));
        }
    }

    private function assertCorrectLocale(Cart $cart, Locale $locale): Cart
    {
        if ($cart->currency !== strtoupper($locale->currency)) {
            $cartArray = $cart->dangerousInnerCart;
            $cartArray['country'] = $locale->territory;
            $cartArray['locale'] = $locale->language;
            $cartArray['currency'] = $locale->currency;
            return $this->recreate($cartArray);
        }
        if ($cart->dangerousInnerCart['country'] !== $locale->territory
            || $cart->dangerousInnerCart['locale'] !== $locale->language
        ) {
            $actions = [];

            $setCountryAction = [
                'action'  => 'setCountry',
                'country' => $locale->territory,
            ];
            $setLocaleAction  = [
                'action' => 'setLocale',
                'locale' => $locale->language,
            ];

            array_push($actions, $setCountryAction);
            array_push($actions, $setLocaleAction);

            return $this->postCartActions($cart, $actions);
        }
        return $cart;
    }

    private function recreate(array $dangerousInnerCart): Cart
    {
        $cartId = $dangerousInnerCart['id'];
        $cartVersion = $dangerousInnerCart['version'];
        unset($dangerousInnerCart['id'], $dangerousInnerCart['version']);
        $cart = $this->mapCart($this->client->post(
            '/carts',
            ['expand' => self::EXPAND],
            [],
            json_encode($dangerousInnerCart)
        ));
        $this->client->delete(
            '/carts/' . urlencode($cartId),
            ['version' => $cartVersion]
        );

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
        $locale = Locale::createFromPosix($localeString);

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
            return $this->assertCorrectLocale($this->mapCart($result->results[0]), $locale);
        }

        return $this->mapCart($this->client->post(
            '/carts',
            ['expand' => self::EXPAND],
            [],
            json_encode([
                'country' => $locale->territory,
                'currency' => $locale->currency,
                'locale' => $locale->language,
                'anonymousId' => $anonymousId,
                'state' => 'Active',
                'inventoryMode' => 'ReserveOnOrder',
            ])
        ));
    }

    /**
     * @param string $cartId
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     * @throws \RuntimeExcption if cart with $cartId was not found
     */
    public function getById(string $cartId): Cart
    {
        return $this->mapCart($this->client->get(
            '/carts/' . urlencode($cartId)
        ));
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param \Frontastic\Common\CartApiBundle\Domain\LineItem $lineItem
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function addToCart(Cart $cart, LineItem $lineItem): Cart
    {
        if ($lineItem instanceof LineItem\Variant) {
            return $this->addVariantToCart($cart, $lineItem);
        }

        return $this->addCustomToCart($cart, $lineItem);
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param \Frontastic\Common\CartApiBundle\Domain\LineItem\Variant $lineItem
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    private function addVariantToCart(Cart $cart, LineItem\Variant $lineItem): Cart
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
            ]
        );
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param \Frontastic\Common\CartApiBundle\Domain\LineItem $lineItem
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    private function addCustomToCart(Cart $cart, LineItem $lineItem): Cart
    {
        return $this->postCartActions(
            $cart,
            [
                [
                    'action' => 'addCustomLineItem',
                    'name' => ['de' => $lineItem->name],
                    // Must be unique inside the entire cart. We do not use
                    // this for anything relevant. Random seems fine for now.
                    'slug' => md5(microtime()),
                    'taxCategory' => $this->getTaxCategory(),
                    'money' => [
                        'type' => 'centPrecision',
                        'currencyCode' => 'EUR', // @TODO: Get from context
                        'centAmount' => $lineItem->totalPrice,
                    ],
                    'custom' => !$lineItem->custom ? null : [
                        'type' => $this->getCustomLineItemType(),
                        'fields' => $lineItem->custom,
                    ],
                    'quantity' => $lineItem->count,
                ],
            ]
        );
    }

    public function updateLineItem(Cart $cart, LineItem $lineItem, int $count, ?array $custom = null): Cart
    {
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

        return $this->postCartActions($cart, $actions);
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param \Frontastic\Common\CartApiBundle\Domain\LineItem $lineItem
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function removeLineItem(Cart $cart, LineItem $lineItem): Cart
    {
        if ($lineItem instanceof LineItem\Variant) {
            return $this->postCartActions(
                $cart,
                [
                    [
                        'action' => 'removeLineItem',
                        'lineItemId' => $lineItem->lineItemId,
                    ],
                ]
            );
        } else {
            return $this->postCartActions(
                $cart,
                [
                    [
                        'action' => 'removeCustomLineItem',
                        'customLineItemId' => $lineItem->lineItemId,
                    ],
                ]
            );
        }
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param string $email
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function setEmail(Cart $cart, string $email): Cart
    {
        return $this->postCartActions(
            $cart,
            [
                [
                    'action' => 'setCustomerEmail',
                    'email' => $email,
                ],
            ]
        );
    }


    public function setShippingMethod(Cart $cart, string $shippingMethod): Cart
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
            ]
        );
    }

    public function setCustomField(Cart $cart, array $fields): Cart
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

        return $this->postCartActions($cart, $actions);
    }

    public function setCustomType(Cart $cart, string $id): Cart
    {
        $actions = [];
            $actions[] = [
                'action' => 'setCustomType',
                'type' => [
                    "id"=> $id,
                    "typeId"=> "type"
                ]
            ];
        return $this->postCartActions($cart, $actions);
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param array $address
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function setShippingAddress(Cart $cart, array $address): Cart
    {
        return $this->postCartActions(
            $cart,
            [
                [
                    'action' => 'setShippingAddress',
                    'address' => $this->reverseMapAddress($address),
                ],
            ]
        );
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param array $address
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    public function setBillingAddress(Cart $cart, array $address): Cart
    {
        return $this->postCartActions(
            $cart,
            [
                [
                    'action' => 'setBillingAddress',
                    'address' => $this->reverseMapAddress($address),
                ],
            ]
        );
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\Cart $cart
     * @param \Frontastic\Common\CartApiBundle\Domain\Payment $payment
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     * @todo Should we catch the RequestException here?
     */
    public function addPayment(Cart $cart, Payment $payment, ?array $custom = null): Cart
    {
        $payment = $this->client->post(
            '/payments',
            [],
            [],
            json_encode([
                'key'               => $payment->id,
                'amountPlanned'     => [
                    'centAmount' => $payment->amount,
                    'currencyCode' => $payment->currency,
                ],
                'interfaceId'       => $payment->paymentId,
                'paymentMethodInfo' => [
                    'paymentInterface' => $payment->paymentProvider,
                    'method' => $payment->paymentMethod,
                ],
                'paymentStatus'     => [
                    'interfaceCode' => $payment->paymentStatus,
                    'interfaceText' => $payment->debug,
                ],
                'custom'            => $custom,
            ])
        );

        return $this->postCartActions(
            $cart,
            [
                [
                    'action' => 'addPayment',
                    'payment' => [
                        'typeId' => 'payment',
                        'id' => $payment['id']
                    ]
                ],
            ]
        );
    }

    public function redeemDiscountCode(Cart $cart, string $code): Cart
    {
        return $this->postCartActions(
            $cart,
            [
                [
                    'action' => 'addDiscountCode',
                    'code' => str_replace('%', '', $code),
                ],
            ]
        );
    }

    public function removeDiscountCode(Cart $cart, string $discountId): Cart
    {
        return $this->postCartActions(
            $cart,
            [
                [
                    'action' => 'removeDiscountCode',
                    'discountCode' => [
                        'typeId' => 'discount-code',
                        'id' => $discountId
                    ],
                ],
            ]
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
        $order = $this->mapOrder($this->client->post(
            '/orders',
            ['expand' => self::EXPAND],
            [],
            json_encode([
                'id' => $cart->cartId,
                'version' => $cart->cartVersion,
                'orderNumber' => $this->orderIdGenerator->getOrderId($cart),
            ])
        ));

        $cart = $this->getById($cart->cartId);
        $this->client->delete(
            '/carts/' . urlencode($cart->cartId),
            ['version' => $cart->cartVersion]
        );

        return $order;
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
                ]
            )
            ->wait();

        return array_map(
            [$this, 'mapOrder'],
            $result->results
        );
    }

    /**
     * This method is a temporary hack to recieve new orders. The
     * synchronization is based on a locally stored sequence number.
     *
     * @return \Frontastic\Common\CartApiBundle\Domain\Order[]
     * @throws \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException
     * @todo Should we catch the RequestException here?
     */
    public function getNewOrders(): array
    {
        $since = @file_get_contents('/tmp/lastOrder') ?: '2000-01-01T01:00:00.000Z';

        $result = $this->client
            ->fetchAsync(
                '/messages',
                [
                    'where' => 'type="OrderCreated" and createdAt > "' . $since . '"',
                ]
            )
            ->wait();

        $orders = [];
        foreach ($result->results as $orderCreated) {
            $orders[] = $this->mapOrder($orderCreated['order']);
            file_put_contents('/tmp/lastOrder', $orderCreated['createdAt']);
        }

        return $orders;
    }

    /**
     * @param array $cart
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    private function mapCart(array $cart): Cart
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
            'lineItems' => $this->mapLineItems($cart),
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
    private function mapOrder(array $order): Order
    {
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
            'lineItems' => $this->mapLineItems($order),
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
            'currency' =>  $order['totalPrice']['currencyCode']
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
    private function mapLineItems(array $cart): array
    {
        $lineItems = array_merge(
            array_map(
                function (array $lineItem): LineItem {
                    list($price, $currency, $discountedPrice) = $this->mapper->dataToPrice($lineItem, new Locale());
                    return new LineItem\Variant([
                        'lineItemId' => $lineItem['id'],
                        'name' => reset($lineItem['name']),
                        'type' => 'variant',
                        'variant' => $this->mapper->dataToVariant($lineItem['variant'], new Query(), new Locale()),
                        'custom' => $lineItem['custom']['fields'] ?? [],
                        'count' => $lineItem['quantity'],
                        'price' => $price,
                        'discountedPrice' => $discountedPrice,
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
                        'currency' => $currency,
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
            $payments[] = $this->mapPayment($payment);
        }

        return $payments;
    }

    private function mapPayment(array $payment): Payment
    {
        $payment = isset($payment['obj']) ? $payment['obj'] : $payment;

        return new Payment(
            [
                'id'              => $payment['key'] ?? null,
                'paymentId'       => $payment['interfaceId'] ?? null,
                'paymentProvider' => $payment['paymentMethodInfo']['paymentInterface'] ?? null,
                'paymentMethod'   => $payment['paymentMethodInfo']['method'] ?? null,
                'amount'          => $payment['amountPlanned']['centAmount'] ?? null,
                'currency'        => $payment['amountPlanned']['currencyCode'] ?? null,
                'debug'           => json_encode($payment),
                'paymentStatus'   => $payment['paymentStatus']['interfaceCode'] ?? null,
                'version'         => $payment['version'] ?? 0,
            ]
        );
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
                'code' => $discount['code'] ?? null,
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
    protected function postCartActions(Cart $cart, array $actions)
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

        return $this->mapCart($this->client->post(
            '/carts/' . $cart->cartId,
            ['expand' => self::EXPAND],
            [],
            json_encode([
                'version' => $cart->cartVersion,
                'actions' => $actions,
            ])
        ));
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
    public function commit(): Cart
    {
        $cart = $this->inTransaction;
        $this->inTransaction = null;
        $cart = $this->postCartActions($cart, $this->actions);
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

    public function updatePaymentStatus(Payment $payment): void
    {
        $this->client->post(
            'payments/key='.$payment->id,
            [],
            [],
            json_encode(
                [
                    'version' => $payment->version,
                    'actions' => [
                        [
                            'action'        => 'setStatusInterfaceCode',
                            'interfaceCode' => $payment->paymentStatus,
                        ],
                    ],
                ]
            )
        );
    }

    public function getPayment(string $paymentId): ?Payment
    {
        $payment = $this->client->get(
            'payments/key='.$paymentId,
            ['expand' => self::EXPAND]
        );

        if (empty($payment)) {
            return null;
        }

        return $this->mapPayment($payment);
    }

    public function updatePaymentInterfaceId(Payment $payment): void
    {
        $this->client->post(
            'payments/key='.$payment->id,
            [],
            [],
            json_encode(
                [
                    'version' => $payment->version,
                    'actions' => [
                        [
                            'action'      => 'setInterfaceId',
                            'interfaceId' => $payment->paymentId,
                        ],
                    ],
                ]
            )
        );
    }
}
