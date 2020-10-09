<?php

namespace Frontastic\Common\ApiTests\ProductApi;

use Frontastic\Common\ApiTests\FrontasticApiTestCase;
use Frontastic\Common\ProductApiBundle\Domain\Category;
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
        $this->requireCategoryEndpointToSupportOffsetPagination($project);

        $result = $this->queryProducts($project, $language);

        $this->assertSame(0, $result->offset);

        $this->assertGreaterThanOrEqual(50, $result->total);

        $this->assertGreaterThanOrEqual(ProductApi\PaginatedQuery::DEFAULT_LIMIT, $result->count);
        $this->assertCount($result->count, $result->items);

        $this->assertIsArray($result->items);
        $this->assertContainsOnlyInstancesOf(Product::class, $result->items);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryProductsReturnsValidResultWithNoPagination(Project $project, string $language): void
    {
        $result = $this->queryProducts($project, $language);

        $this->assertCount($result->count, $result->items);

        $this->assertIsArray($result->items);
        $this->assertContainsOnlyInstancesOf(Product::class, $result->items);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryCursoBasedPaginatedProductsReturnsProducts(Project $project, string $language): void
    {
        $this->requireCategoryEndpointToSupportCursorBasedPagination($project);

        $firstResult = $this->queryProducts(
            $project,
            $language,
            [],
            1,
            null,
            null
        );

        $this->assertNull($firstResult->previousCursor);
        $this->assertNotNull($firstResult->nextCursor);

        $secondResult = $this->queryProducts(
            $project,
            $language,
            [],
            1,
            null,
            $firstResult->nextCursor
        );

        $this->assertNotSame($firstResult->items[0]->productId, $secondResult->items[0]->productId);
        $this->assertNotNull($secondResult->previousCursor);
        $this->assertNotNull($secondResult->nextCursor);

        $thirdResult = $this->queryProducts(
            $project,
            $language,
            [],
            1,
            null,
            $secondResult->nextCursor
        );

        $this->assertNotSame($secondResult->items[0]->productId, $thirdResult->items[0]->productId);
        $this->assertNotNull($thirdResult->previousCursor);
        $this->assertNotNull($thirdResult->nextCursor);

        $secondResultPreviousCursor = $this->queryProducts(
            $project,
            $language,
            [],
            1,
            null,
            $thirdResult->previousCursor
        );

        $this->assertNotSame($thirdResult->items[0]->productId, $secondResultPreviousCursor->items[0]->productId);
        $this->assertSame($secondResult->items[0]->productId, $secondResultPreviousCursor->items[0]->productId);
        $this->assertNotNull($secondResultPreviousCursor->previousCursor);
        $this->assertNotNull($secondResultPreviousCursor->nextCursor);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryProductsByCustomQueryParameterReturnsProducts(Project $project, string $language): void
    {
        $queryParameters = [
            'rawApiInput' => [
                'foo' => 'var',
            ],
        ];

        $result = $this->queryProducts($project, $language, $queryParameters);

        $this->assertSame($result->query->rawApiInput, $queryParameters['rawApiInput']);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryProductsBySingleQueryParameterReturnsProducts(Project $project, string $language): void
    {
        $result = $this->getProductApiForProject($project)
            ->query(new ProductQuery($this->buildQueryParameters($language)));

        /** @var Product $product */
        $product = $result->items[0];

        /**
         * Filter by SKUs
         */
        $queryParameters = [
            'skus' => [$product->sku],
        ];

        $result = $this->queryProducts($project, $language, $queryParameters);

        $this->assertNotEmpty($result->items);
        $this->assertSame($product->productId, $result->items[0]->productId);
        $this->assertSame($product->sku, $result->items[0]->sku);

        /**
         * Filter by name
         */
        $queryParameters = [
            'query' => $product->name,
        ];

        $result = $this->queryProducts($project, $language, $queryParameters);

        $this->assertSame($product->productId, $result->items[0]->productId);
        $this->assertSame($product->sku, $result->items[0]->sku);

        /**
         * Filter by productId
         */
        $queryParameters = [
            'productId' => $product->productId,
        ];

        $result = $this->queryProducts($project, $language, $queryParameters);

        $this->assertSame($product->productId, $result->items[0]->productId);
        $this->assertSame($product->sku, $result->items[0]->sku);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryProductsByMultipleQueryParametersReturnsProducts(Project $project, string $language): void
    {
        $result = $this->getProductApiForProject($project)
            ->query(new ProductQuery($this->buildQueryParameters($language)));

        $this->assertNotEmpty($result->items);

        /** @var Product $product */
        $product = $result->items[0];

        $queryParameters = [
            'query' => $product->name,
            'skus' => [$product->sku],
        ];

        $result = $this->queryProducts($project, $language, $queryParameters);

        $this->assertSame($product->productId, $result->items[0]->productId);
        $this->assertSame($product->sku, $result->items[0]->sku);
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
                ProductApi\Query\SingleProductQuery::bySkuWithLocale($sku, $language),
                ProductApi::QUERY_SYNC
            );

        $this->assertProductsAreWellFormed($project, $language, [$productBySku]);
        $this->assertSameProduct($product, $productBySku);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testGetProductSyncByNonExistingSkuReturnsNull(Project $project, string $language): void
    {
        $query = ProductApi\Query\SingleProductQuery::bySkuWithLocale(self::NON_EXISTING_SKU, $language);

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
                ProductApi\Query\SingleProductQuery::bySkuWithLocale($sku, $language),
                ProductApi::QUERY_ASYNC
            );

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $productBySku = $promise->wait();

        $this->assertProductsAreWellFormed($project, $language, [$productBySku]);
        $this->assertSameProduct($product, $productBySku);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testGetProductAsyncByNonExistingSkuReturnsFailedPromise(Project $project, string $language): void
    {
        $promise = $this->getProductApiForProject($project)
            ->getProduct(
                ProductApi\Query\SingleProductQuery::bySkuWithLocale(self::NON_EXISTING_SKU, $language),
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
                ProductApi\Query\SingleProductQuery::byProductIdWithLocale($productId, $language),
                ProductApi::QUERY_SYNC
            );

        $this->assertProductsAreWellFormed($project, $language, [$productById]);
        $this->assertSameProduct($product, $productById);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testGetProductSyncByNonExistingIdThrowsException(Project $project, string $language): void
    {
        $query = ProductApi\Query\SingleProductQuery::byProductIdWithLocale(self::NON_EXISTING_PRODUCT_ID, $language);

        $productApi = $this->getProductApiForProject($project);

        $this->expectException(ProductApi\ProductNotFoundException::class);
        $productApi->getProduct($query, ProductApi::QUERY_SYNC);
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
                ProductApi\Query\SingleProductQuery::byProductIdWithLocale($productId, $language),
                ProductApi::QUERY_ASYNC
            );

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $productById = $promise->wait();

        $this->assertProductsAreWellFormed($project, $language, [$productById]);
        $this->assertSameProduct($product, $productById);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testGetProductAsyncByNonExistingIdReturnsFailedPromise(Project $project, string $language): void
    {
        $promise = $this->getProductApiForProject($project)
            ->getProduct(
                ProductApi\Query\SingleProductQuery::byProductIdWithLocale(self::NON_EXISTING_PRODUCT_ID, $language),
                ProductApi::QUERY_ASYNC
            );

        $this->assertInstanceOf(PromiseInterface::class, $promise);

        $this->expectException(ProductApi\ProductNotFoundException::class);
        $promise->wait();
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testGetProductAsyncValidatesQuerySynchronously(Project $project, string $language): void
    {
        $query = ProductApi\Query\SingleProductQuery::bySkuWithLocale(self::NON_EXISTING_SKU, $language);
        $query->productId = self::NON_EXISTING_PRODUCT_ID;

        $productApi = $this->getProductApiForProject($project);

        $this->expectException(ProductApi\Exception\InvalidQueryException::class);
        $productApi->getProduct($query, ProductApi::QUERY_ASYNC);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testGetProductSyncBySkuWithProductQueryReturnsProduct(Project $project, string $language): void
    {
        $product = $this->getAProduct($project, $language);
        $sku = $product->variants[0]->sku;
        $this->assertNotEmptyString($sku);

        $productBySku = $this->getProductApiForProject($project)
            ->getProduct(
                new ProductQuery(array_merge($this->buildQueryParameters($language), ['sku' => $sku])),
                ProductApi::QUERY_SYNC
            );

        $this->assertProductsAreWellFormed($project, $language, [$productBySku]);
        $this->assertSameProduct($product, $productBySku);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testGetProductSyncByIdWithProductQueryReturnsProduct(Project $project, string $language): void
    {
        $product = $this->getAProduct($project, $language);
        $productId = $product->productId;
        $this->assertNotEmptyString($productId);

        $productById = $this->getProductApiForProject($project)
            ->getProduct(
                new ProductQuery(array_merge($this->buildQueryParameters($language), ['productId' => $productId])),
                ProductApi::QUERY_SYNC
            );

        $this->assertProductsAreWellFormed($project, $language, [$productById]);
        $this->assertSameProduct($product, $productById);
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
        $this->requireCategoryEndpointToSupportOffsetPagination($project);

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
    public function testQueryAllProductsOrderedByIdWithLowLimitWithCursorBasedPaginationReturnsSameAsWithHighLimit(
        Project $project,
        string $language
    ): void {
        $this->requireCategoryEndpointToSupportCursorBasedPagination($project);

        $limit = 24;
        $productsQueriedOneStep = $this->queryProducts($project, $language, $this->sortReproducibly(), $limit);
        $productsQueriedInMultipleSteps = $this->queryProductsInMultipleStepsWithCursorBasesPagination(
            $project,
            $language,
            $this->sortReproducibly(),
            $limit,
            2
        );

        $this->assertEquals($productsQueriedOneStep->items, $productsQueriedInMultipleSteps);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testProductsFromSearchAreWellFormed(Project $project, string $language): void
    {
        $result = $this->queryProducts($project, $language, [], 50);
        $this->assertProductsAreWellFormed($project, $language, $result->items);
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
    public function testQueryProductByCategoryReturnsWellFormedProducts(Project $project, string $language): void
    {
        $allProducts = $this->queryProducts($project, $language);

        $categories = $this
            ->getProductApiForProject($project)
            ->getCategories(new ProductApi\Query\CategoryQuery([
                'locale' => $language,
                'limit' => 3 // don't query too much categories so this test will not get too slow
            ]));
        foreach ($categories as $category) {
            $categoryId = $category->categoryId;

            $productsByCategory = $this->queryProducts($project, $language, ['category' => $categoryId]);
            $this->assertLessThanOrEqual($allProducts->total, $productsByCategory->total);
            $this->assertProductsAreWellFormed($project, $language, $productsByCategory->items);
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

        $productsBySku = $this->queryProducts($project, $language, ['skus' => [$sku]]);
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
            $this->queryProducts($project, $language, ['productIds' => [$productId]]);
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

    /**
     * @return Product[]
     */
    private function queryProductsInMultipleStepsWithCursorBasesPagination(
        Project $project,
        string $language,
        array $queryParameters,
        int $limit,
        int $stepSize
    ): array {
        $products = [];

        $nextCursor = null;
        do {
            $resultFromCurrentStep = $this->queryProducts(
                $project,
                $language,
                $queryParameters,
                $stepSize,
                null,
                $nextCursor
            );
            $products = array_merge($products, $resultFromCurrentStep->items);

            $nextCursor = $resultFromCurrentStep->nextCursor;
        } while ($resultFromCurrentStep->nextCursor !== null && count($products) < $limit);

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
        if ($actual->count !== null) {
            $this->assertEquals(0, $actual->count);
        }
        if ($actual->total !== null) {
            $this->assertEquals(0, $actual->total);
        }

        $this->assertEmpty($actual->items);
    }

    private function assertSingleProductResult(Product $expectedProduct, Result $actual)
    {
        if ($actual->total !== null) {
            $this->assertEquals(1, $actual->total);
        }
        $this->assertEquals(1, $actual->count);
        $this->assertCount(1, $actual->items);
        $this->assertResultContainsProduct($expectedProduct, $actual);
    }

    private function assertResultContainsProduct(Product $expectedProduct, Result $actual)
    {
        if ($actual->total !== null) {
            $this->assertGreaterThanOrEqual($actual->count, $actual->total);
        }

        $this->assertGreaterThanOrEqual(1, $actual->count);
        $this->assertCount($actual->count, $actual->items);

        $actualProducts = [];
        foreach ($actual->items as $product) {
            $actualProducts[$product->productId] = $product;
        }

        $this->assertArrayHasKey($expectedProduct->productId, $actualProducts);
        $this->assertSameProduct($expectedProduct, $actualProducts[$expectedProduct->productId]);
    }

    private function assertSameProduct(Product $expected, Product $actual): void
    {
        // We only check certain attributes here since the search index may contain less data then the product DB and
        // the information from the search index might be outdated.

        $this->assertSame(
            $expected->productId,
            $actual->productId,
            sprintf(
                'Expected product %s (SKU %s), got product %s (SKU %s)',
                $expected->productId,
                $expected->sku,
                $actual->productId,
                $actual->sku
            )
        );
        $this->assertSame(
            $expected->name,
            $actual->name,
            sprintf('Product %s (SKU %s) has different names', $expected->productId, $expected->sku)
        );
        $this->assertSame(
            $expected->slug,
            $actual->slug,
            sprintf('Product %s (SKU %s) has different slugs', $expected->productId, $expected->sku)
        );

        if ($expected->description !== null) {
            $this->assertSame(
                $expected->description,
                $actual->description,
                sprintf('Product %s (SKU %s) has different descriptions', $expected->productId, $expected->sku)
            );
        }

        $this->assertSameSize($expected->variants, $actual->variants);
        for ($variantIndex = 0; $variantIndex < count($expected->variants); ++$variantIndex) {
            $this->assertArrayHasKey($variantIndex, $expected->variants);
            $this->assertArrayHasKey($variantIndex, $actual->variants);

            $expectedVariant = $expected->variants[$variantIndex];
            $actualVariant = $actual->variants[$variantIndex];

            $this->assertSame($expectedVariant->id, $actualVariant->id);
            $this->assertSame($expectedVariant->sku, $actualVariant->sku);
            $this->assertSame($expectedVariant->groupId, $actualVariant->groupId);
            $this->assertSame($expectedVariant->currency, $actualVariant->currency);
        }
    }

    /**
     * @param Product[] $products
     */
    private function assertProductsAreWellFormed(Project $project, string $language, array $products): void
    {
        $existingCategoryIds = array_map(
            function (Category $category): string {
                return $category->categoryId;
            },
            $this->fetchAllCategories($project, $language)
        );

        $productIds = array_map(
            function (Product $product): string {
                return $product->productId;
            },
            $products
        );
        $this->assertArrayHasDistinctValues($productIds);

        $previousGroupIds = [];
        foreach ($products as $product) {
            $this->assertNotEmptyString($product->productId);

            if ($product->changed !== null) {
                $this->assertInstanceOf(\DateTimeImmutable::class, $product->changed);
                $this->assertLessThan(new \DateTimeImmutable(), $product->changed);
            }

            if ($product->version !== null) {
                $this->assertNotEmptyString($product->version);
            }

            $this->assertNotEmptyString(
                $product->name,
                sprintf('Product %s (SKU %s) has an invalid name', $product->productId, $product->sku)
            );
            $this->assertContainsNoHtml($product->name);

            $this->assertNotEmptyString($product->slug);
            $this->assertRegExp(
                self::URI_PATH_SEGMENT_REGEX,
                $product->slug,
                sprintf(
                    'Product %s (SKU %s) has an invalid slug: %s',
                    $product->productId,
                    $product->sku,
                    $product->slug
                )
            );

            $this->assertIsString($product->description);
            $this->assertContainsNoHtml(
                $product->description,
                sprintf('Description of product %s (SKU %s) contains HTML', $product->productId, $product->sku)
            );

            $this->assertIsArray($product->categories);
            foreach ($product->categories as $category) {
                $this->assertIsString($category);
                $this->assertNotEmpty($category);
                $this->assertContains($category, $existingCategoryIds);
            }

            $this->assertIsArray($product->variants);
            $this->assertNotEmpty($product->variants);
            $this->assertContainsOnlyInstancesOf(Variant::class, $product->variants);

            $currentProductGroupId = null;
            foreach ($product->variants as $variant) {
                $this->assertProductVariantIsWellFormed($variant);

                if ($currentProductGroupId === null) {
                    $currentProductGroupId = $variant->groupId;
                    $this->assertNotContains($currentProductGroupId, $previousGroupIds);
                    $previousGroupIds[] = $currentProductGroupId;
                }
                $this->assertSame(
                    $currentProductGroupId,
                    $variant->groupId,
                    sprintf(
                        'All variants of product %s (%s) should have the same groupId',
                        $product->productId,
                        $product->sku
                    )
                );
            }

            $this->assertNull($product->dangerousInnerProduct);
        }
    }

    private function assertContainsNoHtml(string $actual, string $message = null): void
    {
        $this->assertEquals($actual, strip_tags($actual), $message ?? 'The string may not contain HTML tags.');
    }

    private function requireCategoryEndpointToSupportCursorBasedPagination(Project $project): void
    {
        $this->requireProjectFeature($project, 'supportCursorBasedPagination');
    }

    private function requireCategoryEndpointToSupportOffsetPagination(Project $project): void
    {
        $this->requireProjectFeature($project, 'supportOffsetPagination');
    }
}
