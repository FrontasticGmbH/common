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
    public function testQueryProductByCategoryReturnsProduct(Project $project, string $language): void
    {
        $allProducts = $this->queryProducts($project, $language);

        $categoryId = null;
        foreach ($allProducts->items as $product) {
            if (count($product->categories) > 0) {
                $categoryId = reset($product->categories);
                break;
            }
        }
        $this->assertNotEmptyString($categoryId, 'At least one product needs a category.');

        $productsByCategory = $this->queryProducts($project, $language, ['category' => $categoryId]);
        $this->assertLessThanOrEqual($allProducts->total, $productsByCategory->total);

        $descendantCategories = [];
        foreach ($this->fetchAllCategories($project, $language) as $category) {
            if (in_array($categoryId, $category->getPathAsArray())) {
                $descendantCategories[] = $category->categoryId;
            }
        }

        foreach ($productsByCategory->items as $product) {
            $this->assertNotEmpty(array_intersect($descendantCategories, $product->categories));
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

        $this->assertSame($expected->productId, $actual->productId);
        $this->assertSame($expected->name, $actual->name);
        $this->assertSame($expected->slug, $actual->slug);
        $this->assertSame($expected->description, $actual->description);

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

            $this->assertNotEmptyString($product->name);

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

            $this->assertInternalType('string', $product->description);

            $this->assertInternalType('array', $product->categories);
            foreach ($product->categories as $category) {
                $this->assertInternalType('string', $category);
                $this->assertNotEmpty($category);
                $this->assertContains($category, $existingCategoryIds);
            }

            $this->assertInternalType('array', $product->variants);
            $this->assertNotEmpty($product->variants);
            $this->assertContainsOnlyInstancesOf(Variant::class, $product->variants);

            $currentProductGroupId = null;
            foreach ($product->variants as $variant) {
                $this->assertNotEmptyString($variant->id);
                $this->assertNotEmptyString($variant->sku);

                $this->assertNotEmptyString($variant->groupId);
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
}
