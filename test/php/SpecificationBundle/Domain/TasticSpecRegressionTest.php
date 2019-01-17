<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

use PHPUnit\Framework\TestCase;

class TasticSpecRegressionTest extends TestCase
{
    const FIXTURE_DIR = __DIR__ . '/_fixtures/tastic_regression';

    /**
     * @dataProvider provideSchemaFiles
     */
    public function testSchemasStillParseCorrectly($schemaFile)
    {
        $specParser = new TasticSpecParser();

        try {
            $actualSchema = $specParser->parse(file_get_contents(
                self::FIXTURE_DIR . '/' . $schemaFile
            ));
        } catch (InvalidSchemaException $e) {
            $this->fail(
                sprintf('InvalidSchemaException (%s): %s', $e->getMessage(), $e->getError())
            );
        }

        $this->assertInstanceOf(\stdClass::class, $actualSchema);
    }

    public function provideSchemaFiles(): array
    {
        return array_map(function ($filename) {
            return [
                basename($filename)
            ];
        }, glob(self::FIXTURE_DIR . '/*.json'));
    }
}
