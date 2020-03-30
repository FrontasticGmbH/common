<?php

use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Filter\Contains;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Filter\Equals;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Filter\EqualsAny;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Filter\Multi;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Filter\Not;
use Frontastic\Common\ShopwareBundle\Domain\ProductApi\Search\Filter\Range;

# Filters example

# Contains filter
$contains = new Contains([
    'field' => 'name',
    'value' => 'Lightweight'
]);

$equals = new Equals([
    'field' => 'stock',
    'value' => 1,
]);

$equalsAny = new EqualsAny([
    'field' => 'productNumber',
    'value' => [
        '3fed029475fa4d4585f3a119886e0eb1',
        '77d26d011d914c3aa2c197c81241a45b',
    ],
]);

$multi = new Multi([
    'value' => [
        new Equals([
            'field' => 'stock',
            'value' => 1,
        ]),
        new Equals([
            'field' => 'active',
            'value' => true,
        ]),
    ],
]);

$not = new Not([
    'operator' => 'or',
    'value' => [
        new Equals([
            'field' => 'stock',
            'value' => 1,
        ]),
        new Equals([
            'field' => 'availableStock',
            'value' => 1,
        ]),
    ],
]);

$range = new Range([
    'field' => 'stock',
    'value' => [
        'gte' => 20,
        'gt' => 21,
        'lte' => 30,
        'lt' => 31,
    ],
]);
