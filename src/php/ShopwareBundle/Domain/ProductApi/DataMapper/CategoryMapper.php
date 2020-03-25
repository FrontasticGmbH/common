<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\DataMapper;

use Frontastic\Common\ProductApiBundle\Domain\Category;
use Frontastic\Common\ShopwareBundle\Domain\QueryAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\QueryAwareDataMapperTrait;

class CategoryMapper extends AbstractDataMapper implements QueryAwareDataMapperInterface
{
    private const PATH_SEPARATOR_SHOPWARE = '|';
    private const PATH_SEPARATOR_FRONTASTIC = '/';

    use QueryAwareDataMapperTrait;

    public const MAPPER_NAME = 'category';

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map(array $resource)
    {
        $result = [];
//        $sortMapByDepth = [];
        foreach ($this->extractData($resource) as $categoryData) {
            $category = $this->mapDataToCategory($categoryData);

//            if (!isset($sortMapByDepth[$category->depth])) {
//                $sortMapByDepth[$category->depth] = [];
//            }
//
//            $refByDepth =& $sortMapByDepth[$category->depth];
//
//            if (!empty($categoryData['afterCategoryId'])) {
//                $refByDepth[] = $category->categoryId;
//            } else {
//                array_splice(
//                    $refByDepth,
//                    array_search($categoryData['afterCategoryId'], $refByDepth, true),
//                    0,
//                    $category['afterCategoryId']
//                );
//            }

            if ($this->getQuery()->loadDangerousInnerData) {
                $category->dangerousInnerCategory = $categoryData;
            }

            $result[] = $category;
        }

        return $result;
    }

    private function mapDataToCategory(array $categoryData): Category
    {
        return new Category([
            'categoryId' => $categoryData['id'],
            'name' => $categoryData['translated']['name'] ?? $categoryData['name'],

            //@TODO: shopware API appears not to return slugs
            'slug' => '',
            'depth' => $categoryData['level'],
            'path' => $this->resolveCategoryPath($categoryData),
        ]);
    }

    private function resolveCategoryPath(array $categoryData): string
    {
        // Case for root category
        if ($categoryData['path'] === null) {
            return $categoryData['id'];
        }

        // Path just includes parent path
        $path = trim($categoryData['path'], self::PATH_SEPARATOR_SHOPWARE);

        $pathParts = array_merge(explode(self::PATH_SEPARATOR_SHOPWARE, $path), [
            $categoryData['id'],
        ]);

        return implode(self::PATH_SEPARATOR_FRONTASTIC, $pathParts);
    }
}
