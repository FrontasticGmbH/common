<?php

namespace Frontastic\Common\ApiTests;

use Frontastic\Common\ProjectApiBundle\Domain\Attribute;
use Frontastic\Common\ProjectApiBundle\Domain\ProjectApiFactory;
use Frontastic\Common\ReplicatorBundle\Domain\Project;

class ProjectApiTest extends FrontasticApiTestCase
{
    /**
     * @var ProjectApiFactory
     */
    private $projectApiFactory;

    protected function setUp(): void
    {
        parent::setUp();
        $this->projectApiFactory = self::$container->get(ProjectApiFactory::class);
    }

    /**
     * @dataProvider projectsToTest
     */
    public function testSearchableAttributesAreNotEmpty(Project $project): void
    {
        $projectApi = $this->projectApiFactory->factor($project);

        $searchableAttributes = $projectApi->getSearchableAttributes();
        $this->assertNotEmpty($searchableAttributes);
    }

    /**
     * @dataProvider projectsToTest
     */
    public function testSearchableAttributesContainAttributeInstances(Project $project): void
    {
        $projectApi = $this->projectApiFactory->factor($project);

        $searchableAttributes = $projectApi->getSearchableAttributes();
        $this->assertContainsOnlyInstancesOf(Attribute::class, $searchableAttributes);
    }
}
