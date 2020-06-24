<?php

namespace Frontastic\Common\SprykerBundle\Domain\Product\Mapper;

use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\SprykerBundle\Domain\MapperInterface;
use Frontastic\Common\SprykerBundle\Domain\Product\SprykerSlugger;

class ProductMapper implements MapperInterface
{
    public const MAPPER_NAME = 'product';

    /**
     * @var VariantMapper
     */
    private $variantMapper;

    /**
     * @param VariantMapper $variantMapper
     */
    public function __construct(VariantMapper $variantMapper)
    {
        $this->variantMapper = $variantMapper;
    }

    /**
     * @param $resource
     * @return Product
     */
    public function mapResource($resource): Product
    {
        // Support for list with single resources as well as direct single resource
        $productData = $this->extractData($resource, $resource);
        $productData = $productData[0] ?? $productData;

        $product = new Product();
        $product->name = $productData['name'];
        $product->productId = (string)$resource->id();
        $product->description = $productData['description'];
        $product->slug = SprykerSlugger::slugify($productData['name']);
        $product->version = $productData['productType'];
        $product->dangerousInnerProduct = $resource->attributes();

        $product->categories = $this->mapCategories($resource);
        $product->variants = $this->mapConcreteProducts($resource);

        return $product;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::MAPPER_NAME;
    }

    /**
     * @param array $resource
     * @return Variant[]
     */
    private function mapConcreteProducts(array $resource): array
    {
        $variants = [];

        foreach ($resource->relationship('concrete-products')->resources() as $concreteProductResource) {
            $variants[] = $this->variantMapper->mapResource($concreteProductResource);
        }

        return $variants;
    }

    /**
     * @param array $resource
     * @return string[]
     */
    private function mapCategories(array $resource): array
    {
        $categories = [];

        if ($resource->hasRelationship('category-nodes')) {
            foreach ($resource->relationship('category-nodes')->resources() as $categoryResource) {
                $categories[] = $categoryResource->attribute('name');
            }
        }

        return $categories;
    }

    protected function extractData(array $resource, array $fallback = []): array
    {
        return $resource[self::KEY_DATA] ?? $fallback;
    }
}
