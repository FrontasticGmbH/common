<?php

namespace Frontastic\Common\ReplicatorBundle\Domain;

use Kore\DataObject\DataObject;

class Customer extends DataObject
{
    /**
     * @var string
     */
    public $name;

    /**
     * @var string
     */
    public $secret;

    /**
     * @var string
     */
    public $edition = 'micro';

    /**
     * Used to indicate this customer is only "half" configured or similar.
     *
     * @var bool
     */
    public $isTransient = false;

    /**
     * @var array
     */
    public $configuration = [];

    /**
     * @var array
     */
    public $environments = [
        'production',
        'staging',
        'development',
    ];

    /**
     * @var Project[]
     */
    public $projects = [];
}
