<?php

namespace Frontastic\Common\ContentApiBundle\Domain;

use Kore\DataObject\DataObject;

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
