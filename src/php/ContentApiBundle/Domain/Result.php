<?php

namespace Frontastic\Common\ContentApiBundle\Domain;

use Kore\DataObject\DataObject;

/**
 * @type
 */
class Result extends DataObject
{
    /**
     * @var integer
     * @required
     */
    public $offset;

    /**
     * @var integer
     * @required
     */
    public $total;

    /**
     * @var integer
     * @required
     */
    public $count;

    /**
     * @var mixed[]
     * @required
     */
    public $items = [];
}
