<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectApi\DataMapper;

use Frontastic\Common\ProjectApiBundle\Domain\Attribute;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\AbstractDataMapper;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\LanguageAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\LanguageAwareDataMapperTrait;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Aggregation;

class PropertiesGroupAggregationMapper extends AbstractDataMapper implements LanguageAwareDataMapperInterface
{
    use LanguageAwareDataMapperTrait;

    public const MAPPER_NAME = 'properties_group_aggregation';
    private const AGGREGATION_NAME_SEPARATOR = '#';
    private const AGGREGATION_FIELD = 'properties.id';
    private const AGGREGATION_DEFINITION = 'property_group_option';

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map($aggregationData)
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
            $name = $this->resolveTranslatedValue($group, 'name');

            $attribute = new Attribute();
            $attribute->type = Attribute::TYPE_LOCALIZED_ENUM;
            /**
             * Even though we are mapping group aggregation, we have to set the field to properties.id rather
             * than original properties.groupId. This field will be used in the fronted for aggregation building.
             * In the frontend, the properties must be aggregated by their ID (instead of groupId).
             *
             * Same for definition, the value defined in the constant is needed for the frontend to work
             */
            $attribute->attributeId = sprintf(
                '%s#%s#%s#%s',
                $group['id'],
                $name,
                self::AGGREGATION_FIELD,
                self::AGGREGATION_DEFINITION
            );
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
                    $this->getLanguage() => $this->resolveTranslatedValue($property, 'name'),
                ]
            ];
        }

        return $attributes;
    }
}
