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
        $this->projectApiFactory = self::$container->get(ProjectApiFactory::class);
    }

    /**
     * @dataProvider projectsToTest
     */
    public function testSearchableAttributesAreNotEmpty(Project $project): void
    {
        $this->assertNotEmpty($this->getSearchableAttributesForProject($project));
    }

    /**
     * @dataProvider projectsToTest
     */
    public function testSearchableAttributesContainAttributeInstances(Project $project): void
    {
        $this->assertContainsOnlyInstancesOf(Attribute::class, $this->getSearchableAttributesForProject($project));
    }

    /**
     * @dataProvider projectsToTest
     */
    public function testSearchableAttributesHaveUniqueIds(Project $project): void
    {
        $attributeIds = array_map(
            function (Attribute $attribute) {
                return $attribute->attributeId;
            },
            $this->getSearchableAttributesForProject($project)
        );
        foreach (array_count_values($attributeIds) as $attributeId => $count) {
            $this->assertEquals(1, $count, 'Attribute ' . $attributeId . ' returned more then once');
        }
    }

    /**
     * @dataProvider projectsToTest
     */
    public function testSearchableAttributesHaveAttributeIdAsKey(Project $project): void
    {
        $searchableAttributes = $this->getSearchableAttributesForProject($project);
        foreach ($searchableAttributes as $key => $searchableAttribute) {
            $this->assertEquals($searchableAttribute->attributeId, $key);
        }
    }

    /**
     * @dataProvider projectsToTest
     */
    public function testSearchableAttributesHaveValidType(Project $project)
    {
        $searchableAttributes = $this->getSearchableAttributesForProject($project);
        foreach ($searchableAttributes as $searchableAttribute) {
            $this->assertContains(
                $searchableAttribute->type,
                Attribute::TYPES,
                $searchableAttribute->attributeId . ' has invalid type'
            );
        }
    }

    /**
     * @dataProvider projectsToTest
     */
    public function testSearchableAttributesHaveALabelForAllLanguages(Project $project)
    {
        $searchableAttributes = $this->getSearchableAttributesForProject($project);
        foreach ($searchableAttributes as $searchableAttribute) {
            if ($searchableAttribute->label === null) {
                // Special case for money
                continue;
            }

            $this->assertIsValidTranslatedLabel($project, $searchableAttribute->label);
        }
    }

    /**
     * @dataProvider projectsToTest
     */
    public function testSearchableAttributesContainsRequiredAttributeTypesAtLeastOnce(Project $project)
    {
        $attributeTypes = array_map(
            function (Attribute $attribute) {
                return $attribute->type;
            },
            $this->getSearchableAttributesForProject($project)
        );
        foreach ([Attribute::TYPE_MONEY, Attribute::TYPE_CATEGORY_ID] as $attributeType) {
            $this->assertContains($attributeType, $attributeTypes);
        }
    }

    /**
     * @dataProvider projectsToTest
     */
    public function testSearchableAttributesHaveValidValues(Project $project)
    {
        $searchableAttributes = $this->getSearchableAttributesForProject($project);
        foreach ($searchableAttributes as $searchableAttribute) {
            if ($searchableAttribute->type === Attribute::TYPE_ENUM ||
                $searchableAttribute->type === Attribute::TYPE_LOCALIZED_ENUM) {
                $this->assertNotNull($searchableAttribute->values);

                foreach ($searchableAttribute->values as $value) {
                    $this->assertArrayHasKey('key', $value);
                    $this->assertInternalType('string', $value['key']);

                    if ($searchableAttribute->type === Attribute::TYPE_ENUM) {
                        $this->assertArrayHasKey('label', $value);
                        $this->assertInternalType('string', $value['label']);
                    } else {
                        $this->assertArrayHasKey('label', $value);
                        $this->assertIsValidTranslatedLabel($project, $value['label']);
                    }
                }
            } else {
                $this->assertNull($searchableAttribute->values);
            }
        }
    }

    private function getSearchableAttributesForProject(Project $project)
    {
        $projectApi = $this->projectApiFactory->factor($project);
        return $projectApi->getSearchableAttributes();
    }

    private function assertIsValidTranslatedLabel(Project $project, $label): void
    {
        $this->assertInternalType('array', $label);
        $this->assertContainsOnly('string', $label);
        $this->assertEquals($project->languages, array_keys($label));
    }
}
