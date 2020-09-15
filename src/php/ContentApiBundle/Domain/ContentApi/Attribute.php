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
     * @required
     */
    public $attributeId;

    /**
     * @var string
     * @required
     */
    public $content;

    /**
     * @var string
     * @required
     */
    public $type;

    public function __toString()
    {
        return (string) $this->content;
    }
}
