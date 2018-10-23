<?php
/** @var \Frontastic\Common\ProductApiBundle\Domain\ProductApiFactory $factory */
$factory = include __DIR__ . '/common.php';

$customer = new \Frontastic\Common\ReplicatorBundle\Domain\Customer([
    'configuration' => [
        'commercetools' => (object) [
            'clientId' => 'fb8gXZ36T_7VsfwVVMH2U9PW',
            'clientSecret' => 'jdK_hpspd6_sWgbhxninS4ugSNZ0fRFK',
            'projectKey' => 'frontastic-1',
        ]
    ]
]);

$query         = new \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery([
    'locale' => 'en_GB@euro',
    'offset' => 0,
    'limit' => 250,
]);

$api = $factory->factor($customer);
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
