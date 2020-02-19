<?php

namespace Frontastic\Common\ApiTests\ProductApi;

use Frontastic\Common\ApiTests\FrontasticApiTestCase;
use Frontastic\Common\ProductApiBundle\Domain\Category;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApiFactory;
use Frontastic\Common\ReplicatorBundle\Domain\Project;

class CategoriesTest extends FrontasticApiTestCase
{
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
    public function testAllCategoriesAreNotEmpty(Project $project, ?string $language): void
    {
        $productApi = $this->productApiFactory->factor($project);

        $categories = $productApi->getCategories(new CategoryQuery([
            'locale' => $language,
        ]));

        $this->assertNotEmpty($categories);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testQueryCategoriesTwiceProducesSameResult(Project $project, ?string $language): void
    {
        $productApi = $this->productApiFactory->factor($project);

        $firstCategories = $productApi->getCategories(new CategoryQuery([
            'locale' => $language,
        ]));
        $secondCategories = $productApi->getCategories(new CategoryQuery([
            'locale' => $language,
        ]));

        $this->assertEquals($firstCategories, $secondCategories);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testCategoriesWithHighOffsetAreEmpty(Project $project, ?string $language): void
    {
        $productApi = $this->productApiFactory->factor($project);

        $categories = $productApi->getCategories(new CategoryQuery([
            'locale' => $language,
            'offset' => 5000,
        ]));

        $this->assertEmpty($categories);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testFetchCategoriesWithLowLimitReturnsSameAsWithHighLimit(Project $project, ?string $language): void
    {
        $productApi = $this->productApiFactory->factor($project);

        $limit = 24;
        $stepSize = 2;

        $categoriesFetchedInOneRequest = $productApi->getCategories(new CategoryQuery([
            'locale' => $language,
            'limit' => $limit,
        ]));

        $categoriesFetchedInMultipleRequests = [];
        for ($offset = 0; $offset < $limit; $offset += $stepSize) {
            $categoriesFetchedInMultipleRequests = array_merge(
                $categoriesFetchedInMultipleRequests,
                $productApi->getCategories(new CategoryQuery([
                    'locale' => $language,
                    'limit' => $stepSize,
                    'offset' => $offset,
                ]))
            );
        }

        usort(
            $categoriesFetchedInOneRequest,
            function (Category $lhs, Category $rhs) {
                return strcmp($lhs->categoryId, $rhs->categoryId);
            }
        );
        usort(
            $categoriesFetchedInMultipleRequests,
            function (Category $lhs, Category $rhs) {
                return strcmp($lhs->categoryId, $rhs->categoryId);
            }
        );

        $this->assertGreaterThan($stepSize, count($categoriesFetchedInMultipleRequests));
        $this->assertEquals($categoriesFetchedInOneRequest, $categoriesFetchedInMultipleRequests);
    }
}
