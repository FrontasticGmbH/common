<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectApi\DataMapper;

use Frontastic\Common\ProjectApiBundle\Domain\Attribute;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\LanguageAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\LanguageAwareDataMapperTrait;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Aggregation;

class PropertiesGroupAggregationMapper implements LanguageAwareDataMapperInterface
{
    use LanguageAwareDataMapperTrait;

    public const MAPPER_NAME = 'properties_group_aggregation';
    private const AGGREGATION_NAME_SEPARATOR = '#';

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map(array $aggregationData)
    {
        [$groupAggregation, $propertiesAggregation] = $aggregationData;

        $attributes = $this->mapGroupAggregationToAttributes($groupAggregation);

        return $this->mapPropertiesAggregationToAttributes($attributes, $propertiesAggregation);
    }

    /**
     * @param \Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Aggregation\Entity $aggregation
     *
     * @return \Frontastic\Common\ProjectApiBundle\Domain\Attribute[]
     */
    private function mapGroupAggregationToAttributes(Aggregation\Entity $aggregation): array
    {
        $result = [];
        foreach ($aggregation->getResultData() as $group) {
            $name = $group['translated']['name'] ?? $group['name'];

            $attribute = new Attribute();
            $attribute->type = Attribute::TYPE_LOCALIZED_ENUM;
            $attribute->attributeId = sprintf('%s#%s', $name, $group['id']);
            $attribute->label = [
                $this->getLanguage() => $name,
            ];

            $result[$group['id']] = $attribute;
        }

        return $result;
    }

    /**
     * @param \Frontastic\Common\ProjectApiBundle\Domain\Attribute[] $attributes
     * @param \Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Aggregation\Entity $aggregation
     *
     * @return \Frontastic\Common\ProjectApiBundle\Domain\Attribute[]
     */
    private function mapPropertiesAggregationToAttributes(array $attributes, Aggregation\Entity $aggregation): array
    {
        foreach ($aggregation->getResultData() as $property) {
            $attribute = $attributes[$property['groupId']];

            $attribute->values[$property['id']] = [
                'key' => $property['id'],
                'label' => [
                    $this->getLanguage() => $property['translated']['name'] ?? $property['name'],
                ]
            ];
        }

        return $attributes;
    }
}
