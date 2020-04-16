<?php

namespace Frontastic\Common\CartApiBundle\Domain\CartApi\Commercetools;

use Frontastic\Common\CartApiBundle\Domain\Discount;
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
     * @dataProvider provideMapDataToDiscountsExamples
     */
    public function testMapDataToDiscounts($discountsFixture, $expectedDiscounts)
    {
        $actualDiscounts = $this->mapper->mapDataToDiscounts($discountsFixture);

        $this->assertEquals($expectedDiscounts, $actualDiscounts);
    }

    public function provideMapDataToDiscountsExamples()
    {
        return [
            'Empty cart' => [
                [],
                [],
            ],
            'Empty discount codes' => [
                [
                    'discountCodes' => [],
                ],
                [],
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
            ],
            'Single discount' => [
                [
                    'discountCodes' => [
                        [
                            'discountCode' => [
                                'id' => '111',
                                'name' => 'Discount name',
                                'code' => '123',
                            ],
                        ],
                    ],
                ],
                [
                    new Discount([
                        'discountId' => '111',
                        'name' => 'Discount name',
                        'code' => '123',
                        'dangerousInnerDiscount' => [
                             'id' => '111',
                             'name' => 'Discount name',
                             'code' => '123',
                        ],
                    ]),
                ],
            ],
            'Multiple discounts' => [
                [
                    'discountCodes' => [
                        [
                            'discountCode' => [
                                'id' => '222',
                                'name' => 'Discount name',
                                'code' => '456',
                            ],
                        ],
                    ],
                ],
                [
                    new Discount([
                        'discountId' => '222',
                        'name' => 'Discount name',
                        'code' => '456',
                        'dangerousInnerDiscount' => [
                            'id' => '222',
                            'name' => 'Discount name',
                            'code' => '456',
                        ],
                    ]),
                ],
            ],
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
}
