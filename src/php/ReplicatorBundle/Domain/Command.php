<?php

namespace Frontastic\Common\ReplicatorBundle\Domain;

use Kore\DataObject\DataObject;

/**
 * @type
 */
class Command extends DataObject
{
    /**
     * @var string
     * @required
     */
    public $command;

    /**
     * @var string
     * @required
     */
    public $channel;

    /**
     * @var string
     * @required
     */
    public $customer;

    /**
     * @var array
     * @required
     */
    public $payload = [];
}
