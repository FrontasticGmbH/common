<?php

use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Aggregation;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Filter;

# Aggregations example

## Metric aggregations
$avg = new Aggregation\Avg([
    'name' => 'avg-price',
    'field' => 'price',
]);

$count = new Aggregation\Count([
    'name' => 'count-manufacturers',
    'field' => 'manufacturerId',
]);

$max = new Aggregation\Max([
    'name' => 'max-price',
    'field' => 'price',
]);

$min = new Aggregation\Max([
    'name' => 'min-price',
    'field' => 'price',
]);

$sum = new Aggregation\Sum([
    'name' => 'sum-price',
    'field' => 'price',
]);

$stats = new Aggregation\Sum([
    'name' => 'stats-price',
    'field' => 'price',
]);

## Bucket aggregations

$entity = new Aggregation\Entity([
    'name' => 'manufacturers',
    'definition' => 'product_manufacturer',
    'field' => 'manufacturerId',
]);

// Calculate total sum of items having stock equals to 1
$filter1 = new Aggregation\Filter([
    'name' => 'my-filter',
    'filter' => [
        new Filter\Equals([
            'field' => 'stock',
            'value' => 1,
        ])
    ],
    'aggregation' => new Aggregation\Sum([
        'name' => 'sum-price',
        'field' => 'price',
    ]),
]);

// Manufacturers per category
$filte2 = new Aggregation\Filter([
    'name' => 'my-filter',
    'filter' => [
        new Filter\Range([
            'field' => 'price',
            'value' => ['gte' => 500,],
        ]),
    ],
    'aggregation' => new Aggregation\Terms([
        'name' => 'per-category',
        'field' => 'categories.id',
        'aggregation' => new Aggregation\Terms([
            'name' => 'manufacturer-ids',
            'field' => 'manufacturerId',
        ]),
    ]),
]);

$terms = new Aggregation\Terms([
    'name' => 'manufacturer-ids',
    'field' => 'manufacturerId',
]);

$histogram = new Aggregation\Histogram([
    'name' => 'release-dates',
    'field' => 'releaseDate',
    'interval' => 'month',
]);
