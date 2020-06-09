<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi;

use Kore\DataObject\DataObject;

/**
 * @type
 */
class Attribute extends DataObject
{
    /**
     * @var array
     */
    public $attributeId;

    /**
     * @var string
     */
    public $content;

    /**
     * @var string
     */
    public $type;

    public function __toString()
    {
        return (string) $this->content;
    }
}
