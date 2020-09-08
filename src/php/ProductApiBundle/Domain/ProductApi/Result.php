<?php

namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi;

use Kore\DataObject\DataObject;

/**
 * Class Result can be used for both, offset and cursor-based pagination.
 *
 * In general terms, REST APIs use offset pagination whereas GraphQL APIs use cursor-based pagination.
 *
 * Regardless the pagination implemented by your backend of choice, we highly recommend you to use in both cases
 * the property $nextCursor to store the position where the pagination should continue.
 *
 * Additionally, and only for GraphQL APIs, you can use $previousCursor to store the position
 * of the first element to allow backward pagination.
 *
 * NOTE: the property $offset will be deprecated in a further commit.
 *
 * @type
 */
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
     * @var string
     */
    public $previousCursor;

    /**
     * @var string
     */
    public $nextCursor;

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
