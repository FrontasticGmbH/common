<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi;

use Kore\DataObject\DataObject;

class Result extends DataObject implements \Countable, \IteratorAggregate
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
    public $items = [];

    /**
     * @var \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result\Facet[]
     */
    public $facets = [];

    /**
     * The query used to generate this result (cloned)
     *
     * @var Query
     */
    public $query;

    /**
     * @return \Traversable
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return $this->count;
    }
}
