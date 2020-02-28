<?php

namespace Frontastic\Common\ApiTests\ProductApi;

use Frontastic\Common\ApiTests\FrontasticApiTestCase;
use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use GuzzleHttp\Promise\PromiseInterface;

class ProductsTest extends FrontasticApiTestCase
{
    private const NON_EXISTING_CATEGORY = 'THIS_CATEGORY_SHOULD_NEVER_EXIST_IN_ANY_DATA_SET';
    private const NON_EXISTING_SKU = 'THIS_SKU_SHOULD_NEVER_EXIST_IN_ANY_DATA_SET';
    private const NON_EXISTING_PRODUCT_ID = 'THIS_PRODUCT_ID_SHOULD_NEVER_EXIST_IN_ANY_DATA_SET';

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQuerySyncReturnsResult(Project $project, string $language): void
    {
        $result = $this->getProductApiForProject($project)
            ->query(new ProductQuery($this->buildQueryParameters($language)), ProductApi::QUERY_SYNC);

        $this->assertInstanceOf(Result::class, $result);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryAsyncReturnsPromiseToResult(Project $project, string $language): void
    {
        $promise = $this->getProductApiForProject($project)
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

        $this->assertSame(ProductApi\PaginatedQuery::DEFAULT_LIMIT, $result->count);
        $this->assertCount($result->count, $result->items);

        $this->assertInternalType('array', $result->items);
        $this->assertContainsOnlyInstancesOf(Product::class, $result->items);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testGetProductSyncBySkuReturnsProduct(Project $project, string $language): void
    {
        $product = $this->getAProduct($project, $language);
        $sku = $product->variants[0]->sku;
        $this->assertNotEmptyString($sku);

        $productBySku = $this->getProductApiForProject($project)
            ->getProduct(
                new ProductQuery(array_merge($this->buildQueryParameters($language), ['sku' => $sku])),
                ProductApi::QUERY_SYNC
            );

        $this->assertInstanceOf(Product::class, $productBySku);
        $this->assertEquals($product, $productBySku);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testGetProductSyncByNonExistingSkuReturnsNull(Project $project, string $language): void
    {
        $query = new ProductQuery(
            array_merge($this->buildQueryParameters($language), ['sku' => self::NON_EXISTING_SKU])
        );

        $this->expectException(ProductApi\ProductNotFoundException::class);
        $this->getProductApiForProject($project)->getProduct($query, ProductApi::QUERY_SYNC);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testGetProductAsyncBySkuReturnsPromiseToProduct(Project $project, string $language): void
    {
        $product = $this->getAProduct($project, $language);
        $sku = $product->variants[0]->sku;
        $this->assertNotEmptyString($sku);

        $promise = $this->getProductApiForProject($project)
            ->getProduct(
                new ProductQuery(array_merge($this->buildQueryParameters($language), ['sku' => $sku])),
                ProductApi::QUERY_ASYNC
            );

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $productBySku = $promise->wait();

        $this->assertInstanceOf(Product::class, $productBySku);
        $this->assertEquals($product, $productBySku);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testGetProductAsyncByNonExistingSkuReturnsFailedPromise(Project $project, string $language): void
    {
        $promise = $this->getProductApiForProject($project)
            ->getProduct(
                new ProductQuery(
                    array_merge($this->buildQueryParameters($language), ['sku' => self::NON_EXISTING_SKU])
                ),
                ProductApi::QUERY_ASYNC
            );

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $this->expectException(ProductApi\ProductNotFoundException::class);
        $promise->wait();
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testGetProductSyncByIdReturnsProduct(Project $project, string $language): void
    {
        $product = $this->getAProduct($project, $language);
        $productId = $product->productId;
        $this->assertNotEmptyString($productId);

        $productById = $this->getProductApiForProject($project)
            ->getProduct(
                new ProductQuery(array_merge($this->buildQueryParameters($language), ['productId' => $productId])),
                ProductApi::QUERY_SYNC
            );

        $this->assertInstanceOf(Product::class, $productById);
        $this->assertEquals($product, $productById);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testGetProductSyncByNonExistingIdThrowsException(Project $project, string $language): void
    {
        $query = new ProductQuery(
            array_merge($this->buildQueryParameters($language), ['productId' => self::NON_EXISTING_PRODUCT_ID])
        );

        $this->expectException(ProductApi\ProductNotFoundException::class);
        $this->getProductApiForProject($project)->getProduct($query, ProductApi::QUERY_SYNC);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testGetProductAsyncByIdReturnsPromiseToProduct(Project $project, string $language): void
    {
        $product = $this->getAProduct($project, $language);
        $productId = $product->productId;
        $this->assertNotEmptyString($productId);

        $promise = $this->getProductApiForProject($project)
            ->getProduct(
                new ProductQuery(array_merge($this->buildQueryParameters($language), ['productId' => $productId])),
                ProductApi::QUERY_ASYNC
            );

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $productById = $promise->wait();

        $this->assertInstanceOf(Product::class, $productById);
        $this->assertEquals($product, $productById);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testGetProductAsyncByNonExistingIdReturnsFailedPromise(Project $project, string $language): void
    {
        $promise = $this->getProductApiForProject($project)
            ->getProduct(
                new ProductQuery(
                    array_merge($this->buildQueryParameters($language), ['productId' => self::NON_EXISTING_PRODUCT_ID])
                ),
                ProductApi::QUERY_ASYNC
            );

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $this->expectException(ProductApi\ProductNotFoundException::class);
        $promise->wait();
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryAllProductsOrderedByIdTwiceReturnsSameResult(Project $project, string $language): void
    {
        $firstResult = $this->queryProducts($project, $language, $this->sortReproducibly());
        $secondResult = $this->queryProducts($project, $language, $this->sortReproducibly());

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
        $productsQueriedOneStep = $this->queryProducts($project, $language, $this->sortReproducibly(), $limit);
        $productsQueriedInMultipleSteps = $this->queryProductsInMultipleSteps(
            $project,
            $language,
            $this->sortReproducibly(),
            $productsQueriedOneStep->total,
            $limit,
            2
        );

        $this->assertEquals($productsQueriedOneStep->items, $productsQueriedInMultipleSteps);
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
            $this->assertNotEmptyString($product->productId);

            if ($product->changed !== null) {
                $this->assertNotEmptyString($product->changed);
                $changedDate =
                    \DateTimeImmutable::createFromFormat(\DateTimeImmutable::RFC3339_EXTENDED, $product->changed);
                $this->assertLessThan(new \DateTimeImmutable(), $changedDate);
            }

            $this->assertInternalType('integer', $product->version);

            $this->assertNotEmptyString($product->name);

            $this->assertNotEmptyString($product->slug);
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

            foreach ($product->variants as $variant) {
                $this->assertInternalType('integer', $variant->id);

                $this->assertNotEmptyString($variant->sku);
                $this->assertNotEmptyString($variant->groupId);

                $this->assertInternalType('integer', $variant->price);
                $this->assertGreaterThanOrEqual(0, $variant->price);

                if ($variant->discountedPrice !== null) {
                    $this->assertInternalType('integer', $variant->discountedPrice);
                    $this->assertGreaterThanOrEqual(0, $variant->discountedPrice);
                    $this->assertLessThanOrEqual($variant->price, $variant->discountedPrice);
                }

                $this->assertInternalType('array', $variant->discounts);

                $this->assertNotEmptyString($variant->currency);

                $this->assertInternalType('array', $variant->attributes);

                $this->assertInternalType('array', $variant->images);
                foreach ($variant->images as $image) {
                    $this->assertNotEmptyString($image);
                }

                $this->assertInternalType('boolean', $variant->isOnStock);

                $this->assertNull($variant->dangerousInnerVariant);
            }

            $this->assertNull($product->dangerousInnerProduct);
        }
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryByNonExistingCategoryReturnsEmptyResult(Project $project, string $language): void
    {
        $products = $this->queryProducts($project, $language, ['category' => self::NON_EXISTING_CATEGORY]);
        $this->assertEmptyResult($products);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryByNonExistingSkuReturnsEmptyResult(Project $project, string $language): void
    {
        $products = $this->queryProducts($project, $language, ['sku' => self::NON_EXISTING_SKU]);
        $this->assertEmptyResult($products);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryByNonExistingSkusReturnsEmptyResult(Project $project, string $language): void
    {
        $products = $this->queryProducts($project, $language, ['skus' => [self::NON_EXISTING_SKU]]);
        $this->assertEmptyResult($products);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryByNonExistingProductIdsReturnsEmptyResult(Project $project, string $language): void
    {
        $products = $this->queryProducts($project, $language, ['productIds' => [self::NON_EXISTING_PRODUCT_ID]]);
        $this->assertEmptyResult($products);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryProductByCategoryReturnsProduct(Project $project, string $language): void
    {
        $allProducts = $this->queryProducts($project, $language);
        $product = $allProducts->items[0];
        $categoryId = $product->categories[0];
        $this->assertNotEmptyString($categoryId);

        $productsByCategory = $this->queryProducts($project, $language, ['category' => $categoryId]);
        $this->assertResultContainsProduct($product, $productsByCategory);
        $this->assertLessThanOrEqual($allProducts->total, $productsByCategory->total);

        foreach ($productsByCategory->items as $product) {
            $this->assertContains($categoryId, $product->categories);
        }
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryProductBySkuReturnsOnlyProduct(Project $project, string $language): void
    {
        $product = $this->getAProduct($project, $language);
        $sku = $product->variants[0]->sku;
        $this->assertNotEmptyString($sku);

        $productsBySku = $this->queryProducts($project, $language, ['sku' => $sku]);
        $this->assertSingleProductResult($product, $productsBySku);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryProductBySkusReturnsOnlyProduct(Project $project, string $language): void
    {
        $product = $this->getAProduct($project, $language);
        $sku = $product->variants[0]->sku;
        $this->assertNotEmptyString($sku);

        $productsBySku = $this->queryProducts($project, $language, ['skus' => [$sku, self::NON_EXISTING_SKU]]);
        $this->assertSingleProductResult($product, $productsBySku);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryProductByProductIdsReturnsOnlyProduct(Project $project, string $language): void
    {
        $product = $this->getAProduct($project, $language);
        $productId = $product->productId;
        $this->assertNotEmptyString($productId);

        $productsByProductId =
            $this->queryProducts($project, $language, ['productIds' => [$productId, self::NON_EXISTING_PRODUCT_ID]]);
        $this->assertSingleProductResult($product, $productsByProductId);
    }

    /**
     * @return Product[]
     */
    private function queryProductsInMultipleSteps(
        Project $project,
        string $language,
        array $queryParameters,
        int $expectedTotal,
        int $limit,
        int $stepSize
    ): array {
        $products = [];
        for ($offset = 0; $offset < $limit; $offset += $stepSize) {
            $result = $this->queryProducts(
                $project,
                $language,
                $queryParameters,
                $stepSize,
                $offset
            );
            $this->assertEquals($expectedTotal, $result->total);

            $products = array_merge($products, $result->items);
        }
        return $products;
    }

    private function sortReproducibly(): array
    {
        return [
            'sortAttributes' => [
                'id' => ProductQuery::SORT_ORDER_ASCENDING,
            ],
        ];
    }

    private function assertEmptyResult(Result $actual): void
    {
        $this->assertEquals(0, $actual->count);
        $this->assertEquals(0, $actual->total);
        $this->assertEmpty($actual->items);
    }

    private function assertSingleProductResult(Product $expectedProduct, Result $actual)
    {
        $this->assertEquals(1, $actual->count);
        $this->assertEquals(1, $actual->total);
        $this->assertCount(1, $actual->items);
        $this->assertResultContainsProduct($expectedProduct, $actual);
    }

    private function assertResultContainsProduct(Product $expectedProduct, Result $actual)
    {
        $this->assertGreaterThanOrEqual(1, $actual->count);
        $this->assertGreaterThanOrEqual($actual->count, $actual->total);
        $this->assertCount($actual->count, $actual->items);
        $this->assertContains($expectedProduct, $actual->items, '', false, false);
    }
}
