<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\DataMapper;

use Frontastic\Common\ProductApiBundle\Domain\Category;
use Frontastic\Common\ShopwareBundle\Domain\QueryAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\QueryAwareDataMapperTrait;
use Frontastic\Common\ShopwareBundle\Domain\Slugger;

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
        foreach ($this->extractData($resource) as $categoryData) {
            $category = $this->mapDataToCategory($categoryData);

            if ($this->getQuery()->loadDangerousInnerData) {
                $category->dangerousInnerCategory = $categoryData;
            }

            $result[] = $category;
        }

        return $result;
    }

    private function mapDataToCategory(array $categoryData): Category
    {
        $name = $categoryData['translated']['name'] ?? $categoryData['name'];

        return new Category([
            'categoryId' => $categoryData['id'],
            'name' => $name,
            'slug' => Slugger::slugify($name),
            // Subtracting 1 because Shopware starts level with 1, while Frontastic with 0
            'depth' => $categoryData['level'] - 1,
            'path' => $this->resolveCategoryPath($categoryData),
        ]);
    }

    private function resolveCategoryPath(array $categoryData): string
    {
        // Case for root category
        if ($categoryData['path'] === null) {
            return self::PATH_SEPARATOR_FRONTASTIC . $categoryData['id'];
        }

        // Path just includes parent path
        $path = trim($categoryData['path'], self::PATH_SEPARATOR_SHOPWARE);

        $pathParts = array_merge(
            explode(self::PATH_SEPARATOR_SHOPWARE, $path),
            [
                $categoryData['id'],
            ]
        );

        return self::PATH_SEPARATOR_FRONTASTIC . implode(self::PATH_SEPARATOR_FRONTASTIC, $pathParts);
    }
}
