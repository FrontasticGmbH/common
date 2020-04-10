<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectApi\DataMapper;

use Frontastic\Common\ProjectApiBundle\Domain\Attribute;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\LanguageAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\LanguageAwareDataMapperTrait;

class GenericGroupAggregationMapper implements LanguageAwareDataMapperInterface
{
    use LanguageAwareDataMapperTrait;

    public const MAPPER_NAME = 'generic_group_aggregation';
    private const AGGREGATION_NAME_SEPARATOR = '#';

    public function getName(): string
    {
        return static::MAPPER_NAME;
    }

    public function map(array $aggregationData)
    {
        /** @var \Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Aggregation\AbstractAggregation $aggregation */
        $aggregation = $aggregationData[0];

        $result = new Attribute();
        $result->attributeId = $aggregation->field;
        $result->type = Attribute::TYPE_LOCALIZED_ENUM;
        $result->label = [
            $this->getLanguage() => $aggregation->field,
        ];

        foreach ($aggregation->getResultData() as $value) {
            $result->values[$value['id']] = [
                'key' => $value['id'],
                'label' => [
                    $this->getLanguage() => $value['translated']['name'] ?? $value['name'],
                ]
            ];
        }

        return [$aggregation->field => $result];
    }
}
