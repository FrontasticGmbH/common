<?php

namespace Frontastic\Common\ApiTests\ProjectApi;

use Frontastic\Common\ApiTests\FrontasticApiTestCase;
use Frontastic\Common\ProjectApiBundle\Domain\Attribute;
use Frontastic\Common\ProjectApiBundle\Domain\ProjectApiFactory;
use Frontastic\Common\ReplicatorBundle\Domain\Project;

class SearchableAttributesTest extends FrontasticApiTestCase
{
    /**
     * @dataProvider project
     */
    public function testAttributesAreNotEmpty(Project $project): void
    {
        $this->assertNotEmpty($this->getSearchableAttributesForProject($project));
    }

    /**
     * @dataProvider project
     */
    public function testAttributesAreWellFormed(Project $project): void
    {
        $attributes = $this->getSearchableAttributesForProject($project);

        $this->assertContainsOnlyInstancesOf(Attribute::class, $attributes);

        foreach ($attributes as $key => $searchableAttribute) {
            $this->assertEquals($searchableAttribute->attributeId, $key);

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
    public function testAttributesHaveUniqueIds(Project $project): void
    {
        $attributeIds = array_map(
            function (Attribute $attribute) {
                return $attribute->attributeId;
            },
            $this->getSearchableAttributesForProject($project)
        );
        $this->assertArrayHasDistinctValues($attributeIds);
    }

    /**
     * @dataProvider project
     */
    public function testAttributesHaveALabelForAllLanguages(Project $project)
    {
        $attributes = $this->getSearchableAttributesForProject($project);
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
            $this->getSearchableAttributesForProject($project)
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
        $attributes = $this->getSearchableAttributesForProject($project);
        foreach ($attributes as $searchableAttribute) {
            if ($searchableAttribute->type === Attribute::TYPE_ENUM ||
                $searchableAttribute->type === Attribute::TYPE_LOCALIZED_ENUM) {
                $this->assertNotNull($searchableAttribute->values);

                foreach ($searchableAttribute->values as $value) {
                    $this->assertArrayHasKey('key', $value);
                    $this->assertIsString($value['key']);

                    if ($searchableAttribute->type === Attribute::TYPE_ENUM) {
                        $this->assertArrayHasKey('label', $value);
                        $this->assertIsString($value['label']);
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
}
