<?php

namespace Frontastic\Common\ReplicatorBundle\Domain;

use PHPUnit\Framework\TestCase;
use Frontastic\Common\ReplicatorBundle\Domain\Project;

class ProjectTest extends TestCase
{
    public function testSandboxExtensionRunnerHost()
    {
        $project = new Project([
            'configuration' => [
                'multitenant' => [
                    'environments' => [
                        'dev' => 'http://localhost:8087',
                        'staging' => 'http://localhost:8087',
                    ],
                ],
            ],
        ]);

        $this->assertEquals('http://localhost:8087', $project->getExtensionRunnerManagerHost('dev'));
        $this->assertEquals('http://localhost:8087', $project->getExtensionRunnerManagerHost('staging'));
    }

    public function testNonSandboxExtensionRunnerHost()
    {
        $project = new Project([
            'configuration' => [
                'multitenant' => [
                    'environments' => [
                        'dev' => '000',
                        'staging' => 'XXX',
                    ],
                ],
            ],
        ]);

        $this->assertEquals(
            'https://multitenant-gke-000-extensions.frontastic.cloud',
            $project->getExtensionRunnerManagerHost('dev')
        );
        $this->assertEquals(
            'https://multitenant-gke-XXX-extensions.frontastic.cloud',
            $project->getExtensionRunnerManagerHost('staging')
        );
    }

    public function testNonMultiTenantExtensionRunnerHostDev()
    {
        $this->expectException(\OutOfBoundsException::class);
        $project = new Project([
            'configuration' => [],
        ]);

        $project->getExtensionRunnerManagerHost('dev');
    }

    public function testNonMultiTenantExtensionRunnerHostStaging()
    {
        $this->expectException(\OutOfBoundsException::class);
        $project = new Project([
            'configuration' => [],
        ]);

        $project->getExtensionRunnerManagerHost('staging');
    }
}
