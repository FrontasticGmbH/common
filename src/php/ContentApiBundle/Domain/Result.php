<?php

namespace Frontastic\Common\ContentApiBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\ApiDataObject;

/**
 * @type
 */
class Result extends ApiDataObject
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
