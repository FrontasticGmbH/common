<?php

namespace Frontastic\Common\SprykerBundle\Domain\Product\Mapper;

use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\SprykerBundle\Domain\Product\Mapper\VariantMapper;
use WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject;

class VariantMapperExtended extends VariantMapper
{
    private const ATTRIBUTE_DISCOUNT_FOOTNOTE = '_footnoteText';
    private const RESOURCE_KEY_FOOTNOTE = 'footprintText';
    private const FIELD_EAN = 'ean';

    /**
     * @param \WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject $resource
     *
     * @return \Frontastic\Common\ProductApiBundle\Domain\Variant
     */
    public function mapResource(ResourceObject $resource): Variant
    {
        $variant = parent::mapResource($resource);
        $this->mapDiscountFootnote($variant, $resource);
        $this->mapEan($variant, $resource);

        return $variant;
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\Variant $variant
     * @param \WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject $resource
     *
     * @return void
     */
    private function mapDiscountFootnote(Variant $variant, ResourceObject $resource): void
    {
        if (!$resource->hasRelationship(self::RELATION_PRICES)) {
            return;
        }

        $priceResource = $resource->relationship(self::RELATION_PRICES)->resources()[0];
        $variant->attributes[self::ATTRIBUTE_DISCOUNT_FOOTNOTE] = $priceResource->attribute(
            self::RESOURCE_KEY_FOOTNOTE
        );
    }

    /**
     * @param \WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject $concreteProductResource
     *
     * @return array
     */
    protected function mapImages(ResourceObject $concreteProductResource): array
    {
        return [];
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\Variant $variant
     * @param \WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject $resource
     *
     * @return void
     */
    private function mapEan(Variant $variant, ResourceObject $resource): void
    {
        $ean = $resource->attribute(self::FIELD_EAN, null);

        if ($ean) {
            $variant->attributes[self::FIELD_EAN] = [
                'value' => $ean,
                'label' => 'EAN/GTIN'
            ];
        }
    }
}
