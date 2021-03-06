<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

use Frontastic\Common\SpecificationBundle\Domain\Schema\FieldConfiguration;

class ConfigurationSchema
{
    /**
     * @var array
     */
    private $schema;

    /**
     * @var array
     */
    private $configuration;

    /**
     * @var array<string, FieldConfiguration>
     */
    private $fieldConfigurations;

    private function __construct(array $schema, array $configuration, array $fieldConfigurations)
    {
        $this->schema = $schema;
        $this->configuration = $configuration;
        $this->fieldConfigurations = $fieldConfigurations;
    }

    public static function fromSchemaAndConfiguration(array $schema, array $configuration): self
    {
        $fieldConfigurations = [];

        foreach ($schema as $sectionSchema) {
            if (!is_array($sectionSchema)) {
                throw new \InvalidArgumentException('Sections have to be arrays');
            }

            $sectionName = $sectionSchema['name'] ?? '';
            $sectionFields = $sectionSchema['fields'] ?? [];

            if (!is_string($sectionName)) {
                throw new \InvalidArgumentException('The section name has to be a string');
            }
            if (!is_array($sectionFields)) {
                throw new \InvalidArgumentException('The section fields have to be an array');
            }

            foreach ($sectionFields as $fieldSchema) {
                if (!is_array($fieldSchema)) {
                    throw new \InvalidArgumentException('Fields have to be arrays');
                }

                $fieldConfiguration = FieldConfiguration::fromSchema($fieldSchema);
                $fieldConfigurations[$fieldConfiguration->getField()] = $fieldConfiguration;
            }
        }

        return new self($schema, $configuration, $fieldConfigurations);
    }

    public function hasField(string $fieldName): bool
    {
        return array_key_exists($fieldName, $this->fieldConfigurations);
    }

    public function getFieldValue(string $fieldName)
    {
        $fieldConfig = $this->getFieldConfiguration($fieldName);

        if (array_key_exists($fieldName, $this->configuration)) {
            return $this->configuration[$fieldName];
        }

        return $fieldConfig->getDefault();
    }

    private function getFieldConfiguration(string $fieldName): FieldConfiguration
    {
        if (!$this->hasField($fieldName)) {
            throw new \RuntimeException(sprintf('Unknown field %s', $fieldName));
        }

        return $this->fieldConfigurations[$fieldName];
    }
}
