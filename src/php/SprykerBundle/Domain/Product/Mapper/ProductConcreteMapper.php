<?php

namespace Frontastic\Common\SprykerBundle\Domain\Product\Mapper;

use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\SprykerBundle\Domain\MapperInterface;
use Frontastic\Common\SprykerBundle\Domain\Product\Expander\Nested\NestedAttributeValueTransformExpander;
use Frontastic\Common\SprykerBundle\Domain\Product\Expander\Nested\NestedVariantAttributeUrlExpander;
use Frontastic\Common\SprykerBundle\Domain\Product\SprykerSlugger;
use WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject;

class ProductConcreteMapper implements MapperInterface
{
    public const MAPPER_NAME = 'product-concrete';

    /**
     * @var \Frontastic\Common\SprykerBundle\Domain\Product\Mapper\VariantMapperExtended
     */
    private $variantMapper;

    /**
     * @var \Frontastic\Common\SprykerBundle\Domain\Product\Expander\Nested\NestedAttributeValueTransformExpander[]
     */
    private $expanders = [];

    public function __construct(
        VariantMapperExtended $variantMapper,
        NestedAttributeValueTransformExpander $expander1,
        NestedVariantAttributeUrlExpander $expander2
    )    {
        $this->variantMapper = $variantMapper;
        $this->expanders[] = $expander1;
        $this->expanders[] = $expander2;
    }

    /**
     * @inheritDoc
     */
    public function mapResource(ResourceObject $resource): Product
    {
        $product = new Product();
        /** TODO: Workaroud to get productId. Needs to be replaced when Spryker returns
         * the abstractSku as part of concrete-product response.
         */
        // $product->productId = $resource->attribute('abstractSku');
        $product->productId = explode('_', $resource->attribute('sku'))[0];
        $product->name = $resource->attribute('name');
        $product->description = $resource->attribute('description');
        $product->slug = SprykerSlugger::slugify($resource->attribute('name'));
        $variant = $this->variantMapper->mapResource($resource);
        $product->variants[] = $variant;

        return $this->expand($product);
    }

    /**
     * @inheritDoc
     */
    public function getName(): string
    {
        return self::MAPPER_NAME;
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\Product $product
     *
     * @return \Frontastic\Common\ProductApiBundle\Domain\Product
     */
    private function expand(Product $product): Product
    {
        foreach ($this->expanders as $expander) {
            $expander->expand($product, []);
        }

        return $product;
    }
}
