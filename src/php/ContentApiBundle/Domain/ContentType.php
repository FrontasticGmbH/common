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
     * @required
     */
    public $contentTypeId;

    /**
     * @var string
     * @required
     */
    public $name;
}
