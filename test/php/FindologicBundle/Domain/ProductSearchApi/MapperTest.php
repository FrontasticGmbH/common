<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi;

use Frontastic\Common\ContentApiBundle\Domain;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi\Contentful\LocaleMapper;
use Frontastic\Common\FindologicBundle\Domain\FindologicClient;
use Frontastic\Common\FindologicBundle\Domain\ProductSearchApi\FindologicProductSearchApi;
use Frontastic\Common\FindologicBundle\Domain\ProductSearchApi\Mapper;
use Frontastic\Common\FindologicBundle\Domain\SearchRequest;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApi;
use GuzzleHttp\Promise\FulfilledPromise;
use PHPUnit\Framework\TestCase;

class MapperTest extends TestCase
{
    public function testSortAttributes(): void
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

        $sortAttributes = $mapper->sortAttributesToRequest($query);

        $this->assertSame('label dynamic ASC', $sortAttributes);
    }
}
