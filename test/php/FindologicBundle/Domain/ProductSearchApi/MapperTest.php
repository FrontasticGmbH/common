<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi;

use Frontastic\Common\ContentApiBundle\Domain;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi\Contentful\LocaleMapper;
use Frontastic\Common\FindologicBundle\Domain\FindologicClient;
use Frontastic\Common\FindologicBundle\Domain\ProductSearchApi\FindologicProductSearchApi;
use Frontastic\Common\FindologicBundle\Domain\ProductSearchApi\Mapper;
use Frontastic\Common\FindologicBundle\Domain\SearchRequest;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\RangeFacet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\RangeFilter;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\TermFacet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\TermFilter;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApi;
use GuzzleHttp\Promise\FulfilledPromise;
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
}
