<?php

namespace Frontastic\Common\ReplicatorBundle\Domain;

use Symfony\Component\Yaml\Yaml;

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
     * @var \Frontastic\Common\ReplicatorBundle\Domain\Customer[]
     */
    private $customers = null;

    /**
     * List of APIs with their default engines
     */
    private $apis = [
        'product' => [
            'engine' => 'commercetools',
        ],
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
                if (strstr($customerFile, 'demo') !== false) {
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
        foreach ($this->apis as $api => $defaultEngine) {
            $baseConfiguration[$api] = array_replace_recursive(
                $baseConfiguration[$api],
                $baseConfiguration[$baseConfiguration[$api]['engine']] ?? []
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
            'features' => $customer['features'] ?? [],
            'isTransient' => $transient,
            'configuration' => $this->convertConfigurationToObjects($customerConfiguration),
            'environments' => $customer['environments'] ?? [
                'production',
                'staging',
                'development',
            ],
            'projects' => array_map(
                function (array $project) use ($customer, $customerConfiguration): Project {
                    return new Project([
                        'projectId' => $project['projectId'],
                        'name' => $project['name'],
                        'customer' => $customer['name'],
                        'apiKey' => $customer['secret'],
                        'publicUrl' => $project['publicUrl'] ?? null,
                        'previewUrl' => $project['previewUrl'] ?? null,
                        'webpackPort' => $project['webpackPort'] ?? 3000,
                        'ssrPort' => $project['ssrPort'] ?? 8000,
                        'configuration' => $this->convertConfigurationToObjects(
                            array_replace_recursive(
                                $customerConfiguration,
                                $this->explodeConfiguration($project['configuration'] ?? [], $customerConfiguration)
                            )
                        ),
                        'languages' => $project['languages'] ?? [$project['defaultLanguage'] ?? 'eng_GB'],
                        'defaultLanguage' => $project['defaultLanguage'] ?? 'eng_GB',
                        'projectSpecific' => $project['projectSpecific'] ?? [],
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
