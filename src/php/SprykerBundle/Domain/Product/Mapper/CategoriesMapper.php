<?php

namespace Frontastic\Common\SprykerBundle\Domain\Product\Mapper;

use Frontastic\Common\ProductApiBundle\Domain\Category;
use Frontastic\Common\SprykerBundle\Domain\MapperInterface;
use Frontastic\Common\SprykerBundle\Domain\Product\SprykerSlugger;
use WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject;

class CategoriesMapper implements MapperInterface
{
    public const MAPPER_NAME = 'categories';

    /**
     * @var Category[]
     */
    private $categories = [];

    /**
     * @param ResourceObject $resource
     * @return Category[]
     */
    public function mapResource(ResourceObject $resource): array
    {
        $this->categories = [];

        foreach ($resource->attribute('categoryNodesStorage') as $categoryStorage) {
            $this->addCategory($categoryStorage);
        }

        $result = $this->categories;
        $this->categories = [];
        usort($result, [$this, 'compareDepthAndName']);

        return $result;
    }

    /**
     * @param array $categoryStorage
     * @param int $depth
     * @param string $path
     *
     * @return void
     */
    private function addCategory(array $categoryStorage, int $depth = 0, string $path = ''): void
    {
        $this->categories[] = $this->createCategory($categoryStorage, $depth, $path);
    }

    /**
     * @param array $categoryStorage
     * @param int $depth
     * @param string $path
     *
     * @return \Frontastic\Common\ProductApiBundle\Domain\Category
     */
    private function createCategory(array $categoryStorage, int $depth, string $path): Category
    {
        $category = new Category();
        $category->name = $categoryStorage['name'];
        $category->categoryId = $categoryStorage['nodeId'];
        $category->depth = $depth;
        $category->path = $this->buildPath($category->name, $path);
        $children = $categoryStorage['children'] ?? [];

        foreach ($children as $child) {
            $this->addCategory($child, $depth + 1, $category->path);
        }

        return $category;
    }

    /**
     * @param string $name
     * @param string $parentPath
     *
     * @return string
     */
    private function buildPath(string $name, string $parentPath): string
    {
        $slug = SprykerSlugger::slugify($name);

        return "{$parentPath}/{$slug}";
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::MAPPER_NAME;
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\Category $a
     * @param \Frontastic\Common\ProductApiBundle\Domain\Category $b
     *
     * @return int
     */
    private function compareDepthAndName(Category $a, Category $b): int
    {
        if ($a->depth > $b->depth) {
            return 1;
        }

        if ($a->depth < $b->depth) {
            return -1;
        }

        return $a->name <=> $b->name;
    }
}
