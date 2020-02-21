<?php

namespace Frontastic\Common\ApiTests;

use Frontastic\Common\EnvironmentResolver;
use Frontastic\Common\ReplicatorBundle\Domain\CustomerService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FrontasticApiTestCase extends KernelTestCase
{
    /**
     * @before
     */
    protected function setUpKernel(): void
    {
        $environmentResolver = new EnvironmentResolver();
        $environmentResolver->loadEnvironmentVariables([
            dirname(__DIR__, 5),
            dirname(__DIR__, 2),
            __DIR__,
        ]);

        self::bootKernel();
    }

    public function customerAndProject(): array
    {
        $customerService = new CustomerService(__DIR__ . '/config/customers', '');

        $projects = [];
        foreach ($customerService->getCustomers() as $customer) {
            foreach ($customer->projects as $project) {
                $description = sprintf(
                    'customer: %s, project: %s (ID %s)',
                    $customer->name,
                    $project->name,
                    $project->projectId
                );
                $projects[$description] = [$customer, $project];
            }
        }
        return $projects;
    }

    public function project(): array
    {
        return array_map(
            function (array $customerAndProject): array {
                return [$customerAndProject[1]];
            },
            $this->customerAndProject()
        );
    }

    public function projectAndLanguage(): array
    {
        $projectsAndLocales = [];

        foreach ($this->project() as $projectDescription => [$project]) {
            foreach ($project->languages as $language) {
                $projectsAndLocales[$projectDescription . ', language: ' . $language] = [
                    $project,
                    $language,
                ];
            }
        }

        return $projectsAndLocales;
    }

    /**
     * @param string[] $values
     */
    protected function assertArrayHasDistinctValues(array $values): void
    {
        foreach (array_count_values($values) as $value => $count) {
            $this->assertEquals(1, $count, 'Value ' . $value . ' occurred more then once');
        }
    }

    protected function buildQueryParameters(string $language, ?int $limit = null, ?int $offset = null)
    {
        $parameters = [
            'locale' => $language,
        ];

        if ($limit !== null) {
            $parameters['limit'] = $limit;
        }
        if ($offset !== null) {
            $parameters['offset'] = $offset;
        }

        return $parameters;
    }

}
