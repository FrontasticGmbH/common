<?php

namespace Frontastic\Common\SpecificationBundle\Domain;


class CustomAppSpecParserTest extends \PHPUnit\Framework\TestCase
{
    public function getFailingSchemaFiles()
    {
        return array(
            ['parse_error.json', 'Failed to parse JSON.'],
            ['invalid_schema.json', 'JSON does not follow schema.'],
            ['reserved_name.json', 'JSON does not follow schema.'],
            ['duplicate_field.json', 'Inconsistent field definition.'],
            ['invalid_reference.json', 'Invalid display field reference.'],
            ['invalid_index.json', 'Invalid index field reference.'],
            ['invalid_index_type.json', 'Invalid index field type.'],
            ['disallowed_type_custom.json', 'JSON does not follow schema.'],
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

        $specParser->parse(file_get_contents(__DIR__ . '/_fixtures/' . $schemaFile));
    }
}
