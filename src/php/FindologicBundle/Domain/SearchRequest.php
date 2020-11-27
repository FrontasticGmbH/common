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
     * @var null|string
     */
    public $identifier;

    /**
     * @var array
     */
    public $attributes;

    /**
     * @var array
     */
    public $outputAttributes;

    /**
     * @var array
     */
    public $properties;

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
    public $first;

    public function toArray()
    {
        return [
            'query' => $this->query,
            'identifier' => $this->identifier,
            'first' => $this->first,
            'count' => $this->count,
            'order' => $this->order,
            'attrib' => $this->attributes,
            'outputAttrib' => $this->outputAttributes,
            'properties' => $this->properties,
        ];
    }
}
