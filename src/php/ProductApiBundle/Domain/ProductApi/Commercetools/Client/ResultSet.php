<?php
namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client;

use Kore\DataObject\DataObject;
use Traversable;

/**
 * @type
 */
class ResultSet extends DataObject implements \IteratorAggregate
{
    /**
     * @var array
     */
    public $facets = [];

    /**
     * @var int
     */
    public $limit = 0;

    /**
     * @var int
     */
    public $offset = 0;

    /**
     * @var int
     */
    public $count = 0;

    /**
     * @var int
     */
    public $total = 0;

    /**
     * @var array
     */
    public $results = [];

    /**
     * @return \Traversable
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->results);
    }
}
