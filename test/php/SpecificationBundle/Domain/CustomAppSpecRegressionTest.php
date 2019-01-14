<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

use PHPUnit\Framework\TestCase;

class CustomAppSpecRegressionTest extends TestCase
{
    const FIXTURE_DIR = __DIR__ . '/_fixtures/custom_app_regression';

    /**
     * @dataProvider provideSchemaFiles
     */
    public function testSchemasStillParseCorrectly($schemaFile)
    {
        $specParser = new CustomAppSpecParser();

        $actualSchema = $specParser->parse(file_get_contents($schemaFile));

        $this->assertInstanceOf(\stdClass::class, $actualSchema);
    }

    public function provideSchemaFiles(): array
    {
        return array_map(function ($filename) {
            return [
                $filename
            ];
        }, glob(self::FIXTURE_DIR . '/*.json'));
    }
}
