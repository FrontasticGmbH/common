<?php

namespace Frontastic\Common\SprykerBundle\Domain\Cart\Mapper;

use Frontastic\Common\CartApiBundle\Domain\LineItem;
use Frontastic\Common\SprykerBundle\Domain\MapperInterface;
use Frontastic\Common\SprykerBundle\Domain\Product\Mapper\VariantMapper;
use WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject;

class LineItemMapper implements MapperInterface
{
    public const MAPPER_NAME = 'line-item';

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
        $concreteProductResource = $this->getConcreteProductResource($resource);

        $lineItem = new LineItem\Variant();
        $lineItem->lineItemId = $resource->id();
        $lineItem->name = $concreteProductResource->attribute('name', $resource->attribute('sku'));
        $lineItem->count = $resource->attribute('quantity');

        $lineItem->dangerousInnerItem = $resource->attributes();

        $calculations = $resource->attribute('calculations');

        $lineItem->price = $calculations['unitPrice'];
        $lineItem->discountedPrice = $calculations['unitPrice'];
        $lineItem->totalPrice = $calculations['sumPrice'];

        $lineItem->variant = $this->variantMapper->mapResource($concreteProductResource);

        return $lineItem;
    }

    /**
     * @param ResourceObject[] $resources
     * @return mixed
     */
    public function mapResourceArray(array $resources)
    {
        throw new \RuntimeException('Not implemented for this mapper');
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
     * @return ResourceObject
     */
    private function getConcreteProductResource(ResourceObject $resource): ResourceObject
    {
        return $resource->relationship('concrete-products')->resources()[0];
    }
}
