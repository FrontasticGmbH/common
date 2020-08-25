<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectApi;

use ArrayObject;
use Frontastic\Common\ProjectApiBundle\Domain\Attribute;
use Frontastic\Common\ProjectApiBundle\Domain\ProjectApi;
use Frontastic\Common\ShopwareBundle\Domain\AbstractShopwareApi;
use Frontastic\Common\ShopwareBundle\Domain\ClientInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperResolver;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\LanguageAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\Exception\MapperNotFoundException;
use Frontastic\Common\ShopwareBundle\Domain\Locale\LocaleCreator;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Aggregation;
use Frontastic\Common\ShopwareBundle\Domain\ProjectApi\DataMapper\GenericGroupAggregationMapper;

class ShopwareProjectApi extends AbstractShopwareApi implements ProjectApi
{
    /**
     * @var string[]
     */
    private $projectLanguages;

    public function __construct(
        ClientInterface $client,
        DataMapperResolver $mapperResolver,
        LocaleCreator $localeCreator,
        array $projectLanguages
    ) {
        parent::__construct($client, $mapperResolver, $localeCreator);

        $this->projectLanguages = $projectLanguages;
    }

    /**
     * @return \Frontastic\Common\ProjectApiBundle\Domain\Attribute[] Attributes mapped by ID
     */
    public function getSearchableAttributes(): array
    {
        $localizedAttributes = $this->getLocalizedSearchableAttributes();

        $attributes = new ArrayObject();

        foreach ($localizedAttributes as $localizedAttribute) {
            $attributes[$localizedAttribute->attributeId] = $localizedAttribute;
        }

        $attributeId = 'price';
        $attributes[$attributeId] = new Attribute([
            'attributeId' => $attributeId,
            'type' => Attribute::TYPE_MONEY,
        ]);

        $attributeId = 'listingPrices';
        $attributes[$attributeId] = new Attribute([
            'attributeId' => $attributeId,
            'type' => Attribute::TYPE_MONEY,
        ]);

        $attributeId = 'categories.id';
        $attributes[$attributeId] = new Attribute([
            'attributeId' => $attributeId,
            'type' => Attribute::TYPE_CATEGORY_ID,
        ]);

        return $attributes->getArrayCopy();
    }

    /**
     * @param string $languageId
     *
     * @return \Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Aggregation\AbstractAggregation[][]
     */
    private function fetchProductAggregations(string $languageId): array
    {
        $criteriaAggregations = $this->getDefaultCriteriaAggregations();

        $criteria = [
            'page' => 1,
            'limit' => 1,
            'source' => [
                'id',
            ],
            'aggregations' => $criteriaAggregations,
        ];

        return $this->client
            ->forLanguage($languageId)
            ->post('/sales-channel-api/v2/product', [], $criteria)
            ->then(static function ($response) use ($criteriaAggregations) {
                $groupedAggregations = [];
                foreach ($criteriaAggregations as $criteriaAggregation) {
                    $criteriaAggregation->setResultData($response['aggregations'][$criteriaAggregation->getFullName()]);

                    [$resolvedGroup,] = explode('.', $criteriaAggregation->field, 2);
                    if (!array_key_exists($resolvedGroup, $groupedAggregations)) {
                        $groupedAggregations[$resolvedGroup] = [];
                    }

                    $groupedAggregations[$resolvedGroup][] = $criteriaAggregation;
                }

                return $groupedAggregations;
            })
            ->wait();
    }

    /**
     * @return \ArrayObject|\Frontastic\Common\ProjectApiBundle\Domain\Attribute[]
     */
    private function getLocalizedSearchableAttributes(): \ArrayObject
    {
        $localizedAttributes = new ArrayObject();
        foreach ($this->resolveLanguagesToFetch() as $languageId => $language) {
            $groupedAggregations = $this->fetchProductAggregations($languageId);

            // Aggregations need to be grouped in order to be properly resolved. For example in order to build
            // facet which represents some Shopware product property, we need to combine result from two separate
            // aggregations - one being an aggregation for property groups and other - aggregation for actual
            // properties. Dedicated mapper then will receive that aggregation group and map the result of both
            // aggregations to Frontastic facet data model
            foreach ($groupedAggregations as $aggregationGroup => $groupAggregations) {
                $this->mapAggregationGroupToAttributes(
                    $localizedAttributes,
                    $aggregationGroup,
                    $groupAggregations,
                    $language
                );
            }
        }
        return $localizedAttributes;
    }

    private function mapAggregationGroupToAttributes(
        ArrayObject $attributes,
        string $aggregationGroup,
        array $aggregations,
        string $language
    ): void {
        $mapper = $this->getAggregationGroupMapper($aggregationGroup);

        if ($mapper instanceof LanguageAwareDataMapperInterface) {
            $mapper->setLanguage($language);
        }

        /**
         * @var \Frontastic\Common\ProjectApiBundle\Domain\Attribute $attribute
         */
        foreach ($mapper->map($aggregations) as $attributeId => $attribute) {
            if ($attributes->offsetExists($attributeId)) {
                $existingAttribute = $attributes->offsetGet($attributeId);

                $this->mergeAttributes($existingAttribute, $attribute);
            } else {
                $attributes[$attributeId] = $attribute;
            }
        }
    }

    /**
     * @param string $aggregationGroup
     *
     * @return \Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperInterface|\Frontastic\Common\ShopwareBundle\Domain\DataMapper\LanguageAwareDataMapperInterface
     */
    private function getAggregationGroupMapper(string $aggregationGroup): DataMapperInterface
    {
        try {
            $mapperName = sprintf('%s_group_aggregation', $aggregationGroup);
            $mapper = $this->mapperResolver->getMapper($mapperName);
        } catch (MapperNotFoundException $exception) {
            $mapper = $this->mapperResolver->getMapper(GenericGroupAggregationMapper::MAPPER_NAME);
        }

        return $mapper;
    }

    private function mergeAttributes(Attribute $main, Attribute $merge): void
    {
        $main->label = array_merge($main->label, $merge->label);

        foreach ($main->values as $valueId => $value) {
            $mergeLabel = $merge->values[$valueId]['label'];
            $main->values[$valueId]['label'] = array_merge($main->values[$valueId]['label'], $mergeLabel);
        }

        $main->values = array_values($main->values);
    }

    /**
     * @return string[]
     */
    private function resolveLanguagesToFetch(): array
    {
        $languagesToFetch = [];
        foreach ($this->projectLanguages as $language) {
            $locale = $this->parseLocaleString($language);

            $languagesToFetch[$locale->languageId] = $language;
        }

        return $languagesToFetch;
    }

    /**
     * @return \Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\SearchAggregationInterface[]
     */
    private function getDefaultCriteriaAggregations(): array
    {
        return [
            new Aggregation\Entity([
                'name' => 'property_groups',
                'field' => 'properties.group.id',
                'definition' => 'property_group',
            ]),
            new Aggregation\Entity([
                'name' => 'properties',
                'field' => 'properties.id',
                'definition' => 'property_group_option',
            ]),
            new Aggregation\Entity([
                'name' => 'manufacturers',
                'field' => 'manufacturerId',
                'definition' => 'product_manufacturer',
            ]),
        ];
    }
}
