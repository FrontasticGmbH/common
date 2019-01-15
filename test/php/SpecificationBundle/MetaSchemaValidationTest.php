<?php

namespace Frontastic\Common\SpecificationBundle;

use JsonSchema\Validator;

class MetaSchemaValidationTest extends \PHPUnit\Framework\TestCase
{
    const SCHEMA_DIRECTORY = __DIR__ . '/../../../src/json';

    public static function provideSchemaFiles()
    {
        return [
            ['appSchema.json'],
            ['tasticSchema.json'],
        ];
    }

    /**
     * @dataProvider provideSchemaFiles
     */
    public function testCustomAppSchemaValid(string $schemaFile)
    {
        $validator = new Validator();

        $metaSchema = json_decode(file_get_contents(__DIR__ . '/_fixtures/json_schema.json'));
        $schema = json_decode(file_get_contents(self::SCHEMA_DIRECTORY . '/' . $schemaFile));

        $validator->validate($schema, $metaSchema);

        $this->assertTrue($validator->isValid(),$this->getValidationMessages($validator));
    }

    private function getValidationMessages(Validator $validator): string
    {
        if ($validator->isValid()) {
            return '';
        }

        var_dump ($validator->getErrors());

        return (implode("\n", $validator->getErrors()));
    }
}
