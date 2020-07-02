<?php

namespace Frontastic\Common\SprykerBundle\Domain\Product\Mapper;

use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\SprykerBundle\Domain\MapperInterface;
use Frontastic\Common\SprykerBundle\Domain\Product\SprykerProductApiConstants;
use Frontastic\Common\SprykerBundle\Common\VariantImagesHelper;
use WoohooLabs\Yang\JsonApi\Exception\DocumentException;
use WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject;

class VariantMapper implements MapperInterface
{
    public const MAPPER_NAME = 'variant';
    protected const RELATION_PRICES = 'concrete-product-prices';
    protected const RELATION_IMAGE_SETS = 'concrete-product-image-sets';

    /**
     * /**
     * @param ResourceObject $resource
     * @return Variant
     */
    public function mapResource(ResourceObject $resource): Variant
    {
        $variant = new Variant();
        $variant->id = $resource->id();
        $variant->sku = (string)$resource->attribute('sku');
        $variant->groupId = (string)$resource->attribute('idProductAbstract');
        $variant->attributes = $resource->attribute('attributes');
        // @TODO: Use the value of Query.loadDangerousInnerData to asses if dangerousInnerVariant should be setted
        // $variant->dangerousInnerVariant = $resource->attributes();
        $variant->images = $this->mapImages($resource);

        try {
            $this->mapAvailability($resource, $variant);
        } catch (DocumentException $e) {
            $variant->isOnStock = false;
        }

        $this->mapPrice($resource, $variant);
        $this->mapAttributeLabels($resource, $variant);
        $variant->attributes['_super'] = $resource->attribute('superAttributesDefinition', []);

        return $variant;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::MAPPER_NAME;
    }

    /**
     * @param ResourceObject $concreteProductResource
     * @param Variant $variant
     */
    private function mapPrice(ResourceObject $concreteProductResource, Variant $variant): void
    {
        if (!$concreteProductResource->hasRelationship(self::RELATION_PRICES)) {
            return;
        }

        $priceResource = $concreteProductResource->relationship(self::RELATION_PRICES)
            ->resources()[0];
        $map = $this->getPriceMap($priceResource);

        $variant->discountedPrice = $map[SprykerProductApiConstants::PRICE_WITH_DISCOUNT] ?? null;
        $variant->price = $map[SprykerProductApiConstants::PRICE_OLD] ?? $variant->discountedPrice;
        $variant->currency = $priceResource->attribute('prices')[0]['currency']['code'];
    }

    /**
     * @param ResourceObject $priceResource
     *
     * @return int[]
     */
    private function getPriceMap(ResourceObject $priceResource): array
    {
        $map = [];

        foreach ($priceResource->attribute('prices', []) as $item) {
            $key = $item['priceTypeName'];
            $value = $item['grossAmount'];
            $map[$key] = $value;
        }

        return $map;
    }

    /**
     * @param ResourceObject $concreteProductResource
     * @param string[]
     *
     * @return array
     */
    protected function mapImages(ResourceObject $concreteProductResource): array
    {
        $images = [];

        if (!$concreteProductResource->hasRelationship(self::RELATION_IMAGE_SETS)) {
            return $images;
        }

        foreach ($concreteProductResource->relationship(self::RELATION_IMAGE_SETS)->resources() as $imageResource) {
            $images = VariantImagesHelper::mapImageSets($imageResource->attribute('imageSets', []));
        }

        return $images;
    }

    /**
     * @param ResourceObject $concreteProductResource
     * @param Variant $variant
     */
    private function mapAvailability(ResourceObject $concreteProductResource, Variant $variant): void
    {
        /*** @var $resource ResourceObject */
        $resource = $concreteProductResource->relationship('concrete-product-availabilities')->resources()[0];

        $variant->isOnStock = $resource->attribute('availability', false);

        if ($resource->attribute('isNeverOutOfStock')) {
            $variant->isOnStock = true;
        }
    }

    /**
     * @param ResourceObject $resource
     * @param \Frontastic\Common\ProductApiBundle\Domain\Variant $variant
     *
     * @return void
     */
    private function mapAttributeLabels(ResourceObject $resource, Variant $variant): void
    {
        $names = $resource->attribute('attributeNames', []);

        foreach ($names as $id => $label) {
            $key = "_label_{$id}";
            $variant->attributes[$key] = $label;
        }
    }
}
