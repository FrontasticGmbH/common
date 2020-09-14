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
     * @required
     */
    public $facets = [];

    /**
     * @var int
     * @required
     */
    public $limit = 0;

    /**
     * @var int
     * @required
     */
    public $offset = 0;

    /**
     * @var int
     * @required
     */
    public $count = 0;

    /**
     * @var int
     * @required
     */
    public $total = 0;

    /**
     * @var array
     * @required
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
