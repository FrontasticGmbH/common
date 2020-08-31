<?php


namespace Frontastic\Common\FindologicBundle\Domain;

use Kore\DataObject\DataObject;

class SearchRequest extends DataObject
{
    /**
     * @var string
     */
    public $query;

    /**
     * @var array
     */
    public $attributes;

    /**
     * @var array
     */
    public $order;

    /**
     * @var int
     */
    public $count;

    /**
     * @var int
     */
    public $limit;

    /**
     * @var int
     */
    public $offset;

    public function toArray()
    {
        return [
            'query' => $this->query,
            'attrib' => $this->attributes,
            'order' => $this->order,
        ];
    }
}
