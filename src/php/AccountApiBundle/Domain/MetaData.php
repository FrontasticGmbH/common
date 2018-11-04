<?php

namespace Frontastic\Backstage\UserBundle\Domain;

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
