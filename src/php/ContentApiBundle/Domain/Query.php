<?php

namespace Frontastic\Common\ContentApiBundle\Domain;

use Kore\DataObject\DataObject;

class Query extends DataObject
{
    /**
     * @var string
     */
    public $contentType;

    /**
     * @var string
     */
    public $query;

    /**
     * @var AttributeFilter[]
     */
    public $attributes = [];

    public static function fromArray(array $data)
    {
        $data['attributes'] = array_map(
            function ($attribute) {
                return new AttributeFilter($attribute);
            },
            array_filter(
                $data['attributes'] ?? [],
                function ($attribute) {
                    return !is_null($attribute);
                }
            )
        );

        return new self($data);
    }
}
