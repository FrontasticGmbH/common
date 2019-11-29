<?php

namespace Frontastic\Common\AccountApiBundle\Domain;

use Kore\DataObject\DataObject;

class MetaData extends DataObject
{
    /**
     * @var string
     */
    public $author;

    /**
     * @var \DateTimeImmutable
     */
    public $changed;
}
