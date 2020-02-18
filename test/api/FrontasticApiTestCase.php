<?php

namespace Frontastic\Common\ApiTests;

use Frontastic\Common\EnvironmentResolver;
use Frontastic\Common\ReplicatorBundle\Domain\CustomerService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FrontasticApiTestCase extends KernelTestCase
{
    protected function setUp(): void
    {
        $environmentResolver = new EnvironmentResolver();
        $environmentResolver->loadEnvironmentVariables([
            dirname(__DIR__, 5),
            dirname(__DIR__, 2),
            __DIR__,
        ]);

        self::bootKernel();
    }

    public function projectsToTest(): array
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
                $projects[$description] = [$project, $customer];
            }
        }
        return $projects;
    }
}
