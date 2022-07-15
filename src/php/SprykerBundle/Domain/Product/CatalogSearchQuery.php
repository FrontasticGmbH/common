<?php

namespace Frontastic\Common\SprykerBundle\Domain\Product;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\Facet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\RangeFacet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\TermFacet;

class CatalogSearchQuery
{
    private const DEFAULT_ITEMS_PER_PAGE_FRONTASTIC = 24;
    private const DEFAULT_ITEMS_PER_PAGE = 36;

    private const SORT_FORMATTING_EXCLUDED = [
        '_asc',
        '_desc',
        'rating',
    ];

    /**
     * @param ProductQuery $productQuery
     *
     * @return string
     */
    public static function createFromProductQuery(ProductQuery $productQuery): string
    {
        $query = [];
        $queryString = [];

        if ($productQuery->query) {
            $queryString[] = self::parseQuery($productQuery->query);
        }

        if ($productQuery->productId) {
            $queryString[] = $productQuery->productId;
        }

        if ($productQuery->productIds) {
            $queryString[] = implode(' ', $productQuery->productIds);
        }

        if ($productQuery->sku) {
            $queryString[] = $productQuery->sku;
        }

        if ($productQuery->skus) {
            $queryString[] = implode(' ', $productQuery->skus);
        }

        $query['q'] = implode(' ', $queryString);

        $categories = $productQuery->getAllUniqueCategories();
        if (count($categories) > 0) {
            $query['category'] = (int)$categories[0];
        }

        if ($productQuery->limit === self::DEFAULT_ITEMS_PER_PAGE_FRONTASTIC) {
            $productQuery->limit = self::DEFAULT_ITEMS_PER_PAGE;
        }

        $query['page']['offset'] = $productQuery->offset ?: 0;
        $query['page']['limit'] = $productQuery->limit ?: self::DEFAULT_ITEMS_PER_PAGE;

        foreach ($productQuery->facets as $facet) {
            $query[$facet->handle] = self::formatFacet($facet);
        }

        if ($productQuery->sortAttributes) {
            $query['sort'] = self::formatSortAttributes($productQuery->sortAttributes);
        }

        return http_build_query($query);
    }

    /**
     * @param string|null $rawQuery
     *
     * @return string
     */
    private static function parseQuery(?string $rawQuery): string
    {
        $query = $rawQuery ? urldecode($rawQuery) : '';

        if ($query === '*') {
            $query = '';
        }

        return $query;
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\Facet $facet
     *
     * @return array|string
     */
    private static function formatFacet(Facet $facet)
    {
        $result = [];

        if ($facet instanceof TermFacet) {
            // Return single term as string instead of array
            if (count($facet->terms) === 1) {
                return urldecode($facet->terms[0]);
            }

            foreach ($facet->terms as $term) {
                $result[] = urldecode($term);
            }
        } elseif ($facet instanceof RangeFacet) {
            $result['min'] = $facet->min;
            $result['max'] = $facet->max;
        }

        return $result;
    }

    /**
     * @param array $sortAttributes
     *
     * @return string
     */
    private static function formatSortAttributes(array $sortAttributes): string
    {
        $remapped = [];

        foreach ($sortAttributes as $field => $direction) {
            $remapped[] = self::formatSortAttribute($field, $direction);
        }

        return implode(',', $remapped);
    }

    /**
     * @param string $field
     * @param string $direction
     *
     * @return string
     */
    private static function formatSortAttribute(string $field, string $direction): string
    {
        foreach (self::SORT_FORMATTING_EXCLUDED as $fieldPart) {
            if (strpos($field, $fieldPart) !== false) {
                return $field;
            }
        }

        return $field . ($direction === ProductQuery::SORT_ORDER_ASCENDING ? '_asc' : '_desc');
    }
}
