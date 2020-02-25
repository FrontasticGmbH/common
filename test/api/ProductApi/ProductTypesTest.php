<?php

namespace Frontastic\Common\ApiTests\ProductApi;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductTypeQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductType;
use Frontastic\Common\ReplicatorBundle\Domain\Project;

class ProductTypesTest extends ProductApiTestCase
{
    /**
     * @dataProvider projectAndLanguage
     */
    public function testProductTypesAreNotEmpty(Project $project, string $language): void
    {
        $productTypes = $this->fetchProductTypes($project, $language);
        $this->assertNotEmpty($productTypes);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testGetProductTypesIgnoresLimitAndOffset(Project $project, string $language): void
    {
        $manyProductTypes = $this->fetchProductTypes($project, $language, 100, 0);
        $fewProductTypesWithOffset = $this->fetchProductTypes($project, $language, 5, 20);
        $this->assertEquals($manyProductTypes, $fewProductTypesWithOffset);
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testProductTypesAreWellFormed(Project $project, string $language): void
    {
        $productTypes = $this->fetchProductTypes($project, $language);

        $this->assertContainsOnlyInstancesOf(ProductType::class, $productTypes);

        foreach ($productTypes as $productType) {
            $this->assertInternalType('string', $productType->productTypeId);
            $this->assertNotEmpty($productType->productTypeId);

            $this->assertInternalType('string', $productType->name);
            $this->assertNotEmpty($productType->name);

            $this->assertNull($productType->dangerousInnerProductType);
        }
    }

    /**
     * @dataProvider projectAndLanguage
     */
    public function testProductTypesHaveDistinctIds(Project $project, string $language): void
    {
        $productTypes = $this->fetchProductTypes($project, $language);
        $productTypeIds = array_map(
            function (ProductType $productType): string {
                return $productType->productTypeId;
            },
            $productTypes
        );
        $this->assertArrayHasDistinctValues($productTypeIds);
    }

    /**
     * @return ProductType[]
     */
    private function fetchProductTypes(
        Project $project,
        string $language,
        ?int $limit = null,
        ?int $offset = null
    ): array {
        return $this
            ->productApiForProject($project)
            ->getProductTypes(new ProductTypeQuery($this->buildQueryParameters($language, $limit, $offset)));
    }
}
