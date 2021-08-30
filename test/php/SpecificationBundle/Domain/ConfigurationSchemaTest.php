<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

use PHPUnit\Framework\TestCase;

class ConfigurationSchemaTest extends TestCase
{
    /**
     * @dataProvider provideRegressionExamples
     */
    public function testRegression(string $exampleName, array $inputFixture, array $outputExpectation)
    {
        $schema = ConfigurationSchema::fromSchemaAndConfiguration(
            $inputFixture['schema'],
            $inputFixture['configuration'] ?? []
        );

        foreach ($outputExpectation as $expectationSet) {
            $this->assertEquals(
                $expectationSet->value,
                $schema->getFieldValue($expectationSet->key)
            );
        }
    }

    public static function provideRegressionExamples(): array
    {
        $examples = [];
        foreach (glob(__DIR__ . '/../../../_fixture/configuration/*') as $dir) {
            $examples[] = [
                basename($dir),
                json_decode(file_get_contents($dir . '/input_fixture.json'), true),
                json_decode(file_get_contents($dir . '/output_expectation.json')),
            ];
        }
        return $examples;
    }
}
