<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools;

use Kore\DataObject\DataObject;

class Options extends DataObject
{
    /**
     * Facets to be fetched with ProductQuery.
     *
     * @var array
     */
    public $facetsToQuery = [
        [
            'attributeId' => 'variants.price',
            'attributeType' => 'money',
        ]
    ];
}
