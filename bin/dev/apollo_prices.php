<?php

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Exception\RequestException;

define('BASEDIR', realpath(__DIR__ . '/../../../../'));

/** @var \Frontastic\Common\ProductApiBundle\Domain\ProductApiFactory $factory */
$factory = include __DIR__ . '/common.php';

$customer = new \Frontastic\Common\ReplicatorBundle\Domain\Customer([
    'configuration' => [
        'commercetools' => (object) [
            'clientId' => 'd4NhOzjuPCENZfaqJNqqKnXM',
            'clientSecret' => '1fG0s6u8EclsjxKcZEcXGT9vXMR7DpZx',
            'projectKey' => 'apollo-77',
        ]
    ]
]);

$lines = array_map('trim', file(BASEDIR . '/product_price.txt'));
$prices = [];
foreach ($lines as $line) {
    list($sku, $price) = explode(";", $line);
    list($price) = str_replace(',', '', preg_split('(\s+)', trim($price)));

    $prices[$sku] = (int) $price;
}

$api = $factory->factor($customer);

/** @var \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client $client */
$client = $api->getDangerousInnerClient();

$updated = [];
$skipped = [];

$limit = 250;
$offset = 0;
do {
    $result = $api->query(
        new \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery(
            [
                'query' => '',
                'locale' => 'de_DE@euro',
                'limit' => $limit,
                'offset' => $offset,
            ]
        )
    );
    $offset += $limit;

    /** @var \Frontastic\Common\ProductApiBundle\Domain\Product $product */
    foreach ($result->items as $product) {

        if (!isset($prices[$product->sku])) {
            $skipped[] = $product->sku;
            echo "Skipping: No price found for {$product->sku}", PHP_EOL;

            $client->post(
                sprintf('/products/%s', $product->productId),
                [],
                [],
                json_encode(
                    [
                        'version' => $product->version,
                        'actions' => [
                            [
                                'action' => 'unpublish',
                            ]
                        ]
                    ]
                )
            );

            continue;
        }

        if ($product->price == $prices[$product->sku]) {
            continue;
        }

        $updated[] = $product->sku;
        echo 'Updating price of ', $product->sku, ' (', $prices[$product->sku], ')', PHP_EOL;
        $client->post(
            sprintf('/products/%s', $product->productId),
            [],
            [],
            json_encode(
                [
                    'version' => $product->version,
                    'actions' => [
                        [
                            'action' => 'setPrices',
                            'variantId' => $product->variants[0]->id,
                            'staged' => false,
                            'prices' => [
                                [
                                    'value' => [
                                        'type' => 'centPrecision',
                                        'currencyCode' => 'EUR',
                                        'centAmount' => $prices[$product->sku],
                                        'fractionDigits' => 2
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            )
        );

        usleep(rand(10000, 100000));
    }
} while ($result->count > 0);

echo "Updated: ", count($updated), "\nSkipped: ", count($skipped), "\n";

sort($skipped);
file_put_contents("/tmp/apollo_price_skipped.log", join("\n", $skipped));
