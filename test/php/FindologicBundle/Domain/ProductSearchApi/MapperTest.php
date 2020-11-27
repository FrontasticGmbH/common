<?php

namespace Frontastic\Common\FindologicBundle\Domain\ProductSearchApi;

use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\RangeFacet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\RangeFilter;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\TermFacet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\TermFilter;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use PHPUnit\Framework\TestCase;

class MapperTest extends TestCase
{
    public function testMapSortAttributes(): void
    {
        $mapper = new Mapper();
        $query = new ProductQuery(
            [
                'locale' => 'en_GB@GBP',
                'sortAttributes' => [
                    'label' => ProductQuery::SORT_ORDER_ASCENDING,
                ],
            ]
        );

        $sortAttributes = $mapper->getSortAttributesForRequest($query);

        $this->assertSame('label dynamic ASC', $sortAttributes);
    }

    public function testMapAttributes(): void
    {
        $mapper = new Mapper();

        $query = new ProductQuery(
            [
                'locale' => 'en_GB@GBP',
                'facets' => [
                    new TermFacet(
                        [
                            'terms' => ['blue'],
                            'handle' => 'Color',
                        ]
                    ),
                    new RangeFacet(
                        [
                            'min' => 0,
                            'max' => 1000,
                            'handle' => 'Price',
                        ]
                    ),
                ],
                'filter' => [
                    new TermFilter(
                        [
                            'handle' => 'InStock',
                            'terms' => ['true'],
                        ]
                    ),
                    new RangeFilter(
                        [
                            'handle' => 'Some range',
                            'min' => 50,
                            'max' => 100,
                        ]
                    ),
                ],
                'category' => 'Backpacks',
            ]
        );

        $requestAttributes = $mapper->getAttributesForRequest($query);

        $this->assertEquals(
            [
                'cat' => ['Backpacks'],
                'InStock' => ['true'],
                'Some range' => [
                    'min' => 50,
                    'max' => 100,
                ],
                'Color' => ['blue'],
                'Price' => [
                    'min' => 0,
                    'max' => 1000,
                ],
            ],
            $requestAttributes
        );
    }

    public function testDataToProducts()
    {
        $mapper = new Mapper();
        $query = new ProductQuery(['locale' => 'en_GB@GBP']);

        $response = json_decode(file_get_contents(__DIR__ . '/fixtures/findologicGenericResponse.json'), true);
        $productData = $response['result']['items'];

        $result = $mapper->dataToProducts($productData, $query);

        $this->assertEquals(
            [
                new Product(
                    [
                        'productId' => '131',
                        'name' => '(SW) JACKET PIZ BIANCO',
                        'slug' => '/mountain-air-adventure/fashion/men/131/sw-jacket-piz-bianco',
                        'description' => '',
                        'categories' => [
                        ],
                        'variants' => [
                            new Variant(
                                [
                                    'id' => '131',
                                    'sku' => 'SW10131',
                                    'groupId' => '131',
                                    'price' => 24995,
                                    'currency' => 'GBP',
                                    'attributes' => [
                                        'cat' => [
                                            'Mountain air & adventure_Fashion_Men',
                                            'Mountain air & adventure',
                                            'Mountain air & adventure_Fashion',
                                        ],
                                    ],
                                    'images' => ['https://ose.demo.findologic.com/media/image/18/c2/bd/SW10131.jpg'],
                                ]
                            ),
                        ],
                    ]
                ),
                new Product(
                    [
                        'productId' => '133',
                        'name' => '(SW) HYBRID JACKET',
                        'slug' => '/mountain-air-adventure/fashion/women/133/sw-hybrid-jacket',
                        'description' => '',
                        'categories' => [],
                        'variants' => [
                            new Variant(
                                [
                                    'id' => '133',
                                    'sku' => 'SW10133',
                                    'groupId' => '133',
                                    'price' => 17995,
                                    'currency' => 'GBP',
                                    'attributes' => [
                                        'cat' => [
                                            'Mountain air & adventure_Fashion_Women',
                                            'Mountain air & adventure',
                                            'Mountain air & adventure_Fashion',
                                        ],
                                    ],
                                    'images' => [
                                        'https://ose.demo.findologic.com/media/image/04/2b/ac/SW10133.jpg',
                                    ],
                                ]
                            ),
                        ],
                    ]
                ),
                new Product(
                    [
                        'productId' => '148',
                        'name' => '(SW) HYBRID PANTS MEN',
                        'slug' => '/mountain-air-adventure/fashion/men/148/sw-hybrid-pants-men',
                        'description' => '',
                        'categories' => [],
                        'variants' => [
                            new Variant(
                                [
                                    'id' => '148',
                                    'sku' => 'SW10148',
                                    'groupId' => '148',
                                    'price' => 29995,
                                    'currency' => 'GBP',
                                    'attributes' => [
                                        'cat' => [
                                            'Mountain air & adventure_Fashion_Men',
                                            'Mountain air & adventure',
                                            'Mountain air & adventure_Fashion',
                                        ],
                                    ],
                                    'images' => [
                                        'https://ose.demo.findologic.com/media/image/32/35/dd/SW10148.jpg',
                                    ],
                                ]
                            ),
                        ],
                    ]
                ),
                new Product(
                    [
                        'productId' => '149',
                        'name' => '(SW) HYBRID PANTS WOMEN',
                        'slug' => '/mountain-air-adventure/fashion/women/149/sw-hybrid-pants-women',
                        'description' => '',
                        'categories' => [],
                        'variants' => [
                            new Variant(
                                [
                                    'id' => '149',
                                    'sku' => 'SW10149',
                                    'groupId' => '149',
                                    'price' => 29995,
                                    'currency' => 'GBP',
                                    'attributes' => [
                                        'cat' => [
                                            'Mountain air & adventure_Fashion_Women',
                                            'Mountain air & adventure',
                                            'Mountain air & adventure_Fashion',
                                        ],
                                    ],
                                    'images' => [
                                        'https://ose.demo.findologic.com/media/image/17/06/ab/SW10149.jpg',
                                    ],
                                ]
                            ),
                        ],
                    ]
                ),
            ],
            $result
        );
    }

    public function testDataToFacets()
    {
        $mapper = new Mapper();
        $query = new ProductQuery(['locale' => 'en_GB@GBP']);

        $response = json_decode(file_get_contents(__DIR__ . '/fixtures/findologicGenericResponse.json'), true);
        $facetData = $response['result']['filters'];

        $result = $mapper->dataToFacets($facetData, $query);

        $this->assertEquals(
            [
                new Result\TermFacet(
                    [
                        'terms' => [
                            new Result\Term(
                                [
                                    'handle' => 'Mountain air & adventure',
                                    'name' => 'Mountain air & adventure',
                                    'value' => 'Mountain air & adventure',
                                    'count' => 4,
                                    'selected' => false,
                                ]
                            ),
                        ],
                        'handle' => 'cat',
                        'key' => 'cat',
                        'selected' => false,
                    ]
                ),
                new Result\RangeFacet(
                    [
                        'min' => 17995,
                        'max' => 29995,
                        'step' => 10,
                        'value' => [
                            'min' => 17995,
                            'max' => 29995,
                        ],
                        'handle' => 'price',
                        'key' => 'price',
                        'selected' => true,
                    ]
                ),
                new Result\TermFacet(
                    [
                        'terms' => [
                            new Result\Term(
                                [
                                    'handle' => 'Black',
                                    'name' => 'Black',
                                    'value' => 'Black',
                                    'count' => null,
                                    'selected' => false,
                                ]
                            ),
                            new Result\Term(
                                [
                                    'handle' => 'Blue',
                                    'name' => 'Blue',
                                    'value' => 'Blue',
                                    'count' => null,
                                    'selected' => false,
                                ]
                            ),
                            new Result\Term(
                                [
                                    'handle' => 'White',
                                    'name' => 'White',
                                    'value' => 'White',
                                    'count' => null,
                                    'selected' => false,
                                ]
                            ),
                        ],
                        'handle' => 'Color',
                        'key' => 'Color',
                        'selected' => false,
                    ]
                ),
            ],
            $result
        );
    }

    public function testGetSlugFromProperty()
    {
        $mapper = new Mapper([], null, 'properties.productslug');
        $query = new ProductQuery(['locale' => 'en_GB@GBP']);

        $response = json_decode(file_get_contents(__DIR__ . '/fixtures/findologicSlugFieldResponse.json'), true);

        $productData = $response['result']['items'];

        $result = $mapper->dataToProducts($productData, $query);

        $this->assertCount(1, $result);
        $this->assertSame('this-is-the-slug', $result[0]->slug);
    }

    public function testGetSlugFromUrl()
    {
        $mapper = new Mapper([], null, null, '#^/p/(?P<url>\w[\w-]+)\-(?P<identifier>\w+)$#sD');
        $query = new ProductQuery(['locale' => 'en_GB@GBP']);

        $response = json_decode(file_get_contents(__DIR__ . '/fixtures/findologicSlugUrlResponse.json'), true);

        $productData = $response['result']['items'];

        $result = $mapper->dataToProducts($productData, $query);

        $this->assertCount(1, $result);
        $this->assertSame('this-is-the-slug', $result[0]->slug);
    }

    public function testGetCategoriesFromAttribute()
    {
        $mapper = new Mapper(['customCategoryField'], 'attributes.customCategoryField');
        $query = new ProductQuery(['locale' => 'en_GB@GBP']);

        $response = json_decode(
            file_get_contents(__DIR__ . '/fixtures/findologicCategoryAttributeResponse.json'),
            true
        );

        $productData = $response['result']['items'];

        $result = $mapper->dataToProducts($productData, $query);

        $this->assertCount(1, $result);
        $this->assertEquals(
            [
                "mountain_air_adventure",
                "fashion",
                "men",
            ],
            $result[0]->categories
        );
    }

    public function testGetCategoriesWithUnknownFieldReturnsEmptyArray()
    {
        $mapper = new Mapper([], 'this.is.wrong');
        $query = new ProductQuery(['locale' => 'en_GB@GBP']);

        $response = json_decode(
            file_get_contents(__DIR__ . '/fixtures/findologicCategoryAttributeResponse.json'),
            true
        );

        $productData = $response['result']['items'];

        $result = $mapper->dataToProducts($productData, $query);

        $this->assertCount(1, $result);
        $this->assertEquals([], $result[0]->categories);
    }
}
