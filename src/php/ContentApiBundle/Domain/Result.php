<?php

namespace Frontastic\Common\ContentApiBundle\Domain;

use Kore\DataObject\DataObject;

class Result extends DataObject
{
    /**
     * @var integer
     */
    public $offset;

    /**
     * @var integer
     */
    public $total;

    /**
     * @var integer
     */
    public $count;

    /**
     * @var mixed[]
     */
    public $items;
}
