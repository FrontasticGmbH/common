<?php

namespace Frontastic\Common\ContentApiBundle\Domain;

use Kore\DataObject\DataObject;

class Query extends DataObject
{
    /**
     * @TODO: Move into filter?
     *
     * @var string
     */
    public $contentType;

    /**
     * @var string
     */
    public $query;
}
