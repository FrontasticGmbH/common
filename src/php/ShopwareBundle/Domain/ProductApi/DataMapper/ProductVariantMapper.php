<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi\DataMapper;

use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\LocaleAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\LocaleAwareDataMapperTrait;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperTrait;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\QueryAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\QueryAwareDataMapperTrait;
use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyPathBuilder;

class ProductVariantMapper extends AbstractDataMapper implements
    LocaleAwareDataMapperInterface,
    ProjectConfigApiAwareDataMapperInterface,
    QueryAwareDataMapperInterface
{
    use LocaleAwareDataMapperTrait,
        ProjectConfigApiAwareDataMapperTrait,
        QueryAwareDataMapperTrait;

    public const MAPPER_NAME = 'product-variant';

    protected const ROOT_ATTRIBUTE_PREFIX = '_';

    /**
     * Contains a collection of root attributes in variant data that should be mapped to attributes
     * in a similar way as properties are mapped
     *
     * @const string[]
     */
    protected const ROOT_ATTRIBUTES_AS_PROPERTIES = [
        'ean',
        'manufacturer.name',
        'manufacturer.link',
    ];

    /**
     * @var \Symfony\Component\PropertyAccess\PropertyAccessor
     */
    private $propAccessor;

    public function __construct()
    {
        $this->propAccessor = PropertyAccess::createPropertyAccessorBuilder()
            ->disableExceptionOnInvalidPropertyPath()
            ->disableExceptionOnInvalidIndex()
            ->getPropertyAccessor();
    }

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map($resource)
    {
        $variantData = $this->extractElements($resource, $resource);

        $variantId = (string)$variantData['id'];
        return new Variant([
            'id' => $variantId,
            'sku' => $variantData['productNumber'],
            'groupId' => $variantData['parentId'] ?? $variantId,
            'price' => $this->convertPriceToCent(
                $variantData['calculatedPrice']['totalPrice'] ?? $variantData['price'][0]['gross']
            ),
            'currency' => $this->resolveCurrencyCodeFromLocale(),
            'attributes' => $this->mapDataToAttributes($variantData),
            'images' => $this->mapDataToImages($variantData),
            'isOnStock' => $variantData['available'] && $variantData['availableStock'] > 0,
            'dangerousInnerVariant' => $this->mapDangerousInnerData($variantData),
        ]);
    }

    private function mapDataToAttributes(array $variantData): array
    {
        $properties = $variantData['properties'] ?? [];
        $options = $variantData['options'] ?? [];

        // Fallback to options if there are no properties
        if (empty($properties)) {
            $properties = $options;
        }

        return array_filter(
            array_merge(
                $this->mapRootAttributesToAttributes($variantData),
                $this->mapPropertiesToAttributes($properties)
            )
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
        $result = [];
        foreach (static::ROOT_ATTRIBUTES_AS_PROPERTIES as $propertyPath) {
            $pathParts = explode('.', $propertyPath);
            $pathBuilder = new PropertyPathBuilder();

            foreach ($pathParts as $pathPart) {
                $pathBuilder->appendIndex($pathPart);
            }

            $attributeKey = sprintf(
                '%s%s',
                static::ROOT_ATTRIBUTE_PREFIX,
                str_replace('.', '_', $propertyPath)
            );

            try {
                $result[$attributeKey] = $this->propAccessor->isReadable($variantData, $pathBuilder->getPropertyPath());
            } catch (UnexpectedTypeException $exception) {
                $result[$attributeKey] = null;
            }
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
                    'name' => $this->resolveTranslatedValue($group, 'name'),
                    'properties' => [],
                ];
            }

            $result[$groupId]['properties'][$property['id']] = $this->resolveTranslatedValue($property, 'name');
        }
        return $result;
    }

    private function resolveCurrencyCodeFromLocale(): ?string
    {
        $shopwareCurrency = $this->projectConfigApi->getCurrency($this->getLocale()->currencyId);

        return $shopwareCurrency ? $shopwareCurrency->isoCode : null;
    }
}
