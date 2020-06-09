<?php

namespace Frontastic\Common\ContentApiBundle\Domain;

use Kore\DataObject\DataObject;

/**
 * @type
 */
class ContentType extends DataObject
{
    /**
     * @var string
     */
    public $contentTypeId;

    /**
     * @var string
     */
    public $name;
}
