<?php

namespace Frontastic\Common\ApiTests\ProjectApi;

use Frontastic\Common\ApiTests\FrontasticApiTestCase;
use Frontastic\Common\ProjectApiBundle\Domain\Attribute;
use Frontastic\Common\ProjectApiBundle\Domain\ProjectApiFactory;
use Frontastic\Common\ReplicatorBundle\Domain\Project;

class SearchableAttributesTest extends FrontasticApiTestCase
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
     * @dataProvider project
     */
    public function testAttributesAreNotEmpty(Project $project): void
    {
        $this->assertNotEmpty($this->getAttributesForProject($project));
    }

    /**
     * @dataProvider project
     */
    public function testAttributesContainAttributeInstances(Project $project): void
    {
        $this->assertContainsOnlyInstancesOf(Attribute::class, $this->getAttributesForProject($project));
    }

    /**
     * @dataProvider project
     */
    public function testAttributesHaveUniqueIds(Project $project): void
    {
        $attributeIds = array_map(
            function (Attribute $attribute) {
                return $attribute->attributeId;
            },
            $this->getAttributesForProject($project)
        );
        foreach (array_count_values($attributeIds) as $attributeId => $count) {
            $this->assertEquals(1, $count, 'Attribute ' . $attributeId . ' returned more then once');
        }
    }

    /**
     * @dataProvider project
     */
    public function testAttributesHaveAttributeIdAsKey(Project $project): void
    {
        $attributes = $this->getAttributesForProject($project);
        foreach ($attributes as $key => $searchableAttribute) {
            $this->assertEquals($searchableAttribute->attributeId, $key);
        }
    }

    /**
     * @dataProvider project
     */
    public function testAttributesHaveValidType(Project $project)
    {
        $attributes = $this->getAttributesForProject($project);
        foreach ($attributes as $searchableAttribute) {
            $this->assertContains(
                $searchableAttribute->type,
                Attribute::TYPES,
                $searchableAttribute->attributeId . ' has invalid type'
            );
        }
    }

    /**
     * @dataProvider project
     */
    public function testAttributesHaveALabelForAllLanguages(Project $project)
    {
        $attributes = $this->getAttributesForProject($project);
        foreach ($attributes as $searchableAttribute) {
            if ($searchableAttribute->label === null) {
                // Special case for money
                continue;
            }

            $this->assertIsValidTranslatedLabel($project, $searchableAttribute->label);
        }
    }

    /**
     * @dataProvider project
     */
    public function testAttributesContainsRequiredAttributeTypesAtLeastOnce(Project $project)
    {
        $attributeTypes = array_map(
            function (Attribute $attribute) {
                return $attribute->type;
            },
            $this->getAttributesForProject($project)
        );
        foreach ([Attribute::TYPE_MONEY, Attribute::TYPE_CATEGORY_ID] as $attributeType) {
            $this->assertContains($attributeType, $attributeTypes);
        }
    }

    /**
     * @dataProvider project
     */
    public function testAttributesHaveValidValues(Project $project)
    {
        $attributes = $this->getAttributesForProject($project);
        foreach ($attributes as $searchableAttribute) {
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

    private function getAttributesForProject(Project $project)
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
