<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

use \InvalidArgumentException;
use Frontastic\Common\CoreBundle\Domain\Json\Json;

class DefaultSchemaLoader
{
    const DEFAULT_SCHEMA_DIR = __DIR__ . '/../../../js/configuration/defaultSchemas';

    public function load(string $schemaType): \stdClass
    {
        $schemaFile = self::DEFAULT_SCHEMA_DIR . '/' . $schemaType . '.json';

        if (!file_exists($schemaFile)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Default schema for type "%s" not found',
                    $schemaType
                )
            );
        }

        return Json::decode(file_get_contents($schemaFile));
    }
}
