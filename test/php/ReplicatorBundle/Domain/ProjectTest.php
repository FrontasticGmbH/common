<?php

namespace Frontastic\Common\ReplicatorBundle\Domain;

use PHPUnit\Framework\TestCase;
use Frontastic\Common\ReplicatorBundle\Domain\Project;

class ProjectTest extends TestCase
{
    public function testSandboxExtensionRunnerUrl()
    {
        $project = new Project([
            'configuration' => [
                'multitenant' => (object) [
                    'environments' => [
                        'dev' => 'http://localhost:8087',
                        'staging' => 'http://localhost:8087',
                    ],
                ],
            ],
        ]);

        $this->assertEquals('http://localhost:8087', $project->getExtensionRunnerManagerUrl('dev'));
        $this->assertEquals('http://localhost:8087', $project->getExtensionRunnerManagerUrl('staging'));
    }

    public function testNonSandboxExtensionRunnerUrl()
    {
        $project = new Project([
            'configuration' => [
                'multitenant' => (object) [
                    'environments' => [
                        'dev' => '000',
                        'staging' => 'XXX',
                    ],
                ],
            ],
        ]);

        $this->assertEquals(
            'https://multitenant-gke-000-extensions.frontastic.cloud',
            $project->getExtensionRunnerManagerUrl('dev')
        );
        $this->assertEquals(
            'https://multitenant-gke-XXX-extensions.frontastic.cloud',
            $project->getExtensionRunnerManagerUrl('staging')
        );
    }

    public function testNonMultiTenantExtensionRunnerHostDev()
    {
        $this->expectException(\OutOfBoundsException::class);
        $project = new Project([
            'configuration' => [],
        ]);

        $project->getExtensionRunnerManagerUrl('dev');
    }

    public function testNonMultiTenantExtensionRunnerUrlStaging()
    {
        $this->expectException(\OutOfBoundsException::class);
        $project = new Project([
            'configuration' => [],
        ]);

        $project->getExtensionRunnerManagerUrl('staging');
    }
}
