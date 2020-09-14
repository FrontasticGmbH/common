<?php

namespace Frontastic\Common\ReplicatorBundle\Domain;

use Kore\DataObject\DataObject;

/**
 * @type
 */
class Endpoint extends DataObject
{
    /**
     * @var string
     * @required
     */
    public $name;

    /**
     * @var string
     * @required
     */
    public $url;

    /**
     * @var bool
     * @required
     */
    public $push = true;

    /**
     * @var string
     * @required
     */
    public $environment = 'production';
}
