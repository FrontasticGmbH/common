<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi\GraphCMS;

use Frontastic\Common\ContentApiBundle\Domain\ContentApi\Attribute;
use Kore\DataObject\DataObject;

class ClientResult extends DataObject
{
    /**
     * JSON-string containing the result of the query.
     *
     * @var string
     */
    public $queryResultJson;

    /**
     * Array of attributes used in the query.
     *
     * @var Attribute[]
     */
    public $attributes;
}
