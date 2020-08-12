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
        'enumeration' => Attribute::TYPE_LOCALIZED_ENUM,
        'range' => Attribute::TYPE_TEXT,
        'price-range' => Attribute::TYPE_MONEY,
        'category' => Attribute::TYPE_CATEGORY_ID,
    ];

    /**
     * @var LocalizedEnumAttributesMapper
     */
    private $localizedEnumAttributesMapper;

    /**
     * @param LocalizedEnumAttributesMapper $localizedEnumAttributesMapper
     */
    public function __construct(LocalizedEnumAttributesMapper $localizedEnumAttributesMapper)
    {
        $this->localizedEnumAttributesMapper = $localizedEnumAttributesMapper;
    }

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
        $attributeName = $resource->attribute('name');
        $attributeType = $this->mapType($resource->attribute('type'));
        if ($attributeName === null) {
            throw new SprykerClientException('Attribute is missing a name for type: ' . $attributeType);
        }

        $attribute = new Attribute();
        $attribute->attributeId = $attributeName;
        $attribute->type = $attributeType;
        $attribute->label = $resource->attribute('label');
        $attribute->values = $resource->attribute('values');

        $this->localizedEnumAttributesMapper->process($attribute);

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
