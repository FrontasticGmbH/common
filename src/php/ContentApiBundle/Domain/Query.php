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
     * Contains a key value pair of <field> => <value to filter for>
     *
     * @var array
     */
    public $filter = [];
}
