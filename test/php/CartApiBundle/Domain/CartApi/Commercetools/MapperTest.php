<?php

namespace Frontastic\Common\CartApiBundle\Domain\CartApi\Commercetools;

use Frontastic\Common\AccountApiBundle\Domain\Address;
use Frontastic\Common\CartApiBundle\Domain\Discount;
use Frontastic\Common\CartApiBundle\Domain\Payment;
use Frontastic\Common\CartApiBundle\Domain\ShippingMethod;

class MapperTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var Mapper
     */
    private $mapper;

    public function setUp()
    {
        $this->mapper = new Mapper();
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
}
