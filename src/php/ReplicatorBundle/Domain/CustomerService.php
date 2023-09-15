<?php

namespace Frontastic\Common\ReplicatorBundle\Domain;

use Frontastic\Backstage\DeveloperBundle\Domain\BuildVersion;
use Frontastic\Backstage\DeveloperBundle\Domain\CustomStream;
use Frontastic\Backstage\DeveloperBundle\Domain\Tastic;
use Frontastic\Backstage\NotificationBundle\Domain\Notification;
use Frontastic\Backstage\ProjectConfigurationBundle\Domain\ProjectConfiguration;
use Frontastic\Backstage\StageBundle\Domain\NodesTreeCache;
use Frontastic\Backstage\VersionBundle\Domain\Version;
use Symfony\Component\Yaml\Yaml;
use Frontastic\Common\ReplicatorBundle\Domain\Customer;

class CustomerService
{
    /**
     * @var string
     */
    private $customerDir;

    /**
     * @var string
     */
    private $deployedCustomers;

    /**
     * @var Customer[]
     */
    private $customers = null;

    /**
     * List of APIs with their default engines
     */
    private $apis = [
        'product' => [
            'engine' => 'commercetools',
        ],
        'productSearch' => [],
        'account' => [
            'engine' => 'commercetools',
        ],
        'cart' => [
            'engine' => 'commercetools',
        ],
        'wishlist' => [
            'engine' => 'commercetools',
        ],
        'search' => [
            'engine' => 'commercetools',
        ],
        'content' => [
            'engine' => 'contentful',
        ],
        'media' => [
            'engine' => 'cloudinary',
        ],
    ];

    public function __construct(string $customerDir, string $deployedCustomers)
    {
        $this->customerDir = $customerDir;
        $this->deployedCustomers = $deployedCustomers;
    }

    private function parseCustomers(): void
    {
        if ($this->customers !== null) {
            return;
        }

        $this->customers = [];
        foreach (glob($this->customerDir . '/*.yml') as $customerFile) {
            $this->parseCustomerFile($customerFile);
        }

        foreach (glob($this->deployedCustomers) as $customerFile) {
            try {
                $customer = $this->parseCustomerFile($customerFile, true);
            } catch (\Throwable $e) {
                if (strstr($customerFile, 'demo.frontastic.io') === false &&
                    strstr($customerFile, 'show.frontastic.io') === false) {
                    // throw yml-problem for normal customer (but not for demo)
                    throw $e;
                }
                // Ignore wrong YML files
            }
        }
        $this->customers = array_values($this->customers);
    }

    private function explodeConfiguration(array $values, ?array $defaults = null): array
    {
        $baseConfiguration = array_replace_recursive($defaults ?: $this->apis, $values);

        foreach (array_keys($this->apis) as $api) {
            $engine = $baseConfiguration[$api]['engine'] ?? null;

            if ($engine === null) {
                continue;
            }

            $baseConfiguration[$api] = array_replace_recursive(
                $baseConfiguration[$api],
                $baseConfiguration[$engine] ?? []
            );
        }

        return $baseConfiguration;
    }

    private function convertConfigurationToObjects(array $configuration): array
    {
        return array_map(
            function (array $values) {
                return is_array($values) ? (object)$values : $values;
            },
            $configuration
        );
    }

    private function parseCustomerFile(string $customerFile, $transient = false): Customer
    {
        $customer = Yaml::parse(file_get_contents($customerFile));
        if (isset($this->customers[$customer['name']])) {
            return $this->customers[$customer['name']];
        }

        $customerConfiguration = $this->explodeConfiguration($customer['configuration'] ?? []);

        $this->customers[$customer['name']] = $customer = new Customer([
            'name' => $customer['name'],
            'secret' => $customer['secret'],
            'edition' => $customer['edition'] ?? 'mirco',
            'hasPaasModifications' => $customer['hasPaasModifications'] ?? false,
            'machineLimit' => $customer['machineLimit'] ?? 3,
            'machineRegionToProviderMap' => $customer['machineRegionToProviderMap'] ?? [],
            'features' => $customerFeatures = ($customer['features'] ?? []),
            'isTransient' => $transient,
            'configuration' => $this->convertConfigurationToObjects($customerConfiguration),
            'environments' => $customer['environments'] ?? [
                'production',
                'staging',
                'development',
            ],
            'projects' => array_map(
                function (array $project) use ($customer, $customerConfiguration, $customerFeatures): Project {
                    $projectSpecificEntities = $project['projectSpecific'] ?? [];
                    // Frontastic.Backstage.StageBundle.Domain.NodesTreeCache entity is used for nodes tree
                    // caching and must follow Frontastic.Backstage.StageBundle.Domain.Node replication rules
                    if (\in_array(Node::COUCHDB_DOCUMENT_TYPE, $projectSpecificEntities)) {
                        $projectSpecificEntities[] = NodesTreeCache::COUCHDB_DOCUMENT_TYPE;
                    }

                    if (!\in_array(Notification::COUCHDB_DOCUMENT_TYPE, $projectSpecificEntities)) {
                        $projectSpecificEntities[] = Notification::COUCHDB_DOCUMENT_TYPE;
                    }

                    if (!\in_array(ProjectConfiguration::COUCHDB_DOCUMENT_TYPE, $projectSpecificEntities)) {
                        $projectSpecificEntities[] = ProjectConfiguration::COUCHDB_DOCUMENT_TYPE;
                    }

                    if (!\in_array(Version::COUCHDB_DOCUMENT_TYPE, $projectSpecificEntities)) {
                        $projectSpecificEntities[] = Version::COUCHDB_DOCUMENT_TYPE;
                    }

                    if (\in_array(Customer::FEATURE_MULTITENANT, $customerFeatures) &&
                        !\in_array(BuildVersion::COUCHDB_DOCUMENT_TYPE, $customerFeatures)) {
                        $projectSpecificEntities[] = BuildVersion::COUCHDB_DOCUMENT_TYPE;
                    }

                    if (\in_array(Tastic::COUCHDB_DOCUMENT_TYPE, $projectSpecificEntities) &&
                        !\in_array(CustomStream::COUCHDB_DOCUMENT_TYPE, $projectSpecificEntities)
                    ) {
                        $projectSpecificEntities[] = CustomStream::COUCHDB_DOCUMENT_TYPE;
                    }

                    $publicKey = $project['encryptedFieldsPublicKey'] ?? null;

                    return new Project([
                        'projectId' => $project['projectId'],
                        'name' => $project['name'],
                        'customer' => $customer['name'],
                        'apiKey' => $customer['secret'],
                        'publicUrl' => $project['publicUrl'] ?? null,
                        'preview' => isset($project['preview']) ? (object) $project['preview'] : null,
                        'previewUrl' => $project['previewUrl'] ?? null,
                        'webpackPort' => $project['webpackPort'] ?? 3000,
                        'ssrPort' => $project['ssrPort'] ?? 8000,
                        'encryptedFieldsPublicKey' => $publicKey ? base64_decode($publicKey) : $publicKey,
                        'configuration' => $this->convertConfigurationToObjects(
                            array_replace_recursive(
                                $customerConfiguration,
                                $this->explodeConfiguration($project['configuration'] ?? [], $customerConfiguration)
                            )
                        ),
                        'languages' => $project['languages'] ?? [$project['defaultLanguage'] ?? 'eng_GB'],
                        'defaultLanguage' => $project['defaultLanguage'] ?? 'eng_GB',
                        'projectSpecific' => $projectSpecificEntities,
                        'data' => array_merge_recursive($customer['data'] ?? [], $project['data'] ?? []),
                        'endpoints' => array_map(
                            function (array $endpoint): Endpoint {
                                return new Endpoint([
                                    'name' => $endpoint['name'],
                                    'url' => $endpoint['url'],
                                    'push' => $endpoint['push'] ?? true,
                                    'environment' => $endpoint['environment'] ?? 'production',
                                ]);
                            },
                            $project['endpoints'] ?? []
                        )
                    ]);
                },
                $customer['projects']
            )
        ]);

        return $customer;
    }

    /**
     * @return \Frontastic\Common\ReplicatorBundle\Domain\Customer[]
     */
    public function getCustomers(): array
    {
        $this->parseCustomers();
        return $this->customers;
    }

    public function getCustomer(string $customerName): Customer
    {
        $this->parseCustomers();
        foreach ($this->customers as $customer) {
            if ($customer->name === $customerName) {
                return $customer;
            }
        }

        throw new \OutOfBoundsException("Customer $customerName not found.");
    }

    public function getProject(string $customerName, string $projectName): Project
    {
        $customer = $this->getCustomer($customerName);

        foreach ($customer->projects as $project) {
            if ($project->projectId === $projectName) {
                return $project;
            }
        }

        throw new \OutOfBoundsException("Project $projectName not found for customer $customerName.");
    }

    public function getForHost(string $host): Customer
    {
        if (strpos($host, 'localhost') === 0) {
            return $this->getCustomer('demo');
        }

        return $this->getCustomer(explode('.', $host)[0]);
    }
}
