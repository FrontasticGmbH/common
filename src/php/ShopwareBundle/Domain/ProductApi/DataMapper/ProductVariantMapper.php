<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\DataMapper;

use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\ShopwareBundle\Domain\QueryAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\QueryAwareDataMapperTrait;

class ProductVariantMapper extends AbstractDataMapper implements QueryAwareDataMapperInterface
{
    use QueryAwareDataMapperTrait;

    public const MAPPER_NAME = 'product-variant';

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map(array $resource)
    {
        $variantData = $this->extractData($resource, $resource);

        return new Variant([
            'id' => (string)$variantData['id'],
            'sku' => $variantData['productNumber'],
            'groupId' => $variantData['parentId'],
            'price' => $this->extractPriceData($variantData),
            'attributes' => $this->mapDataToAttributes($variantData),
            'images' => $this->mapDataToImages($variantData),
//            array_merge(
//                array_map(
//                    function (array $asset): string {
//                        return $asset['sources'][0]['uri'];
//                    },
//                    $variantData['assets']
//                ),
//                array_map(
//                    function (array $image): string {
//                        return $image['url'];
//                    },
//                    $variantData['images']
//                )
//            ),
            'isOnStock' => $variantData['available'] && $variantData['availableStock'] > 0,
            'dangerousInnerVariant' => $this->mapDangerousInnerData($variantData),
        ]);
    }

    private function extractPriceData(array $variantData): int
    {
        return $variantData['price'][0]['gross'] * 100;
    }

    private function mapDataToAttributes(array $variantData)
    {
        $this->mapPropertiesToAttributes($variantData['properties'] ?? []);
    }

    private function mapDataToImages(array $variantData): array
    {
        $result = [];

        return $result;
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
                    'value' => current($group['properties']),
                ];
            }

            $result[$group['name']] = $attribute;
        }
        return $result;
    }

    private function groupProperties(array $properties): array
    {
        $result = [];
        foreach ($properties as $property) {
            $group =& $property['group'];
            $groupId = $group['id'];
            if (!isset($result[$groupId])) {
                $result[$groupId] = [
                    'name' => $group['translated']['name'] ?? $group['name'],
                    'properties' => [],
                ];
            }

            $result[$groupId]['properties'][$property['id']] = $property['translated']['name'] ?? $property['name'];
        }
        return $result;
    }
}
