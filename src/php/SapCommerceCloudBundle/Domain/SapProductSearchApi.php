<?php

namespace Frontastic\Common\SapCommerceCloudBundle\Domain;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApiBase;
use Frontastic\Common\ProjectApiBundle\Domain\Attribute;
use Frontastic\Common\SapCommerceCloudBundle\Domain\Locale\SapLocaleCreator;
use GuzzleHttp\Promise\PromiseInterface;
use function GuzzleHttp\Promise\promise_for;
use function GuzzleHttp\Promise\unwrap;

class SapProductSearchApi extends ProductSearchApiBase
{
    private const ATTRIBUTE_TYPES = [
        'price' => Attribute::TYPE_MONEY,
    ];

    /** @var SapClient */
    private $client;

    /** @var SapLocaleCreator */
    private $localeCreator;

    /** @var SapDataMapper */
    private $dataMapper;

    /** @var string[] */
    private $projectLanguages;

    public function __construct(
        SapClient $client,
        SapLocaleCreator $localeCreator,
        SapDataMapper $dataMapper,
        array $projectLanguages
    ) {
        $this->client = $client;
        $this->localeCreator = $localeCreator;
        $this->dataMapper = $dataMapper;
        $this->projectLanguages = $projectLanguages;
    }

    protected function queryImplementation(ProductQuery $query): PromiseInterface
    {
        $sapLocale = $this->localeCreator->createLocaleFromString($query->locale);

        $queryFilter = [];

        $codes = [];
        if ($query->sku !== null) {
            $codes[] = $query->sku;
        }
        if ($query->skus !== null) {
            $codes = array_merge($codes, $query->skus);
        }
        if ($query->productId !== null) {
            $codes[] = $query->productId;
        }
        if ($query->productIds !== null) {
            $codes = array_merge($codes, $query->productIds);
        }
        $codes = array_unique($codes);
        if (count($codes) === 1) {
            $queryFilter['code'] = reset($codes);
        } elseif (count($codes) > 1) {
            throw new \InvalidArgumentException('Can currently only search for a single code');
        }

        if ($query->category !== null) {
            $queryFilter['allCategories'] = $query->category;
        }

        $parameters = array_merge(
            $query->rawApiInput,
            $sapLocale->toQueryParameters(),
            [
                'currentPage' => $query->offset / $query->limit,
                'pageSize' => $query->limit,
                'fields' => 'FULL',
                'query' => sprintf(
                    '%s:relevance:%s',
                    $query->query,
                    $this->encodeFilterString($queryFilter)
                ),
            ]
        );

        $promise = $this->client
            ->get('/rest/v2/{siteId}/products/search', $parameters)
            ->then(function (array $result) use ($query): ProductApi\Result {
                $products = array_map([$this->dataMapper, 'mapDataToProduct'], $result['products']);

                return new ProductApi\Result([
                    'offset' => $result['pagination']['currentPage'] * $result['pagination']['pageSize'],
                    'total' => $result['pagination']['totalResults'],
                    'count' => count($products),
                    'items' => $products,
                    'query' => clone $query,
                ]);
            });

        return $promise;
    }

    protected function getSearchableAttributesImplementation(): PromiseInterface
    {
        $languagesToFetch = [];
        foreach ($this->projectLanguages as $language) {
            $locale = $this->localeCreator->createLocaleFromString($language);
            if (!array_key_exists($locale->languageCode, $languagesToFetch)) {
                $languagesToFetch[$locale->languageCode] = [];
            }
            $languagesToFetch[$locale->languageCode][] = $language;
        }

        $results = [];
        foreach ($languagesToFetch as $languageCode => $languages) {
            $results[] = $this->client
                ->get(
                    '/rest/v2/{siteId}/products/search',
                    [
                        'fields' => 'facets',
                        'lang' => $languageCode,
                    ]
                )
                ->then(function (array $data) use ($languages): array {
                    $attributes = [];

                    foreach ($data['facets'] as $facet) {
                        $attributeId = explode(':', $facet['values'][0]['query']['query']['value'])[2];

                        $attributeData = [
                            'label' => array_fill_keys($languages, $facet['name']),
                        ];

                        if ($facet['category'] ?? false === true) {
                            $attributeData['type'] = Attribute::TYPE_CATEGORY_ID;
                        }

                        $attributes[$attributeId] = $attributeData;
                    }

                    return $attributes;
                });
        }
        $results = unwrap($results);

        $attributes = [];
        foreach ($results as $result) {
            foreach ($result as $attributeId => $attributeData) {
                if (!array_key_exists($attributeId, $attributes)) {
                    $attributeType =
                        $attributeData['type'] ??
                        static::ATTRIBUTE_TYPES[$attributeId] ??
                        Attribute::TYPE_TEXT;

                    $attributes[$attributeId] = new Attribute([
                        'attributeId' => $attributeId,
                        'type' => $attributeType,
                        'label' => [],
                    ]);
                }

                $attributes[$attributeId]->label = array_merge(
                    $attributes[$attributeId]->label,
                    $attributeData['label']
                );
            }
        }

        return promise_for($attributes);
    }

    public function getDangerousInnerClient()
    {
        return $this->client;
    }

    private function encodeFilterString(array $filter): string
    {
        $elements = [];

        foreach ($filter as $key => $value) {
            foreach ((array)$value as $item) {
                $elements[] = $key;
                $elements[] = $item;
            }
        }

        return implode(':', $elements);
    }
}
