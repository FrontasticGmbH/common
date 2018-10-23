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

$lines = array_map('trim', file(BASEDIR . '/apollo_images.csv'));
$urls = [];
foreach ($lines as $line) {
    list($sku, $uri) = explode("\t", $line);
    $uri = trim($uri);
    if (!isset($urls[$sku])) {
        $urls[$sku] = [];
    }

    $urls[$sku][] = $uri;

    $urls[$sku] = array_unique($urls[$sku]);
}


$query         = new \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery([
    'locale' => 'en_GB@euro',
    'offset' => 0,
    'limit' => 250,
]);

$api = $factory->factor($customer);

/** @var \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client $client */
$client = $api->getDangerousInnerClient();

$index = 0;
$index1 = 0;
$index2 = 0;
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
        if (count($product->variants[0]->images) > 2) {
            file_put_contents(
                BASEDIR . '/apollo.log',
                var_export($product->images, true) . PHP_EOL,
                FILE_APPEND
            );

            $actions = [];

            for ($i = 2; $i < count($product->images); ++$i) {
                $actions[] = [
                    'action'    => 'removeImage',
                    'imageUrl'  => $product->images[$i],
                    'variantId' => $product->variants[0]->id,
                    'staged'    => false
                ];
            }
            $client->post(
                sprintf('/products/%s', $product->productId),
                [],
                [],
                json_encode(
                    [
                        'version' => $product->version,
                        'actions' => $actions
                    ]
                )
            );
        }

        if (count($product->variants[0]->images) > 0) {
            file_put_contents(
                BASEDIR . '/apollo.log',
                sprintf("% 6s - Images for product '%s' exist!\n", ++$index1,  $product->sku),
                FILE_APPEND
            );
            continue;
        }

        $images = array_filter(
            array_map(
                'trim', explode(
                    "\n",
                    shell_exec(
                        sprintf(
                            "find '%s/apollo_image_export/' -iname '%s*'",
                            BASEDIR,
                            $product->sku
                        )
                    )
                )
            )
        );
        if (0 === count($images)) {
            $images = find_best_images($urls, $product->sku);
        }

        usort($images,function ($image) { return stripos($image, 'front') ? +1 : -1; });

        foreach ($images as $image) {
            $type = getimagesize($image)[2];

            try {
                $client->post(
                    "/products/{$product->productId}/images",
                    [
                        'variant' => $product->variants[0]->id,
                        'filename' => basename($image),
                        'staged' => 'false'
                    ],
                    [
                        'Content-Type: ' . image_type_to_mime_type($type)
                    ],
                    file_get_contents($image)
                );

                printf("% 5s Upload '%s' -> '%s'\n", ++$index,  $image, $product->sku);
            } catch (RequestException $e) {
                fwrite(STDERR, $e->getMessage() . PHP_EOL . $e->getTraceAsString() . PHP_EOL);

                $offset -= $limit;

                echo 'Waiting 60s - ';
                for ($i = 0; $i < 30; ++$i) {
                    echo '.';
                    sleep(2);
                }
                echo PHP_EOL;

                continue 2;
            }
        }

        if (count($images) > 0) {
            file_put_contents(
                BASEDIR . '/apollo.log',
                sprintf("Images for product '%s' uploaded!\n",  $product->sku),
                FILE_APPEND
            );
        } else {
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
            printf("% 5s skipping -> %s\n", ++$index2, $product->sku);
        }

        usleep(rand(5000, 20000));
    }
} while ($result->count > 0);

function find_best_images(array $images, string $sku)
{
    if (false === isset($images[$sku])) {
        return [];
    }
    $images = $images[$sku];

    $list = [];
    foreach ($images as $url) {
        $basename = basename($url);
        if (!isset($list[$basename])) {
            $list[$basename] = [];
        }
        $list[$basename][] = $url;
    }

    $alreadyDone = 0;
    foreach (array_keys($list) as $basename) {
        if ($files = glob(BASEDIR . "/apollo_image_export/*/{$basename}")) {
            $list[$basename] = $files[0];
            ++$alreadyDone;
        }
    }

    if ($alreadyDone > 0) {
        return array_filter(
            $list,
            function ($data) {
                return !is_array($data);
            }
        );
    }


    foreach ($list as $basename => $urls) {
        $last = 0;
        foreach ($urls as $i => $url) {
            $size = getimagesize($url)[0];
            if ($size > $last) {
                $last = $size;
                $list[$basename] = $url;
                //echo "Use image '{$url}' with size {$size}.\n";
            }
        }
    }

    foreach ($list as $basename => $url) {
        $blob = file_get_contents($url);
        if (stripos($basename, 'Front')) {
            $file = BASEDIR . "/apollo_image_export/front_fertig/{$basename}";
        } else {
            $file = BASEDIR . "/apollo_image_export/angle_fertig/{$basename}";
        }
        file_put_contents($file, $blob);
    }

    return $list;
}


return;


print_r($api->getCategories($query));
print_r($api->getProductTypes());
/*
print_r($api->query(
    new \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery(
        [
            'productType' => '580283f4-22d3-4b04-a710-df355d18ae6e',
            //'category' => 'bca2cb61-c50a-4337-8d46-ff07e7efba26',
            'locale' => 'en_GB@euro',
        ]
    )
));
*/
print_r($api->getProduct(
    new \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery([
        'productId' => 'd8ba1a8f-99aa-4516-b14c-2dc0b84901d9',
        'locale' => 'en_GB@euro',
    ])
));
print_r($api->query(
    new \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery(
        [
            'productIds' => [
                'd8ba1a8f-99aa-4516-b14c-2dc0b84901d9',
//                '36376f05-8eac-4215-a670-ce8d4a28136a'
            ],
            'locale' => 'en_GB@euro',
            //'category' => 'bca2cb61-c50a-4337-8d46-ff07e7efba26'
        ]
    )
));
