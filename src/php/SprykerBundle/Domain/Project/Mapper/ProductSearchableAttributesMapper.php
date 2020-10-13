<?php

namespace Frontastic\Common\SprykerBundle\Domain\Project\Mapper;

use Frontastic\Common\ProjectApiBundle\Domain\Attribute;
use Frontastic\Common\SprykerBundle\Domain\Exception\SprykerClientException;
use Frontastic\Common\SprykerBundle\Domain\ExtendedMapperInterface;
use WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject;

class ProductSearchableAttributesMapper implements ExtendedMapperInterface
{
    public const MAPPER_NAME = 'product-searchable-attributes';

    private const SPRYKER_TO_FRONTASTIC_TYPE_MAPPING = [
        'text' => Attribute::TYPE_TEXT,
        'textarea' => Attribute::TYPE_TEXT,
        'number' => Attribute::TYPE_NUMBER,
        'float' => Attribute::TYPE_NUMBER,
        'date' => Attribute::TYPE_TEXT,
        'time' => Attribute::TYPE_TEXT,
        'datetime' => Attribute::TYPE_TEXT,
        'select' => Attribute::TYPE_ENUM,
    ];

    /**
     * @param \WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject[] $resources
     *
     * @return \Frontastic\Common\ProjectApiBundle\Domain\Attribute[]
     */
    public function mapResourceArray(array $resources): array
    {
        $list = [];

        foreach ($resources as $primaryResource) {
            $attribute = $this->mapResource($primaryResource);
            $list[$attribute->attributeId] = $attribute;
        }

        return $list;
    }

    /**
     * @param \WoohooLabs\Yang\JsonApi\Schema\Resource\ResourceObject $resource
     *
     * @return \Frontastic\Common\ProjectApiBundle\Domain\Attribute
     */
    public function mapResource(ResourceObject $resource): Attribute
    {
        $attributeName = $resource->id();
        $attributeType = $this->mapType($resource->attribute('inputType'));
        if ($attributeName === null) {
            throw new SprykerClientException('Attribute is missing a name for type: ' . $attributeType);
        }

        $attribute = new Attribute();
        $attribute->attributeId = $attributeName;
        $attribute->type = $attributeType;
        $attribute->label = array_combine(
            array_map(
                function (array $localizedKey): string {
                    return $localizedKey['localeName'];
                },
                $resource->attribute('localizedKeys')
            ),
            array_map(
                function (array $localizedKey): string {
                    return $localizedKey['translation'];
                },
                $resource->attribute('localizedKeys')
            )
        );

        foreach ($resource->attribute('values') as $attributeValue) {
            $value = array_combine(
                array_map(
                    function (array $localizedValue): string {
                        return $localizedValue['localeName'];
                    },
                    $attributeValue['localizedValues']
                ),
                array_map(
                    function (array $localizedValue): string {
                        return $localizedValue['translation'];
                    },
                    $attributeValue['localizedValues']
                )
            );

            $attribute->values[] = !empty($value) ? $value : $attributeValue['value'];
        }

        return $attribute;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return self::MAPPER_NAME;
    }

    /**
     * @param string $type
     *
     * @return string
     */
    private function mapType(string $type): string
    {
        return self::SPRYKER_TO_FRONTASTIC_TYPE_MAPPING[$type] ?? $type;
    }
}
