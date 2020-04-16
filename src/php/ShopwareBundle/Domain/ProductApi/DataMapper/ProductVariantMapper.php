<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\DataMapper;

use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\QueryAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\QueryAwareDataMapperTrait;

class ProductVariantMapper extends AbstractDataMapper implements QueryAwareDataMapperInterface
{
    use QueryAwareDataMapperTrait;

    public const MAPPER_NAME = 'product-variant';

    /**
     * Contains a collection of root attributes in variant data that should be mapped to attributes
     * in a similar way as properties are mapped
     *
     * @const string[]
     */
    private const ROOT_ATTRIBUTES_AS_PROPERTIES = [
        'ean',
    ];

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map($resource)
    {
        $variantData = $this->extractData($resource, $resource);

        return new Variant([
            'id' => (string)$variantData['id'],
            'sku' => $variantData['productNumber'],
            'groupId' => $variantData['parentId'],
            'price' => $this->extractPriceData($variantData),
            'attributes' => $this->mapDataToAttributes($variantData),
            'images' => $this->mapDataToImages($variantData),
            'isOnStock' => $variantData['available'] && $variantData['availableStock'] > 0,
            'dangerousInnerVariant' => $this->mapDangerousInnerData($variantData),
        ]);
    }

    private function extractPriceData(array $variantData): int
    {
        return (int)bcmul((string)$variantData['price'][0]['gross'], '100');
    }

    private function mapDataToAttributes(array $variantData): array
    {
        return array_merge(
            $this->mapRootAttributesToAttributes($variantData),
            $this->mapPropertiesToAttributes($variantData['properties'] ?? [])
        );
    }

    private function mapDataToImages(array $variantData): array
    {
        $coverId = $variantData['coverId'];
        $coverImage = $variantData['cover'] ?? null;
        $allImages = $variantData['media'] ?? [];
        $imagesWithoutCover = array_filter(
            $allImages,
            static function (array $imageData) use ($coverId) {
                return $coverId !== null && $imageData['id'] !== $coverId;
            }
        );

        return array_map(
            static function ($image) {
                return $image['media']['url'];
            },
            array_filter(array_merge([$coverImage], $imagesWithoutCover))
        );
    }

    private function mapPropertiesToAttributes(array $properties): array
    {
        $propertiesByGroups = $this->groupProperties($properties);

        $result = [];
        foreach ($propertiesByGroups as $groupId => $group) {
            $attribute = [
                'label' => $group['name'],
            ];
            if (count($group['properties']) > 1) {
                $attribute['value'] = [
                    'key' => implode('|', array_keys($group['properties'])),
                    'label' => implode('; ', $group['properties']),
                ];
            } else {
                $attribute['value'] = [
                    'key' => key($group['properties']),
                    'label' => current($group['properties']),
                ];
            }

            $result[$group['name']] = $attribute;
        }
        return $result;
    }

    private function mapRootAttributesToAttributes(array $variantData): array
    {
        return array_filter(array_intersect_key($variantData, array_flip(self::ROOT_ATTRIBUTES_AS_PROPERTIES)));
    }

    private function groupProperties(array $properties): array
    {
        $result = [];
        foreach ($properties as $property) {
            $group =& $property['group'];
            $groupId = $group['id'];
            if (!isset($result[$groupId])) {
                $result[$groupId] = [
                    'name' => $this->resolveTranslatedValue($group, 'name'),
                    'properties' => [],
                ];
            }

            $result[$groupId]['properties'][$property['id']] = $this->resolveTranslatedValue($property, 'name');
        }
        return $result;
    }
}
