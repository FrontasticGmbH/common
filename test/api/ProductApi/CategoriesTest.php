<?php

namespace Frontastic\Common\ApiTests\ProductApi;

use Frontastic\Common\ApiTests\FrontasticApiTestCase;
use Frontastic\Common\ProductApiBundle\Domain\Category;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApiFactory;
use Frontastic\Common\ReplicatorBundle\Domain\Project;

class CategoriesTest extends FrontasticApiTestCase
{
    private const NON_EXISTING_SLUG = 'THIS_SLUG_SHOULD_NEVER_EXIST_IN_ANY_DATA_SET';

    /**
     * @var array<string, ProductApi>
     */
    private $productApis = [];

    /**
     * @var ProductApiFactory
     */
    private $productApiFactory;

    protected function setUp()
    {
        $this->productApiFactory = self::$container->get(ProductApiFactory::class);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testAllCategoriesAreNotEmpty(Project $project, string $language): void
    {
        $categories = $this->fetchCategories($project, $language);
        $this->assertNotEmpty($categories);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testAllCategoriesReturnsCategories(Project $project, string $language): void
    {
        $categories = $this->fetchCategories($project, $language);
        $this->assertContainsOnlyInstancesOf(Category::class, $categories);
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
    public function testCategoriesHaveNonEmptyStringId(Project $project, string $language): void
    {
        $categories = $this->fetchCategories($project, $language);

        foreach ($categories as $category) {
            $this->assertInternalType('string', $category->categoryId);
            $this->assertNotEmpty($category->categoryId);
        }
        $this->assertGreaterThanOrEqual(5, count($categories));
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testCategoriesHaveNonEmptyStringName(Project $project, string $language): void
    {
        $categories = $this->fetchCategories($project, $language);

        foreach ($categories as $category) {
            $this->assertInternalType('string', $category->name);
            $this->assertNotEmpty($category->name);
        }
        $this->assertGreaterThanOrEqual(5, count($categories));
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testCategoriesHaveNonEmptyStringSlug(Project $project, string $language): void
    {
        $categories = $this->fetchCategories($project, $language);

        foreach ($categories as $category) {
            $this->assertInternalType('string', $category->slug);
            $this->assertNotEmpty($category->slug);
        }
        $this->assertGreaterThanOrEqual(5, count($categories));
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testCategoriesDontHaveDangerousInnerCategory(Project $project, string $language): void
    {
        $categories = $this->fetchCategories($project, $language);

        foreach ($categories as $category) {
            $this->assertNull($category->dangerousInnerCategory);
        }
        $this->assertGreaterThanOrEqual(5, count($categories));
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
        $limit = 50;
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
        $categories = [];

        $limit = 50;
        $offset = 0;
        do {
            $categoriesFromCurrentStep = $this->fetchCategories($project, $language, $limit, $offset);
            $categories = array_merge($categories, $categoriesFromCurrentStep);

            $offset += $limit;
        } while (count($categoriesFromCurrentStep) === $limit);

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
        $productApi = $this->productApiForProject($project);

        $query = new CategoryQuery(['locale' => $language]);

        if ($limit !== null) {
            $query->limit = $limit;
        }
        if ($offset !== null) {
            $query->offset = $offset;
        }

        return $productApi->getCategories($query);
    }

    /**
     * @return Category[]
     */
    private function fetchCategoriesBySlug(Project $project, string $language, string $slug): array
    {
        $productApi = $this->productApiForProject($project);

        $query = new CategoryQuery([
            'locale' => $language,
            'slug' => $slug,
        ]);

        return $productApi->getCategories($query);
    }

    private function productApiForProject(Project $project): ProductApi
    {
        $key = sprintf('%s_%s', $project->customer, $project->projectId);
        if (!array_key_exists($key, $this->productApis)) {
            $this->productApis[$key] = $this->productApiFactory->factor($project);
        }

        return $this->productApis[$key];
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
