<?php

namespace Frontastic\Common\CartApiBundle\Domain\CartApi;

use Frontastic\Common\CartApiBundle\Domain\Payment;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Mapper;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;
use Frontastic\Common\CartApiBundle\Domain\Category;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\Order;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\CartApiBundle\Domain\CartApi;
use Frontastic\Common\CartApiBundle\Domain\OrderIdGenerator;

class Commercetools implements CartApi
{
    const EXPAND_DISCOUNTS = 'lineItems[*].discountedPrice.includedDiscounts[*].discount';

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

    public function __construct(Client $client, Mapper $mapper, OrderIdGenerator $orderIdGenerator)
    {
        $this->client = $client;
        $this->mapper = $mapper;
        $this->orderIdGenerator = $orderIdGenerator;
    }

    public function getForUser(string $userId): Cart
    {
        try {
            return $this->mapCart($this->client->get('/carts', [
                'customerId' => $userId,
            ]));
        } catch (RequestException $e) {
            return $this->mapCart($this->client->post(
                '/carts',
                ['expand' => self::EXPAND_DISCOUNTS],
                [],
                json_encode([
                    // @TODO: Currency should only be stored in context. Property should be removed.
                    'currency' => 'EUR',
                    'country' => 'DE',
                    'customerId' => $userId,
                    'state' => 'Active',
                ])
            ));
        }
    }

    public function getAnonymous(string $anonymousId): Cart
    {
        $result = $this->client->fetch('/carts', [
            'where' => 'anonymousId="' . $anonymousId . '"',
            'limit' => 1,
            'expand' => self::EXPAND_DISCOUNTS,
        ]);

        if ($result->count >= 1) {
            return $this->mapCart($result->results[0]);
        }

        return $this->mapCart($this->client->post(
            '/carts',
            ['expand' => self::EXPAND_DISCOUNTS],
            [],
            json_encode([
                // @TODO: Currency should only be stored in context. Property should be removed.
                'currency' => 'EUR',
                'country' => 'DE',
                'anonymousId' => $anonymousId,
                'state' => 'Active',
            ])
        ));
    }

    public function addToCart(Cart $cart, LineItem $lineItem): Cart
    {
        if ($lineItem instanceof LineItem\Variant) {
            return $this->addVariantToCart($cart, $lineItem);
        }

        return $this->addCustomToCart($cart, $lineItem);
    }

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

    public function updateLineItem(Cart $cart, LineItem $lineItem, int $count): Cart
    {
        if ($lineItem instanceof LineItem\Variant) {
            return $this->postCartActions(
                $cart,
                [
                    [
                        'action' => 'changeLineItemQuantity',
                        'lineItemId' => $lineItem->lineItemId,
                        'quantity' => $count,
                    ],
                ]
            );
        } else {
            return $this->postCartActions(
                $cart,
                [
                    [
                        'action' => 'changeCustomLineItemQuantity',
                        'customLineItemId' => $lineItem->lineItemId,
                        'quantity' => $count,
                    ],
                ]
            );
        }
    }

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

    public function setShippingAddress(Cart $cart, array $address): Cart
    {
        return $this->postCartActions(
            $cart,
            [
                [
                    'action' => 'setShippingAddress',
                    'address' => $address,
                ],
            ]
        );
    }

    public function setBillingAddress(Cart $cart, array $address): Cart
    {
        return $this->postCartActions(
            $cart,
            [
                [
                    'action' => 'setBillingAddress',
                    'address' => $address,
                ],
            ]
        );
    }

    public function setPayment(Cart $cart, Payment $payment): Cart
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
                ]
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

    public function order(Cart $cart): Order
    {
        return $this->mapOrder($this->client->post(
            '/orders',
            [],
            [],
            json_encode([
                'id' => $cart->cartId,
                'version' => $cart->cartVersion,
                'orderNumber' => $this->orderIdGenerator->getOrderId($cart),
            ])
        ));
    }

    public function getOrder(string $orderId): Order
    {
        return $this->mapOrder($this->client->get(
            '/orders/order-number=' . $orderId
        ));
    }

    /**
     * This method is a temporary hack to recieve new orders. The
     * synchronization is based on a locally stored sequence number.
     */
    public function getNewOrders(): array
    {
        $since = @file_get_contents('/tmp/lastOrder') ?: '2000-01-01T01:00:00.000Z';

        $result = $this->client->fetch(
            '/messages',
            [
                'where' => 'type="OrderCreated" and createdAt > "' . $since . '"',
            ]
        );

        $orders = [];
        foreach ($result->results as $orderCreated) {
            $orders[] = $this->mapOrder($orderCreated['order']);
            file_put_contents('/tmp/lastOrder', $orderCreated['createdAt']);
        }

        return $orders;
    }

    private function mapCart(array $cart): Cart
    {
        /**
         * @TODO:
         *
         * [ ] Map (and sort) custom line items
         * [ ] Map delivery costs / properties
         * [ ] Map product discounts
         * [ ] Map discount codes
         * [ ] Map tax information
         * [ ] Map discount text locales to our scheme
         */
        return new Cart([
            'cartId' => $cart['id'],
            'cartVersion' => $cart['version'],
            'lineItems' => $this->mapLineItems($cart),
            'sum' => $cart['totalPrice']['centAmount'],
        ]);
    }

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
        return new Order([
            'cartId' => $order['id'],
            'orderId' => $order['orderNumber'],
            'orderVersion' => $order['version'],
            'lineItems' => $this->mapLineItems($order),
            'sum' => $order['totalPrice']['centAmount'],
        ]);
    }

    private function mapLineItems(array $cart): array
    {
        $lineItems = array_merge(
            array_map(
                function (array $lineItem): LineItem {
                    return new LineItem\Variant([
                        'lineItemId' => $lineItem['id'],
                        'name' => reset($lineItem['name']),
                        'type' => 'variant',
                        'variant' => $this->mapper->dataToVariant($lineItem['variant'], new Query(), new Locale()),
                        'custom' => $lineItem['custom']['fields'] ?? [],
                        'count' => $lineItem['quantity'],
                        'price' => $lineItem['price']['value']['centAmount'],
                        'discountedPrice' => (isset($lineItem['discountedPrice']) ?
                            $lineItem['discountedPrice']['value']['centAmount'] : null),
                        'discountTexts' => array_map(
                            function ($discount): array {
                                return $discount['discount']['obj']['name'];
                            },
                            (isset($lineItem['discountedPrice']['includedDiscounts'])
                                ? $lineItem['discountedPrice']['includedDiscounts']
                                : []
                            )
                        ),
                        'totalPrice' => $lineItem['totalPrice']['centAmount'],
                        'isGift' => ($lineItem['lineItemMode'] === 'GiftLineItem'),
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
                        'discountedPrice' => (isset($lineItem['discountedPrice']) ?
                            $lineItem['discountedPrice']['value']['centAmount'] : null),
                        'discountTexts' => array_map(
                            function ($discount): array {
                                return $discount['discount']['obj']['name'];
                            },
                            (isset($lineItem['discountedPrice']['includedDiscounts'])
                                ? $lineItem['discountedPrice']['includedDiscounts']
                                : []
                            )
                        ),
                        'totalPrice' => $lineItem['totalPrice']['centAmount'],
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

    protected function postCartActions(Cart $cart, array $actions)
    {
        if ($cart === $this->inTransaction) {
            $this->actions = array_merge(
                $this->actions,
                $actions
            );

            return $cart;
        }

        return $this->mapCart($this->client->post(
            '/carts/' . $cart->cartId,
            ['expand' => self::EXPAND_DISCOUNTS],
            [],
            json_encode([
                'version' => $cart->cartVersion,
                'actions' => $actions,
            ])
        ));
    }

    public function startTransaction(Cart $cart)
    {
        $this->inTransaction = $cart;
    }

    public function commit()
    {
        $cart = $this->inTransaction;
        $this->inTransaction = null;
        $cart = $this->postCartActions($cart, $this->actions);
        $this->actions = [];

        return $cart;
    }

    /**
     * @return \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client
     */
    public function getDangerousInnerClient()
    {
        return $this->client;
    }

    public function setCustomLineItemType(array $lineItemType)
    {
        $this->lineItemType = $lineItemType;
    }

    public function getCustomLineItemType()
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

    public function setTaxCategory(array $taxCategory)
    {
        $this->taxCategory = $taxCategory;
    }

    public function getTaxCategory()
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
}
