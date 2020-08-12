<?php

namespace Frontastic\Common\SprykerBundle\Domain\Product\Mapper;

use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\SprykerBundle\Domain\MapperInterface;
use Frontastic\Common\SprykerBundle\Domain\Product\SprykerSlugger;
use WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject;

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
     * @param ResourceObject $resource
     * @return Product
     */
    public function mapResource(ResourceObject $resource): Product
    {
        $product = new Product();
        $product->name = $resource->attribute('name');
        $product->productId = (string)$resource->id();
        $product->description = $resource->attribute('description');
        $product->slug = SprykerSlugger::slugify($resource->attribute('name'));
        $product->version = $resource->attribute('productType');
        // @TODO: Use the value of Query.loadDangerousInnerData to asses if dangerousInnerVariant should be setted
        // $product->dangerousInnerProduct = $resource->attributes();

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
     * @param ResourceObject $resource
     * @return Variant[]
     */
    private function mapConcreteProducts(ResourceObject $resource): array
    {
        $variants = [];

        foreach ($resource->relationship('concrete-products')->resources() as $concreteProductResource) {
            $variants[] = $this->variantMapper->mapResource($concreteProductResource);
        }

        return $variants;
    }

    /**
     * @param ResourceObject $resource
     * @return string[]
     */
    private function mapCategories(ResourceObject $resource): array
    {
        $categories = [];

        if ($resource->hasRelationship('category-nodes')) {
            foreach ($resource->relationship('category-nodes')->resources() as $categoryResource) {
                $categories[] = $categoryResource->attribute('name');
            }
        }

        return $categories;
    }
}
