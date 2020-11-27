<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi;

use Frontastic\Common\CoreBundle\Domain\ApiDataObject;

/**
 * @type
 */
class Attribute extends ApiDataObject
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
