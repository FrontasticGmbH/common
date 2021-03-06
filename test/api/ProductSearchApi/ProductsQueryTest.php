<?php

namespace Frontastic\Common\ApiTests\ProductSearchApi;

use Frontastic\Common\ApiTests\FrontasticApiTestCase;
use Frontastic\Common\ProductApiBundle\Domain\Category;
use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use GuzzleHttp\Promise\PromiseInterface;

class ProductsQueryTest extends FrontasticApiTestCase
{
    private const NON_EXISTING_CATEGORY = 'THIS_CATEGORY_SHOULD_NEVER_EXIST_IN_ANY_DATA_SET';
    private const NON_EXISTING_SKU = 'THIS_SKU_SHOULD_NEVER_EXIST_IN_ANY_DATA_SET';
    private const NON_EXISTING_PRODUCT_ID = 'THIS_PRODUCT_ID_SHOULD_NEVER_EXIST_IN_ANY_DATA_SET';

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryReturnsPromiseToResult(Project $project, string $language): void
    {
        $promise = $this->getProductSearchApiForProject($project)
            ->query(new ProductQuery($this->buildQueryParameters($language)));

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

        $result = $this->queryProductsWithProductSearchApi($project, $language);

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
        $result = $this->queryProductsWithProductSearchApi($project, $language);

        $this->assertCount($result->count, $result->items);

        $this->assertIsArray($result->items);
        $this->assertContainsOnlyInstancesOf(Product::class, $result->items);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryCursorBasedPaginatedProductsReturnsProducts(Project $project, string $language): void
    {
        $firstResult = $this->queryProductsWithProductSearchApi(
            $project,
            $language,
            $this->sortReproducibly(),
            1,
            null,
            null
        );

        $this->assertNull($firstResult->previousCursor);
        $this->assertNotNull($firstResult->nextCursor);

        $secondResult = $this->queryProductsWithProductSearchApi(
            $project,
            $language,
            $this->sortReproducibly(),
            1,
            null,
            $firstResult->nextCursor
        );

        $this->assertNotSame($firstResult->items[0]->productId, $secondResult->items[0]->productId);
        $this->assertNotNull($secondResult->previousCursor);
        $this->assertNotNull($secondResult->nextCursor);

        $thirdResult = $this->queryProductsWithProductSearchApi(
            $project,
            $language,
            $this->sortReproducibly(),
            1,
            null,
            $secondResult->nextCursor
        );

        $this->assertNotSame($secondResult->items[0]->productId, $thirdResult->items[0]->productId);
        $this->assertNotNull($thirdResult->previousCursor);
        $this->assertNotNull($thirdResult->nextCursor);

        $secondResultPreviousCursor = $this->queryProductsWithProductSearchApi(
            $project,
            $language,
            $this->sortReproducibly(),
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

        $result = $this->queryProductsWithProductSearchApi($project, $language, $queryParameters);

        $this->assertSame($result->query->rawApiInput, $queryParameters['rawApiInput']);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryProductsBySingleQueryParameterReturnsProducts(Project $project, string $language): void
    {
        $this->requireCategoryEndpointToSupportSearchByIdentifierAndQueryParameters($project);

        $promise = $this->getProductSearchApiForProject($project)
            ->query(new ProductQuery($this->buildQueryParameters($language)));

        $result = $promise->wait();

        /** @var Product $product */
        $product = $result->items[0];

        /**
         * Filter by SKUs
         */
        $queryParameters = [
            'skus' => [$product->sku],
        ];

        $result = $this->queryProductsWithProductSearchApi($project, $language, $queryParameters);

        $this->assertSingleProductResult($project, $product, $result);
        $this->assertNotEmpty($result->items);
        $this->assertSame($product->productId, $result->items[0]->productId);
        $this->assertSame($product->sku, $result->items[0]->sku);

        /**
         * Filter by name
         */
        $queryParameters = [
            'query' => $product->name,
        ];

        $result = $this->queryProductsWithProductSearchApi($project, $language, $queryParameters);

        $this->assertResultContainsProduct($project, $product, $result);
        $this->assertSame($product->productId, $result->items[0]->productId);
        $this->assertSame($product->sku, $result->items[0]->sku);

        /**
         * Filter by productId
         */
        $queryParameters = [
            'productId' => $product->productId,
        ];

        $result = $this->queryProductsWithProductSearchApi($project, $language, $queryParameters);

        $this->assertSingleProductResult($project, $product, $result);
        $this->assertSame($product->productId, $result->items[0]->productId);
        $this->assertSame($product->sku, $result->items[0]->sku);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryProductsByMultipleQueryParametersReturnsProducts(Project $project, string $language): void
    {
        $this->requireCategoryEndpointToSupportSearchByIdentifierAndQueryParameters($project);

        $promise = $this->getProductSearchApiForProject($project)
            ->query(new ProductQuery($this->buildQueryParameters($language)));

        $result = $promise->wait();

        $this->assertNotEmpty($result->items);

        /** @var Product $product */
        $product = $result->items[0];

        $queryParameters = [
            'query' => $product->name,
            'skus' => [$product->sku],
        ];

        $result = $this->queryProductsWithProductSearchApi($project, $language, $queryParameters);

        $this->assertSame($product->productId, $result->items[0]->productId);
        $this->assertSame($product->sku, $result->items[0]->sku);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryAllProductsOrderedByIdTwiceReturnsSameResult(Project $project, string $language): void
    {
        $firstResult = $this->queryProductsWithProductSearchApi($project, $language, $this->sortReproducibly());
        $secondResult = $this->queryProductsWithProductSearchApi($project, $language, $this->sortReproducibly());

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
        $productsQueriedOneStep =
            $this->queryProductsWithProductSearchApi($project, $language, $this->sortReproducibly(), $limit);
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
        $limit = 24;
        $productsQueriedOneStep =
            $this->queryProductsWithProductSearchApi($project, $language, $this->sortReproducibly(), $limit);
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
        $this->requireCategoryEndpointToHaveConsistentProductSearchData($project);

        $result = $this->queryProductsWithProductSearchApi($project, $language, [], 50);
        $this->assertProductsAreWellFormed($project, $language, $result->items);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryByNonExistingCategoryReturnsEmptyResult(Project $project, string $language): void
    {
        $products =
            $this->queryProductsWithProductSearchApi($project, $language, ['category' => self::NON_EXISTING_CATEGORY]);
        $this->assertEmptyResult($products);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryByNonExistingSkuReturnsEmptyResult(Project $project, string $language): void
    {
        $this->requireCategoryEndpointToSupportSearchByIdentifierAndQueryParameters($project);

        $products = $this->queryProductsWithProductSearchApi($project, $language, ['sku' => self::NON_EXISTING_SKU]);
        $this->assertEmptyResult($products);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryByNonExistingSkusReturnsEmptyResult(Project $project, string $language): void
    {
        $this->requireCategoryEndpointToSupportSearchByIdentifierAndQueryParameters($project);

        $products = $this->queryProductsWithProductSearchApi($project, $language, ['skus' => [self::NON_EXISTING_SKU]]);
        $this->assertEmptyResult($products);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryByNonExistingProductIdsReturnsEmptyResult(Project $project, string $language): void
    {
        $this->requireCategoryEndpointToSupportSearchByIdentifierAndQueryParameters($project);

        $products = $this->queryProductsWithProductSearchApi($project,
            $language,
            ['productIds' => [self::NON_EXISTING_PRODUCT_ID]]);
        $this->assertEmptyResult($products);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryProductBySkuReturnsOnlyProduct(Project $project, string $language): void
    {
        $this->requireCategoryEndpointToSupportSearchByIdentifierAndQueryParameters($project);

        $product = $this->getAProductWithProductSearchApi($project, $language);
        $sku = $product->variants[0]->sku;
        $this->assertNotEmptyString($sku);

        $productsBySku = $this->queryProductsWithProductSearchApi($project, $language, ['sku' => $sku]);
        $this->assertSingleProductResult($project, $product, $productsBySku);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryProductBySkusReturnsOnlyProduct(Project $project, string $language): void
    {
        $this->requireCategoryEndpointToSupportSearchByIdentifierAndQueryParameters($project);

        $product = $this->getAProductWithProductSearchApi($project, $language);
        $sku = $product->variants[0]->sku;
        $this->assertNotEmptyString($sku);

        $productsBySku = $this->queryProductsWithProductSearchApi($project, $language, ['skus' => [$sku]]);
        $this->assertSingleProductResult($project, $product, $productsBySku);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryProductByProductIdsReturnsOnlyProduct(Project $project, string $language): void
    {
        $this->requireCategoryEndpointToSupportSearchByIdentifierAndQueryParameters($project);

        $product = $this->getAProductWithProductSearchApi($project, $language);
        $productId = $product->productId;
        $this->assertNotEmptyString($productId);

        $productsByProductId =
            $this->queryProductsWithProductSearchApi($project, $language, ['productIds' => [$productId]]);
        $this->assertSingleProductResult($project, $product, $productsByProductId);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryProductsByFacetOrFilterReturnsResults(Project $project, string $language)
    {
        $result = $this->queryProductsWithProductSearchApi($project, $language, ['limit' => 1]);

        if (empty($result->facets)) {
            $this->markTestSkipped('This test requires facets to be returned from an empty query. Consider looking at Facets returned from your configured EnabledFacetService implementation.');
        }

        /** @var Result\TermFacet[] $resultTermFacets */
        $resultTermFacets = array_values(array_filter($result->facets,
            function (Result\Facet $facet) {
                return $facet instanceof Result\TermFacet;
            }));

        /** @var Result\RangeFacet[] $resultRangeFacets */
        $resultRangeFacets = array_values(array_filter($result->facets,
            function (Result\Facet $facet) {
                return $facet instanceof Result\RangeFacet;
            }));

        $termFacets = count($resultTermFacets) === 0 ? [] : [
            new ProductApi\Query\TermFacet([
                'handle' => $resultTermFacets[0]->handle,
                'terms' => [$resultTermFacets[0]->terms[0]->value],
            ]),
        ];

        $rangeFacets = count($resultRangeFacets) === 0 ? [] : [
            new ProductApi\Query\RangeFacet([
                'handle' => $resultRangeFacets[0]->handle,
                'min' => $resultRangeFacets[0]->min,
                'max' => $resultRangeFacets[0]->max,
            ]),
        ];

        $facetResults = $this->queryProductsWithProductSearchApi($project,
            $language,
            [
                'facets' => array_merge($termFacets, $rangeFacets),
            ]);

        if ($result->total) {
            $this->assertLessThan($result->total, $facetResults->total);
        }
        $this->assertGreaterThan(0, count($facetResults->items));

        $termFacets = count($resultTermFacets) === 0 ? [] : [
            new ProductApi\Query\TermFilter([
                'handle' => $resultTermFacets[0]->handle,
                'terms' => [$resultTermFacets[0]->terms[0]->value],
            ]),
        ];

        $rangeFacets = count($resultRangeFacets) === 0 ? [] : [
            new ProductApi\Query\RangeFilter([
                'handle' => $resultRangeFacets[0]->handle,
                'min' => $resultRangeFacets[0]->min,
                'max' => $resultRangeFacets[0]->max,
            ]),
        ];

        $filterResults = $this->queryProductsWithProductSearchApi($project,
            $language,
            [
                'filter' => array_merge($termFacets, $rangeFacets),
            ]);

        if ($result->total) {
            $this->assertLessThan($result->total, $filterResults->total);
        }
        $this->assertGreaterThan(0, count($filterResults->items));
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
            $result = $this->queryProductsWithProductSearchApi(
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
            $resultFromCurrentStep = $this->queryProductsWithProductSearchApi(
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

    private function assertSingleProductResult(Project $project, Product $expectedProduct, Result $actual)
    {
        if ($actual->total !== null) {
            $this->assertEquals(1, $actual->total);
        }
        $this->assertEquals(1, $actual->count);
        $this->assertCount(1, $actual->items);
        $this->assertResultContainsProduct($project, $expectedProduct, $actual);
    }

    private function assertResultContainsProduct(Project $project, Product $expectedProduct, Result $actual)
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
        $this->assertSameProduct($project, $expectedProduct, $actualProducts[$expectedProduct->productId]);
    }

    private function assertSameProduct(Project $project, Product $expected, Product $actual): void
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

        if ($this->hasProjectFeature($project, 'searchIncludesAllVariants')) {
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

    private function requireCategoryEndpointToSupportOffsetPagination(Project $project): void
    {
        $this->requireProjectFeature($project, 'supportOffsetPagination');
    }

    private function requireCategoryEndpointToHaveConsistentProductSearchData(Project $project): void
    {
        $this->requireProjectFeature($project, 'hasConsistentProductSearchData');
    }

    private function requireCategoryEndpointToSupportSearchByIdentifierAndQueryParameters(Project $project): void
    {
        $this->requireProjectFeature($project, 'supportSearchByIdentifierAndQueryParameters');
    }
}
