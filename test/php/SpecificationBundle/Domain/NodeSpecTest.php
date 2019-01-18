<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

use PHPUnit\Framework\TestCase;

class NodeSpecTest extends TestCase
{
    const FIXTURE_DIR = __DIR__ . '/_fixtures/node';

    /**
     * @dataProvider provideValidSchemaFiles
     */
    public function testSchemaParsesCorrectly($schemaFile)
    {
        $specParser = new NodeSpecParser();

        try {
            $actualSchema = $specParser->parse(file_get_contents($schemaFile));
        } catch (InvalidSchemaException $e) {
            $this->fail(
                sprintf('InvalidSchemaException (%s): %s', $e->getMessage(), $e->getError())
            );
        }

        $this->assertInstanceOf(\stdClass::class, $actualSchema);
    }

    public function provideValidSchemaFiles(): array
    {
        return array_map(function ($filename) {
            return [
                basename($filename) => $filename,
            ];
        }, glob(self::FIXTURE_DIR . '/valid/*.json'));
    }

    /**
     * @dataProvider provideInvalidSchemaFiles
     */
    public function testSchemaInvalid($schemaFile)
    {
        $specParser = new NodeSpecParser();

        $this->expectException(InvalidSchemaException::class);

        $specParser->parse(file_get_contents($schemaFile));
    }

    public function provideInvalidSchemaFiles(): array
    {
        return array_map(function ($filename) {
            return [
                basename($filename) => $filename,
            ];
        }, glob(self::FIXTURE_DIR . '/invalid/*.json'));
    }
}
