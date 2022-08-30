<?php

namespace Frontastic\Common\SpecificationBundle\Domain;


use InvalidArgumentException;

class CustomAppSpecParserTest extends \PHPUnit\Framework\TestCase
{
    const FIXTURE_DIR = __DIR__ . '/_fixtures';

    public function getFailingSchemaFiles()
    {
        return array(
            ['parse_error.json', 'Failed to lint JSON.'],
            ['invalid_schema.json', 'JSON does not follow schema.'],
            ['reserved_name.json', 'JSON does not follow schema.'],
            ['duplicate_field.json', 'Inconsistent field definition.'],
            ['invalid_reference.json', 'Invalid display field reference.'],
            ['invalid_index.json', 'Invalid index field reference.'],
            ['invalid_index_type.json', 'Invalid index field type.'],
        );
    }

    /**
     * @dataProvider getFailingSchemaFiles
     */
    public function testFailOnInvalidSchema(string $schemaFile, string $message)
    {
        $specParser = new CustomAppSpecParser();

        $this->expectException(InvalidSchemaException::class);
        $this->expectExceptionMessage($message);

        $schema = file_get_contents(self::FIXTURE_DIR . '/' . $schemaFile);
        $specParser->parse($schema);
    }

    public function testUnknownFieldTypeRaisesSenisbleError()
    {
        $specParser = new CustomAppSpecParser();

        try {
            $specParser->parse(file_get_contents(self::FIXTURE_DIR . '/unknown_field_type.json'));
        } catch (InvalidSchemaException $e) {
            $this->assertStringContainsString(
                "schema[0].fields[0]: Field type doesn't have a valid value.Check the field type matches the value type",
                $e->getError(),
                'Exception error does not contain required text.'
            );
            return;
        }
        $this->fail(sprintf(
            'Expected exception of type "%s" not thrown',
            InvalidSchemaException::class
        ));

    }
}
