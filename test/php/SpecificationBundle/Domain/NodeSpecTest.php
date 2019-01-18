<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

use PHPUnit\Framework\TestCase;

class NodeSpecTest extends TestCase
{
    const FIXTURE_DIR = __DIR__ . '/_fixtures/node';

    /**
     * @dataProvider provideSchemaFiles
     */
    public function testSchemaParsesCorrectly($schemaFile)
    {
        $specParser = new NodeSpecParser();

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
