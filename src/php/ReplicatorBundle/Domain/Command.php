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
     */
    public $command;

    /**
     * @var string
     */
    public $channel;

    /**
     * @var string
     */
    public $customer;

    /**
     * @var array
     */
    public $payload = [];
}
