<?php

namespace Frontastic\Common\CartApiBundle\Domain\CartApi\Commercetools;

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
     * @dataProvider provideDataToShippingExamples
     */
    public function testDataToShippingMethod($shippingFixture, $expectedShippingMethod)
    {
        $actualShippingMethod = $this->mapper->dataToShippingMethod($shippingFixture);

        $this->assertEquals($expectedShippingMethod, $actualShippingMethod);
    }

    public function provideDataToShippingExamples()
    {
        return [
            'Empty shipping method' => [
                [],
                null
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
