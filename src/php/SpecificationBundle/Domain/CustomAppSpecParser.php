<?php

namespace Frontastic\Common\SpecificationBundle\Domain;

class CustomAppSpecParser extends ValidatingSpecParser
{
    public function __construct()
    {
        parent::__construct('appSchema.json');
    }

    protected function verifySchema(\stdClass $schema): \stdClass
    {
        $fields = $this->getVerifiedFieldArray($schema);
        $this->verifyDisplayedFields($schema, $fields);
        $this->verifyIndexedFields($schema, $fields);
        return $schema;
    }

    private function getVerifiedFieldArray(\stdClass $schema): array
    {
        $fields = [];
        foreach ($schema->schema as $group) {
            foreach ($group->fields as $field) {
                if (!isset($field->field)) {
                    continue;
                }

                if (isset($fields[$field->field])) {
                    throw new InvalidSchemaException(
                        "Inconsistent field definition.",
                        "Duplicate definition of field " . $field->field
                    );
                }

                $fields[$field->field] = $field->type;
            }
        }

        return $fields;
    }

    private function verifyDisplayedFields(\stdClass $schema, array $fields)
    {
        foreach ($schema->fields as $field) {
            if (!isset($fields[$field->field])) {
                throw new InvalidSchemaException(
                    "Invalid display field reference.",
                    "The field with name " . $field->field . " does not exist."
                );
            }
        }

        return $fields;
    }

    private function verifyIndexedFields(\stdClass $schema, array $fields)
    {
        if (!\property_exists($schema, 'indexes')) {
            return $fields;
        }
        
        $validIndexTypes = ["string", "integer", "boolean", "decimal"];

        foreach ($schema->indexes as $index) {
            foreach ($index->fields as $field) {
                if (!isset($fields[$field])) {
                    throw new InvalidSchemaException(
                        "Invalid index field reference.",
                        "The indexed field with name " . $field . " does not exist."
                    );
                }

                if (!in_array($fields[$field], $validIndexTypes)) {
                    throw new InvalidSchemaException(
                        "Invalid index field type.",
                        "The indexed field with name " . $field . " is of an invalid index type " .
                        $fields[$field] . " - supported types are: " . implode(', ', $validIndexTypes)
                    );
                }

                if (!preg_match('(^[0-9a-zA-Z_]+$)', $index->name)) {
                    throw new InvalidSchemaException(
                        "Invalid index field name.",
                        "Invalid index field name {$index->name} - must match the pattern: ^[0-9a-zA-Z_]+$"
                    );
                }
            }
        }

        return $fields;
    }
}
