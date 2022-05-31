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
     * @var bool
     * @required
     */
    public $allowCache;

    /**
     * @var array
     * @required
     */
    public $payload = [];


    public function __construct(array $values = array(), bool $ignoreAdditionalAttributes = false)
    {
        $this->allowCache = true; // set default value
        parent::__construct($values, $ignoreAdditionalAttributes);
    }
}
