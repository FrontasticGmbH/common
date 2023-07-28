<?php

namespace Frontastic\Common\ReplicatorBundle\Domain;

use Kore\DataObject\DataObject;

/**
 * @type
 */
class Customer extends DataObject
{
    final public const FEATURE_FRONTASTIC_NEXTJS = 'frontasticNextJs';
    final public const FEATURE_MULTITENANT = 'multiTenant';
    final public const FEATURE_DB_IN_CLUSTER = 'dbInCluster';
    final public const FEATURE_FECL = 'fecl';
    final public const FEATURE_PAGE_SCHEDULE_FECL = 'pageScheduleFecl';
    final public const FEATURE_MACHINES_REGIONS = 'machinesRegions';
    final public const FEATURE_COLLABORATION = 'collaboration';
    final public const FEATURE_VERSION_HISTORY = 'versionHistory';

    final public const FEATURE_FLAG_DESCRIPTIONS = [
        self::FEATURE_FRONTASTIC_NEXTJS => 'Differentiate between coFE (Next.JS) and legacy (frontastic) customers',
        self::FEATURE_MULTITENANT => 'Defines whether the customer is hosted in a multi-tenant infrastructure or not',
        self::FEATURE_DB_IN_CLUSTER => 'Defines whether the database is hosted in the studio host or in the cluster',
        self::FEATURE_FECL => 'Toggle support for FECL criteria in dynamic page rule scheduling',
        self::FEATURE_PAGE_SCHEDULE_FECL => 'Toggle support for FECL criteria in page version scheduling (deprecated?)',
        self::FEATURE_MACHINES_REGIONS => 'Toggle support for alternate regions in sandboxes',
        self::FEATURE_COLLABORATION => 'Toggle the "Collaboration" section for the Editor Page on the Frontend',
        self::FEATURE_VERSION_HISTORY => 'Toggle the "Version History" section for the Editor Page on the Frontend',
    ];

    // these feature flags should only be toggled by developers
    // only after major changes to the customer's infrastructure
    final public const INFTRASTRUCTURE_SPECIFIC_FEATURES = [
        self::FEATURE_FRONTASTIC_NEXTJS,
        self::FEATURE_MULTITENANT,
        self::FEATURE_DB_IN_CLUSTER,
    ];

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
