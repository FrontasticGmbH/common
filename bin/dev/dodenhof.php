<?php
/** @var \Frontastic\Common\ProductApiBundle\Domain\ProductApiFactory $factory */

use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;

$factory = include __DIR__ . '/common.php';

$customer = new \Frontastic\Common\ReplicatorBundle\Domain\Customer(
    [
        'configuration' => [
            'commercetools' => (object)[
                'clientId'     => 'FALWhepLAM1SciPsK03_RdiT',
                'clientSecret' => '62lt7-EuQZqF7l70g6GXhX0dU6aPOQDH',
                'projectKey'   => 'dodenhof-neu',
            ]
        ]
    ]
);

$imageFeed = [];
foreach (file(__DIR__ . '/dodenhof.csv') as $line) {
    $line = str_replace(';https://', "\thttps://", $line);
    list($sku, $uri) = explode("\t", trim($line));
    $imageFeed[$sku] = $uri;
}

$priceFeed = [];
foreach (json_decode(
             file_get_contents(__DIR__ . '/exported-prices.json'),
             true
         )['prices'] as $variant) {
    foreach ($variant['prices'] as $price) {
        $sku             = $price['variant-sku'];
        $priceFeed[$sku] = $price['value']['centAmount'];
    }
}

$api = $factory->factor($customer);

/** @var \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client $ctclient */
$ctclient = $api->getDangerousInnerClient();

$skipped = 0;
$offset  = 0;
do {

    $result = $api->query(
        new ProductQuery(
            [
                'offset' => $offset,
                'limit'  => 200,
                'locale' => 'de_DE'
            ]
        )
    );

    foreach ($result as $product) {

        if (0 !== strpos($product->sku, '7200426733')) {
            //continue;
        }


        foreach ($product->variants as $variant) {

            if (!isset($imageFeed[$variant->sku])) {
                //fwrite(STDERR, sprintf("SKIPPING not existing Variant (%s)\n", $variant->sku));
                continue;
            }
            if (count($variant->images) > 0) {
                echo count($variant->images), PHP_EOL;
            }
            foreach ($variant->images as $image) {
                $response = $ctclient->post(
                    sprintf('/products/%s', $product->productId),
                    [],
                    [],
                    json_encode(
                        [
                            'version' => $product->version,
                            'actions' => [
                                [
                                    'action'    => 'removeImage',
                                    'imageUrl'  => $image,
                                    'variantId' => $variant->id,
                                    'staged'    => false
                                ]
                            ]
                        ]
                    )
                );
                print_r($response);
            }


            if (count($variant->images) > 0) {
                fwrite(
                    STDERR,
                    sprintf(
                        "SKIPPING Variant with Images (%s)\n",
                        $variant->sku
                    )
                );
                continue;
            }


            $html = @file_get_contents($imageFeed[$variant->sku]);
            if (!$html) {
                fwrite(STDERR, sprintf("Cannot load uri '%s'\n", $uri));
            }

            preg_match_all(
                '(<div\s+[^>]*id="zoom-product"[^>]+background-image:\s*url\(([^\)>]+)\))siU',
                $html,
                $matches
            );

            $images = [];
            foreach ($matches[1] as $match) {
                $images[basename($match)] = 'https:' . preg_replace(
                        '(/[^/]+::[^/]+/)',
                        '/',
                        $match
                    );
            }
            continue;
            echo $imageFeed[$variant->sku], PHP_EOL;
            foreach ($images as $filename => $image) {
                // Don't reimport existing images
                if (isset($existing[$filename])) {
                    continue;
                }

                echo $variant->sku, ' - ', $image, PHP_EOL;

                $blob = file_get_contents($image);
                if (!$blob) {
                    fwrite(STDERR, sprintf("Cannot load image '%s'\n", $image));
                }

                $response = $ctclient->post(
                    sprintf('/products/%s/images', $product->productId),
                    [
                        'variant'  => $variant->id,
                        'filename' => $filename,
                        'staged'   => 'false'
                    ],
                    ['Content-Type: image/jpeg'],
                    $blob
                );
            }
        }
    }

    /*
        //print_r($result->items[0]);
        echo end($result->items)->name, ' (', end($result->items)->sku, ')', PHP_EOL;
        echo $offset, PHP_EOL;
    */
    $offset += $result->count;
} while ($offset < $result->total);

