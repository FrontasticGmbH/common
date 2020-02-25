<?php

namespace Frontastic\Common\ApiTests\ProductApi;

use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use GuzzleHttp\Promise\PromiseInterface;

class ProductsTest extends ProductApiTestCase
{
    /**
     * @dataProvider projectAndLanguage
     */
    public function testQuerySyncReturnsResult(Project $project, string $language): void
    {
        $result = $this->productApiForProject($project)
            ->query(new ProductQuery($this->buildQueryParameters($language)), ProductApi::QUERY_SYNC);

        $this->assertInstanceOf(Result::class, $result);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryAsyncReturnsPromiseToResult(Project $project, string $language): void
    {
        $promise = $this->productApiForProject($project)
            ->query(new ProductQuery($this->buildQueryParameters($language)), ProductApi::QUERY_ASYNC);

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $result = $promise->wait();

        $this->assertInstanceOf(Result::class, $result);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryAllProductsReturnsValidResult(Project $project, string $language): void
    {
        $result = $this->queryProducts($project, $language);

        $this->assertSame(0, $result->offset);

        $this->assertGreaterThanOrEqual(50, $result->total);

        $this->assertSame(ProductApi\Query::DEFAULT_LIMIT, $result->count);
        $this->assertCount($result->count, $result->items);

        $this->assertInternalType('array', $result->items);
        $this->assertContainsOnlyInstancesOf(Product::class, $result->items);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryAllProductsOrderedByIdTwiceReturnsSameResult(Project $project, string $language): void
    {
        $firstResult = $this->queryProducts($project, $language, $this->sortById());
        $secondResult = $this->queryProducts($project, $language, $this->sortById());

        $this->assertEquals($firstResult, $secondResult);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryAllProductsOrderedByIdWithLowLimitReturnsSameAsWithHighLimit(
        Project $project,
        string $language
    ): void {
        $limit = 24;
        $productsQueriedOneStep = $this->queryProducts($project, $language, $this->sortById(), $limit)->items;
        $productsQueriedInMultipleSteps =
            $this->queryProductsInMultipleSteps($project, $language, $this->sortById(), $limit, 2);

        $this->assertEquals($productsQueriedOneStep, $productsQueriedInMultipleSteps);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testProductsHaveDistinctIds(Project $project, string $language): void
    {
        $result = $this->queryProducts($project, $language);
        $productIds = array_map(
            function (Product $product): string {
                return $product->productId;
            },
            $result->items
        );

        $this->assertArrayHasDistinctValues($productIds);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testProductsAreWellFormed(Project $project, string $language): void
    {
        $result = $this->queryProducts($project, $language);

        foreach ($result->items as $product) {
            $this->assertInternalType('string', $product->productId);
            $this->assertNotEmpty($product->productId);

            if ($product->changed !== null) {
                $this->assertInternalType('string', $product->changed);
                $changedDate =
                    \DateTimeImmutable::createFromFormat(\DateTimeImmutable::RFC3339_EXTENDED, $product->changed);
                $this->assertLessThan(new \DateTimeImmutable(), $changedDate);
            }

            $this->assertInternalType('integer', $product->version);

            $this->assertInternalType('string', $product->name);
            $this->assertNotEmpty($product->name);

            $this->assertInternalType('string', $product->slug);
            $this->assertNotEmpty($product->slug);
            $this->assertRegExp(self::URI_PATH_SEGMENT_REGEX, $product->slug);

            $this->assertInternalType('string', $product->description);

            $this->assertInternalType('array', $product->categories);
            $this->assertNotEmpty($product->categories);
            foreach ($product->categories as $category) {
                $this->assertInternalType('string', $category);
                $this->assertNotEmpty($category);
            }

            $this->assertInternalType('array', $product->variants);
            $this->assertNotEmpty($product->variants);
            $this->assertContainsOnlyInstancesOf(Variant::class, $product->variants);

            $this->assertNull($product->dangerousInnerProduct);
        }
    }

    private function queryProducts(
        Project $project,
        string $language,
        array $queryParameters = [],
        ?int $limit = null,
        ?int $offset = null
    ): Result {
        return $this
            ->productApiForProject($project)
            ->query(
                new ProductQuery(
                    array_merge(
                        $this->buildQueryParameters($language, $limit, $offset),
                        $queryParameters
                    )
                ),
                ProductApi::QUERY_ASYNC
            )
            ->wait();
    }

    /**
     * @return Product[]
     */
    private function queryProductsInMultipleSteps(
        Project $project,
        string $language,
        array $queryParameters,
        int $limit,
        int $stepSize
    ): array {
        $products = [];
        for ($offset = 0; $offset < $limit; $offset += $stepSize) {
            $products = array_merge(
                $products,
                $this->queryProducts(
                    $project,
                    $language,
                    $queryParameters,
                    $stepSize,
                    $offset
                )->items
            );
        }
        return $products;
    }

    private function sortById(): array
    {
        return [
            'sortAttributes' => [
                'id' => ProductQuery::SORT_ORDER_ASCENDING,
            ],
        ];
    }
}
