<?php

namespace Frontastic\Common\ShopwareBundle\Domain\ProductSearchApi;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\EnabledFacetService;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApiBase;
use Frontastic\Common\ProjectApiBundle\Domain\Attribute;
use Frontastic\Common\ShopwareBundle\Domain\ClientInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\DataMapperResolver;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\LanguageAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\LocaleAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\ProjectConfigApiAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\DataMapper\QueryAwareDataMapperInterface;
use Frontastic\Common\ShopwareBundle\Domain\Exception\MapperNotFoundException;
use Frontastic\Common\ShopwareBundle\Domain\Locale\LocaleCreator;
use Frontastic\Common\ShopwareBundle\Domain\Locale\ShopwareLocale;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\DataMapper\ProductResultMapper;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Query\QueryFacetExpander;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Aggregation;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\SearchAggregationInterface;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\SearchCriteriaBuilder;
use Frontastic\Common\ShopwareBundle\Domain\ProjectApi\DataMapper\GenericGroupAggregationMapper;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareProjectConfigApiFactory;
use Frontastic\Common\ShopwareBundle\Domain\ProjectConfigApi\ShopwareProjectConfigApiInterface;
use GuzzleHttp\Promise\FulfilledPromise;
use GuzzleHttp\Promise\PromiseInterface;
use function GuzzleHttp\Promise\promise_for;

class ShopwareProductSearchApi extends ProductSearchApiBase
{
    /** @var ClientInterface */
    private $client;

    /** @var LocaleCreator */
    private $localeCreator;

    /** @var DataMapperResolver */
    private $mapperResolver;

    /** @var EnabledFacetService */
    private $enabledFacetService;

    /** @var ShopwareProjectConfigApiInterface */
    private $projectConfigApi;

    /** @var string[] */
    private $projectLanguages;

    /** @var string|null */
    private $defaultLanguage;

    public function __construct(
        ClientInterface $client,
        LocaleCreator $localeCreator,
        DataMapperResolver $mapperResolver,
        EnabledFacetService $enabledFacetService,
        ShopwareProjectConfigApiFactory $projectConfigApiFactory,
        array $projectLanguages,
        ?string $defaultLanguage
    ) {
        $this->client = $client;
        $this->localeCreator = $localeCreator;
        $this->mapperResolver = $mapperResolver;
        $this->enabledFacetService = $enabledFacetService;
        $this->projectConfigApi = $projectConfigApiFactory->factor($this->client);
        $this->projectLanguages = $projectLanguages;
        $this->defaultLanguage = $defaultLanguage;
    }

    protected function queryImplementation(ProductQuery $query): PromiseInterface
    {
        $query = QueryFacetExpander::expandQueryEnabledFacets(
            $query,
            $this->enabledFacetService->getEnabledFacetDefinitions()
        );

        $criteria = SearchCriteriaBuilder::buildFromProductQuery($query);
        $locale = $this->parseLocaleString($query->locale);
        $mapper = $this->buildProductResultMapper($locale, $query);

        $promise = $this->client
            ->forCurrency($locale->currencyId)
            ->forLanguage($locale->languageId)
            ->post('/sales-channel-api/v2/product', [], $criteria)
            ->then(function ($response) use ($mapper) {
                return $mapper->map($response);
            });

        return $promise;
    }

    protected function getSearchableAttributesImplementation(): PromiseInterface
    {
        $localizedAttributes = $this->getLocalizedSearchableAttributes();

        $attributes = new \ArrayObject();

        foreach ($localizedAttributes as $localizedAttribute) {
            $attributes[$localizedAttribute->attributeId] = $localizedAttribute;
        }

        // Shophware 6 v2 and v3 are not accepting at the moment the following parameters.
        // $attributeId = 'price';
        // $attributes[$attributeId] = new Attribute([
            // 'attributeId' => $attributeId,
            // 'type' => Attribute::TYPE_MONEY,
        // ]);

        // $attributeId = 'listingPrices';
        // $attributes[$attributeId] = new Attribute([
            // 'attributeId' => $attributeId,
            // 'type' => Attribute::TYPE_MONEY,
        // ]);

         $attributeId = 'categories.id#category';
         $attributes[$attributeId] = new Attribute([
             'attributeId' => $attributeId,
             'type' => Attribute::TYPE_CATEGORY_ID,
         ]);

        return promise_for($attributes->getArrayCopy());
    }

    public function getDangerousInnerClient(): ClientInterface
    {
        return $this->client;
    }

    private function parseLocaleString(string $localeString): ShopwareLocale
    {
        return $this->localeCreator->createLocaleFromString($localeString ?? $this->defaultLanguage);
    }

    private function buildProductResultMapper(ShopwareLocale $locale, ProductQuery $query): DataMapperInterface
    {
        $mapper = $this->mapperResolver->getMapper(ProductResultMapper::MAPPER_NAME);
        if ($mapper instanceof LocaleAwareDataMapperInterface) {
            $mapper->setLocale($locale);
        }
        if ($mapper instanceof ProjectConfigApiAwareDataMapperInterface) {
            $mapper->setProjectConfigApi($this->projectConfigApi);
        }
        if ($mapper instanceof QueryAwareDataMapperInterface) {
            $mapper->setQuery($query);
        }
        return $mapper;
    }

    private function getLocalizedSearchableAttributes(): \ArrayObject
    {
        $localizedAttributes = new \ArrayObject();
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

    private function mapAggregationGroupToAttributes(
        \ArrayObject $attributes,
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
     * @return array<string, string>
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
     * @return SearchAggregationInterface[]
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
