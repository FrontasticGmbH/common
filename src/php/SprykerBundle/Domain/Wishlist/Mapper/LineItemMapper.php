<?php

namespace Frontastic\Common\SprykerBundle\Domain\Wishlist\Mapper;

use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\WishlistApiBundle\Domain\LineItem;
use Frontastic\Common\SprykerBundle\Domain\MapperInterface;
use Frontastic\Common\SprykerBundle\Domain\Product\Mapper\VariantMapper;
use WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject;

class LineItemMapper implements MapperInterface
{
    public const MAPPER_NAME = 'wishlist-line-item';

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
     * @return LineItem\Variant
     */
    public function mapResource(ResourceObject $resource): LineItem\Variant
    {
        $lineItem = new LineItem\Variant();
        $lineItem->lineItemId = $resource->id();

        try {
            $concreteProductResource = $this->getConcreteProductResource($resource);
            $lineItem->name = $concreteProductResource->attribute('name', $resource->attribute('sku'));
            $lineItem->variant = $this->variantMapper->mapResource($concreteProductResource);
        } catch (\Exception $e) {
            $lineItem->variant = new Variant();
            $lineItem->variant->sku = (string)$resource->attribute('sku');
        }

        $lineItem->dangerousInnerItem = $resource->attributes();

        return $lineItem;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::MAPPER_NAME;
    }

    /**
     * @return string
     */
    public function getRelationship(): string
    {
        return 'concrete-products';
    }

    /**
     * @param ResourceObject $resource
     * @return ResourceObject
     */
    private function getConcreteProductResource(ResourceObject $resource): ResourceObject
    {
        return $resource->relationship($this->getRelationship())->resources()[0];
    }
}
