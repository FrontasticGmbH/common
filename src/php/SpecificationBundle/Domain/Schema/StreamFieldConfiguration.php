<?php

namespace Frontastic\Common\SpecificationBundle\Domain\Schema;

use Frontastic\Common\SpecificationBundle\Domain\ConfigurationSchema;

class StreamFieldConfiguration extends FieldConfiguration
{
    private ?string $streamType;

    protected static function doCreateFromSchema(string $type, array $fieldSchema): FieldConfiguration
    {
        /** @var StreamFieldConfiguration $schema */
        $schema = parent::doCreateFromSchema($type, $fieldSchema);
        $schema->streamType = $fieldSchema['streamType'] ?? $fieldSchema['dataSourceType'] ?? null;
        return $schema;
    }

    public function getStreamType()
    {
        return $this->streamType;
    }
}
