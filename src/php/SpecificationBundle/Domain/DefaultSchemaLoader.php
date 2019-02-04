<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

use http\Exception\InvalidArgumentException;

class DefaultSchemaLoader
{
    const DEFAULT_SCHEMA_DIR = __DIR__ . '/../../../js/configuration/defaultSchemas';

    public function load(string $schemaType): \stdClass
    {
        $schemaFile = self::DEFAULT_SCHEMA_DIR . '/' . $schemaType . '.json';

        if (!file_exists($schemaFile)) {
<<<<<<< HEAD
            throw new InvalidArgumentException(
                sprintf(
                    'Default schema for type "%s" not found',
                    $schemaType
                )
            );
=======
            throw new InvalidArgumentException(sprintf(
                'Default schema for type "%s" not found',
                $schemaType
            ));
>>>>>>> Fixed minor PHP code style issues
        }

        return json_decode(file_get_contents($schemaFile));
    }
}
