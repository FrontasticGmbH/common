<?php

namespace Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApi;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Locale\CommercetoolsLocaleCreator;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Mapper;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\EnabledFacetService;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApiBase;
use Frontastic\Common\ProjectApiBundle\Domain\Attribute;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Promise\PromiseInterface;

class Commercetools extends ProductSearchApiBase
{
    public const SORT_ORDER_ASCENDING_MIN = 'ascending_min';
    public const SORT_ORDER_ASCENDING_MAX = 'ascending_max';
    public const SORT_ORDER_DESCENDING_MIN = 'descending_min';
    public const SORT_ORDER_DESCENDING_MAX = 'descending_max';

    private const TYPE_MAP = [
        'lenum' => Attribute::TYPE_LOCALIZED_ENUM,
        'ltext' => Attribute::TYPE_LOCALIZED_TEXT,
    ];

    // Commercetools defines a default maximum offset of 10000, https://docs.commercetools.com/api/contract#queries.
    // This can be edit from project.yml using the property maxQueryOffset within the engine configuration.
    private const DEFAULT_MAX_QUERY_OFFSET = 10000;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var Mapper
     */
    private $mapper;

    /**
     * @var CommercetoolsLocaleCreator
     */
    private $localeCreator;

    /**
     * @var EnabledFacetService
     */
    private $enabledFacetService;

    /**
     * @var string[]
     */
    private $languages;

    /**
     * @var string
     */
    private $defaultLocale;

    /**
     * @var string
     */
    private $maxQueryOffset;

    public function __construct(
        Client $client,
        Mapper $mapper,
        CommercetoolsLocaleCreator $localeCreator,
        EnabledFacetService $enabledFacetService,
        array $languages,
        string $defaultLocale,
        ?int $maxQueryOffset = null
    ) {
        $this->client = $client;
        $this->mapper = $mapper;
        $this->localeCreator = $localeCreator;
        $this->defaultLocale = $defaultLocale;
        $this->languages = $languages;
        $this->enabledFacetService = $enabledFacetService;
        $this->maxQueryOffset = $maxQueryOffset ?? self::DEFAULT_MAX_QUERY_OFFSET;
    }

    /**
     * You can send all query fields which are part of the Search Product Projections specification of Commercetools
     * as $query>rawApiInput.
     * @see https://docs.commercetools.com/api/projects/products-search#search-productprojections
     */
    protected function queryImplementation(ProductQuery $query): PromiseInterface
    {
        $locale = $this->localeCreator->createLocaleFromString($query->locale);
        $defaultLocale = $this->localeCreator->createLocaleFromString($this->defaultLocale);

        if ($query->offset > $this->maxQueryOffset) {
            $promise = new Promise();

            $promise->resolve(new Result([
                'query' => clone $query,
            ]));

            return $promise;
        }

        $parameters = [
            'offset' => $query->offset,
            'limit' => $query->limit,
            'filter' => [],
            'filter.query' => [],
            'filter.facets' => [],
            'facet' => $this->mapper->facetsToRequest(
                $this->enabledFacetService->getEnabledFacetDefinitions(),
                $locale
            ),
            'priceCurrency' => $locale->currency,
            'priceCountry' => $locale->country,
            'fuzzy' => $query->fuzzy ? 'true' : 'false',
        ];

        if (count($query->filter) > 0) {
            // The defaultLocale is used to ensure that the configured filter does work in any locale the user chooses
            $parameters['filter.query'] = $this->mapper->prepareQueryFilter($query->filter, $defaultLocale);
        }

        if ($query->productType) {
            $parameters['filter.query'][] = sprintf('productType.id:"%s"', $query->productType);
        }

        $categories = $query->getAllUniqueCategories();
        if (count($categories) > 0) {
            $parameters['filter.query'][] = 'categories.id: ' .
                join(
                    ', ',
                    array_map(
                        function ($category) {
                            return sprintf('subtree("%s")', $category);
                        },
                        $categories
                    )
                );
        }
        if ($query->query) {
            $parameters[sprintf('text.%s', $locale->language)] = $query->query;
        }
        if ($query->productIds) {
            $parameters['filter.query'][] = sprintf('id: "%s"', join('","', $query->productIds));
        }
        if ($query->sku) {
            $parameters['filter.query'][] = sprintf('variants.sku:"%s"', $query->sku);
        }
        if ($query->skus) {
            $parameters['filter.query'][] = sprintf('variants.sku:"%s"', join('","', $query->skus));
        }

        if ($query->sortAttributes) {
            $parameters['sort'] = array_map(
                function (string $direction, string $field): string {
                    switch ($direction) {
                        case ProductQuery::SORT_ORDER_ASCENDING:
                            $direction = 'asc';
                            break;
                        case self::SORT_ORDER_ASCENDING_MIN:
                            $direction = 'asc.min';
                            break;
                        case self::SORT_ORDER_ASCENDING_MAX:
                            $direction = 'asc.max';
                            break;
                        case self::SORT_ORDER_DESCENDING_MIN:
                            $direction = 'desc.min';
                            break;
                        case self::SORT_ORDER_DESCENDING_MAX:
                            $direction = 'desc.max';
                            break;
                        default:
                            $direction = 'desc';
                    }
                    return "$field $direction";
                },
                $query->sortAttributes,
                array_keys($query->sortAttributes)
            );
        }

        $facetsToFilter = $this->mapper->facetsToFilter(
            $query->facets,
            $this->enabledFacetService->getEnabledFacetDefinitions(),
            $locale
        );
        $parameters['filter'] = $facetsToFilter;
        $parameters['filter.facets'] = $facetsToFilter;

        return $this->client
            ->fetchAsync(
                '/product-projections/search',
                array_filter(
                    array_merge($query->rawApiInput, $parameters)
                )
            )
            ->then(function ($result) use ($query, $locale) {
                return new Result([
                    'offset' => $result->offset,
                    'total' => $result->total,
                    'count' => $result->count,
                    'items' => array_map(
                        function (array $productData) use ($query, $locale) {
                            return $this->mapper->dataToProduct($productData, $query, $locale);
                        },
                        $result->results
                    ),
                    'facets' => $this->mapper->dataToFacets($result->facets, $query),
                    'query' => clone $query,
                ]);
            });
    }

    protected function getSearchableAttributesImplementation(): PromiseInterface
    {
        return $this->client->fetchAsync('/product-types')
            ->then(function ($productTypes) {
                $attributes = [];
                foreach ($productTypes->results as $productType) {
                    foreach ($productType['attributes'] as $rawAttribute) {
                        if (!$rawAttribute['isSearchable']) {
                            continue;
                        }

                        $attributeId = 'variants.attributes.' . $rawAttribute['name'];

                        $rawType = $rawAttribute['type']['name'];
                        $rawValues = $rawAttribute['type']['values'] ?? null;
                        if ($rawType === 'set') {
                            $rawType = $rawAttribute['type']['elementType']['name'];
                            $rawValues = $rawAttribute['type']['elementType']['values'] ?? null;
                        }

                        $attributes[$attributeId] = new Attribute([
                            'attributeId' => $attributeId,
                            'type' => $this->mapAttributeType($rawType),
                            'label' => $this->mapLocales($rawAttribute['label']),
                            'values' => $this->mapValueLocales($rawValues),
                        ]);
                    }
                }

                // Not included in attributes in CT
                $attributeId = 'variants.price';
                $attributes[$attributeId] = new Attribute([
                    'attributeId' => $attributeId,
                    'type' => Attribute::TYPE_MONEY,
                    'label' => null, // Can we get the price label somehow?
                ]);

                $attributeId = 'variants.scopedPrice.value';
                $attributes[$attributeId] = new Attribute([
                    'attributeId' => $attributeId,
                    'type' => Attribute::TYPE_MONEY,
                    'label' => null, // Can we get the price label somehow?
                ]);

                $attributeId = 'categories.id';
                $attributes[$attributeId] = new Attribute([
                    'attributeId' => $attributeId,
                    'type' => Attribute::TYPE_CATEGORY_ID,
                    'label' => null, // Can we get the label somehow?
                ]);

                return $attributes;
            });
    }

    public function getDangerousInnerClient(): Client
    {
        return $this->client;
    }

    private function mapLocales(array $localizedStrings): array
    {
        $localizedResult = [];
        foreach ($this->languages as $language) {
            $locale = $this->localeCreator->createLocaleFromString($language);
            $localizedResult[$language] =
                $localizedStrings[$locale->language] ??
                (reset($localizedStrings) ?: '');
        }
        return $localizedResult;
    }

    private function mapValueLocales(array $values = null): ?array
    {
        if ($values === null) {
            return null;
        }

        foreach ($values as $key => $value) {
            if (is_array($value['label'])) {
                $values[$key]['label'] = $this->mapLocales($value['label']);
            }
        }
        return $values;
    }

    private function mapAttributeType(string $commerceToolsType): string
    {
        if (isset(self::TYPE_MAP[$commerceToolsType])) {
            return self::TYPE_MAP[$commerceToolsType];
        }
        return $commerceToolsType;
    }
}
