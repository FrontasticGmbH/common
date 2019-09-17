<?php

namespace Frontastic\Common\ReplicatorBundle\Domain;

use Kore\DataObject\DataObject;

class Endpoint extends DataObject
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $url;

    /**
     * @var bool
     */
    public $push = true;

    /**
     * @var string
     */
    public $environment = 'production';
}
