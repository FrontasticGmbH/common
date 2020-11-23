<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\ApiDataObject;

/**
 * @type
 */
class MetaData extends ApiDataObject
{
    /**
     * @var string
     * @required
     */
    public $author;

    /**
     * @var \DateTimeImmutable
     * @required
     */
    public $changed;
}
