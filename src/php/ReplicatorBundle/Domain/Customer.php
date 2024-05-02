<?php

namespace Frontastic\Common\ReplicatorBundle\Domain;

use Kore\DataObject\DataObject;

/**
 * @type
 */
class Customer extends DataObject
{
    public const FEATURE_FRONTASTIC_NEXTJS = 'frontasticNextJs';
    public const FEATURE_MULTITENANT = 'multiTenant';
    public const FEATURE_DB_IN_CLUSTER = 'dbInCluster';
    public const FEATURE_CUSTOM_GIT_REPO = 'customGitRepo';
    public const FEATURE_DISABLE_FRONTASTIC_CI = 'disableFrontasticCi';
    public const FEATURE_ALLOW_CLEAR_FIXTURES = 'allowClearFixtures';
    public const FEATURE_FECL = 'fecl';
    public const FEATURE_REFACTOR_FECL = 'refactorFECL';
    public const FEATURE_PAGE_SCHEDULE_FECL = 'pageScheduleFecl';
    public const FEATURE_MACHINES_REGIONS = 'machinesRegions';
    public const FEATURE_VERSION_HISTORY = 'versionHistory';
    public const FEATURE_API_KEYS_PAGE = 'apiKeysPage';
    public const FEATURE_HUMIO = 'humio';
    public const FEATURE_PREVIEW_LOCALE = 'previewLocale';
    public const FEATURE_STUDIO_ORCHESTRATED_DEPLOYMENTS = 'studioOrchestratedDeployments';
    public const FEATURE_DISABLE_SLUG_TRANSLITERATION = 'disableSlugTransliteration';
    public const FEATURE_UPDATE_LOCALE_DROPDOWN = 'updateLocaleDropdown';
    public const FEATURE_NETLIFY_PREVIEW = 'netlifyPreview';

    public const FEATURE_FLAG_DESCRIPTIONS = [
        self::FEATURE_FRONTASTIC_NEXTJS => 'Differentiate between coFE (Next.JS) and legacy (frontastic) customers',
        self::FEATURE_MULTITENANT => 'Defines whether the customer is hosted in a multi-tenant infrastructure or not',
        self::FEATURE_DB_IN_CLUSTER => 'Defines whether the database is hosted in the studio host or in the cluster',
        self::FEATURE_CUSTOM_GIT_REPO =>
            '[FP-4863] Defines whether the customer have brought their own custom git repository',
        self::FEATURE_DISABLE_FRONTASTIC_CI => 'Defines that the code is not build by our Frontastic CI',
        self::FEATURE_ALLOW_CLEAR_FIXTURES => 'Toggle the locking of the clear fixtures endpoint for a customer',
        self::FEATURE_FECL => 'Toggle support for FECL criteria in dynamic page rule scheduling',
        self::FEATURE_PAGE_SCHEDULE_FECL => 'Toggle support for FECL criteria in page version scheduling (deprecated?)',
        self::FEATURE_MACHINES_REGIONS => 'Toggle support for alternate regions in sandboxes',
        self::FEATURE_VERSION_HISTORY =>
            '[FP-3387] Toggle the "Version History" section for the Editor Page on the Frontend',
        self::FEATURE_API_KEYS_PAGE => '[FP-4392] Toggle the "API Key Page" for the Developer section on the Frontend.',
        self::FEATURE_HUMIO => '[FP-4536] Toggle the "humio" feature for the User Profile section on the Frontend.',
        self::FEATURE_STUDIO_ORCHESTRATED_DEPLOYMENTS => 'Toggle manual staging deployments for multi-tenant customers',
        self::FEATURE_PREVIEW_LOCALE => '[Hackday] Toggle the forced locale feature on page previews',
        self::FEATURE_DISABLE_SLUG_TRANSLITERATION =>
            '[FP-5341] Defines whether the customer wants URL Transliteration to be disabled in Studio',
        self::FEATURE_UPDATE_LOCALE_DROPDOWN =>
            '[FP-4145] Update locale dropdown on stage/page/PageSettingsDialog.tsx',
        self::FEATURE_REFACTOR_FECL => '[FP-2380] Refactor FECL to use the new FECL API and Designs',
        self::FEATURE_NETLIFY_PREVIEW => '[FP-5733] Add Netlify Preview to the Studio Developer Section',
    ];

    // these feature flags should only be toggled by developers
    // only after major changes to the customer's infrastructure
    public const INFTRASTRUCTURE_SPECIFIC_FEATURES = [
        self::FEATURE_FRONTASTIC_NEXTJS,
        self::FEATURE_MULTITENANT,
        self::FEATURE_DB_IN_CLUSTER,
        self::FEATURE_CUSTOM_GIT_REPO,
        self::FEATURE_DISABLE_FRONTASTIC_CI,
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
}
