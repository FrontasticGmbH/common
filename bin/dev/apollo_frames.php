<?php

$updateFields = [
    'ean' => 'string',
    'frame_box_width' => 'int',
    'frame_box_height' => 'int',
];

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

$lines = array_map('trim', file(BASEDIR . '/apollo_frames.csv'));

$indexes = [];
foreach (explode(";", array_shift($lines)) as $i => $field) {
    if (isset($updateFields[$field])) {
        $indexes[$i] = $field;
    }
}

$attributes = [];
foreach ($lines as $i => $line) {
    $data = explode(";", $line);
    foreach ($indexes as $j => $field) {
        $attributes[$i][$field] = $data[$j];
        settype($attributes[$i][$field], $updateFields[$field]);
    }
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

        $actions = [];
        foreach ($attributes as $attribute) {
            if ($attribute['ean'] !== $product->sku) {
                continue;
            }
            unset($attribute['ean']);
            foreach ($attribute as $name => $value) {
                $actions[] = [
                    'action' => 'setAttribute',
                    'variantId' => $product->variants[0]->id,
                    'staged' => false,
                    'name' => $name,
                    'value' => $value
                ];
            }
        }

        if (0 === count($actions)) {
            $skipped[] = $product->sku;
            echo "Skipping: No price found for {$product->sku}", PHP_EOL;
            continue;
        }

        $updated[] = $product->sku;
        echo 'Updating attributes of ', $product->sku, ' (', json_encode($actions) , ')', PHP_EOL;
        //continue;
        $client->post(
            sprintf('/products/%s', $product->productId),
            [],
            [],
            json_encode(
                [
                    'version' => $product->version,
                    'actions' => $actions,
                ]
            )
        );

        usleep(rand(10000, 100000));
    }
} while ($result->count > 0);

echo "Updated: ", count($updated), "\nSkipped: ", count($skipped), "\n";

sort($skipped);
file_put_contents("/tmp/apollo_frames_skipped.log", join("\n", $skipped));
