<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProductApi;

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
            'attributeId' => 'price',
            'attributeType' => 'money',
        ]
    ];
}
