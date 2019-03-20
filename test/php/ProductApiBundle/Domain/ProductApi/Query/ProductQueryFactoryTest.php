<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query;

use PHPUnit\Framework\TestCase;

class ProductQueryFactoryTest extends TestCase
{
    public function testDefaultOverride()
    {
        $this->assertEquals(
            new ProductQuery([
                'sku' => 'overridden',
            ]),
            ProductQueryFactory::queryFromParameters(
                [
                    'sku' => 'default'
                ],
                [
                    'sku' => 'overridden'
                ]
            )
        );
    }

    public function testParameterOverride()
    {
        $this->assertEquals(
            new ProductQuery([
                'sku' => 'even_overridden',
            ]),
            ProductQueryFactory::queryFromParameters(
                [
                    'sku' => 'default'
                ],
                [
                    'sku' => 'overridden'
                ],
                [
                    'sku' => 'even_overridden'
                ]
            )
        );
    }

    public function testMergeFacets()
    {
        $this->assertEquals(
            new ProductQuery([
                'facets' => [
                    new RangeFacet([
                        'handle' => 'variants.price',
                        'min' => 23,
                        'max' => 42,
                    ])
                ]
            ]),
            ProductQueryFactory::queryFromParameters(
                [
                    'facets' => [
                        'variants.price' => [
                            'min' => 1,
                            'max' => 2,
                        ]
                    ]
                ],
                [
                    'facets' => [
                        'variants.price' => [
                            'min' => 23,
                            'max' => 42,
                        ]
                    ]
                ]
            )
        );
    }

    public function testOverrideFacets()
    {
        $this->assertEquals(
            new ProductQuery([
                'facets' => [
                    new RangeFacet([
                        'handle' => 'variants.price',
                        'min' => 11,
                        'max' => 22,
                    ])
                ]
            ]),
            ProductQueryFactory::queryFromParameters(
                [
                    'facets' => [
                        'variants.price' => [
                            'min' => 1,
                            'max' => 2,
                        ],
                    ],
                ],
                [
                    'facets' => [
                        'variants.price' => [
                            'min' => 23,
                            'max' => 42,
                        ],
                    ],
                ],
                [
                    'facets' => [
                        'variants.price' => [
                            'min' => 11,
                            'max' => 22,
                        ],
                    ],
                ]
            )
        );
    }
}
