<?php

namespace Frontastic\Common\ApiTests\ProductApi;

use Frontastic\Common\ApiTests\FrontasticApiTestCase;
use Frontastic\Common\ProductApiBundle\Domain\Category;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ReplicatorBundle\Domain\Project;

class CategoriesTest extends FrontasticApiTestCase
{
    /**
     * @dataProvider projectAndLanguage
     */
    public function testGetCategoriesAreNotEmpty(Project $project, string $language): void
    {
        $categories = $this->fetchCategories($project, $language);
        $this->assertNotEmpty($categories);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryCategoriesAreNotEmpty(Project $project, string $language): void
    {
        $result = $this->queryCategories($project, $language);
        $this->assertNotEmpty($result->items);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testGetCategoriesTwiceProducesSameResult(Project $project, string $language): void
    {
        $firstCategories = $this->fetchCategories($project, $language);
        $secondCategories = $this->fetchCategories($project, $language);

        $this->assertEquals($firstCategories, $secondCategories);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryCategoriesTwiceProducesSameResult(Project $project, string $language): void
    {
        $firstResult = $this->queryCategories($project, $language);
        $secondResult = $this->queryCategories($project, $language);

        $this->assertEquals($firstResult, $secondResult);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testAtLeastFiveCategoriesAreReturnedForFetchCategories(Project $project, string $language): void
    {
        $categories = $this->fetchCategories($project, $language);
        $this->assertGreaterThanOrEqual(5, count($categories));
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testAtLeastFiveCategoriesAreReturnedForQueryCategories(Project $project, string $language): void
    {
        $result = $this->queryCategories($project, $language);
        $this->assertGreaterThanOrEqual(5, count($result->items));
        $this->assertGreaterThanOrEqual(5, $result->count);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testGetCategoriesAreWellFormed(Project $project, string $language): void
    {
        $categories = $this->fetchAllCategories($project, $language);

        $this->assertCategoriesAreWellFormed($categories);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryCategoriesAreWellFormed(Project $project, string $language): void
    {
        $this->requireCategoryEndpointToSupportCursorBasedPagination($project);

        $categories = $this->queryAllCategoriesWithCursor($project, $language);

        $this->assertCategoriesAreWellFormed($categories);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testFetchCategoriesPathMatchesParentPath(Project $project, string $language): void
    {
        $categories = $this->fetchAllCategories($project, $language);

        $this->assertPathsAreWellFormed($categories);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryCategoriesPathMatchesParentPathWithCursor(Project $project, string $language): void
    {
        $this->requireCategoryEndpointToSupportCursorBasedPagination($project);

        $categories = $this->queryAllCategoriesWithCursor($project, $language);

        $this->assertPathsAreWellFormed($categories);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testCategoriesWithHighOffsetAreEmpty(Project $project, string $language): void
    {
        $this->requireCategoryEndpointToSupportOffsetPagination($project);

        $categories = $this->fetchCategories($project, $language, null, 5000);
        $this->assertEmpty($categories);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testFetchingManyCategoriesWorks(Project $project, string $language): void
    {
        $categories = $this->fetchCategories($project, $language, 250);
        $this->assertNotEmpty($categories);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryManyCategoriesWorks(Project $project, string $language): void
    {
        $result = $this->queryCategories($project, $language, 250);
        $this->assertNotEmpty($result->items);
        $this->assertNotNull($result->count);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testFetchingPastAllCategoriesReturnsEmptyResult(Project $project, string $language): void
    {
        $this->requireCategoryEndpointToSupportOffsetPagination($project);

        $limit = 10;
        $offset = 0;
        do {
            $categoriesFromCurrentStep = $this->fetchCategories($project, $language, $limit, $offset);
            $this->assertNotEmpty($categoriesFromCurrentStep);

            $offset += $limit;
        } while (count($categoriesFromCurrentStep) === $limit);

        $categoriesPastLastCategory = $this->fetchCategories($project, $language, $limit, $offset);
        $this->assertEmpty($categoriesPastLastCategory);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testFetchingAllCategoriesReturnsDistinctPaths(Project $project, string $language): void
    {
        $categories = $this->fetchAllCategories($project, $language);
        $this->assertDistinctPaths($categories);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryAllCategoriesReturnsDistinctPaths(Project $project, string $language): void
    {
        $this->requireCategoryEndpointToSupportCursorBasedPagination($project);

        $categories = $this->queryAllCategoriesWithCursor($project, $language);
        $this->assertDistinctPaths($categories);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testFetchCategoriesWithLowLimitReturnsSameAsWithHighLimit(Project $project, string $language): void
    {
        $this->requireCategoryEndpointToSupportOffsetPagination($project);

        $limit = 24;

        $categoriesFetchedInOneRequest = $this->fetchCategories($project, $language, $limit);
        $categoriesFetchedInMultipleRequests = $this->fetchCategoriesInMultipleSteps($project, $language, $limit, 2);

        $this->assertCategoriesEqualIgnoringOrder($categoriesFetchedInOneRequest, $categoriesFetchedInMultipleRequests);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryCategoriesWithLowLimitReturnsSameAsWithHighLimit(Project $project, string $language): void
    {
        $this->requireCategoryEndpointToSupportCursorBasedPagination($project);

        $limit = 24;

        $resultInOneRequest = $this->queryCategories($project, $language, $limit);
        $categoriesInMultipleRequests = $this->queryCategoriesInMultipleSteps($project, $language, $limit, 2);

        $this->assertCategoriesEqualIgnoringOrder($resultInOneRequest->items, $categoriesInMultipleRequests);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testFetchCategoryBySlugReturnsResult(Project $project, string $language): void
    {
        $this->requireCategoryEndpointToSupportSlugs($project);

        $categories = $this->fetchCategories($project, $language);
        $this->assertNotEmpty($categories);

        $category = $categories[0];

        $categoriesBySlug = $this->fetchCategoriesBySlug($project, $language, $category->slug);

        $this->assertNotEmpty($categoriesBySlug);
        foreach ($categoriesBySlug as $categoryBySlug) {
            $this->assertEquals($category->slug, $categoryBySlug->slug);
        }

        $this->assertContains($category, $categoriesBySlug, '');
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryCategoryBySlugReturnsResult(Project $project, string $language): void
    {
        $this->requireCategoryEndpointToSupportSlugs($project);

        $result = $this->queryCategories($project, $language);
        $this->assertNotEmpty($result->items);

        $category = $result->items[0];

        $resultBySlug = $this->queryCategoriesBySlug($project, $language, $category->slug);

        $this->assertNotEmpty($resultBySlug->items);
        foreach ($resultBySlug->items as $categoryBySlug) {
            $this->assertEquals($category->slug, $categoryBySlug->slug);
        }

        $this->assertContains($category, $resultBySlug->items, '');
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testFetchCategoryByNotExistingSlugReturnsEmptyResult(Project $project, string $language): void
    {
        $this->requireCategoryEndpointToSupportSlugs($project);

        $categories = $this->fetchCategoriesBySlug($project, $language, self::NON_EXISTING_SLUG);
        $this->assertEmpty($categories);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryCategoryByNotExistingSlugReturnsEmptyResult(Project $project, string $language): void
    {
        $this->requireCategoryEndpointToSupportSlugs($project);

        $resultBySlug = $this->queryCategoriesBySlug($project, $language, self::NON_EXISTING_SLUG);
        $this->assertEmpty($resultBySlug->items);
    }

    /**
     * @return Category[]
     */
    private function fetchCategoriesBySlug(Project $project, string $language, string $slug): array
    {
        $query = new CategoryQuery($this->buildQueryParameters($language));
        $query->slug = $slug;

        return $this->getProductApiForProject($project)->getCategories($query);
    }

    /**
     * @return Result
     */
    private function queryCategoriesBySlug(Project $project, string $language, string $slug): object
    {
        $query = new CategoryQuery($this->buildQueryParameters($language));
        $query->slug = $slug;

        return $this->getProductApiForProject($project)->queryCategories($query);
    }

    /**
     * @return Category[]
     */
    private function fetchCategoriesInMultipleSteps(
        Project $project,
        string $language,
        int $limit,
        int $stepSize
    ): array {
        $categoriesFetchedInMultipleRequests = [];
        for ($offset = 0; $offset < $limit; $offset += $stepSize) {
            $categoriesFetchedInMultipleRequests = array_merge(
                $categoriesFetchedInMultipleRequests,
                $this->fetchCategories($project, $language, $stepSize, $offset)
            );
        }
        return $categoriesFetchedInMultipleRequests;
    }

    /**
     * @return Category[]
     */
    private function queryCategoriesInMultipleSteps(
        Project $project,
        string $language,
        int $limit,
        int $stepSize
    ): array {
        $categoriesInMultipleRequests = [];

        $cursor = null;
        do {
            $categoryResult = $this->queryCategories($project, $language, $stepSize, null, $cursor);
            $cursor = $categoryResult->nextCursor;
            $categoriesInMultipleRequests = array_merge($categoriesInMultipleRequests, $categoryResult->items);
        } while ($categoryResult->nextCursor !== null && count($categoriesInMultipleRequests) < $limit);

        return $categoriesInMultipleRequests;
    }

    /**
     * @param Category[] $expected
     * @param Category[] $actual
     */
    private function assertCategoriesEqualIgnoringOrder(array $expected, array $actual): void
    {
        $compareCategoryIds = function (Category $lhs, Category $rhs) {
            return strcmp($lhs->categoryId, $rhs->categoryId);
        };
        usort($expected, $compareCategoryIds);
        usort($actual, $compareCategoryIds);

        $this->assertEquals($expected, $actual);
    }

    private function requireCategoryEndpointToSupportSlugs(Project $project): void
    {
        $this->requireProjectFeature($project, 'canQueryCategoriesBySlug');
    }

    private function requireCategoryEndpointToSupportCursorBasedPagination(Project $project): void
    {
        $this->requireProjectFeature($project, 'supportCursorBasedPagination');
    }

    private function requireCategoryEndpointToSupportOffsetPagination(Project $project): void
    {
        $this->requireProjectFeature($project, 'supportOffsetPagination');
    }

    /**
     * @param array $categories
     */
    private function assertCategoriesAreWellFormed(array $categories): void
    {
        $this->assertContainsOnlyInstancesOf(Category::class, $categories);

        foreach ($categories as $category) {
            $this->assertNotEmptyString($category->categoryId);

            $this->assertNotEmptyString($category->name);

            if ($category->slug !== null) {
                $this->assertNotEmptyString($category->slug);
                $this->assertRegExp(self::URI_PATH_SEGMENT_REGEX, $category->slug);
            }

            $this->assertIsInt($category->depth);
            $this->assertEquals(count($category->getAncestorIds()), $category->depth);

            $this->assertNotEmptyString($category->path);

            $this->assertNull($category->dangerousInnerCategory);
        }
    }

    /**
     * @param array $categories
     */
    private function assertPathsAreWellFormed(array $categories): void
    {
        $pathsByCategoryId = [];
        foreach ($categories as $category) {
            if (!array_key_exists($category->categoryId, $pathsByCategoryId)) {
                $pathsByCategoryId[$category->categoryId] = [];
            }

            $pathsByCategoryId[$category->categoryId][] = $category->path;
        }

        foreach ($categories as $category) {
            $parentCategoryId = $category->getParentCategoryId();

            if ($parentCategoryId === null) {
                $expectedPaths = ['/' . $category->categoryId];
            } else {
                $this->assertArrayHasKey($parentCategoryId, $pathsByCategoryId);

                $expectedPaths = [];
                foreach ($pathsByCategoryId[$parentCategoryId] as $parentPath) {
                    $expectedPaths[] = $parentPath . '/' . $category->categoryId;
                }
            }

            $this->assertContains($category->path, $expectedPaths);
        }
    }

    /**
     * @param array $categories
     */
    private function assertDistinctPaths(array $categories): void
    {
        $categoryPaths = array_map(
            function (Category $category): string {
                return $category->path;
            },
            $categories
        );
        $this->assertArrayHasDistinctValues($categoryPaths);
    }
}
