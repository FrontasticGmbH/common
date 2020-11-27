<?php

namespace Frontastic\Common\ContentApiBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\ApiDataObject;

/**
 * @type
 */
class Query extends ApiDataObject
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
     * @var array
     */
    public $contentIds;

    /**
     * @var AttributeFilter[]
     */
    public $attributes = [];

    /**
     * @deprecated use \Frontastic\Common\ContentApiBundle\Domain\ContentQueryFactory::queryFromParameters instead
     */
    public static function fromArray(array $data, bool $ignoreAdditionalAttributes = false): Query
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

        return new self($data, $ignoreAdditionalAttributes);
    }
}
