<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Mapper;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\RangeFacet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\TermFacet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result\Term;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CommercetoolsTest extends TestCase
{
    /**
     * @var Client|MockObject
     */
    private $clientMock;

    /**
     * @var Commercetools
     */
    private $api;

    public function setup()
    {
        $this->clientMock = $this->getMockBuilder(Client::class)->disableOriginalConstructor()->getMock();
        $this->api = new Commercetools($this->clientMock, new Mapper());
    }

    public function testQueryWithFacets()
    {
        $expectedFilterValues = [
            'variants.term-value:"red"',
            'variants.term-value:"blue"',
            'variants.price-max-only:range (0 to 42)',
            'variants.price-min-only:range (23 to ' . PHP_INT_MAX . ')',
            'variants.price-min-max:range (23 to 42)'
        ];

        $this->clientMock->expects($this->once())->method('fetch')->with(
            $this->anything(),
            $this->callback(
            function ($parameters) use ($expectedFilterValues) {
                $this->assertArrayHasKey('filter', $parameters);
                foreach ($expectedFilterValues as $filterValue) {
                    $this->assertContains($filterValue, $parameters['filter']);
                }
                return true;
            })
        );

        $this->api->query(
            new ProductQuery([
                'locale' => 'en_GB',
                'facets' => [
                    new TermFacet([
                        'handle' => 'variants.term-value',
                        'terms' => ['red', 'blue'],
                    ]),
                    new RangeFacet([
                        'handle' => 'variants.price-max-only',
                        'max' => 42,
                    ]),
                    new RangeFacet([
                        'handle' => 'variants.price-min-only',
                        'min' => 23,
                    ]),
                    new RangeFacet([
                        'handle' => 'variants.price-min-max',
                        'min' => 23,
                        'max' => 42,
                    ])
                ],
            ])
        );
    }

    public function testParseFacetsResult()
    {
        $this->clientMock->expects($this->any())->method('fetch')
            ->will($this->returnValue(
                require __DIR__ . '/_fixtures/CommercetoolsTest/testParseFacetsResult.php'
            ));

        $result = $this->api->query(
            new ProductQuery([
                'locale' => 'en_GB',
                'facets' => [
                    new TermFacet([
                        'handle' => 'variants.attributes.color.key',
                        'terms' => ['blue'],
                    ]),
                    new RangeFacet([
                        'handle' => 'variants.price.centAmount',
                        'max' => 42,
                    ]),
                ]
            ])
        );

        $this->assertCount(2, $result->facets);

        $this->assertEquals([
            new \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result\RangeFacet([
                'min' => 15000,
                'max' => 99375,
                // 'step' => ??? // TODO
                'value' => [
                    'min' => 0,
                    'max' => 42,
                ],
                'handle' => 'variants.price.centAmount',
                'key' => 'variants.price.centAmount',
                'selected' => true,
            ]),
            new \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result\TermFacet([
                'terms' => [
                    new Term([
                        'name' => 'blue',
                        'value' => 'blue',
                        'handle' => 'blue',
                        'count' => 2917,
                        'selected' => true,
                    ]),
                    new Term([
                        'name' => 'black',
                        'value' => 'black',
                        'handle' => 'black',
                        'count' => 1902,
                        'selected' => false,
                    ]),
                    new Term([
                        'name' => 'grey',
                        'value' => 'grey',
                        'handle' => 'grey',
                        'count' => 1397,
                        'selected' => false,
                    ]),
                ],
                'handle' => 'variants.attributes.color.key',
                'key' => 'variants.attributes.color.key',
                'selected' => true,
            ])],
            $result->facets
        );
    }
}
