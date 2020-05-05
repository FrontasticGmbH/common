<?php

use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Filter;

# Filters example

$contains = new Filter\Contains([
    'field' => 'name',
    'value' => 'Lightweight'
]);

$equals = new Filter\Equals([
    'field' => 'stock',
    'value' => 1,
]);

$equalsAny = new Filter\EqualsAny([
    'field' => 'productNumber',
    'value' => [
        '3fed029475fa4d4585f3a119886e0eb1',
        '77d26d011d914c3aa2c197c81241a45b',
    ],
]);

$multi = new Filter\Multi([
    'operator' => Filter\Multi::CONNECTION_AND,
    'value' => [
        new Filter\Equals([
            'field' => 'stock',
            'value' => 1,
        ]),
        new Filter\Equals([
            'field' => 'active',
            'value' => true,
        ]),
    ],
]);

$not = new Filter\Not([
    'operator' => Filter\Not::CONNECTION_OR,
    'value' => [
        new Filter\Equals([
            'field' => 'stock',
            'value' => 1,
        ]),
        new Filter\Equals([
            'field' => 'availableStock',
            'value' => 1,
        ]),
    ],
]);

$range = new Filter\Range([
    'field' => 'stock',
    'value' => [
        'gte' => 20,
        'gt' => 21,
        'lte' => 30,
        'lt' => 31,
    ],
]);
