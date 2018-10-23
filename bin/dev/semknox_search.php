<?php

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Facets;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result\Term;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\TermFacet;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\RangeFacet;

/** @var \Frontastic\Common\ProductApiBundle\Domain\ProductApiFactory $factory */
$factory = include __DIR__ . '/common.php';

/** @var \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox $semknox */
$semknox = $factory->factor(
    new \Frontastic\Common\ReplicatorBundle\Domain\Customer([
        'configuration' => [
            'semknox' => (object) [
                'languages' => [
                    'en' => [
                        /* Search Index */
                        'host' => 'stage.semknox.com',
                        'customerId' => '605',
                        'apiKey' => '48e31v735ywfh9u2s73f85r55h743n37',
                        /* Data Studio */
                        'projectId' => '5adf00a54bfce8512030d2eb',
                        'accessToken' => 'eyJhbGciOiJIUzI1NiJ9.eyJlbWFpbEFkcmVzcyI6Im1hbnVlbEBmcm9udGFzdGljLmNsb3VkIiwidXNlcklkIjoiNWFkZGEzODk0YmZjZTg1MTIwMjczNjc0In0.qvnrx4_8gFlQJtQBDu35oJbL19ZISbvQfqzGUZn1z8M',
                    ],
                    'de' => [
                        /* Search Index */
                        'host' => 'stage.semknox.com',
                        'customerId' => '596',
                        'apiKey' => '249yfy8spo5g97t37dbna9jrh1yc45sa',
                        /* Data Studio */
                        'projectId' => '5adf01154bfce8512030d2ec',
                        'accessToken' => 'eyJhbGciOiJIUzI1NiJ9.eyJlbWFpbEFkcmVzcyI6Im1hbnVlbEBmcm9udGFzdGljLmNsb3VkIiwidXNlcklkIjoiNWFkZGEzODk0YmZjZTg1MTIwMjczNjc0In0.qvnrx4_8gFlQJtQBDu35oJbL19ZISbvQfqzGUZn1z8M',
                    ]
                ]
            ]
        ]
    ])
);

$locale = 'en_GB@euro';
/*
print_r($categories = $semknox->getCategories(new CategoryQuery([
    'locale' => $locale,
    'parentId' => 27072970
])));

print_r($semknox->query(new ProductQuery([
    'query' => '_#',
    'category' => 27123810,//'5c01cbb4-ed6c-45ce-aaba-3cae1cfa3361',
    'locale' => $locale,
    'limit' => 2
])));
*/
$query = new ProductQuery([
    'query' => '_#',
    'category' => 27123815, //27123810,//'5c01cbb4-ed6c-45ce-aaba-3cae1cfa3361',
    'locale' => $locale,
    'limit' => 2
]);

$result = $semknox->query($query);
echo '# Total (', $result->total, ')', PHP_EOL;
foreach ($result as $product) {
    echo '  -> ', $product->name, ' (', join(', ', $product->attributes), ')', PHP_EOL;
}
echo PHP_EOL;

foreach ($result->facets as $facet) {
    if (Facets::KEY_COLOR_TERMS === $facet->key) {
        $terms = array_filter($facet->terms, function (Term $term) {
            return ($term->value === 'YELLOW');
        });
        $query->facets[] = new TermFacet([
            'handle' => $facet->handle,
            'terms' => [reset($terms)->handle]
        ]);
    }
}

$result = $semknox->query($query);
echo '# Total (', $result->total, ')', PHP_EOL;
foreach ($result as $product) {
    echo '  -> ', $product->name, ' (', join(', ', $product->attributes), ')', PHP_EOL;
}
echo PHP_EOL;

foreach ($result->facets as $facet) {
     if (Facets::KEY_PRICE_RANGE === $facet->key) {
        $price = round(($facet->max - $facet->min) / 2);
        $query->facets[] = new RangeFacet(array(
            'handle' => $facet->handle,
            'min' => 0,
            'max' => $price
        ));
    }
}

$result = $semknox->query($query);
echo '# Total (', $result->total, ')', PHP_EOL;
foreach ($result as $product) {
    echo '  -> ', $product->name, ' (', join(', ', $product->attributes), ')', PHP_EOL;
}
echo PHP_EOL;

foreach ($result->facets as $facet) {
    if (Facets::KEY_TARGET_AUDIENCE_TERMS === $facet->key) {
        $terms = array_filter($facet->terms, function (Term $term) {
            return ($term->value === 'WOMEN_AUDIENCE');
        });
        $query->facets[] = new TermFacet([
            'handle' => $facet->handle,
            'terms' => [reset($terms)->handle]
        ]);
    }
}

$result = $semknox->query($query);
echo '# Total (', $result->total, ')', PHP_EOL;
foreach ($result as $product) {
    echo '  -> ', $product->name, ' (', join(', ', $product->attributes), ')', PHP_EOL;
}
echo PHP_EOL;

echo "ENDE(", $result->total, ")\n";
