<?php

namespace Frontastic\Common\ContentApiBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\ApiDataObject;

/**
 * @type
 */
class ContentType extends ApiDataObject
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
