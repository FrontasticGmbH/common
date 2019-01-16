<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

use JsonSchema\Constraints\Factory;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator;

class SchemaValidatorFactory
{
    const SCHEMA_DIRECTORY = __DIR__ . '/../../../json';

    public static function createValidator(): Validator
    {
        $schemaStorage = new SchemaStorage();
        $schemaStorage->addSchema(
            'https://frontastic.cloud/json/schema/common',
            json_decode(file_get_contents(
                self::schemaFilePath('schema/common.json')
            ))
        );

        $validator = new Validator(new Factory($schemaStorage));
        return $validator;
    }

    public static function schemaFilePath(string $fileName): string
    {
        return self::SCHEMA_DIRECTORY . '/' . $fileName;
    }
}
