<?php

namespace Frontastic\Common\ReplicatorBundle\Domain;

use Kore\DataObject\DataObject;

/**
 * @type
 */
class Customer extends DataObject
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
    public $secret;

    /**
     * @var string
     * @required
     */
    public $edition = 'micro';

    /**
     * @var bool
     * @required
     */
    public $hasPaasModifications = false;

    /**
     * Number of available Frontastic Machines
     *
     * @var int
     * @required
     */
    public $machineLimit = 10;

    /**
     * Frontastic Machines Map to define providers use in each region
     *
     * @var array
     * @required
     */
    public $machineRegionToProviderMap = [];

    /**
     * @var array
     * @required
     */
    public $features = [];

    /**
     * Used to indicate this customer is only "half" configured or similar.
     *
     * @var bool
     * @required
     */
    public $isTransient = false;

    /**
     * Used to indicate the customer uses the new down-sharded CouchDB
     * schema with the "_downsharded" suffix database name convention
     *
     * @var bool
     * @required
     */
    public bool $dbIsDownsharded = false;

    /**
     * @var array
     * @required
     */
    public $configuration = [];

    /**
     * @var array
     * @required
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
     * @required
     */
    public $projects = [];

    /**
     * @var ?string
     */
    public $netlifyUrl = null;
}
