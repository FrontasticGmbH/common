<?php

namespace Frontastic\Common\ApiTests\ProductApi;

use Frontastic\Common\ProductApiBundle\Domain\Category;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery;
use Frontastic\Common\ReplicatorBundle\Domain\Project;

class CategoriesTest extends ProductApiTestCase
{
    /**
     * @dataProvider projectAndLanguage
     */
    public function testCategoriesAreNotEmpty(Project $project, string $language): void
    {
        $categories = $this->fetchCategories($project, $language);
        $this->assertNotEmpty($categories);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryCategoriesTwiceProducesSameResult(Project $project, string $language): void
    {
        $firstCategories = $this->fetchCategories($project, $language);
        $secondCategories = $this->fetchCategories($project, $language);

        $this->assertEquals($firstCategories, $secondCategories);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testAtLeastFiveCategoriesAreReturned(Project $project, string $language): void
    {
        $categories = $this->fetchCategories($project, $language);
        $this->assertGreaterThanOrEqual(5, count($categories));
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testFetchCategoriesReturnsCategories(Project $project, string $language): void
    {
        $categories = $this->fetchAllCategories($project, $language);
        $this->assertContainsOnlyInstancesOf(Category::class, $categories);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testCategoriesHaveNonEmptyStringId(Project $project, string $language): void
    {
        $categories = $this->fetchAllCategories($project, $language);

        foreach ($categories as $category) {
            $this->assertInternalType('string', $category->categoryId);
            $this->assertNotEmpty($category->categoryId);
        }
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testCategoriesHaveNonEmptyStringName(Project $project, string $language): void
    {
        $categories = $this->fetchAllCategories($project, $language);

        foreach ($categories as $category) {
            $this->assertInternalType('string', $category->name);
            $this->assertNotEmpty($category->name);
        }
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testCategoriesHaveNonEmptyStringSlug(Project $project, string $language): void
    {
        $categories = $this->fetchAllCategories($project, $language);

        foreach ($categories as $category) {
            $this->assertInternalType('string', $category->slug);
            $this->assertNotEmpty($category->slug);
        }
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testCategoriesHaveValidPathSegmentAsSlug(Project $project, string $language): void
    {
        $categories = $this->fetchAllCategories($project, $language);

        foreach ($categories as $category) {
            $this->assertRegExp(self::URI_PATH_SEGMENT_REGEX, $category->slug);
        }
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testCategoriesHaveNonEmptyStringPath(Project $project, string $language): void
    {
        $categories = $this->fetchAllCategories($project, $language);

        foreach ($categories as $category) {
            $this->assertInternalType('integer', $category->depth);
            $this->assertEquals(count($category->getAncestorIds()), $category->depth);
        }
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testCategoriesHaveCorrectDepth(Project $project, string $language): void
    {
        $categories = $this->fetchAllCategories($project, $language);

        foreach ($categories as $category) {
            $this->assertInternalType('string', $category->path);
            $this->assertNotEmpty($category->path);
        }
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testCategoriesPathMatchesParentPath(Project $project, string $language): void
    {
        $categories = $this->fetchAllCategories($project, $language);

        $pathByCategoryId = [];
        foreach ($categories as $category) {
            $pathByCategoryId[$category->categoryId] = $category->path;
        }

        foreach ($categories as $category) {
            $parentCategoryId = $category->getParentCategoryId();

            $parentPath = '';
            if ($parentCategoryId !== null) {
                $this->assertArrayHasKey($parentCategoryId, $pathByCategoryId);
                $parentPath = $pathByCategoryId[$parentCategoryId];
            }

            $this->assertEquals($parentPath . '/' . $category->categoryId, $category->path);
        }
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testCategoriesDontHaveDangerousInnerCategory(Project $project, string $language): void
    {
        $categories = $this->fetchAllCategories($project, $language);

        foreach ($categories as $category) {
            $this->assertNull($category->dangerousInnerCategory);
        }
        $this->assertGreaterThanOrEqual(5, count($categories));
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testCategoriesWithHighOffsetAreEmpty(Project $project, string $language): void
    {
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
    public function testFetchingPastAllCategoriesReturnsEmptyResult(Project $project, string $language): void
    {
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
    public function testFetchingAllCategoriesReturnsDistinctIds(Project $project, string $language): void
    {
        $categories = $this->fetchAllCategories($project, $language);
        $categoryIds = array_map(
            function (Category $category): string {
                return $category->categoryId;
            },
            $categories
        );
        $this->assertArrayHasDistinctValues($categoryIds);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testFetchCategoriesWithLowLimitReturnsSameAsWithHighLimit(Project $project, string $language): void
    {
        $limit = 24;

        $categoriesFetchedInOneRequest = $this->fetchCategories($project, $language, $limit);
        $categoriesFetchedInMultipleRequests = $this->fetchCategoriesInMultipleSteps($project, $language, $limit, 2);

        $this->assertCategoriesEqualIgnoringOrder($categoriesFetchedInOneRequest, $categoriesFetchedInMultipleRequests);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testFetchCategoryBySlugReturnsResult(Project $project, string $language): void
    {
        $categories = $this->fetchCategories($project, $language);
        $this->assertNotEmpty($categories);

        $category = $categories[0];

        $categoriesBySlug = $this->fetchCategoriesBySlug($project, $language, $category->slug);

        $this->assertNotEmpty($categoriesBySlug);
        foreach ($categoriesBySlug as $categoryBySlug) {
            $this->assertEquals($category->slug, $categoryBySlug->slug);
        }

        $this->assertContains($category, $categoriesBySlug, '', false, false);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testFetchCategoryByNotExistingSlugReturnsEmptyResult(Project $project, string $language): void
    {
        $categories = $this->fetchCategoriesBySlug($project, $language, self::NON_EXISTING_SLUG);
        $this->assertEmpty($categories);
    }

    /**
     * @return Category[]
     */
    private function fetchCategories(Project $project, string $language, ?int $limit = null, ?int $offset = null): array
    {
        return $this
            ->productApiForProject($project)
            ->getCategories(new CategoryQuery($this->buildQueryParameters($language, $limit, $offset)));
    }

    /**
     * @return Category[]
     */
    private function fetchCategoriesBySlug(Project $project, string $language, string $slug): array
    {
        $query = new CategoryQuery($this->buildQueryParameters($language));
        $query->slug = $slug;

        return $this->productApiForProject($project)->getCategories($query);
    }

    /**
     * @return Category[]
     */
    private function fetchAllCategories(Project $project, string $language): array
    {
        $categories = [];

        $limit = 50;
        $offset = 0;
        do {
            $categoriesFromCurrentStep = $this->fetchCategories($project, $language, $limit, $offset);
            $categories = array_merge($categories, $categoriesFromCurrentStep);

            $offset += $limit;
        } while (count($categoriesFromCurrentStep) === $limit);

        return $categories;
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
}
