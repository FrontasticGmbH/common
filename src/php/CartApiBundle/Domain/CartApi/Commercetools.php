<?php

namespace Frontastic\Common\CartApiBundle\Domain\CartApi;

use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\CartApi;
use Frontastic\Common\CartApiBundle\Domain\CartApi\Commercetools\Mapper as CartMapper;
use Frontastic\Common\CartApiBundle\Domain\Category;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\CartApiBundle\Domain\Order;
use Frontastic\Common\CartApiBundle\Domain\OrderIdGenerator;
use Frontastic\Common\CartApiBundle\Domain\Payment;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale\CommercetoolsLocale;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale\CommercetoolsLocaleCreator;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException;

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
     * @var CartMapper
     */
    private $cartMapper;

    /**
     * @var CommercetoolsLocaleCreator
     */
    private $localeCreator;

    /**
     * @var OrderIdGenerator
     */
    private $orderIdGenerator;

    /**
     * @var ?Cart
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

    public function __construct(
        Client $client,
        CartMapper $cartMapper,
        CommercetoolsLocaleCreator $localeCreator,
        OrderIdGenerator $orderIdGenerator
    ) {
        $this->client = $client;
        $this->cartMapper = $cartMapper;
        $this->localeCreator = $localeCreator;
        $this->orderIdGenerator = $orderIdGenerator;
    }

    /**
     * @throws RequestException
     * @todo Should we catch the RequestException here?
     */
    public function getForUser(Account $account, string $localeString): Cart
    {
        $locale = $this->localeCreator->createLocaleFromString($localeString);

        try {
            $cart = $this->cartMapper->mapDataToCart(
                $this->client->get(
                    '/carts',
                    [
                        'customerId' => $account->accountId,
                        'expand' => self::EXPAND,
                    ]
                ),
                $locale
            );

            return $this->assertCorrectLocale($cart, $locale);
        } catch (RequestException $e) {
            return $this->cartMapper->mapDataToCart(
                $this->client->post(
                    '/carts',
                    ['expand' => self::EXPAND],
                    [],
                    json_encode([
                        'country' => $locale->country,
                        'currency' => $locale->currency,

                        'customerId' => $account->accountId,
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
        if ($cart->currency !== strtoupper($locale->currency)) {
            return $this->recreate($cart, $locale);
        }

        if ($this->doesCartNeedLocaleUpdate($cart, $locale)) {
            $actions = [];

            $setCountryAction = [
                'action' => 'setCountry',
                'country' => $locale->country,
            ];
            $setLocaleAction = [
                'action' => 'setLocale',
                'locale' => $locale->language,
            ];

            array_push($actions, $setCountryAction);
            array_push($actions, $setLocaleAction);

            return $this->postCartActions($cart, $actions, $locale);
        }
        return $cart;
    }

    private function recreate(Cart $cart, CommercetoolsLocale $locale): Cart
    {
        // Finish current cart transaction if necessary
        $wasInTransaction = ($this->inTransaction !== null);
        if ($wasInTransaction && $cart !== $this->inTransaction) {
            throw new \RuntimeException(
                'Cart to be re-created is not the one in transaction!'
            );
        }
        if ($wasInTransaction) {
            $cart = $this->commit($cart);
        }

        $dangerousInnerCart = $cart->dangerousInnerCart;

        $cartId = $dangerousInnerCart['id'];
        $newCountry = $dangerousInnerCart['country'];
        $cartVersion = $dangerousInnerCart['version'];
        $lineItems = $dangerousInnerCart['lineItems'];

        unset(
            $dangerousInnerCart['id'],
            $dangerousInnerCart['version'],
            $dangerousInnerCart['lineItems'],
            $dangerousInnerCart['discountCodes']
        );

        $dangerousInnerCart['country'] = $locale->country;
        $dangerousInnerCart['locale'] = $locale->language;
        $dangerousInnerCart['currency'] = $locale->currency;

        $cart = $this->cartMapper->mapDataToCart(
            $this->client->post(
                '/carts',
                ['expand' => self::EXPAND],
                [],
                \json_encode($dangerousInnerCart)
            ),
            $locale
        );

        foreach ($lineItems as $lineItem) {
            try {
                $actions = [
                    [
                        'action' => 'addLineItem',
                        'productId' => $lineItem['productId'],
                        'variantId' => $lineItem['variant']['id'],
                        'quantity' => $lineItem['quantity'],
                    ],
                ];
                // Will directly be posted without transaction batching
                $cart = $this->postCartActions($cart, $actions, $locale);
            } catch (\Exception $e) {
                // Ignore that a line item could not be added due to missing price, etc.
            }
        }

        $this->client->delete(
            '/carts/' . urlencode($cartId),
            ['version' => $cartVersion]
        );

        if ($wasInTransaction) {
            $this->startTransaction($cart);
        }

        return $cart;
    }

    /**
     * @throws RequestException
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
            return $this->assertCorrectLocale($this->cartMapper->mapDataToCart($result->results[0], $locale), $locale);
        }

        return $this->cartMapper->mapDataToCart(
            $this->client->post(
                '/carts',
                ['expand' => self::EXPAND],
                [],
                json_encode([
                    'country' => $locale->country,
                    'currency' => $locale->currency,
                    'locale' => $locale->language,
                    'anonymousId' => $anonymousId,
                    'state' => 'Active',
                    'inventoryMode' => 'ReserveOnOrder',
                ])
            ),
            $locale
        );
    }

    /**
     * @throws \RuntimeException if cart with $cartId was not found
     */
    public function getById(string $cartId, string $localeString = null): Cart
    {
        return $this->cartMapper->mapDataToCart(
            $this->client->get(
                '/carts/' . urlencode($cartId),
                ['expand' => self::EXPAND]
            ),
            $this->parseLocaleString($localeString)
        );
    }

    public function addToCart(Cart $cart, LineItem $lineItem, string $localeString = null): Cart
    {
        $locale = $this->parseLocaleString($localeString);

        if ($lineItem instanceof LineItem\Variant) {
            return $this->addVariantToCart($cart, $lineItem, $locale);
        }

        return $this->addCustomToCart($cart, $lineItem, $locale);
    }

    private function addVariantToCart(Cart $cart, LineItem\Variant $lineItem, CommercetoolsLocale $locale): Cart
    {
        return $this->postCartActions(
            $cart,
            [
                array_merge(
                    (array)$lineItem->rawApiInput,
                    [
                        'action' => 'addLineItem',
                        'sku' => $lineItem->variant->sku,
                        'quantity' => $lineItem->count,
                    ]
                ),
            ],
            $locale
        );
    }

    private function addCustomToCart(Cart $cart, LineItem $lineItem, CommercetoolsLocale $locale): Cart
    {
        return $this->postCartActions(
            $cart,
            [
                array_merge(
                    (array)$lineItem->rawApiInput,
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
                        'quantity' => $lineItem->count,
                    ]
                ),
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
        if (!count($fields)) {
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
     * Intentionally not part of the CartAPI interface.
     *
     * Only for use in scenarios where CommerceTools is set as the backend API.
     */
    public function setCustomType(Cart $cart, string $key, string $localeString = null): Cart
    {
        $actions = [];
        $actions[] = [
            'action' => 'setCustomType',
            'type' => [
                "key" => $key,
                "typeId" => "type",
            ],
        ];
        return $this->postCartActions($cart, $actions, $this->parseLocaleString($localeString));
    }

    public function setShippingAddress(Cart $cart, Address $address, string $localeString = null): Cart
    {
        return $this->postCartActions(
            $cart,
            [
                [
                    'action' => 'setShippingAddress',
                    'address' => $this->cartMapper->mapAddressToData($address),
                ],
            ],
            $this->parseLocaleString($localeString)
        );
    }

    public function setBillingAddress(Cart $cart, Address $address, string $localeString = null): Cart
    {
        return $this->postCartActions(
            $cart,
            [
                [
                    'action' => 'setBillingAddress',
                    'address' => $this->cartMapper->mapAddressToData($address),
                ],
            ],
            $this->parseLocaleString($localeString)
        );
    }

    /**
     * @throws RequestException
     * @todo Should we catch the RequestException here?
     */
    public function addPayment(Cart $cart, Payment $payment, ?array $custom = null, string $localeString = null): Cart
    {
        $payment = $this->client->post(
            '/payments',
            [],
            [],
            json_encode($this->cartMapper->mapPaymentToData($payment))
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

    public function removeDiscountCode(Cart $cart, LineItem $discountLineItem, string $localeString = null): Cart
    {
        return $this->postCartActions(
            $cart,
            [
                [
                    'action' => 'removeDiscountCode',
                    'discountCode' => [
                        'typeId' => 'discount-code',
                        'id' => $discountLineItem->lineItemId,
                    ],
                ],
            ],
            $this->parseLocaleString($localeString)
        );
    }

    /**
     * @throws RequestException
     * @todo Should we catch the RequestException here?
     */
    public function order(Cart $cart, string $locale = null): Order
    {
        $order = $this->cartMapper->mapDataToOrder(
            $this->client->post(
                '/orders',
                ['expand' => self::EXPAND],
                [],
                json_encode([
                    'id' => $cart->cartId,
                    'version' => (int)$cart->cartVersion,
                    'orderNumber' => $this->orderIdGenerator->getOrderId($cart),
                ])
            ),
            $this->parseLocaleString($locale)
        );

        $cart = $this->getById($cart->cartId);
        $this->client->delete(
            '/carts/' . urlencode($cart->cartId),
            ['version' => (int)$cart->cartVersion]
        );

        return $order;
    }

    /**
     * @throws RequestException
     * @todo Should we catch the RequestException here?
     */
    public function getOrder(Account $account, string $orderId, string $locale = null): Order
    {
        return $this->cartMapper->mapDataToOrder(
            $this->client->get(
                '/orders/order-number=' . $orderId,
                ['expand' => self::EXPAND]
            ),
            $this->parseLocaleString($locale)
        );
    }

    /**
     * @return Order[]
     * @throws RequestException
     * @todo Should we catch the RequestException here?
     */
    public function getOrders(Account $account, string $locale = null): array
    {
        $result = $this->client
            ->fetchAsync(
                '/orders',
                [
                    'where' => 'customerId="' . $account->accountId . '"',
                    'expand' => self::EXPAND,
                ]
            )
            ->wait();

        $orders = [];
        foreach ($result->results as $order) {
            $orders[] = $this->cartMapper->mapDataToOrder($order, $this->parseLocaleString($locale));
        }

        return $orders;
    }

    /**
     * @throws RequestException
     */
    protected function postCartActions(Cart $cart, array $actions, CommercetoolsLocale $locale): Cart
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

        return $this->cartMapper->mapDataToCart(
            $this->client->post(
                '/carts/' . $cart->cartId,
                ['expand' => self::EXPAND],
                [],
                json_encode([
                    'version' => (int)$cart->cartVersion,
                    'actions' => $actions,
                ])
            ),
            $locale
        );
    }

    public function startTransaction(Cart $cart): void
    {
        $this->inTransaction = $cart;
    }

    /**
     * @throws RequestException
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

    public function setCustomLineItemType(array $lineItemType): void
    {
        $this->lineItemType = $lineItemType;
    }

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

    public function setTaxCategory(array $taxCategory): void
    {
        $this->taxCategory = $taxCategory;
    }

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
            'payments/key=' . $payment->id,
            [],
            [],
            json_encode(
                [
                    'version' => $payment->version,
                    'actions' => [
                        [
                            'action' => 'setStatusInterfaceCode',
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
            'payments/key=' . $paymentId,
            ['expand' => self::EXPAND]
        );

        if (empty($payment)) {
            return null;
        }

        return $this->cartMapper->mapDataToPayment($payment);
    }

    public function updatePaymentInterfaceId(Payment $payment): void
    {
        $this->client->post(
            'payments/key=' . $payment->id,
            [],
            [],
            json_encode(
                [
                    'version' => $payment->version,
                    'actions' => [
                        [
                            'action' => 'setInterfaceId',
                            'interfaceId' => $payment->paymentId,
                        ],
                    ],
                ]
            )
        );
    }

    private function parseLocaleString(?string $localeString = null): CommercetoolsLocale
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

    private function doesCartNeedLocaleUpdate(Cart $cart, CommercetoolsLocale $locale): bool
    {
        $innerCart = $cart->dangerousInnerCart;

        if (!isset($innerCart['country'])) {
            return true;
        }

        if (!isset($innerCart['locale'])) {
            return true;
        }

        return $innerCart['country'] !== $locale->country
            || $innerCart['locale'] !== $locale->language;
    }
}
