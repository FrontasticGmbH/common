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
     * Get the environment with the lowest priority. This will return 'development' for the default environments.
     */
    public function getLowestEnvironment(): string
    {
        // Get the last element of the array without resetting the internal pointer in the array
        return array_values(array_slice($this->environments, -1))[0];
    }

    /**
     * @var Project[]
     */
    public $projects = [];
}
