<?php

namespace Frontastic\Common\CartApiBundle\Domain\CartApi\Commercetools;

use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\Discount;
use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\CartApiBundle\Domain\Order;
use Frontastic\Common\CartApiBundle\Domain\Payment;
use Frontastic\Common\CartApiBundle\Domain\ShippingMethod;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale\CommercetoolsLocale;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Mapper as ProductMapper;

class MapperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Mapper
     */
    private $mapper;

    /**
     * @var ProductMapper
     */
    private $productMapperMock;

    public function setUp()
    {
        $this->productMapperMock = $this->createMock(ProductMapper::class);
        $this->mapper = new Mapper($this->productMapperMock);
    }

    /**
     * @dataProvider provideMapDataToAddressExamples
     */
    public function testMapDataToAddress($addressFixture, $expectedAddress)
    {
        $actualAddress = $this->mapper->mapDataToAddress($addressFixture);

        $this->assertEquals($expectedAddress, $actualAddress);
    }

    public function provideMapDataToAddressExamples()
    {
        return [
            'Empty address' => [
                [],
                null,
            ],
            'Full address' => [
                $this->getAddressFixture(),
                $this->getAddress(),
            ],
        ];
    }

    /**
     * @dataProvider provideMapAddressToDataExamples
     */
    public function testMapAddressToData($addressFixture, $expectedAddress)
    {
        $actualAddress = $this->mapper->mapAddressToData($addressFixture);

        $this->assertEquals($expectedAddress, $actualAddress);
    }

    public function provideMapAddressToDataExamples()
    {
        return [
            'Empty address' => [
                new Address(),
                [
                    'id' => null,
                    'salutation' => null,
                    'firstName' => null,
                    'lastName' => null,
                    'streetName' => null,
                    'streetNumber' => null,
                    'additionalStreetInfo' => null,
                    'additionalAddressInfo' => null,
                    'postalCode' => null,
                    'city' => null,
                    'country' => null,
                    'phone' => null,
                ],
            ],
            'Full address' => [
                $this->getAddress(),
                $this->getAddressFixture(),
            ],
        ];
    }

    public function testVerifyCartIsComplete()
    {
        $cart = $this->getCart();

        $this->assertInstanceOf(Cart::class, $cart);
        $this->assertTrue($cart->isComplete());
    }

    public function testVerifyCartIsNotCompleteWithoutFullPayment()
    {
        $cart = $this->getCart();
        unset($cart->payments[1]);

        $this->assertFalse($cart->isComplete());
    }

    /**
     * @dataProvider provideMapDataToDiscountsExamples
     */
    public function testMapDataToDiscounts($discountsFixture, $expectedDiscounts, $expectedSize)
    {
        $actualDiscounts = $this->mapper->mapDataToDiscounts($discountsFixture);

        $this->assertEquals($expectedDiscounts, $actualDiscounts);
        $this->assertEquals($expectedSize, count($actualDiscounts));
    }

    public function provideMapDataToDiscountsExamples()
    {
        return [
            'Empty cart' => [
                [],
                [],
                0,
            ],
            'Empty discount codes' => [
                [
                    'discountCodes' => [],
                ],
                [],
                0,
            ],
            'Empty discount code' => [
                [
                    'discountCodes' => [
                        [
                            'discountCode' => [],
                        ],
                    ],
                ],
                [
                    new Discount([
                        'discountId' => 'undefined',
                        'dangerousInnerDiscount' => [],
                    ])
                ],
                1,
            ],
            'Single discount' => [
                [
                    'discountCodes' => [
                        [
                            'discountCode' => $this->getDiscountFixture(),
                        ],
                    ],
                ],
                [
                    new Discount([
                        'discountId' => '111',
                        'name' => 'Discount name',
                        'code' => '123',
                        'dangerousInnerDiscount' => $this->getDiscountFixture(),
                    ]),
                ],
                1,
            ],
            'Multiple discounts' => [
                [
                    'discountCodes' => [
                        [
                            'discountCode' => $this->getDiscountFixture(),
                        ],
                        [
                            'discountCode' => $this->getDiscountFixture(),
                        ],
                    ],
                ],
                [
                    new Discount([
                        'discountId' => '111',
                        'name' => 'Discount name',
                        'code' => '123',
                        'dangerousInnerDiscount' => $this->getDiscountFixture(),
                    ]),
                    new Discount([
                        'discountId' => '111',
                        'name' => 'Discount name',
                        'code' => '123',
                        'dangerousInnerDiscount' => $this->getDiscountFixture(),
                    ]),
                ],
                2,
            ],
        ];
    }

    /**
     * @dataProvider provideMapDataToLineItemsExamples
     */
    public function testMapDataToLineItems($lineItemsFixture, $expectedLineItems, $expectedSize)
    {
        $this->productMapperMock
            ->expects($this->any())
            ->method('dataToPrice')
            ->willReturn([null, null, null]);

        $this->productMapperMock
            ->expects($this->any())
            ->method('getLocalizedValue');

        $actualLineItems = $this->mapper->mapDataToLineItems(
            $lineItemsFixture,
            new CommercetoolsLocale([
                'language' => 'de',
                'country' => 'DE',
                'currency' => 'EUR',
            ])
        );

        foreach ($expectedLineItems as $key => $expectedLineItem) {
            $this->assertInstanceOf($expectedLineItem, $actualLineItems[$key]);
        }

        $this->assertEquals($expectedSize, count($actualLineItems));
    }

    public function provideMapDataToLineItemsExamples()
    {
        $cartFixtures = $this->getCartFixture();

        return [
            'Empty line items' => [
                [
                    'lineItems' => [],
                    'customLineItems' => [],
                ],
                [],
                0,
            ],
            'Single line item' => [
                [
                    'lineItems' => [
                        $cartFixtures['lineItems'][0],
                    ],
                    'customLineItems' => [],
                ],
                [
                    LineItem\Variant::class,
                ],
                1,
            ],
            'Single custom line item' => [
                [
                    'lineItems' => [],
                    'customLineItems' => [
                        $cartFixtures['customLineItems'][0],
                    ],
                ],
                [
                    LineItem::class,
                ],
                1,
            ],
            'Multiple line item' => [
                [
                    'lineItems' => [
                        $cartFixtures['lineItems'][0],
                    ],
                    'customLineItems' => [
                        $cartFixtures['customLineItems'][0],
                    ],
                ],
                [
                    LineItem\Variant::class,
                    LineItem::class,
                ],
                2,
            ],
        ];
    }

    /**
     * @dataProvider provideMapDataToOrderExamples
     */
    public function testMapDataToOrder($orderFixture, $expectedOrder)
    {
        $this->productMapperMock
            ->expects($this->any())
            ->method('dataToPrice')
            ->willReturn([null, null, null]);

        $this->productMapperMock
            ->expects($this->any())
            ->method('getLocalizedValue');

        $actualOrder = $this->mapper->mapDataToOrder(
            $orderFixture,
            new CommercetoolsLocale([
                'language' => 'de',
                'country' => 'DE',
                'currency' => 'EUR',
                ]
            )
        );

        $this->assertInstanceOf($expectedOrder, $actualOrder);
    }

    public function provideMapDataToOrderExamples()
    {
        $cartFixtures = $this->getCartFixture();

        return [
            'Simple order' => [
                $cartFixtures,
                Order::class,
            ],
        ];
    }

    /**
     * @dataProvider provideMapDataToPaymentsExamples
     */
    public function testMapDataToPayments($paymentsFixture, $expectedPayments, $expectedSize)
    {
        $actualPayments = $this->mapper->mapDataToPayments($paymentsFixture);

        $this->assertEquals($expectedPayments, $actualPayments);
        $this->assertEquals($expectedSize, count($actualPayments));
    }

    public function provideMapDataToPaymentsExamples()
    {
        return [
            'Empty payment info' => [
                [],
                [],
                0,
            ],
            'Empty payments' => [
                [
                    'paymentInfo' => [
                        'payments' => [],
                    ],
                ],
                [],
                0,
            ],
            'Single payment' => [
                [
                    'paymentInfo' => [
                        'payments' => [
                            $this->getPaymentFixture(),
                        ],
                    ],
                ],
                [
                    $this->getPayment(),
                ],
                1,
            ],
            'Multiple payment' => [
                [
                    'paymentInfo' => [
                        'payments' => [
                            $this->getPaymentFixture(),
                            $this->getPaymentFixture(),
                        ],
                    ],
                ],
                [
                    $this->getPayment(),
                    $this->getPayment(),
                ],
                2,
            ],
        ];
    }

    /**
     * @dataProvider provideMapDataToPaymentExamples
     */
    public function testMapDataToPayment($paymentFixture, $expectedPayment)
    {
        $actualPayment = $this->mapper->mapDataToPayment($paymentFixture);

        $this->assertEquals($expectedPayment, $actualPayment);
    }

    public function provideMapDataToPaymentExamples()
    {
        return [
            'Empty payment' => [
                [],
                new Payment([
                    'debug' => json_encode([]),
                    'version' => 0,
                ]),
            ],
            'Full payment' => [
                $this->getPaymentFixture(),
                $this->getPayment(),
            ]
        ];
    }

    /**
     * @dataProvider provideMapDataToShippingExamples
     */
    public function testMapDataToShippingMethod($shippingFixture, $expectedShippingMethod)
    {
        $actualShippingMethod = $this->mapper->mapDataToShippingMethod($shippingFixture);

        $this->assertEquals($expectedShippingMethod, $actualShippingMethod);
    }

    public function provideMapDataToShippingExamples()
    {
        return [
            'Empty shipping method' => [
                [],
                null,
            ],
            'Empty shippingMethodName' => [
                [
                    'price' => [
                        'centAmount' => 0,
                    ],
                ],
                new ShippingMethod([
                    'price' => 0,
                ]),
            ],
            'Empty price' => [
                [
                    'shippingMethodName' => 'Versand an Lieferadresse',
                ],
                new ShippingMethod([
                    'name' => 'Versand an Lieferadresse',
                ]),
            ],
            'Completed shipping method' => [
                [
                    'shippingMethodName' => 'Versand an Lieferadresse',
                    'price' => [
                        'centAmount' => 0,
                    ],
                ],
                new ShippingMethod([
                    'name' => 'Versand an Lieferadresse',
                    'price' => 0,
                ]),
            ],
        ];
    }

    /**
     * @return Address
     */
    private function getAddress(): Address
    {
        return new Address([
            'addressId' => 'vSO4VhF-',
            'salutation' => 'Herr',
            'firstName' => 'Max',
            'lastName' => 'Mustermann',
            'streetName' => 'Musterstrasse',
            'streetNumber' => '23',
            'additionalStreetInfo' => '',
            'additionalAddressInfo' => '',
            'postalCode' => '12345',
            'city' => 'Musterstadt',
            'country' => 'DE',
            'phone' => '',
        ]);
    }

    /**
     * @return Payment
     */
    private function getPayment(): Payment
    {
        return new Payment([
            'id' => '111',
            'paymentId' => '7ba6efec-da46-4b06-98c0-412feb9180dd',
            'paymentProvider' => 'paypal',
            'paymentMethod' => 'paypal',
            'amount' => 10000,
            'currency' => 'EUR',
            'debug' => json_encode($this->getPaymentFixture()),
            'paymentStatus' => 'paid',
            'version' => 1,
        ]);
    }

    /**
     * @return array
     */
    private function getCartFixture(): array
    {
        return json_decode(file_get_contents( __DIR__  . '/../_fixtures/cartFixture.json'), true);
    }

    /**
     * @return array
     */
    private function getDiscountFixture(): array
    {
        return $this->loadFixture('discountFixture.json');
    }

    /**
     * @return array
     */
    private function getAddressFixture(): array
    {
        return $this->loadFixture('addressFixture.json');
    }

    /**
     * @return array
     */
    private function getPaymentFixture(): array
    {
        return $this->loadFixture('paymentFixture.json');
    }

    /**
     * @return mixed
     */
    private function loadFixture(string $fileName)
    {
        return json_decode(file_get_contents(__DIR__ . '/_fixtures/' . $fileName), true);
    }

    private function getCart(): Cart
    {
        $this->productMapperMock
            ->expects($this->any())
            ->method('dataToPrice')
            ->willReturn([null, null, null]);

        $this->productMapperMock
            ->expects($this->any())
            ->method('getLocalizedValue');

        return $this->mapper->mapDataToCart(
            $this->getCartFixture(),
            new CommercetoolsLocale([
                    'language' => 'de',
                    'country' => 'DE',
                    'currency' => 'EUR',
                ]
            )
        );
    }
}
