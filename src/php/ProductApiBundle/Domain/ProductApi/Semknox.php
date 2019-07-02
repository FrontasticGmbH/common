<?php
/**
 *
 */

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi;

use Frontastic\Common\ProductApiBundle\Domain\Category;
use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductTypeQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result\Facet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result\RangeFacet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result\Term;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result\TermFacet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox\DataStudioClient;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox\Importer;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox\Importer\FullImporter;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox\SearchIndexClient;
use Frontastic\Common\ProductApiBundle\Domain\ProductType;
use Frontastic\Common\ProductApiBundle\Domain\Variant;

/**
 * Class Semknox
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.ExcessiveClassComplexity)
 */
class Semknox implements ProductApi
{
    /**
     * Mapping between internal filter handle and our facet key.
     */
    const HANDLE_KEY_MAPPING = [
        'cost.PRICE' => Facets::KEY_PRICE_RANGE,
        'has.BREADCRUMB_PART' => Facets::KEY_CATEGORY_TREE_TERMS,
        'has.CLOTHES_SIZE' => Facets::KEY_SIZE_RANGE,
        'are.COLOR' => Facets::KEY_COLOR_TERMS,
        'are.FILTER_CATEGORY' => Facets::KEY_CATEGORY_TERMS,
        'areMadeOf.MATERIAL' => Facets::KEY_MATERIAL_TERMS,
        'areSuitable.TARGET_AUDIENCE' => Facets::KEY_TARGET_AUDIENCE_TERMS,
        'isASpecific' => Facets::KEY_CUSTOM_TERMS,
    ];

    /**
     * @var \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox\DataStudioClient[]
     */
    private $searchIndexClients;

    /**
     * @var \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox\DataStudioClient[]
     */
    private $dataStudioClients;

    /**
     * @var array
     */
    private $filters;

    /**
     * Semknox constructor.
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox\DataStudioClient[] $searchIndexClients
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox\DataStudioClient[] $dataStudioClients
     */
    public function __construct(array $searchIndexClients, array $dataStudioClients)
    {
        $this->searchIndexClients = $searchIndexClients;
        $this->dataStudioClients = $dataStudioClients;
    }

    public function getCategories(CategoryQuery $query): array
    {
        $options = null;
        foreach ($this->loadFilters($query) as $filter) {
            if ('has.BREADCRUMB_PART' !== $filter['idName']) {
                continue;
            }
            $options = $filter['options'];
        }

        if (null === $options) {
            return [];
        }

        foreach ($options as $i => $option) {
            if ('_' === $option['valueName']) {
                unset($options[$i]);
                break;
            }
        }

        $categoryMap = [];
        foreach ($options as $option) {
            $categoryMap[$option['valueName']] = new Category([
                'categoryId' => $option['id'],
                'name' => $option['viewName'],
                'depth' => substr_count($option['valueName'], '#'),
                'path' => $option['valueName'],
                'dangerousInnerCategory' => $this->dataToDangerousInnerData($option, $query),
            ]);
        }

        ksort($categoryMap);
        return array_values($categoryMap);
    }

    public function getProductTypes(ProductTypeQuery $query): array
    {
        foreach ($this->loadFilters($query) as $filter) {
            if ('isASpecific' !== $filter['attributeName']) {
                continue;
            }

            return array_map(
                function ($productType) use ($query): ProductType {
                    return new ProductType([
                        'productTypeId' => $productType['id'],
                        'name' => $productType['viewName'],
                        'dangerousInnerProductType' => $this->dataToDangerousInnerData($productType, $query),
                    ]);
                },
                $filter['options']
            );
        }

        return [];
    }

    private function loadFilters(Query $query): array
    {
        if ($this->filters) {
            return $this->filters;
        }

        $parameters = [
            'offset' => $query->offset,
            'limit' => $query->limit,
            'query' => '_#',
        ];

        $result = $this->getSearchIndexClientForLocale($query->locale)
            ->post('/products/search', http_build_query($parameters));

        return ($this->filters = $result['filters']);
    }

    public function getProduct(ProductQuery $query, string $mode = self::QUERY_SYNC): ?object
    {
        if ($mode !== self::QUERY_SYNC) {
            throw new \RuntimeException('not implemented');
        }

        $productId = $query->productId;

        $newQuery = clone $query;
        $newQuery->query = $productId;
        $newQuery->productId = null;
        $newQuery->productIds = [];

        $result = $this->query($newQuery);
        // @TODO: Semknox returns multiple products even in this
        // case, which is why we cannot check for equality with 1
        // here.
        if ($result->count > 0) {
            return $result->items[0];
        }
        return null;
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery $query
     * @return \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result
     */
    private function getProductsById(ProductQuery $query): Result
    {
        $productIds = $query->productIds;

        $newQuery = clone $query;
        $newQuery->productIds = [];

        $products = [];
        /* @todo N+1 problem, Semknox should provide a solution here */
        foreach ($productIds as $productId) {
            $newQuery->query = $productId;
            if ($product = $this->getProduct($newQuery)) {
                $products[] = $product;
            }
        }
        return new Result([
            'offset' => 0,
            'total' => count($products),
            'count' => count($products),
            'items' => $products,
        ]);
    }

    public function query(ProductQuery $query, string $mode = self::QUERY_SYNC): object
    {
        if ($mode !== self::QUERY_SYNC) {
            throw new \RuntimeException('not implemented');
        }

        if ($query->productIds) {
            return $this->getProductsById($query);
        }

        $parameters = [
            'offset' => $query->offset,
            'limit' => $query->limit,
        ];

        $filters = [];

        if ($query->query) {
            $parameters['query'] = $this->encodeQuery($query->query);
        }

        $filters = array_merge($filters, $this->getCategoryFilters($query));
        $filters = array_merge($filters, $this->getProductTypeFilters($query));
        $filters = array_merge($filters, $this->getFacetFilters($query));

        if (count($filters)) {
            $parameters['filters'] = json_encode($filters);
        }
        if (false === isset($parameters['query'])) {
            $parameters['query'] = '_#';
        }

        $result = $this->getSearchIndexClientForLocale($query->locale)
            ->post('/products/search', http_build_query($parameters));

        $this->throwExceptionOnError($result);

        return new Result([
            'offset' => $result['offset'],
            'total' => $result['groupedResultsAvailable'],
            'count' => count($result['searchResults']),
            'items' => array_map(
                function (array $productData) use ($query) {
                    return $this->dataToProduct($productData, $query);
                },
                $result['searchResults']
            ),
            'facets' => array_map(
                function (array $filterData) use ($query) {
                    return $this->dataToFacet($filterData, $query);
                },
                $result['filters']
            ),
            'query' => clone $query,
        ]);
    }

    private function getCategoryFilters(ProductQuery $query): array
    {
        $filters = [];
        if ($query->category) {
            $filters[] = ['id' => $this->getCategoryFilterId($query), 'values' => [$query->category]];
        }
        return $filters;
    }

    private function getProductTypeFilters(ProductQuery $query): array
    {
        $filters = [];
        if ($query->productType) {
            $filters[] = ['id' => $this->getProductTypeFilterId($query), 'values' => [$query->productType]];
        }
        return $filters;
    }

    private function getFacetFilters(ProductQuery $query): array
    {
        $filters = [];
        foreach ($query->facets as $facet) {
            if (Facets::TYPE_RANGE === $facet->type) {
                $filters[] = [
                    'id' => $facet->handle,
                    'minValue' => $facet->min,
                    'maxValue' => $facet->max,
                ];
            } else {
                $filters[] = [
                    'id' => $facet->handle,
                    'values' => $facet->terms,
                ];
            }
        }
        return $filters;
    }

    private function throwExceptionOnError(array $result): void
    {
        if (isset($result['Error'])) {
            throw new ProductApi\Exception\ApiEndpointException(
                $result['Message'] ?? 'Unknown error',
                $result['Error']
            );
        }
    }

    private function getCategoryFilterId(Query $query): int
    {
        foreach ($this->loadFilters($query) as $filter) {
            if ('has.BREADCRUMB_PART' === $filter['idName']) {
                return $filter['id'];
            }
        }
        return 0;
    }

    private function getProductTypeFilterId(Query $query): int
    {
        foreach ($this->loadFilters($query) as $filter) {
            if ('isASpecific' === $filter['idName']) {
                return $filter['id'];
            }
        }
        return 0;
    }

    private function encodeQuery(string $query): string
    {
        if (0 === strpos($query, '_#')) {
            return join(
                '#',
                array_map(
                    'rawurlencode',
                    explode('#', $query)
                )
            );
        }
        return rawurlencode($query);
    }

    /**
     * @return \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox\SearchIndexClient[]
     */
    public function getDangerousInnerClient(): array
    {
        return $this->searchIndexClients;
    }

    /**
     * @param string $locale
     * @return \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox\Importer
     * @internal
     */
    public function getImporter(string $locale): Importer
    {
        return new FullImporter(
            $this->getSearchIndexClientForLocale($locale),
            $this->getDataStudioClientForLocale($locale),
            $locale
        );
    }

    private function dataToProduct(array $productData, ProductQuery $query): Product
    {
        $passOn = $this->preparePassOnData($productData[0]['passOn']);

        return new Product([
            'productId' => $productData[0]['groupId'],
            'name' => $productData[0]['name'],
            /* @todo link -> slug, strip hostname from link */
            'slug' => $productData[0]['link'],
            'description' => $passOn['description'] ?? '',
            'categories' => $passOn['categories'],
            'variants' => $this->dataToVariants($productData, $query),
            'dangerousInnerProduct' => $this->dataToDangerousInnerData($productData, $query),
        ]);
    }

    private function dataToVariants(array $productData, ProductQuery $query): array
    {
        $variants = [];
        foreach ($productData as $variantData) {
            $variants[] = $this->dataToVariant($variantData, $query);
        }
        return $variants;
    }

    private function dataToVariant(array $variantData, ProductQuery $query): Variant
    {
        list($price, $currency) = $this->dataToPriceAndCurrency($variantData['properties']);

        $passOnData = $this->preparePassOnData($variantData['passOn']);

        return new Variant([
            'id' => $variantData['id'],
            'sku' => $variantData['articleNumber'],
            'groupId' => $variantData['groupId'],
            'price' => $price,
            // @TODO: Currency should only be stored in context. Property should be removed.
            'currency' => $currency,
            'images' => [$variantData['image']],
            'attributes' => $passOnData['attributes'],
            'isOnStock' => $passOnData['availability'],
            'dangerousInnerVariant' => $this->dataToDangerousInnerData($variantData, $query),
        ]);
    }

    private function dataToPriceAndCurrency(array $properties): array
    {
        foreach ($properties as $property) {
            if ('cost.PRICE' !== $property['idName']) {
                continue;
            }
            if (0 === preg_match('((\d+)[,\.](\d+)[,\.])', $property['value'], $match)) {
                continue;
            }
            return [intval($match[1] . $match[2]), $property['unit']];
        }
        /* @todo: Fallback if no default price exists? */
        return [-1, 'EUR'];
    }

    private function preparePassOnData(array $passOnData): array
    {
        $prepared = ['categories' => [], 'attributes' => [], 'availability' => false];

        foreach ($passOnData as $data) {
            if ('availability' === $data['key']) {
                $prepared['availability'] = ('true' === $data['value']);
            }
            if (0 === preg_match('(^(category|attribute)\t(.*)$)', $data['key'], $match)) {
                continue;
            }

            switch ($match[1]) {
                case 'category':
                    $prepared['categories'][] = $data['value'];
                    break;
                case 'attribute':
                    $prepared['attributes'][$match[2]] = json_decode($data['value'], true);
                    break;
            }
        }
        return $prepared;
    }

    private function dataToDangerousInnerData(array $rawData, Query $query): ?array
    {
        if ($query->loadDangerousInnerData) {
            return $rawData;
        }
        return null;
    }

    /**
     * @param string $locale
     * @return \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox\SearchIndexClient
     */
    private function getSearchIndexClientForLocale(string $locale): SearchIndexClient
    {
        $locale = Locale::createFromPosix($locale);

        if (isset($this->searchIndexClients[$locale->language])) {
            return $this->searchIndexClients[$locale->language];
        }
        throw new \RuntimeException(sprintf(
            "No Semknox configuration found for locale '%s', available configurations are '%s'.",
            $locale,
            join("', '", array_keys($this->searchIndexClients))
        ));
    }

    /**
     * @param string $locale
     * @return \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox\DataStudioClient
     */
    private function getDataStudioClientForLocale(string $locale): DataStudioClient
    {
        $locale = Locale::createFromPosix($locale);

        if (isset($this->dataStudioClients[$locale->language])) {
            return $this->dataStudioClients[$locale->language];
        }
        throw new \RuntimeException(sprintf(
            "No Semknox configuration found for locale '%s', available configurations are '%s'.",
            $locale,
            join("', '", array_keys($this->dataStudioClients))
        ));
    }

    private function dataToFacet(array $facetData, ProductQuery $query): Facet
    {
        switch ($facetData['type']) {
            case 'CATEGORY_TREE':
            case 'MULTI_SELECT':
                return $this->dataToTermFacet($facetData, $query);
            case 'RANGE':
                return $this->dataToRangeFacet($facetData, $query);
        }
    }

    private function dataToRangeFacet(array $facetData, ProductQuery $query): RangeFacet
    {
        $selectedFacet = $this->getSelectedFacet($facetData['id'], $query->facets);

        $value = ['min' => $facetData['min'], 'max' => $facetData['max']];
        if (is_object($selectedFacet)) {
            $value = ['min' => $selectedFacet->min, 'max' => $selectedFacet->max];
        }

        $key = $this->filterToFacetKey($facetData['idName']);

        return new RangeFacet([
            'handle' => $facetData['id'],
            'key' => $key,
            'selected' => is_object($selectedFacet),
            'min' => $facetData['min'],
            'max' => $facetData['max'],
            'value' => $value,
            'step' => ($key === Facets::KEY_PRICE_RANGE ? 100 : $facetData['step']),
        ]);
    }

    private function dataToTermFacet(array $facetData, ProductQuery $query): TermFacet
    {
        $selected = $this->getSelectedFacet($facetData['id'], $query->facets);

        return new TermFacet([
            'handle' => $facetData['id'],
            'key' => $this->filterToFacetKey($facetData['idName']),
            'selected' => is_object($selected),
            'terms' => array_map(
                function (array $option) use ($selected) {
                    return new Term([
                        'handle' => $option['id'],
                        'name' => $option['viewName'],
                        'value' => $option['valueName'],
                        'count' => $option['count'],
                        'selected' => (is_object($selected) && in_array($option['id'], $selected->terms)),
                    ]);
                },
                $facetData['options']
            ),
        ]);
    }

    private function filterToFacetKey(string $filterName): string
    {
        if (isset(self::HANDLE_KEY_MAPPING[$filterName])) {
            return self::HANDLE_KEY_MAPPING[$filterName];
        }
        throw new \OutOfRangeException("Unknown filter identfier '{$filterName}'.");
    }

    /**
     * @param integer $handle
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result\Facet[] $facets
     * @return \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\Facet|null
     */
    private function getSelectedFacet($handle, array $facets): ?ProductApi\Query\Facet
    {
        foreach ($facets as $facet) {
            if ($facet->handle == $handle) {
                return $facet;
            }
        }
        return null;
    }
}
