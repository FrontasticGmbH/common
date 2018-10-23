<?php

/** @var \Frontastic\Common\ProductApiBundle\Domain\ProductApiFactory $factory */
$factory = include __DIR__ . '/common.php';

/** @var \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools $commercetools */
$commercetools = $factory->factor(
    new \Frontastic\Common\ReplicatorBundle\Domain\Customer([
        'configuration' => [
            'commercetools' => (object) [
                'clientId' => 'fb8gXZ36T_7VsfwVVMH2U9PW',
                'clientSecret' => 'jdK_hpspd6_sWgbhxninS4ugSNZ0fRFK',
                'projectKey' => 'frontastic-1',
            ]
        ]
    ])
);

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

$importer = $semknox->getImporter($locale);
$importer->import($commercetools, $locale);
