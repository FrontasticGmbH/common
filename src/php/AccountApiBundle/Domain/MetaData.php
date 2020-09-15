<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Kore\DataObject\DataObject;

/**
 * @type
 */
class MetaData extends DataObject
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
