<?php

return new \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client\ResultSet(array (
  'limit' => 1,
  'offset' => 0,
  'count' => 1,
  'total' => 1144,
  'results' => 
  array (
    0 => 
    array (
      'masterVariant' => 
      array (
        'attributes' => 
        array (
          0 => 
          array (
            'name' => 'designer',
            'value' => 
            array (
              'key' => 'philippemodel',
              'label' => 'Philippe Model',
            ),
          ),
          1 => 
          array (
            'name' => 'color',
            'value' => 
            array (
              'key' => 'white',
              'label' => 
              array (
                'en' => 'white',
                'de' => 'weiss',
                'it' => 'blanco',
              ),
            ),
          ),
        ),
        'assets' => 
        array (
        ),
        'images' => 
        array (
          0 => 
          array (
            'url' => 'https://s3-eu-west-1.amazonaws.com/commercetools-maximilian/products/073430_1_medium.jpg',
            'dimensions' => 
            array (
              'w' => 0,
              'h' => 0,
            ),
          ),
        ),
        'prices' => 
        array (
          0 => 
          array (
            'value' => 
            array (
              'currencyCode' => 'EUR',
              'centAmount' => 26250,
            ),
            'id' => '80c01310-3d97-44ca-a42e-61d767beb6f1',
          ),
        ),
        'sku' => 'M0E20000000DQ3V',
        'id' => 1,
      ),
      'variants' => 
      array (
        0 => 
        array (
          'attributes' => 
          array (
            0 => 
            array (
              'name' => 'designer',
              'value' => 
              array (
                'key' => 'philippemodel',
                'label' => 'Philippe Model',
              ),
            ),
            1 => 
            array (
              'name' => 'color',
              'value' => 
              array (
                'key' => 'white',
                'label' => 
                array (
                  'en' => 'white',
                  'de' => 'weiss',
                  'it' => 'blanco',
                ),
              ),
            ),
          ),
          'assets' => 
          array (
          ),
          'images' => 
          array (
            0 => 
            array (
              'url' => 'https://s3-eu-west-1.amazonaws.com/commercetools-maximilian/products/073430_1_medium.jpg',
              'dimensions' => 
              array (
                'w' => 0,
                'h' => 0,
              ),
            ),
          ),
          'prices' => 
          array (
            0 => 
            array (
              'value' => 
              array (
                'currencyCode' => 'EUR',
                'centAmount' => 26250,
              ),
              'id' => '934de6cd-be1e-4fa6-a5dd-48013945c2ad',
            ),
          ),
          'sku' => 'M0E20000000DQ3W',
          'id' => 2,
        ),
        1 => 
        array (
          'attributes' => 
          array (
            0 => 
            array (
              'name' => 'designer',
              'value' => 
              array (
                'key' => 'philippemodel',
                'label' => 'Philippe Model',
              ),
            ),
            1 => 
            array (
              'name' => 'color',
              'value' => 
              array (
                'key' => 'white',
                'label' => 
                array (
                  'en' => 'white',
                  'de' => 'weiss',
                  'it' => 'blanco',
                ),
              ),
            ),
          ),
          'assets' => 
          array (
          ),
          'images' => 
          array (
            0 => 
            array (
              'url' => 'https://s3-eu-west-1.amazonaws.com/commercetools-maximilian/products/073430_1_medium.jpg',
              'dimensions' => 
              array (
                'w' => 0,
                'h' => 0,
              ),
            ),
          ),
          'prices' => 
          array (
            0 => 
            array (
              'value' => 
              array (
                'currencyCode' => 'EUR',
                'centAmount' => 26250,
              ),
              'id' => '35cb6846-2b6d-42e3-9c85-fd87711da777',
            ),
          ),
          'sku' => 'M0E20000000DQ3X',
          'id' => 3,
        ),
      ),
      'slug' => 
      array (
        'de' => 'philippemodel-sneakers-TRLUWT39-weiss',
        'en' => 'philippemodel-sneakers-TRLUWT39-white',
      ),
      'categoryOrderHints' => 
      array (
      ),
      'categories' => 
      array (
        0 => 
        array (
          'typeId' => 'category',
          'id' => 'c35cdb2a-7451-46bf-bfba-174af9146c1e',
        ),
        1 => 
        array (
          'typeId' => 'category',
          'id' => '5c01cbb4-ed6c-45ce-aaba-3cae1cfa3361',
        ),
      ),
      'name' => 
      array (
        'de' => 'Sneakers Philippe Model weiß',
        'en' => 'Sneakers Philippe Model white',
      ),
      'productType' => 
      array (
        'typeId' => 'product-type',
        'id' => '580283f4-22d3-4b04-a710-df355d18ae6e',
      ),
      'version' => 2,
      'id' => '95191aa0-8ded-48f6-afe7-0b1a4316fa00',
    ),
  ),
  'facets' => 
  array (
    'variants.price.centAmount' => 
    array (
      'type' => 'range',
      'dataType' => 'number',
      'ranges' => 
      array (
        0 => 
        array (
          'type' => 'double',
          'from' => 0,
          'fromStr' => '0.0',
          'to' => 0,
          'toStr' => '',
          'count' => 12089,
          'totalCount' => 12089,
          'total' => 439988750,
          'min' => 15000,
          'max' => 99375,
          'mean' => 36395.79369674911,
        ),
      ),
    ),
    'variants.attributes.color.key' => 
    array (
      'type' => 'terms',
      'dataType' => 'text',
      'missing' => 80,
      'total' => 11967,
      'other' => 0,
      'terms' => 
      array (
        0 => 
        array (
          'term' => 'blue',
          'count' => 2917,
        ),
        1 => 
        array (
          'term' => 'black',
          'count' => 1902,
        ),
        2 => 
        array (
          'term' => 'grey',
          'count' => 1397,
        ),
      ),
    ),
  ),
));
